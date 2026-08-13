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

        return $sitemap->toResponse(request());
    }
}
