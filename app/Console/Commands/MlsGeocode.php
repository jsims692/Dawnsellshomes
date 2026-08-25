<?php

namespace App\Console\Commands;

use App\Models\Listing;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Fills lat/lng for listings — MRED leaves the RESO coordinate fields empty.
 * Uses the US Census batch geocoder: free, keyless, 10k addresses a request,
 * no caching restrictions. Runs after each sync; only touches rows that need it.
 */
class MlsGeocode extends Command
{
    protected $signature = 'mls:geocode {--batch=1000 : Addresses per Census request}';

    protected $description = 'Geocode listings without coordinates via the US Census batch geocoder';

    private const API = 'https://geocoding.geo.census.gov/geocoder/locations/addressbatch';

    public function handle(): int
    {
        $done = 0;
        $missed = 0;

        Listing::whereNull('lat')->whereNotNull('street_address')->where('street_address', '!=', '')
            ->chunkById((int) $this->option('batch'), function ($chunk) use (&$done, &$missed) {
                $csv = $chunk->map(fn ($l) => implode(',', [
                    $l->id,
                    '"'.str_replace('"', '', $l->street_address).'"',
                    '"'.str_replace('"', '', (string) $l->city).'"',
                    $l->state ?: 'IL',
                    $l->zip,
                ]))->implode("\n");

                try {
                    // Census batches routinely take minutes under load; a tight
                    // timeout aborts whole runs (Aug 2026 full-crawl backfill).
                    $resp = Http::timeout(300)
                        ->attach('addressFile', $csv, 'addresses.csv')
                        ->post(self::API, ['benchmark' => 'Public_AR_Current']);
                } catch (\Throwable $e) {
                    $this->error('Census geocoder unreachable: '.substr($e->getMessage(), 0, 120));

                    return false;
                }
                if (! $resp->successful()) {
                    $this->error('Census geocoder HTTP '.$resp->status());

                    return false;
                }

                foreach (str_getcsv_rows($resp->body()) as $row) {
                    // id, input, match, exact, matched addr, "lng,lat", tigerline, side
                    if (($row[2] ?? '') !== 'Match' || empty($row[5])) {
                        $missed++;

                        continue;
                    }
                    [$lng, $lat] = array_map(floatval(...), explode(',', $row[5]));
                    Listing::where('id', (int) $row[0])->update(['lat' => $lat, 'lng' => $lng]);
                    $done++;
                }
            });

        $this->info("Geocoded {$done} listings ({$missed} unmatched — withheld or nonstandard addresses).");

        return self::SUCCESS;
    }
}

if (! function_exists('str_getcsv_rows')) {
    /** @return iterable<array> */
    function str_getcsv_rows(string $body): iterable
    {
        foreach (explode("\n", trim($body)) as $line) {
            if ($line !== '') {
                yield str_getcsv($line);
            }
        }
    }
}
