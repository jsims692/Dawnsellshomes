<?php

namespace App\Support;

use App\Models\Listing;
use App\Models\Page;
use Illuminate\Support\Str;

/**
 * MLS-derived subdivision index: every subdivision name agents have entered
 * on listings we hold, normalized and scoped to cities with a city page (the
 * service area). Listing agents don't always fill the field in, so this is
 * "what the MLS is tagged with", never a complete roster — pages built from
 * it say so and link to the full city search.
 */
class Subdivisions
{
    /** Free-text placeholders that aren't subdivision names. */
    private const JUNK = [
        'not applicable', 'not application', 'n/a', 'na', 'none', 'no',
        'other', 'unknown', 'tbd', 'see remarks', 'subdivision', '0', 'manor',
    ];

    /**
     * Auto-generated pages require this many tagged listings. One-offs are
     * where typos live, and a page with a single listing ever is too thin to
     * index; a real small subdivision qualifies as soon as a second listing
     * mentions it. Hand-built pages are exempt — they're curated.
     */
    public const MIN_LISTINGS = 2;

    /** slug => [slug, name, city, citySlug, active, total]. Cached an hour. */
    public static function map(): array
    {
        return cache()->remember('subdivision-map', 3600, function () {
            $citySlugs = Page::where('type', 'city')->pluck('slug')->flip();

            $rows = Listing::displayable()->where('is_demo', false)
                ->whereRaw("TRIM(COALESCE(subdivision, '')) != ''")
                ->selectRaw("city, TRIM(subdivision) AS name, COUNT(*) AS total,
                    SUM(status IN ('Active', 'Active Under Contract')) AS active")
                ->groupBy('city', 'name')->get();

            $out = [];
            foreach ($rows as $r) {
                $citySlug = Str::slug($r->city);
                $nameSlug = Str::slug($r->name);
                if ($nameSlug === '' || ! isset($citySlugs[$citySlug])
                    || in_array(mb_strtolower($r->name), self::JUNK, true)) {
                    continue;
                }
                // ALL-CAPS entries read as shouting; title-case them for display.
                $name = $r->name === mb_strtoupper($r->name)
                    ? self::titleize(mb_strtolower($r->name)) : $r->name;

                // Casing variants of one name share a slug: merge counts, keep
                // the most common variant as the display name. Names that
                // already end with the city ("Cambridge of Buffalo Grove")
                // don't get it appended twice — keeps their slug identical to
                // the hand-built page's so the two merge instead of duplicating.
                if ($nameSlug === $citySlug) {
                    continue;
                }
                $slug = str_ends_with($nameSlug, '-'.$citySlug) ? $nameSlug : $nameSlug.'-'.$citySlug;
                if (isset($out[$slug])) {
                    if ($r->total > $out[$slug]['votes']) {
                        $out[$slug]['name'] = $name;
                        $out[$slug]['votes'] = (int) $r->total;
                    }
                    $out[$slug]['total'] += (int) $r->total;
                    $out[$slug]['active'] += (int) $r->active;
                    continue;
                }
                $out[$slug] = [
                    'slug' => $slug, 'name' => $name, 'city' => $r->city,
                    'citySlug' => $citySlug, 'total' => (int) $r->total,
                    'active' => (int) $r->active, 'votes' => (int) $r->total,
                ];
            }
            ksort($out);

            return $out;
        });
    }

    /** "cambridge-of-buffalo-grove" → "Cambridge of Buffalo Grove". */
    public static function titleize(string $text): string
    {
        $words = explode(' ', Str::title(str_replace('-', ' ', $text)));
        foreach ($words as $i => $w) {
            if ($i > 0 && in_array(strtolower($w), ['of', 'in', 'the', 'on', 'at', 'by', 'a'], true)) {
                $words[$i] = strtolower($w);
            }
        }

        return implode(' ', $words);
    }

    public static function find(string $slug): ?array
    {
        return self::map()[$slug] ?? null;
    }

    /** Entries with no hand-built neighborhood/condo page (for sitemap + fallback routing). */
    public static function dynamicOnly(): array
    {
        $handSlugs = Page::whereIn('type', ['neighborhood', 'condo'])->pluck('slug')->flip();

        return array_filter(self::map(), fn ($e) => ! isset($handSlugs[$e['slug']])
            && $e['total'] >= self::MIN_LISTINGS);
    }
}
