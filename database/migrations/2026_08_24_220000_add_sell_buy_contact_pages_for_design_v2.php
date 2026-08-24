<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Design v2: /sell, /buy and /contact become real pages. The row supplies
     * the SEO head + sitemap entry (heads live in the DB, per the progressive-
     * rewrite rules); the Blade views at pages/{sell,buy,contact} render the
     * body. Also detaches the imported navy stylesheet (css_key) from every
     * Blade-rendered page — those views are now styled by /css/site-v2.css.
     */
    private const NEW_PAGES = [
        'sell' => [
            'title' => 'Sell Your Home — Northwest Suburbs IL | The Dawn Simmons Team, RE/MAX Suburban',
            'description' => 'Sell your home in Chicago\'s northwest suburbs with the Dawn Simmons Team: free home valuation, pricing strategy, and marketing that reaches millions. 550+ homes sold, RE/MAX Hall of Fame.',
            'keywords' => 'sell my home northwest suburbs, home valuation Prospect Heights, sell house Mount Prospect, listing agent Arlington Heights, Dawn Simmons realtor',
            'og_title' => 'Sell Your Home for Every Dollar It\'s Worth — The Dawn Simmons Team',
            'og_description' => 'Free valuation, honest pricing strategy, and marketing with real reach. Two full-time agents, 550+ homes sold.',
            'breadcrumb' => 'Sell Your Home',
        ],
        'buy' => [
            'title' => 'Buy a Home — Northwest Suburbs IL | The Dawn Simmons Team, RE/MAX Suburban',
            'description' => 'Buy a home in Chicago\'s northwest suburbs with two full-time local agents: off-market and Private Listing Network access, honest neighborhood knowledge, and negotiation that wins bidding wars.',
            'keywords' => 'buy home northwest suburbs, buyers agent Prospect Heights, Mount Prospect homes for sale, Arlington Heights buyers agent, first time buyer northwest suburbs',
            'og_title' => 'Find the Right Home — and Actually Win It | The Dawn Simmons Team',
            'og_description' => 'Two full-time local agents, off-market access, and 550+ closed deals of negotiating experience.',
            'breadcrumb' => 'Buy a Home',
        ],
        'contact' => [
            'title' => 'Contact Dawn & Josh Simmons — The Dawn Simmons Team | RE/MAX Suburban',
            'description' => 'Call, text, or message Dawn and Josh Simmons directly — two full-time agents with personal cells, 7 days a week. RE/MAX Suburban, 330 E Northwest Hwy, Mount Prospect IL.',
            'keywords' => 'contact Dawn Simmons, contact Josh Simmons realtor, RE/MAX Suburban Mount Prospect, northwest suburbs realtor phone',
            'og_title' => 'Contact The Dawn Simmons Team',
            'og_description' => 'Two agents, two personal cells, 7 days a week. Start with a free, no-pressure conversation.',
            'breadcrumb' => 'Contact',
        ],
    ];

    /** css_key each Blade-rendered page carried before v2 (for rollback). */
    private const OLD_CSS_KEYS = [
        'team' => 'root-8bc81429',
        'reviews' => 'root-8bc81429',
        'blog' => 'root-da214fc9',
        'property-management' => 'root-d9e79855',
        'sold' => 'root-3c70feee',
        'seller-net-sheet' => 'root-8d998798',
        'mortgage-calculator' => 'root-8bc81429',
    ];

    public function up(): void
    {
        foreach (self::NEW_PAGES as $path => $p) {
            DB::table('pages')->updateOrInsert(['path' => $path], [
                'type' => 'root',
                'slug' => $path,
                'title' => $p['title'],
                'meta_description' => $p['description'],
                'meta_keywords' => $p['keywords'],
                'canonical' => "https://dawnsellshomes.com/{$path}",
                'head_html' => $this->head($path, $p),
                'body_html' => '',
                'css_key' => null,
                'in_sitemap' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('pages')
            ->whereIn('path', array_keys(self::OLD_CSS_KEYS))
            ->update(['css_key' => null, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('pages')->whereIn('path', array_keys(self::NEW_PAGES))->delete();

        foreach (self::OLD_CSS_KEYS as $path => $key) {
            DB::table('pages')->where('path', $path)->update(['css_key' => $key]);
        }
    }

    private function head(string $path, array $p): string
    {
        $url = "https://dawnsellshomes.com/{$path}";
        $breadcrumb = json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://dawnsellshomes.com/'],
                ['@type' => 'ListItem', 'position' => 2, 'name' => $p['breadcrumb'], 'item' => $url],
            ],
        ], JSON_UNESCAPED_SLASHES);

        $e = fn (string $s) => htmlspecialchars($s, ENT_QUOTES);

        return <<<HTML
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$e($p['title'])}</title>
        <meta name="description" content="{$e($p['description'])}">
        <meta name="keywords" content="{$e($p['keywords'])}">
        <link rel="canonical" href="{$url}">
        <meta property="og:title" content="{$e($p['og_title'])}">
        <meta property="og:description" content="{$e($p['og_description'])}">
        <meta property="og:url" content="{$url}">
        <script type="application/ld+json">{$breadcrumb}</script>
        <!--STYLE-->
        <link rel="icon" href="/favicon.ico" sizes="any"><link rel="icon" type="image/png" sizes="32x32" href="/images/favicon-32.png">
        <meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg"><meta property="og:image:width" content="1200"><meta property="og:image:height" content="630"><meta name="twitter:card" content="summary_large_image">
        HTML;
    }
};
