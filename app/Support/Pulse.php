<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * First-party usage telemetry: when people are on the site and which
 * features they touch. Anonymous by construction — the visitor key is
 * sha256(ip + app key + date), so it can't be reversed and resets daily;
 * no cookies, no third party. Events come from server hooks (search,
 * value lookups) and the /t beacon (hearts, carousels, satellite flips).
 * Never throws: telemetry must not be able to break a page.
 */
class Pulse
{
    public const EVENTS = ['page', 'search', 'value', 'heart', 'carousel', 'gallery',
        'map', 'satellite', 'streetview', 'share', 'saved'];

    public static function track(string $event, array $meta = [], ?string $path = null): void
    {
        try {
            if (! in_array($event, self::EVENTS, true)) {
                return;
            }
            $ua = (string) request()->userAgent();
            if (GalleryWarmer::isBot($ua)) {
                return;
            }
            DB::table('site_events')->insert([
                'event' => $event,
                'path' => mb_substr($path ?? request()->path(), 0, 191),
                'meta' => $meta === [] ? null : json_encode($meta),
                'vhash' => hash('sha256', request()->ip().config('app.key').date('Y-m-d')),
                'city' => GeoIp::cityGuess()['city'] ?? null,
                'mobile' => (bool) preg_match('/Mobile|Android|iPhone/i', $ua),
                'created_at' => now(),
            ]);
        } catch (\Throwable) {
            // never let telemetry break the page
        }
    }
}
