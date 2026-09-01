<?php

namespace App\Support;

use App\Console\Commands\MlsMedia;
use App\Models\Listing;
use Symfony\Component\Process\PhpExecutableFinder;

/**
 * Spawns detached mls:media workers to pull a sold listing's photo gallery
 * on demand — from a sold detail page's first view, and pre-emptively for
 * the comps the home-value widget is about to show. The command itself
 * enforces the MLS budget and the disk floor; this class adds the per-
 * listing lock, the crawler gate, and a global worker cap.
 */
class GalleryWarmer
{
    private const MAX_WORKERS = 4;

    private const LOCK_SECONDS = 600;

    public static function isBot(?string $ua): bool
    {
        return (bool) preg_match(
            '/bot|crawl|spider|slurp|preview|facebookexternalhit|headless|python|curl|wget/i',
            (string) $ua);
    }

    /** Fewer photos on disk than the listing is known to have. */
    public static function incomplete(Listing $l): bool
    {
        $cached = count($l->photoUrls());
        $expected = (int) @file_get_contents(
            storage_path('app/public/listings/'.$l->listing_key.'.count'));

        return $cached <= 1
            || ($expected > 0 && $cached < min($expected, MlsMedia::PHOTOS_MAX));
    }

    private static function busy(): bool
    {
        // [m] so the pattern never matches the shell carrying it.
        exec('pgrep -fc -- "[m]ls:media --listing" 2>/dev/null', $out);

        return (int) ($out[0] ?? 0) >= self::MAX_WORKERS;
    }

    /**
     * Start (or join) a gallery fetch for one listing. Returns true when a
     * fetch is running or pending — i.e. the page should keep refreshing —
     * and false when the gallery is already complete.
     */
    public static function warm(Listing $l): bool
    {
        if (! self::incomplete($l)) {
            return false;
        }

        $lockKey = 'gallery-fetch:'.$l->listing_id;
        if (cache()->has($lockKey)) {
            return true; // a fetch is in flight
        }
        if (self::busy()) {
            return true; // workers saturated — a later view retries
        }
        if (! cache()->add($lockKey, 1, self::LOCK_SECONDS)) {
            return true;
        }

        // PHP_BINARY under php-fpm is the fpm daemon, not the CLI —
        // exec'ing it with 'artisan' just prints fpm usage text.
        $php = (new PhpExecutableFinder)->find(false) ?: PHP_BINARY;
        exec(sprintf('%s %s mls:media --listing=%s --all >> %s 2>&1 &',
            escapeshellarg($php),
            escapeshellarg(base_path('artisan')),
            escapeshellarg($l->listing_id),
            escapeshellarg(storage_path('logs/gallery-fetch.log'))));

        return true;
    }
}
