<?php

namespace App\Console\Commands;

use App\Models\Listing;
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
        {--refresh : Re-download photos that are already cached}';

    protected $description = 'Download and cache listing photo galleries from MLS GRID';

    private const API = 'https://api.mlsgrid.com/v2/Property';
    private const MAX_WIDTH = 800;
    private const PACE_MICROSECONDS = 450000; // ~2 requests/second

    /** Gallery cap per listing (photo 0 = {key}.jpg, then {key}-1.jpg …). */
    private const PHOTOS_MAX = 24;

    public function handle(): int
    {
        ini_set('memory_limit', '512M');

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
            ->orderByRaw("FIELD(status, 'Active', 'Active Under Contract', 'Pending', 'Closed')")
            ->orderByDesc('mls_modified_at');

        foreach ($q->cursor() as $l) {
            // For-sale listings get the full gallery (capped); --all rows
            // (closed, for future sold strips) just the primary photo.
            $cap = $l->isForSale() ? self::PHOTOS_MAX : 1;
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

            // One API call per listing needing photos: fresh signed URLs for
            // the whole gallery (stored URLs die within the hour).
            $urls = $this->refreshMedia($l, $token);
            usleep(self::PACE_MICROSECONDS);
            if ($urls === []) {
                $skipped++;

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
                usleep(self::PACE_MICROSECONDS);
            }

            if ($limit > 0 && $done >= $limit) {
                break;
            }
        }

        $this->info("Media cache: {$done} downloaded, {$skipped} without media, {$failed} failed.");

        return self::SUCCESS;
    }

    /** Re-fetch this listing's Media (fresh signed URLs), ordered. */
    private function refreshMedia(Listing $l, string $token): array
    {
        $url = self::API.'?$filter='.rawurlencode("ListingId eq '{$l->listing_id}'").'&$expand=Media&$top=1';
        try {
            $resp = Http::withToken($token)->acceptJson()
                ->withHeaders(['Accept-Encoding' => 'gzip'])
                ->timeout(30)->retry(3, 5000)->get($url);
        } catch (\Throwable) {
            return [];
        }
        if (! $resp->successful()) {
            return [];
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
            try {
                $resp = Http::timeout(30)->get($url);
            } catch (\Throwable) {
                return false;
            }
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
}
