<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Revives the monthly market-update series (lapsed at the Aug cutover;
     * the June/July issues had their own ranking history). September 2026
     * issue — aggregate stats pulled live from the MLS replica on 9/16,
     * MRED/MLS GRID attribution included. Approved by Josh 9/17.
     */
    private const PATH = 'blog/2026-09-northwest-suburbs-market-update';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'Northwest Suburbs Market Update: September 2026';
        $desc = 'Median 8 days to go under contract, 633 homes for sale, 499 under contract, median sold $410K — the September 2026 numbers for Chicago\'s northwest suburbs, straight from the MLS, from the Dawn Simmons Team.';

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Market Updates &bull; Honest Answers</div>
 <h1>Northwest Suburbs Market Update: September 2026</h1>
 <div class="meta">By Josh Simmons · September 2026 · 3 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>The market update is back &mdash; and September's story in one number: <strong>homes that closed in the last 30 days went under contract in a median of 8 days.</strong> Eight. Anyone waiting for the fall slowdown to go bargain-hunting should read that twice.</p>
 <h2>The numbers</h2>
 <p>Our nine core towns &mdash; Prospect Heights, Mount Prospect, Arlington Heights, Palatine, Wheeling, Buffalo Grove, Des Plaines, Rolling Meadows, Elk Grove Village &mdash; straight from the MLS:</p>
 <p><strong>633 homes actively for sale</strong> &mdash; and 448 of the actives hit the market in just the last two weeks, so fresh fall inventory is genuinely arriving.</p>
 <p><strong>499 more already under contract</strong> &mdash; nearly as many homes are spoken for as are available, which is what a seller's market looks like in the data.</p>
 <p><strong>Median asking price: $425,000</strong> &middot; <strong>Median sold price (last 30 days): $410,000</strong></p>
 <p><strong>413 closings in the last 30 days</strong>, down from 488 the month before &mdash; the seasonal cooldown is real, but it's showing up in <em>volume</em>, not price: the median sale actually ticked up $5,000 month-over-month.</p>
 <h2>What it means if you're buying</h2>
 <p>An 8-day market doesn't wait for your second showing. Get pre-approved before you tour, and let the machines watch for you &mdash; <a href='/listings'>save a search</a> and we'll email you the moment a match hits the MLS, or <a href='/listings'>search by monthly payment</a> to know your real number before you fall in love.</p>
 <h2>What it means if you're selling</h2>
 <p>Demand hasn't left &mdash; but with closings down 15% month-over-month, the buyers still out there are decisive <em>and</em> selective. Priced right, you're under contract in a week; priced wrong, you're the listing that watches everyone else sell. That first number is the whole game &mdash; <a href='/sell'>see what your home is actually worth</a>.</p>
 <h2>Dig into your own town</h2>
 <p>Our <a href='/market'>live market reports</a> run these numbers per-town all day, and the <a href='/compare'>town comparison tool</a> puts any two side by side.</p>
 <p style='font-size:12px;color:#8A99AA;'>Sold and listing data courtesy of MRED as distributed by MLS GRID, as of September 16, 2026. Aggregate market statistics &mdash; individual homes vary; deemed reliable but not guaranteed.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => '2026-09-northwest-suburbs-market-update',
            'type' => 'blog',
            'css_key' => $donor->css_key,
            'title' => $title.' | Dawn Simmons Team',
            'meta_description' => $desc,
            'head_html' => $head,
            'body_html' => $body,
            'in_sitemap' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $card = "\n <a class='blog-card' href='/".self::PATH."'>\n"
            ." <div class=\"blog-card-cat\">Market Updates &bull; Honest Answers</div>\n"
            ." <h3>{$title}</h3>\n"
            ." <p>{$desc}</p>\n"
            ." <div class=\"blog-card-meta\">Josh Simmons &middot; September 2026 &middot; 3 min read</div>\n"
            ." </a>";
        $index = DB::table('pages')->where('path', 'blog')->first();
        if ($index && ! str_contains($index->body_html, self::PATH)) {
            DB::table('pages')->where('path', 'blog')->update([
                'body_html' => str_replace('<div class="blog-grid">', '<div class="blog-grid">'.$card, $index->body_html),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('pages')->where('path', self::PATH)->delete();
    }
};
