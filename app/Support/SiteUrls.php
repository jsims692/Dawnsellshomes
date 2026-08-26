<?php

namespace App\Support;

use App\Models\Listing;
use App\Models\Page;
use Illuminate\Support\Carbon;

/**
 * The one inventory of every indexable URL and when it last changed —
 * feeds both sitemap.xml and IndexNow submissions so they can never
 * disagree. lastmod accuracy is what earns fast recrawls from Google.
 */
class SiteUrls
{
    private const BASE = 'https://dawnsellshomes.com';

    /** [absolute url => lastmod ISO string|null], cached briefly (strings cache safely). */
    public static function all(): array
    {
        return cache()->remember('site-urls', 1800, function () {
            $iso = fn ($t) => $t ? Carbon::parse($t)->toIso8601String() : null;
            $out = [];
            foreach (Page::where('in_sitemap', true)->orderBy('id')->get(['path', 'updated_at']) as $p) {
                $out[self::BASE.'/'.$p->path] = $iso($p->updated_at);
            }
            if (config('site.listings_enabled')) {
                $asOf = $iso(Listing::max('mls_modified_at'));
                $out[self::BASE.'/listings'] = $asOf;
                $out[self::BASE.'/neighborhoods'] = $asOf;
                foreach (Subdivisions::dynamicOnly() as $e) {
                    $out[self::BASE.'/neighborhoods/'.$e['slug']] = null;
                }
                // For-sale detail pages: freshest content on the site. Sold
                // pages stay out (they churn; search reaches them via hubs).
                foreach (Listing::displayable()->forSale()->where('is_demo', false)
                    ->orderBy('id')->get(['listing_id', 'mls_modified_at']) as $l) {
                    $out[self::BASE.'/listings/'.$l->listing_id] = $iso($l->mls_modified_at);
                }
            }

            return $out;
        });
    }

    /** URLs whose content changed within the window — the IndexNow freshness ping. */
    public static function recent(int $hours = 2): array
    {
        $cutoff = now()->subHours($hours);

        return array_keys(array_filter(self::all(),
            fn ($m) => $m && Carbon::parse($m)->gte($cutoff)));
    }
}
