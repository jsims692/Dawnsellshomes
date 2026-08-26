<?php

namespace App\Console\Commands;

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
        {--limit=10000 : Max URLs per IndexNow request}
        {--recent= : Only URLs changed in the last N hours (hourly freshness ping)}';

    protected $description = 'Submit the site URLs to IndexNow (Bing/Yandex/etc.) after a deployment';

    public function handle(): int
    {
        $key = config('services.indexnow.key');
        $host = $this->option('host');

        if (! $key) {
            $this->warn('INDEXNOW_KEY not set — skipping submission.');

            return self::SUCCESS;
        }

        // Same inventory the sitemap serves (pages + subdivision pages +
        // for-sale listings); --recent narrows to what the last syncs touched.
        $urls = array_slice(
            ($h = (int) $this->option('recent')) > 0
                ? \App\Support\SiteUrls::recent($h)
                : array_keys(\App\Support\SiteUrls::all()),
            0, (int) $this->option('limit'));

        if ($h > 0 && empty($urls)) {
            $this->info('Nothing changed in the window — no submission.');

            return self::SUCCESS;
        }

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

        // Full submissions (deploys) also re-submit the sitemap to Google
        // Search Console via its API; hourly --recent runs skip this.
        if ($h <= 0) {
            $this->submitToGoogle($host);
        }

        return self::SUCCESS;
    }

    /**
     * Google Search Console sitemaps.submit via a service account (the
     * anonymous ping died in 2023; the authenticated API did not). Needs
     * GSC_CREDENTIALS (path to the service-account JSON, kept out of git)
     * and the service account added as a user on the GSC property.
     */
    private function submitToGoogle(string $host): void
    {
        $credPath = config('services.google.search_console_credentials');
        if (! $credPath || ! is_file($credPath)) {
            $this->line('GSC_CREDENTIALS not set — skipping Google Search Console submission.');

            return;
        }

        try {
            $sa = json_decode((string) file_get_contents($credPath), true);
            $b64 = fn ($d) => rtrim(strtr(base64_encode($d), '+/', '-_'), '=');
            $now = time();
            $unsigned = $b64(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])).'.'
                .$b64(json_encode([
                    'iss' => $sa['client_email'],
                    'scope' => 'https://www.googleapis.com/auth/webmasters',
                    'aud' => 'https://oauth2.googleapis.com/token',
                    'iat' => $now, 'exp' => $now + 3600,
                ]));
            openssl_sign($unsigned, $sig, $sa['private_key'], 'sha256WithRSAEncryption');

            $token = Http::asForm()->timeout(20)->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $unsigned.'.'.$b64($sig),
            ])->json('access_token');
            if (! $token) {
                $this->warn('GSC: could not obtain an access token (is the service account valid?).');

                return;
            }

            $property = config('services.google.search_console_property', 'sc-domain:'.$host);
            // A bare PUT — Google rejects the empty-array body a default
            // ->put() would JSON-encode.
            $resp = Http::withToken($token)->timeout(20)->send('PUT',
                'https://www.googleapis.com/webmasters/v3/sites/'.urlencode($property)
                .'/sitemaps/'.urlencode('https://'.$host.'/sitemap.xml'));

            $resp->successful()
                ? $this->info('Google Search Console accepted the sitemap submission.')
                : $this->warn('GSC returned HTTP '.$resp->status().': '.substr($resp->body(), 0, 200));
        } catch (\Throwable $e) {
            $this->warn('GSC submission failed: '.$e->getMessage());
        }
    }
}
