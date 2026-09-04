<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Monthly refresh of the self-hosted DB-IP City Lite database (free,
 * CC BY 4.0). Downloads the current month's build (falling back to last
 * month's early in a month), gunzips, and swaps it in atomically.
 */
class GeoIpRefresh extends Command
{
    protected $signature = 'geoip:refresh';

    protected $description = 'Download the latest DB-IP City Lite database';

    public function handle(): int
    {
        $dir = storage_path('app/geoip');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        foreach ([date('Y-m'), date('Y-m', strtotime('first day of last month'))] as $month) {
            $url = "https://download.db-ip.com/free/dbip-city-lite-{$month}.mmdb.gz";
            $tmp = "{$dir}/dbip.download.gz";
            try {
                $resp = Http::timeout(300)->sink($tmp)->get($url);
            } catch (\Throwable) {
                continue;
            }
            if (! $resp->successful() || filesize($tmp) < 10_000_000) {
                @unlink($tmp);

                continue;
            }

            $gz = gzopen($tmp, 'rb');
            $out = fopen("{$dir}/dbip.new.mmdb", 'wb');
            while (! gzeof($gz)) {
                fwrite($out, gzread($gz, 1 << 20));
            }
            gzclose($gz);
            fclose($out);
            @unlink($tmp);
            rename("{$dir}/dbip.new.mmdb", "{$dir}/dbip-city-lite.mmdb");
            $this->info("GeoIP database updated ({$month}).");

            return self::SUCCESS;
        }

        $this->warn('GeoIP download failed — keeping the existing database.');

        return self::SUCCESS; // stale is fine; never fail the scheduler
    }
}
