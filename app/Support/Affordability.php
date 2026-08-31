<?php

namespace App\Support;

/**
 * Payment-first search math: what a listing actually costs per month, using
 * each home's REAL tax bill and HOA fee from the MLS (the thing portal
 * estimates get wrong in Cook/Lake County), plus disclosed assumptions for
 * rate, insurance, and PMI. Everything is expressible in SQL so "max
 * monthly payment" works as a true search filter, not a post-filter.
 */
class Affordability
{
    public const TERM_YEARS = 30;

    /** Annual insurance estimate as a share of price (disclosed). */
    public const INS_RATE = 0.0045;

    /** Annual PMI estimate as a share of the loan when under 20% down (disclosed). */
    public const PMI_RATE = 0.0055;

    /** Annual tax fallback as a share of price for the ~7% of listings without a tax bill. */
    public const TAX_FALLBACK = 0.021;

    /** The working rate: shopper override within sanity bounds, else the weekly-set config rate. */
    public static function rate($override = null): float
    {
        $o = is_numeric($override) ? (float) $override : null;

        return round($o !== null && $o >= 2 && $o <= 12 ? $o : (float) config('site.mortgage_rate', 6.1), 2);
    }

    /** Monthly principal & interest per dollar borrowed at $rate over 30 years. */
    public static function factor(float $rate): float
    {
        $r = $rate / 100 / 12;
        $n = self::TERM_YEARS * 12;

        return $r > 0 ? $r * (1 + $r) ** $n / (((1 + $r) ** $n) - 1) : 1 / $n;
    }

    /** SQL expression for a listing row's estimated total monthly payment. */
    public static function sqlMonthly(int $down, float $rate): string
    {
        $down = max(0, $down);
        $k = self::factor($rate);
        $ins = self::INS_RATE / 12;
        $pmi = self::PMI_RATE / 12;

        return "(GREATEST(list_price - {$down}, 0) * {$k}"
            .' + COALESCE(NULLIF(tax_annual, 0), list_price * '.self::TAX_FALLBACK.') / 12'
            .' + COALESCE(hoa_fee, 0)'
            ." + list_price * {$ins}"
            ." + CASE WHEN {$down} < list_price * 0.2 THEN GREATEST(list_price - {$down}, 0) * {$pmi} ELSE 0 END)";
    }
}
