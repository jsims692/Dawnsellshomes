<?php

namespace App\Http\Controllers;

use App\Models\Page;

class SitemapController extends Controller
{
    public function __invoke()
    {
        $base = 'https://dawnsellshomes.com';
        $urls = Page::where('in_sitemap', true)->orderBy('id')->pluck('path')
            ->map(fn ($path) => '  <url><loc>'.$base.'/'.$path.'</loc></url>')
            ->implode("\n");

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n"
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n"
            .$urls."\n</urlset>\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }
}
