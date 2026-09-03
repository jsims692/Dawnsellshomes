<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Support\MlsGridBudget;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * Caches each listing's primary photo locally (storage/app/public/listings).
 *
 * MLS GRID MediaURLs are pre-signed and expire (~1h) and media.mlsgrid.com
 * rate-limits hard, so hotlinking them from pages breaks — a card grid fires
 * 24 parallel requests and gets 429s. The replication license permits storing
 * media locally; this paces requests to stay inside the limits.
 */
class MlsMedia extends Command
{
    protected $signature = 'mls:media
        {--limit=0 : Stop after this many downloads (0 = no limit)}
        {--all : Include closed listings (default: for-sale only — sold rows feed stats, not cards)}
        {--city=* : Only these cities (testing / targeted backfill)}
        {--listing= : One listing id — full gallery regardless of status (on-demand sold galleries)}
        {--refresh : Re-download photos that are already cached}';

    protected $description = 'Download and cache listing photo galleries from MLS GRID';

    private const API = 'https://api.mlsgrid.com/v2/Property';
    private const MAX_WIDTH = 800;
    /** Sanity bound only — buyers get every photo the listing has. */
    public const PHOTOS_MAX = 60;

    /** Never download below this much free disk — the DB, logs, and the
     *  hourly sync all live on the same volume. */
    private const DISK_FLOOR_BYTES = 2_500_000_000;

    public function handle(): int
    {
        try {
            @ini_set('memory_limit', '512M');
        } catch (\Throwable) {
            // capped by max_memory_limit — fine
        }

        $token = config('services.mlsgrid.token');
        if (! $token) {
            $this->warn('MLSGRID_TOKEN not set — skipped.');

            return self::SUCCESS;
        }

        $dir = storage_path('app/public/listings');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $limit = (int) $this->option('limit');
        $done = 0;
        $skipped = 0;
        $failed = 0;

        $q = Listing::displayable()->where('is_demo', false)->whereRaw('JSON_LENGTH(media) > 0')
            ->when(! $this->option('all'), fn ($w) => $w->forSale())
            ->when($this->option('city') !== [], fn ($w) => $w->whereIn(
                DB::raw('LOWER(city)'), array_map(mb_strtolower(...), (array) $this->option('city'))))
            ->when($this->option('listing'), fn ($w) => $w->where('listing_id', $this->option('listing')))
            ->orderByRaw("FIELD(status, 'Active', 'Active Under Contract', 'Pending', 'Closed')")
            ->orderByDesc('mls_modified_at');

        foreach ($q->cursor() as $l) {
            $matched = true;
            if ((float) disk_free_space(storage_path()) < self::DISK_FLOOR_BYTES) {
                $this->warn('Disk floor reached — stopping media downloads.');
                $this->releaseTargetLock();
                break;
            }

            // For-sale listings get the full gallery (capped); --all rows
            // (closed) just the primary — except a targeted --listing fetch,
            // which is a viewer opening a sold page: they get the gallery.
            $cap = ($l->isForSale() || $this->option('listing')) ? self::PHOTOS_MAX : 1;
            $countFile = "{$dir}/{$l->listing_key}.count";
            $known = is_file($countFile) ? (int) file_get_contents($countFile) : null;

            if ($known !== null && ! $this->option('refresh')) {
                $complete = true;
                for ($i = 0; $i < min($known, $cap); $i++) {
                    if (! is_file($this->photoFile($dir, $l->listing_key, $i))) {
                        $complete = false;
                        break;
                    }
                }
                if ($complete) {
                    continue;
                }
            }

            // A visitor is waiting on a single-listing (sold gallery) fetch —
            // let it use the interactive reserve; bulk runs stay background.
            if (! MlsGridBudget::allow(background: ! $this->option('listing'))) {
                $this->warn('MLS GRID usage budget reached ('.MlsGridBudget::summary().') — stopping; rerun next hour.');
                $this->releaseTargetLock();
                break;
            }

            // One API call per listing needing photos: fresh signed URLs for
            // the whole gallery (stored URLs die within the hour).
            $urls = $this->refreshMedia($l, $token);
            if ($urls === null) {
                $skipped++;
                $this->releaseTargetLock(); // transient API failure — retry on a later view

                continue;
            }
            if ($urls === []) {
                $skipped++;
                // MRED strips media from some listings after closing. Record
                // "what's cached is all there is" so viewer pages stop
                // spawning futile fetches for a gallery that's gone upstream.
                if ($this->option('listing')) {
                    file_put_contents($countFile, $this->cachedCount($dir, $l->listing_key));
                    cache()->forget('gallery-fetch:'.$l->listing_id);
                }

                continue;
            }
            file_put_contents($countFile, count($urls));

            for ($i = 0; $i < min(count($urls), $cap); $i++) {
                $file = $this->photoFile($dir, $l->listing_key, $i);
                if (! $this->option('refresh') && is_file($file)) {
                    continue;
                }
                if ($this->download($urls[$i], $file)) {
                    $done++;
                } else {
                    $failed++;
                }
            }

            // Targeted (on-demand) fetch: record what was actually achieved
            // so a permanently-broken photo can't hold the page in
            // "incomplete" forever, and release the fetch lock immediately.
            if ($this->option('listing')) {
                file_put_contents($countFile, $this->cachedCount($dir, $l->listing_key));
                cache()->forget('gallery-fetch:'.$l->listing_id);
            }

            if ($limit > 0 && $done >= $limit) {
                break;
            }
        }

        // A targeted listing the query excluded entirely (e.g. media emptied
        // in the feed) must also converge: record the cached state and unlock.
        if ($this->option('listing') && ! ($matched ?? false)) {
            $key = Listing::where('listing_id', $this->option('listing'))->value('listing_key');
            if ($key) {
                file_put_contents("{$dir}/{$key}.count", $this->cachedCount($dir, $key));
            }
            $this->releaseTargetLock();
        }

        $pruned = $this->prune($dir);

        $this->info("Media cache: {$done} downloaded, {$skipped} without media, {$failed} failed, {$pruned} stale files pruned.");

        return self::SUCCESS;
    }

    /**
     * Disk stays proportional to demand: drop files only for listings gone
     * from the table (sold rows age out after 12 months and shed their files
     * then; on-demand sold galleries live for their comp-viewing window).
     */
    private function prune(string $dir): int
    {
        $known = Listing::pluck('listing_key')->flip();
        $pruned = 0;
        foreach (scandir($dir) ?: [] as $f) {
            if (! preg_match('/^([A-Z0-9]+?)(?:-\d+)?\.(?:jpg|count)$/', $f, $m)) {
                continue;
            }
            if (! isset($known[$m[1]])) {
                @unlink("{$dir}/{$f}");
                $pruned++;
            }
        }

        return $pruned;
    }

    /** Re-fetch this listing's Media (fresh signed URLs), ordered.
     *  null = transient failure (retry later); [] = the feed genuinely has
     *  no media for this listing (safe to record as final). */
    private function refreshMedia(Listing $l, string $token): ?array
    {
        $url = self::API.'?$filter='.rawurlencode("ListingId eq '{$l->listing_id}'").'&$expand=Media&$top=1';
        MlsGridBudget::pace();
        try {
            $resp = Http::withToken($token)->acceptJson()
                ->withHeaders(['Accept-Encoding' => 'gzip'])
                ->timeout(30)
                ->retry(3, 5000, fn ($e) => ! ($e instanceof \Illuminate\Http\Client\RequestException)
                    || $e->response->serverError(), throw: false)
                ->get($url);
        } catch (\Throwable) {
            return null;
        }
        MlsGridBudget::record(strlen($resp->body()));
        if (! $resp->successful()) {
            return null;
        }

        $rec = $resp->json()['value'][0] ?? null;
        if (! $rec) {
            return [];
        }

        $media = collect($rec['Media'] ?? [])
            ->sortBy('Order')
            ->map(fn ($m) => ['url' => $m['MediaURL'] ?? null, 'order' => $m['Order'] ?? 0])
            ->filter(fn ($m) => $m['url'])
            ->values();
        $l->update(['media' => $media->take(1)]);

        return $media->pluck('url')->all();
    }

    private function photoFile(string $dir, string $key, int $i): string
    {
        return $i === 0 ? "{$dir}/{$key}.jpg" : "{$dir}/{$key}-{$i}.jpg";
    }

    private function download(string $url, string $file): bool
    {
        for ($attempt = 1; $attempt <= 3; $attempt++) {
            MlsGridBudget::pace();
            try {
                $resp = Http::timeout(30)->get($url);
            } catch (\Throwable) {
                return false;
            }
            MlsGridBudget::record(strlen($resp->body()));
            if ($resp->status() === 429) {
                sleep(20 * $attempt); // back off and retry

                continue;
            }
            if (! $resp->successful()) {
                return false;
            }

            file_put_contents($file, $this->resize($resp->body()));

            return true;
        }

        return false;
    }

    /** Downscale to a card-sized JPEG; fall back to the original bytes. */
    private function resize(string $bytes): string
    {
        try {
            $src = @imagecreatefromstring($bytes);
            if (! $src) {
                return $bytes;
            }
            $w = imagesx($src);
            $h = imagesy($src);
            if ($w > self::MAX_WIDTH) {
                $nw = self::MAX_WIDTH;
                $nh = (int) round($h * $nw / $w);
                $dst = imagecreatetruecolor($nw, $nh);
                imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);
                $src = $dst;
            }
            ob_start();
            imagejpeg($src, null, 78);

            return ob_get_clean() ?: $bytes;
        } catch (\Throwable) {
            return $bytes;
        }
    }

    private function cachedCount(string $dir, string $key): int
    {
        return count(glob("{$dir}/{$key}-*.jpg") ?: []) + (int) is_file("{$dir}/{$key}.jpg");
    }

    /** A refused targeted fetch releases its page lock right away, so the
     *  next view retries the moment budget or disk frees — not in 10 min. */
    private function releaseTargetLock(): void
    {
        if ($this->option('listing')) {
            cache()->forget('gallery-fetch:'.$this->option('listing'));
        }
    }
}
