<?php

namespace App\Http\Controllers;

class SitemapController extends Controller
{
    public function __invoke()
    {
        // Served from the hourly-warmed cache — building fresh takes ~22s
        // across ~10k URLs, which reads as a timeout to crawlers.
        return response(\App\Support\SiteUrls::sitemapXml(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
