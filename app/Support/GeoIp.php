<?php

namespace App\Support;

use MaxMind\Db\Reader;

/**
 * Visitor-city guess from a self-hosted DB-IP City Lite database
 * (CC BY 4.0 — attributed in the site footer; refreshed monthly by
 * geoip:refresh). Lookup only — no external API call, nothing logged,
 * nothing stored beyond a day's cache. Used to open the sold map on the
 * visitor's own turf. Returns null off-network, for private/unknown IPs,
 * or when the database file is absent — callers fall back gracefully.
 */
class GeoIp
{
    public static function cityGuess(): ?array
    {
        $ip = request()->ip();
        if (! $ip || ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return null;
        }

        try {
            // Wrapped so a cached "no result" is distinguishable from a miss.
            $hit = cache()->remember('geoip:'.$ip, 86400, function () use ($ip) {
                $file = storage_path('app/geoip/dbip-city-lite.mmdb');
                if (! is_file($file)) {
                    return ['v' => null];
                }
                $rec = (new Reader($file))->get($ip);
                $city = $rec['city']['names']['en'] ?? null;
                $lat = $rec['location']['latitude'] ?? null;
                $lng = $rec['location']['longitude'] ?? null;

                return ['v' => ($city && $lat) ? ['city' => $city, 'lat' => (float) $lat, 'lng' => (float) $lng] : null];
            });

            return $hit['v'] ?? null;
        } catch (\Throwable) {
            return null;
        }
    }
}
