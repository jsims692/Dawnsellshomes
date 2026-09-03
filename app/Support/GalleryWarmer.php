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
    /** Concurrency, not rate: every MLS request is globally paced by
     *  MlsGridBudget::pace(), so more workers just share the same ~1.8
     *  req/s lane. The cap only bounds process count on the box. */
    private const MAX_WORKERS = 8;

    private const LOCK_SECONDS = 600;

    public static function isBot(?string $ua): bool
    {
        return (bool) preg_match(
            '/bot|crawl|spider|slurp|preview|facebookexternalhit|headless|python|curl|wget/i',
            (string) $ua);
    }

    /** Fewer photos on disk than the listing is known to have. The .count
     *  sidecar is authoritative once a targeted fetch has written it — some
     *  closed listings' media is stripped from the feed, and their recorded
     *  count (however small) means "this is all that exists upstream". */
    public static function incomplete(Listing $l): bool
    {
        $cached = count($l->photoUrls());
        $countFile = storage_path('app/public/listings/'.$l->listing_key.'.count');
        if (is_file($countFile)) {
            return $cached < min((int) file_get_contents($countFile), MlsMedia::PHOTOS_MAX);
        }

        return $cached <= 1;
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
        $trace = fn (string $why) => @file_put_contents(
            storage_path('logs/gallery-warm.log'),
            date('c')." {$l->listing_id} {$why} (".PHP_SAPI.")\n", FILE_APPEND);

        if (! self::incomplete($l)) {
            $trace('complete');

            return false;
        }

        $lockKey = 'gallery-fetch:'.$l->listing_id;
        if (cache()->has($lockKey)) {
            $trace('locked');

            return true; // a fetch is in flight
        }
        if (self::busy()) {
            $trace('busy');

            return true; // workers saturated — a later view retries
        }
        if (! cache()->add($lockKey, 1, self::LOCK_SECONDS)) {
            $trace('add-failed');

            return true;
        }
        $trace('spawn');

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
