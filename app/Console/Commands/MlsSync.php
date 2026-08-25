<?php

namespace App\Console\Commands;

use App\Models\Listing;
use App\Models\Page;
use App\Support\MlsGridBudget;
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
        {--keys : Re-fetch only the listings already in the local table (chunked by key)}
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
        if ($this->option('keys')) {
            return $this->refreshLocalKeys($token, $cities);
        }
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

        $url = self::API.'?$filter='.rawurlencode($filter).'&$expand=Media,Rooms&$top=1000';
        $seen = 0;
        $written = 0;
        $maxTs = $cursor;

        while ($url) {
            if (! MlsGridBudget::allow()) {
                $this->warn('MLS GRID usage budget reached ('.MlsGridBudget::summary().') — stopping; rerun next hour.');
                break;
            }
            // MLS GRID rejects uncompressed requests ("COMPRESSION REQUIRED").
            // Retry only connection errors / 5xx — never hammer a rate limit.
            $resp = Http::withToken($token)->acceptJson()
                ->withHeaders(['Accept-Encoding' => 'gzip'])
                ->timeout(60)
                ->retry(3, 2000, fn ($e) => ! ($e instanceof \Illuminate\Http\Client\RequestException)
                    || $e->response->serverError(), throw: false)
                ->get($url);
            MlsGridBudget::record(strlen($resp->body()));
            usleep(300000); // ≤ ~2 pages/second regardless of response time
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

    /**
     * Re-fetch every listing already in the local table (fresh Media + Rooms +
     * details) without a feed crawl — chunked OR-filters over listing keys.
     * Patient with 429s: waits out the rate window instead of failing.
     */
    private function refreshLocalKeys(string $token, array $cities): int
    {
        $keys = Listing::where('is_demo', false)->pluck('listing_id');
        $written = 0;
        foreach ($keys->chunk(15) as $chunk) {
            $filter = "OriginatingSystemName eq 'mred' and ("
                .$chunk->map(fn ($k) => "ListingId eq '{$k}'")->implode(' or ').')';
            $url = self::API.'?$filter='.rawurlencode($filter).'&$expand=Media,Rooms&$top=100';

            for ($attempt = 1; ; $attempt++) {
                if (! MlsGridBudget::allow()) {
                    $this->warn('MLS GRID usage budget reached ('.MlsGridBudget::summary().') — stopping.');

                    return self::FAILURE;
                }
                $resp = Http::withToken($token)->acceptJson()
                    ->withHeaders(['Accept-Encoding' => 'gzip'])->timeout(60)->get($url);
                MlsGridBudget::record(strlen($resp->body()));
                if ($resp->status() === 429 && $attempt < 40) {
                    sleep(60); // rate window: wait it out

                    continue;
                }
                if (! $resp->successful()) {
                    $this->error('MLS GRID API '.$resp->status().' during key refresh; stopping.');

                    return self::FAILURE;
                }
                break;
            }

            foreach ($resp->json()['value'] ?? [] as $rec) {
                $written += $this->upsert($rec, $cities) ? 1 : 0;
            }
            usleep(600000);
        }
        $this->info("Key refresh complete: {$written} of {$keys->count()} listings re-written.");

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
        // Signed URLs die within the hour — keep only the primary (the photo
        // cache refreshes per listing on demand; galleries will do the same).
        $media = $media->take(1);

        $listing = Listing::updateOrCreate(['listing_key' => $key], [
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
            // MRED marks auctions via its own MlsStatus (StandardStatus stays Active)
            'is_auction' => ($r['MlsStatus'] ?? null) === 'Auction'
                || in_array('Auction', (array) ($r['SpecialListingConditions'] ?? []), true),
            'year_built' => $r['YearBuilt'] ?? null,
            'remarks' => $r['PublicRemarks'] ?? null,
            'subdivision' => $r['SubdivisionName'] ?? null,
            'list_office_name' => $r['ListOfficeName'] ?? 'See listing brokerage',
            'list_office_phone' => $r['ListOfficePhone'] ?? null,
            'list_office_email' => $r['ListOfficeEmail'] ?? null,
            'lat' => $r['Latitude'] ?? null,
            'lng' => $r['Longitude'] ?? null,
            'media' => $media,

            'mls_modified_at' => $r['ModificationTimestamp'] ?? null,
            'is_demo' => false,
        ] + self::extractDetails($r));

        $this->syncChildren($listing, $r);

        return true;
    }

    /** RESO multi-value fields -> listing_features categories (whitelist = compliance filter). */
    private const FEATURE_MAP = [
        'Appliances' => 'appliances',
        'InteriorFeatures' => 'interior',
        'ExteriorFeatures' => 'exterior',
        'ConstructionMaterials' => 'construction',
        'ArchitecturalStyle' => 'style',
        'Roof' => 'roof',
        'FoundationDetails' => 'foundation',
        'Basement' => 'basement',
        'WindowFeatures' => 'windows',
        'DoorFeatures' => 'doors',
        'LaundryFeatures' => 'laundry',
        'FireplaceFeatures' => 'fireplace',
        'FireplaceLocation' => 'fireplace_location',
        'Flooring' => 'flooring',
        'Heating' => 'heating',
        'Cooling' => 'cooling',
        'Electric' => 'electric',
        'WaterSource' => 'water',
        'Sewer' => 'sewer',
        'OtherEquipment' => 'equipment',
        'ParkingFeatures' => 'parking',
        'PatioAndPorchFeatures' => 'patio',
        'OtherStructures' => 'structures',
        'LotFeatures' => 'lot',
        'AssociationAmenities' => 'amenities',
        'AssociationFeeIncludes' => 'hoa_includes',
        'CommunityFeatures' => 'community',
        'RoomType' => 'additional_rooms',
        'Possession' => 'possession',
        'SpecialListingConditions' => 'conditions',
        'PetsAllowed' => 'pets',
    ];

    /** Rewrite the rooms + features child rows from the fresh record. */
    private function syncChildren(Listing $listing, array $r): void
    {
        $listing->features()->delete();
        $features = [];
        foreach (self::FEATURE_MAP as $field => $category) {
            foreach ((array) ($r[$field] ?? []) as $value) {
                if (is_scalar($value) && trim((string) $value) !== '') {
                    $features[] = ['category' => $category, 'value' => mb_substr(trim((string) $value), 0, 150)];
                }
            }
        }
        if ($features !== []) {
            $listing->features()->createMany($features);
        }

        $listing->rooms()->delete();
        $rooms = [];
        foreach ((array) ($r['Rooms'] ?? []) as $i => $room) {
            $name = $room['RoomType'] ?? ($room['MRD_Type'] ?? null);
            $name = is_array($name) ? implode(', ', $name) : $name;
            if (! $name) {
                continue;
            }
            $rooms[] = [
                'name' => mb_substr((string) $name, 0, 60),
                'dimensions' => mb_substr((string) ($room['RoomDimensions'] ?? ''), 0, 20) ?: null,
                'level' => mb_substr((string) ($room['RoomLevel'] ?? ''), 0, 30) ?: null,
                'flooring' => mb_substr(implode(', ', (array) ($room['MRD_Flooring'] ?? ($room['RoomFlooring'] ?? []))), 0, 40) ?: null,
                'sort' => min($i, 255),
            ];
        }
        if ($rooms !== []) {
            $listing->rooms()->createMany($rooms);
        }
    }

    /** Typed columns for every raw field the site can use (no raw blob kept). */
    private static function extractDetails(array $r): array
    {
        $join = fn ($v, int $max) => $v === null ? null
            : mb_substr(is_array($v) ? implode(', ', $v) : (string) $v, 0, $max);
        $int = fn ($v) => is_numeric($v) && $v >= 0 ? (int) $v : null;

        return [
            'tax_annual' => $int($r['TaxAnnualAmount'] ?? null),
            'tax_year' => $int($r['TaxYear'] ?? null),
            'hoa_fee' => $int($r['AssociationFee'] ?? null),
            'hoa_fee_freq' => $join($r['AssociationFeeFrequency'] ?? null, 20),
            'parking_total' => $int($r['ParkingTotal'] ?? null),
            'garage_spaces' => $int($r['GarageSpaces'] ?? null),
            'lot_dimensions' => $join($r['LotSizeDimensions'] ?? null, 60),
            'elementary_district' => $join($r['ElementarySchoolDistrict'] ?? null, 10),
            'middle_district' => $join($r['MiddleOrJuniorSchoolDistrict'] ?? null, 10),
            'high_district' => $join($r['HighSchoolDistrict'] ?? null, 10),
            'rooms_total' => $int($r['RoomsTotal'] ?? null),
            'stories' => $int($r['StoriesTotal'] ?? null),
            'new_construction' => (bool) ($r['NewConstructionYN'] ?? false),
            'listing_contract_date' => $r['ListingContractDate'] ?? null,
            'waterfront' => (bool) ($r['WaterfrontYN'] ?? false),
            'ownership' => $join($r['Ownership'] ?? null, 30),
            'exposure' => $join($r['MRD_EXP'] ?? null, 40),
            'age_range' => $join($r['MRD_AGE'] ?? null, 20),
            'parcel_number' => $join($r['ParcelNumber'] ?? null, 20),
            'township' => $join($r['Township'] ?? null, 40),
            'county' => $join($r['CountyOrParish'] ?? null, 40),
            'elementary_school' => $join($r['ElementarySchool'] ?? null, 80),
            'middle_school' => $join($r['MiddleOrJuniorSchool'] ?? null, 80),
            'high_school' => $join($r['HighSchool'] ?? null, 80),
            'water_body' => $join($r['WaterBodyName'] ?? null, 60),
            'virtual_tour_url' => $join($r['VirtualTourURLUnbranded'] ?? null, 255),
            'fireplaces' => $int($r['FireplacesTotal'] ?? null),
        ];
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
