<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $base = 'https://dawnsellshomes.com';

        $sitemap = Sitemap::create();
        foreach (Page::where('in_sitemap', true)->orderBy('id')->pluck('path') as $path) {
            $sitemap->add(Url::create($base.'/'.$path));
        }

        // Subdivision directory + auto-generated subdivision pages (hand-built
        // neighborhood pages are already present via the pages table above).
        if (config('site.listings_enabled')) {
            $sitemap->add(Url::create($base.'/neighborhoods'));
            foreach (\App\Support\Subdivisions::dynamicOnly() as $entry) {
                $sitemap->add(Url::create($base.'/neighborhoods/'.$entry['slug']));
            }
        }

        return $sitemap->toResponse(request());
    }
}
