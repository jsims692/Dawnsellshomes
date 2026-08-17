<?php

namespace App\Console\Commands;

use App\Models\Page;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Notifies search engines that the site's URLs have changed.
 *
 * Runs on every deployment. Note that Google retired its sitemap "ping"
 * endpoint in 2023 and Bing retired theirs in favour of IndexNow, so this
 * submits via IndexNow (Bing, Yandex, Seznam, Naver). Google discovers the
 * sitemap through the Sitemap: directive in robots.txt and recrawls on its
 * own schedule; use Search Console for a manual nudge.
 */
class SubmitSitemap extends Command
{
    protected $signature = 'sitemap:submit
        {--host=dawnsellshomes.com : The host whose URLs are being submitted}
        {--limit=10000 : Max URLs per IndexNow request}';

    protected $description = 'Submit the site URLs to IndexNow (Bing/Yandex/etc.) after a deployment';

    public function handle(): int
    {
        $key = config('services.indexnow.key');
        $host = $this->option('host');

        if (! $key) {
            $this->warn('INDEXNOW_KEY not set — skipping submission.');

            return self::SUCCESS;
        }

        $urls = Page::where('in_sitemap', true)
            ->orderBy('id')
            ->pluck('path')
            ->map(fn ($path) => 'https://'.$host.'/'.$path)
            ->take((int) $this->option('limit'))
            ->values()
            ->all();

        if (empty($urls)) {
            $this->warn('No sitemap URLs found — skipping submission.');

            return self::SUCCESS;
        }

        try {
            $response = Http::timeout(20)->post('https://api.indexnow.org/indexnow', [
                'host' => $host,
                'key' => $key,
                'keyLocation' => 'https://'.$host.'/'.$key.'.txt',
                'urlList' => $urls,
            ]);

            $response->successful()
                ? $this->info(sprintf('IndexNow accepted %d URLs (HTTP %d).', count($urls), $response->status()))
                : $this->warn(sprintf('IndexNow returned HTTP %d: %s', $response->status(), substr($response->body(), 0, 200)));
        } catch (\Throwable $e) {
            // Never fail a deployment because a search engine was unreachable.
            $this->warn('IndexNow submission failed: '.$e->getMessage());
        }

        return self::SUCCESS;
    }
}
