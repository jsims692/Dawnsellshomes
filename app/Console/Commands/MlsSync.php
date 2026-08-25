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
        {--dry : Fetch and report without writing}';

    protected $description = 'Replicate MRED listings from the MLS GRID RESO API into the listings table';

    private const API = 'https://api.mlsgrid.com/v2/Property';

    public function handle(): int
    {
        $token = config('services.mlsgrid.token');
        if (! $token) {
            $this->warn('MLSGRID_TOKEN not set — sync skipped (this is expected until MLS GRID approval).');

            return self::SUCCESS;
        }

        $cities = $this->coverageCities();
        $cursor = $this->option('full') ? null : cache()->get('mlsgrid-cursor');

        $filter = "OriginatingSystemName eq 'mred' and StandardStatus eq Odata.Models.StandardStatus'Active'";
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
        }

        if (! $this->option('dry') && $maxTs) {
            cache()->forever('mlsgrid-cursor', $maxTs);
        }

        $this->info(sprintf('Sync complete: %d records seen, %d written, cursor=%s, in DB: %d displayable.',
            $seen, $written, $maxTs ?? '(none)', Listing::displayable()->where('is_demo', false)->count()));

        return self::SUCCESS;
    }

    private function upsert(array $r, array $cities): bool
    {
        $city = $r['City'] ?? null;
        $status = $r['StandardStatus'] ?? ($r['MlsStatus'] ?? '');
        $inCoverage = $city && in_array(mb_strtolower($city), $cities, true);
        $active = in_array($status, ['Active', 'Active Under Contract'], true);
        $key = $r['ListingKey'] ?? null;
        if (! $key) {
            return false;
        }

        // Out of coverage, off-market, or opted out of display -> remove local copy.
        if (! $inCoverage || ! $active || (($r['InternetEntireListingDisplayYN'] ?? true) === false)) {
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
