<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke()
    {
        // Pages, subdivision pages, and for-sale listings — one shared
        // inventory (SiteUrls) with lastmod so recrawls chase real changes.
        $sitemap = Sitemap::create();
        foreach (\App\Support\SiteUrls::all() as $url => $lastmod) {
            $u = Url::create($url);
            if ($lastmod) {
                $u->setLastModificationDate(\Illuminate\Support\Carbon::parse($lastmod));
            }
            $sitemap->add($u);
        }

        return $sitemap->toResponse(request());
    }
}
