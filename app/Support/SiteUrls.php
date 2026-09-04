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
                foreach (array_keys(\App\Http\Controllers\CollectionController::COLLECTIONS) as $slug) {
                    $out[self::BASE.'/homes/'.$slug] = $asOf;
                }
                $out[self::BASE.'/compare'] = $asOf;
                foreach (\App\Http\Controllers\CompareController::FEATURED as [$a, $b]) {
                    $out[self::BASE.'/compare/'.$a.'-vs-'.$b] = $asOf;
                }
                $out[self::BASE.'/market'] = $asOf;
                foreach (Listing::displayable()->where('is_demo', false)
                    ->selectRaw('city, COUNT(*) c')->groupBy('city')->having('c', '>=', 20)
                    ->pluck('city') as $mc) {
                    $out[self::BASE.'/market/'.\Illuminate\Support\Str::slug($mc)] = $asOf;
                }
                foreach (Subdivisions::dynamicOnly() as $e) {
                    $out[self::BASE.'/neighborhoods/'.$e['slug']] = null;
                }
                // For-sale detail pages: freshest content on the site. Sold
                // pages stay out (they churn; search reaches them via hubs).
                foreach (Listing::displayable()->forSale()->where('is_demo', false)
                    ->orderBy('id')->get(['listing_id', 'street_address', 'address_public', 'city', 'mls_modified_at']) as $l) {
                    $out[self::BASE.$l->url()] = $iso($l->mls_modified_at);
                }
            }

            return $out;
        });
    }

    /**
     * The fully rendered sitemap XML. Building it walks ~10k URLs (22s on
     * prod) — far too slow per-request, so the scheduler warms this cache
     * hourly and the controller serves the cached document.
     */
    public static function sitemapXml(bool $fresh = false): string
    {
        if ($fresh) {
            cache()->forget('site-urls');
            cache()->forget('sitemap-xml');
        }

        return cache()->remember('sitemap-xml', 7200, function () {
            $sitemap = \Spatie\Sitemap\Sitemap::create();
            foreach (self::all() as $url => $lastmod) {
                $u = \Spatie\Sitemap\Tags\Url::create($url);
                if ($lastmod) {
                    $u->setLastModificationDate(Carbon::parse($lastmod));
                }
                $sitemap->add($u);
            }

            return $sitemap->render();
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
