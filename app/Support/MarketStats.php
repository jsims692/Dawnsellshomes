<?php

namespace App\Support;

use App\Models\Listing;
use Illuminate\Support\Str;

/**
 * Per-city market snapshot used by /market/{city} and /compare/{a}-vs-{b}:
 * one computation, one cache, so the two surfaces can never disagree.
 */
class MarketStats
{
    /** Resolve a city slug to its proper MLS city name (null if unknown). */
    public static function resolveCity(string $citySlug): ?string
    {
        return cache()->remember('city-for-slug:'.$citySlug, 3600,
            fn () => Listing::displayable()->where('is_demo', false)->distinct()->pluck('city')
                ->first(fn ($c) => Str::slug($c) === $citySlug) ?? '');
        // '' caches "unknown" cheaply; callers treat falsy as not found
    }

    public static function report(string $city): array
    {
        return cache()->remember('market-report:'.Str::slug($city), 1800, function () use ($city) {
            $base = fn () => Listing::displayable()->where('is_demo', false)->where('is_auction', false)
                ->whereRaw('LOWER(city) = ?', [mb_strtolower($city)]);

            $median = function ($q, string $col) {
                $vals = $q->whereNotNull($col)->orderBy($col)->pluck($col);

                return $vals->isEmpty() ? null : $vals[(int) floor(($vals->count() - 1) / 2)];
            };

            $sold30 = $base()->where('status', 'Closed')->where('close_date', '>=', now()->subDays(30));
            $sold30Count = (clone $sold30)->count();
            $dom30 = (clone $sold30)->whereNotNull('days_on_market')->avg('days_on_market');
            $ratio30 = (clone $sold30)->where('original_list_price', '>', 0)->whereNotNull('close_price')
                ->selectRaw('AVG(close_price / original_list_price * 100) r')->value('r');
            $tax = $base()->where('tax_annual', '>', 100)->avg('tax_annual');

            return [
                'active' => $base()->where('status', 'Active')->count(),
                'newWeek' => $base()->where('status', 'Active')->where('days_on_market', '<=', 7)->count(),
                'underContract' => $base()->whereIn('status', ['Active Under Contract', 'Pending'])->count(),
                'medianList' => $median($base()->where('status', 'Active'), 'list_price'),
                'sold30' => $sold30Count,
                'medianClose30' => $median(clone $sold30, 'close_price'),
                'dom30' => $dom30 ? (int) round($dom30) : null,
                'ratio30' => $ratio30 ? round($ratio30, 1) : null,
                'medianClosePrior' => $median($base()->where('status', 'Closed')
                    ->whereBetween('close_date', [now()->subDays(60), now()->subDays(30)]), 'close_price'),
                'avgTax' => $tax ? (int) round($tax) : null,
            ];
        });
    }
}
