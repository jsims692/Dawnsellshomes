<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Incremental MLS GRID (RESO Web API) replication for MRED.
 *
 * Compliance notes (MLS GRID IDX Rules + Data License):
 *  - Runs well inside the 12-hour refresh requirement (scheduled hourly).
 *  - Incremental ModificationTimestamp polling per MLS GRID replication
 *    guidance — efficient, "well-formed queries" (License §VII(e)).
 *  - Coverage filter = the cities this site has pages for (objective,
 *    geographic criterion; the site displays the Rule 9 exclusion notice).
 *  - Honors deletes: MlsStatus changes / OriginatingSystemName removal make
 *    records non-displayable; we remove anything no longer in the feed slice.
 */
class MlsSync extends Command
{
    protected $signature = 'mls:sync
        {--full : Ignore the saved cursor and replicate from scratch}
        {--max=0 : Stop after roughly this many records (testing; skips the cursor save)}
        {--dry : Fetch and report without writing}';

    protected $description = 'Replicate MRED listings from the MLS GRID RESO API into the listings table';

    private const API = 'https://api.mlsgrid.com/v2/Property';

    /** MRD_TYP values seen but not kept, reported so no home class is missed silently. */
    private array $skippedTypes = [];

    /** Closed listings older than this many months are dropped (city sold stats). */
    private const CLOSED_MONTHS = 12;

    public function handle(): int
    {
        // A full page carries 1,000 expanded records; the default 128M limit
        // gets the process OOM-killed silently.
        ini_set('memory_limit', '1G');

        $token = config('services.mlsgrid.token');
        if (! $token) {
            $this->warn('MLSGRID_TOKEN not set — sync skipped (this is expected until MLS GRID approval).');

            return self::SUCCESS;
        }

        $cities = $this->coverageCities();
        $cursor = $this->option('full') ? null : $this->readCursor();

        // CloseDate is not a filterable field on the MLS GRID API; a listing
        // that closed inside the window was necessarily *modified* inside it
        // (the close is a modification), and upsert() enforces the real
        // CloseDate cutoff locally.
        $closedSince = now()->subMonths(self::CLOSED_MONTHS)->toIso8601ZuluString();
        $filter = "OriginatingSystemName eq 'mred' and ("
            ."StandardStatus eq Odata.Models.StandardStatus'Active'"
            ." or StandardStatus eq Odata.Models.StandardStatus'ActiveUnderContract'"
            ." or StandardStatus eq Odata.Models.StandardStatus'Pending'"
            ." or (StandardStatus eq Odata.Models.StandardStatus'Closed' and ModificationTimestamp gt {$closedSince})"
            .')';
        if ($cursor) {
            $filter = "OriginatingSystemName eq 'mred' and ModificationTimestamp gt {$cursor}";
        }

        $url = self::API.'?$filter='.rawurlencode($filter).'&$expand=Media&$top=1000';
        $seen = 0;
        $written = 0;
        $maxTs = $cursor;

        while ($url) {
            // MLS GRID rejects uncompressed requests ("COMPRESSION REQUIRED").
            $resp = Http::withToken($token)->acceptJson()
                ->withHeaders(['Accept-Encoding' => 'gzip'])
                ->timeout(60)->retry(3, 2000)->get($url);
            if (! $resp->successful()) {
                $this->error('MLS GRID API '.$resp->status().': '.substr($resp->body(), 0, 300));

                return self::FAILURE;
            }
            $json = $resp->json();
            foreach ($json['value'] ?? [] as $rec) {
                $seen++;
                $ts = $rec['ModificationTimestamp'] ?? null;
                if ($ts && (! $maxTs || $ts > $maxTs)) {
                    $maxTs = $ts;
                }
                if ($this->option('dry')) {
                    continue;
                }
                $written += $this->upsert($rec, $cities) ? 1 : 0;
            }
            $url = $json['@odata.nextLink'] ?? null;
            if (($max = (int) $this->option('max')) > 0 && $seen >= $max) {
                $this->warn("--max {$max} reached; stopping early (cursor not saved).");
                $url = null;
                $maxTs = null; // a partial crawl must not become the incremental baseline
            }
        }

        if (! $this->option('dry')) {
            // Sold stats window: drop closed listings that have aged out.
            Listing::where('status', 'Closed')
                ->where(fn ($w) => $w->whereNull('close_date')
                    ->orWhere('close_date', '<', now()->subMonths(self::CLOSED_MONTHS)))
                ->delete();

            if ($maxTs) {
                // A file, not the cache: cache:clear must never cost us the
                // cursor (that silently turns hourly increments into full pulls).
                file_put_contents(storage_path('app/mlsgrid-cursor'), $maxTs);
            }
        }

        $this->info(sprintf('Sync complete: %d records seen, %d written, cursor=%s, in DB: %d displayable.',
            $seen, $written, $maxTs ?? '(none)', Listing::displayable()->where('is_demo', false)->count()));
        if ($this->skippedTypes !== []) {
            arsort($this->skippedTypes);
            $this->line('Skipped property types: '.collect($this->skippedTypes)
                ->map(fn ($n, $t) => "{$t}={$n}")->implode(', '));
        }

        return self::SUCCESS;
    }

    private function upsert(array $r, array $cities): bool
    {
        $city = $r['City'] ?? null;
        $status = $r['StandardStatus'] ?? ($r['MlsStatus'] ?? '');
        $inCoverage = $city && in_array(mb_strtolower($city), $cities, true);
        $active = in_array($status, ['Active', 'Active Under Contract', 'Pending'], true);
        $closedRecent = $status === 'Closed'
            && ($r['CloseDate'] ?? null)
            && $r['CloseDate'] >= now()->subMonths(self::CLOSED_MONTHS)->toDateString();
        $key = $r['ListingKey'] ?? null;
        if (! $key) {
            return false;
        }

        // Homes + multi-unit investment property (objective property-type
        // criterion): leases, land and other commercial types are excluded.
        $typ = $r['MRD_TYP'] ?? '';
        $dwelling = match ($typ) {
            'Detached Single' => 'detached',
            'Attached Single' => 'attached',
            'Two to Four Units' => 'multi',
            default => (str_contains($typ, 'Five Plus') || str_contains($typ, 'Five or More') || str_contains($typ, '5+'))
                ? 'multi5' : null,
        };
        if (! $dwelling && $typ !== '') {
            $this->skippedTypes[$typ] = ($this->skippedTypes[$typ] ?? 0) + 1;
        }

        // Out of coverage, not a home, off-market (beyond the sold-stats
        // window), or opted out of display -> remove local copy.
        if (! $inCoverage || ! $dwelling || ! ($active || $closedRecent) || (($r['InternetEntireListingDisplayYN'] ?? true) === false)) {
            Listing::where('listing_key', $key)->delete();

            return false;
        }

        $media = collect($r['Media'] ?? [])
            ->sortBy('Order')
            ->map(fn ($m) => ['url' => $m['MediaURL'] ?? null, 'order' => $m['Order'] ?? 0])
            ->filter(fn ($m) => $m['url'])
            ->values();

        Listing::updateOrCreate(['listing_key' => $key], [
            'listing_id' => $r['ListingId'] ?? $key,
            'status' => $status,
            'list_price' => (int) ($r['ListPrice'] ?? 0),
            'close_price' => ($r['ClosePrice'] ?? 0) > 0 ? (int) $r['ClosePrice'] : null,
            'close_date' => $r['CloseDate'] ?? null,
            'original_list_price' => ($r['OriginalListPrice'] ?? 0) > 0 ? (int) $r['OriginalListPrice'] : null,
            // MRED uses -1 as a DOM sentinel; the column is unsigned.
            'days_on_market' => isset($r['DaysOnMarket']) && (int) $r['DaysOnMarket'] >= 0
                ? min((int) $r['DaysOnMarket'], 65535) : null,
            'street_address' => $r['UnparsedAddress'] ?? trim(($r['StreetNumber'] ?? '').' '.($r['StreetName'] ?? '')),
            'city' => $city,
            'state' => $r['StateOrProvince'] ?? 'IL',
            'zip' => $r['PostalCode'] ?? null,
            'address_public' => $r['InternetAddressDisplayYN'] ?? true,
            'display_public' => $r['InternetEntireListingDisplayYN'] ?? true,
            'avm_allowed' => $r['InternetAutomatedValuationDisplayYN'] ?? true,
            'comments_allowed' => $r['InternetConsumerCommentYN'] ?? true,
            'beds' => $r['BedroomsTotal'] ?? null,
            'baths_full' => $r['BathroomsFull'] ?? null,
            'baths_half' => $r['BathroomsHalf'] ?? null,
            'sqft' => $r['LivingArea'] ?? null,
            'property_type' => $r['PropertyType'] ?? null,
            'property_subtype' => $r['PropertySubType'] ?? null,
            'dwelling' => $dwelling,
            'year_built' => $r['YearBuilt'] ?? null,
            'remarks' => $r['PublicRemarks'] ?? null,
            'subdivision' => $r['SubdivisionName'] ?? null,
            'list_office_name' => $r['ListOfficeName'] ?? 'See listing brokerage',
            'list_office_phone' => $r['ListOfficePhone'] ?? null,
            'list_office_email' => $r['ListOfficeEmail'] ?? null,
            'lat' => $r['Latitude'] ?? null,
            'lng' => $r['Longitude'] ?? null,
            'media' => $media,
            'photo_count' => $media->count(),
            'mls_modified_at' => $r['ModificationTimestamp'] ?? null,
            'raw' => $r,
            'is_demo' => false,
        ]);

        return true;
    }

    private function readCursor(): ?string
    {
        $file = storage_path('app/mlsgrid-cursor');

        return is_file($file) ? (trim((string) file_get_contents($file)) ?: null) : cache()->get('mlsgrid-cursor');
    }

    /** The objective geographic coverage: every city this site has a page for. */
    private function coverageCities(): array
    {
        return cache()->remember('coverage-cities', now()->addDay(), function () {
            return Page::where('type', 'city')->pluck('slug')
                ->map(fn ($slug) => mb_strtolower(str_replace('-', ' ', $slug)))
                ->all();
        });
    }
}
