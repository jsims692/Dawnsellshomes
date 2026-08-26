<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

/**
 * The single source of truth for "homes we've sold". The sales table holds
 * every mappable closing since 2007 and grows automatically as MLS syncs
 * record new team closings; the baseline covers early-career deals that
 * predate it. Every place the site brags about the number reads this, so it
 * is always current and always the same everywhere.
 */
class TeamStats
{
    public static function soldTotal(): int
    {
        return cache()->remember('team-sold-total', 600,
            fn () => DB::table('sales')->count() + (int) config('site.sales_baseline'));
    }

    /**
     * Swap stale sold-count generations baked into stored copy ("644 homes
     * sold", "550+ transactions", stat blocks, meta descriptions) for the
     * live number. Noun/markup lookaheads keep prices, ZIPs, coordinates,
     * and MLS numbers untouched.
     */
    public static function refreshCopy(string $html): string
    {
        return preg_replace([
            '/\b644\b(?=\s+(?:deals?|homes?|times|transactions?|families|sales|closings?)\b)/iu',
            '/\b644\b(?=<\/div>\s*<div class="stat-lbl">[^<]*\bSold\b)/i',
            '/\b550\+(?=\s+(?:deals?|homes?|times|transactions?|families|sales|closed|closings?|properties)\b)/iu',
            '/\b550\+(?=<\/div>\s*<div class="stat-lbl">[^<]*\bSold\b)/i',
        ], (string) self::soldTotal(), $html);
    }

    /** Mapped closings only (what the /sold map can actually pin). */
    public static function mappedSales(): int
    {
        return cache()->remember('team-mapped-sales', 600,
            fn () => (int) DB::table('sales')->count());
    }
}
