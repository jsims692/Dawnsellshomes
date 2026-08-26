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
        // Legal plat-of-survey names agents sometimes enter instead of the
        // consumer neighborhood name (Josh, Aug 2026) — real on a county plat,
        // meaningless to a home shopper.
        'herzog kuntze', 'arling grove',
    ];

    /**
     * Same class, caught by shape: surveyor-initial plats ("J L Shaws",
     * "B F Nabers & Helen F Osmonds") and bare addresses ("4929 Forest").
     */
    private static function looksLikePlatOrAddress(string $name): bool
    {
        return preg_match('/^(?:[a-z]\.? ){2}/i', $name)
            || preg_match('/^\d{3,5} [a-z]+$/i', $name);
    }

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
                    || in_array(mb_strtolower($r->name), self::JUNK, true)
                    || self::looksLikePlatOrAddress($r->name)) {
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

    /** Community-page URL for a listing's subdivision tag, or null if none exists. */
    public static function urlFor(?string $name, ?string $city): ?string
    {
        $nameSlug = Str::slug((string) $name);
        $citySlug = Str::slug((string) $city);
        if ($nameSlug === '' || $citySlug === '') {
            return null;
        }
        $slug = str_ends_with($nameSlug, '-'.$citySlug) ? $nameSlug : $nameSlug.'-'.$citySlug;
        if ($hand = Page::whereIn('type', ['neighborhood', 'condo'])->where('slug', $slug)->value('path')) {
            return '/'.$hand;
        }
        $e = self::find($slug);

        return $e && $e['total'] >= self::MIN_LISTINGS ? '/neighborhoods/'.$slug : null;
    }

    /**
     * The facts a subdivision page states about itself — build era, housing
     * mix, sizes, taxes, assigned schools, recent sales — all derived from
     * the listings we replicate (actives + 12 months of solds). This is the
     * data-backed version of the intro formula that wins subdivision SERPs.
     */
    public static function profile(array $entry): array
    {
        return cache()->remember('subdivision-profile:'.$entry['slug'], 3600, function () use ($entry) {
            $base = fn () => Listing::displayable()->where('is_demo', false)
                ->where('city', $entry['city'])
                ->where('subdivision', $entry['name']);

            $years = $base()->where('year_built', '>', 1800)
                ->selectRaw('MIN(year_built) lo, MAX(year_built) hi')->first();
            $sqft = $base()->where('sqft', '>', 200)
                ->selectRaw('MIN(sqft) lo, MAX(sqft) hi')->first();
            $tax = $base()->where('tax_annual', '>', 100)->avg('tax_annual');

            $mix = $base()->whereNotNull('dwelling')->selectRaw('dwelling, COUNT(*) c')
                ->groupBy('dwelling')->orderByDesc('c')->pluck('c', 'dwelling')->all();
            $phrase = match (true) {
                $mix === [] => 'community',
                count($mix) === 1 && isset($mix['detached']) => 'single-family home community',
                count($mix) === 1 && isset($mix['attached']) => 'townhome and condo community',
                isset($mix['detached']) && array_key_first($mix) === 'detached' => 'community of mostly single-family homes',
                default => 'community of townhomes, condos and single-family homes',
            };

            // Assigned schools: the most common value wins (edge parcels can
            // feed a different school; we state the norm, not a guarantee).
            $school = fn (string $col) => $base()->whereNotNull($col)->where($col, '!=', '')
                ->selectRaw("$col v, COUNT(*) c")->groupBy('v')->orderByDesc('c')->value('v');

            // Plain arrays only — this cache store corrupts serialized objects
            // (Carbon and Eloquent both bit us; see SiteUrls).
            $solds = $base()->where('status', 'Closed')->whereNotNull('close_price')
                ->orderByDesc('close_date')->limit(8)
                ->get(['listing_id', 'street_address', 'address_public', 'close_price', 'close_date', 'beds', 'baths_full', 'baths_half'])
                ->map(fn ($s) => [
                    'id' => $s->listing_id,
                    'address' => $s->address_public && $s->street_address ? $s->street_address : null,
                    'beds' => $s->beds, 'baths' => $s->baths(),
                    'when' => $s->close_date?->format('M Y'),
                    'price' => $s->close_price,
                ])->all();

            return [
                'phrase' => $phrase,
                'yearLo' => $years?->lo, 'yearHi' => $years?->hi,
                'sqftLo' => $sqft?->lo, 'sqftHi' => $sqft?->hi,
                'avgTax' => $tax ? (int) round($tax) : null,
                'elementary' => $school('elementary_school'),
                'middle' => $school('middle_school'),
                'high' => $school('high_school'),
                'solds' => $solds,
            ];
        });
    }

    /** Entries with no hand-built neighborhood/condo page (for sitemap + fallback routing). */
    public static function dynamicOnly(): array
    {
        $handSlugs = Page::whereIn('type', ['neighborhood', 'condo'])->pluck('slug')->flip();

        return array_filter(self::map(), fn ($e) => ! isset($handSlugs[$e['slug']])
            && $e['total'] >= self::MIN_LISTINGS);
    }
}
