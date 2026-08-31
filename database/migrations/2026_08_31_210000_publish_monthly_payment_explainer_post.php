<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes "Why two $500K homes can have very different monthly
     * payments" — the education piece behind the payment-first search
     * (draft approved by Josh 8/31). Clones the head skeleton from an
     * existing post so styling/favicon/meta plumbing stay identical, and
     * prepends the card to the hand-maintained /blog index grid.
     */
    private const PATH = 'blog/why-two-500k-homes-have-different-monthly-payments';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'Why Two $500K Homes Can Have Very Different Monthly Payments';
        $desc = 'Same list price, $400-a-month difference. Property taxes, HOA, and PMI are the three numbers that decide what a northwest-suburbs home actually costs each month — and why we search with the real ones.';

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Honest Answers &bull; Money</div>
 <h1>Why Two $500K Homes Can Have Very Different Monthly Payments</h1>
 <div class="meta">By Josh Simmons · August 2026 · 4 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Here's a conversation I have at least once a week. A buyer falls in love with two houses, both listed at $500,000, and assumes the choice is about granite versus quartz. Then we run the real numbers, and one of them costs $400 more <em>every month</em> than the other. Same price. Same loan. Very different payment.</p>
 <p>Three things drive the gap, and none of them show up in the listing photos.</p>
 <h2>Property taxes &mdash; the big one</h2>
 <p>In the northwest suburbs, two similar homes can carry wildly different tax bills. Across the homes we track, <a href='/compare/arlington-heights-vs-mount-prospect'>Arlington Heights averages about $1,150 a year more in property taxes than Mount Prospect</a> &mdash; and that's <em>town averages</em>. House to house it swings harder: I've seen neighboring subdivisions where one runs $8,000 and the other $12,500 a year. That difference alone is $375 a month, forever, and it doesn't build you a dime of equity.</p>
 <p>This is why our search shows each home's <strong>actual tax bill</strong> &mdash; the number the current owner actually pays &mdash; instead of a portal's county-average guess.</p>
 <h2>HOA fees &mdash; the quiet $340</h2>
 <p>Around half the homes for sale in our area carry an association fee, and the typical one runs about $340 a month &mdash; with plenty north of $900. Sometimes that fee genuinely replaces costs you'd pay anyway (exterior, roof, water, insurance on a condo). Sometimes it's just&hellip; a fee. Either way, $340 a month is the same payment impact as roughly $50,000 of loan. It belongs in your math from day one.</p>
 <h2>PMI &mdash; the under-20%-down surcharge</h2>
 <p>Put down less than 20% and your lender adds private mortgage insurance &mdash; typically several hundred dollars a month on a $500K home until you build enough equity to drop it. Not a reason to panic (sometimes buying sooner with PMI beats renting for two more years), but it's a real line on the statement. Our <a href='/mortgage-calculator'>mortgage calculator</a> includes it automatically when your down payment is under 20%.</p>
 <h2>So &ldquo;what can I afford?&rdquo; was never a price question</h2>
 <p>It's a monthly question. That's exactly why we built our <a href='/listings'>search by monthly payment</a>: tell it your down payment and the monthly number you're comfortable with, and it does this math on every single listing &mdash; real taxes, real HOA, today's rate &mdash; and shows you only the homes that actually fit. Not the ones that fit until the first tax bill arrives.</p>
 <p>Want us to run your specific numbers, including the lender conversation? That's a ten-minute call.</p>
 <p><a class='cta-btn' href='/listings'>Search by monthly payment &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'why-two-500k-homes-have-different-monthly-payments',
            'type' => 'blog',
            'css_key' => $donor->css_key, // shared blog stylesheet (article-hero/body)
            'title' => $title.' | Dawn Simmons Team',
            'meta_description' => $desc,
            'head_html' => $head,
            'body_html' => $body,
            'in_sitemap' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Prepend the card to the /blog index grid (newest first).
        $card = "\n <a class='blog-card' href='/".self::PATH."'>\n"
            ." <div class=\"blog-card-cat\">Honest Answers &bull; Money</div>\n"
            ." <h3>{$title}</h3>\n"
            ." <p>Same list price, \$400-a-month difference. Taxes, HOA, and PMI &mdash; the three numbers that decide what a house actually costs, and why our search does the math with the real ones.</p>\n"
            ." <div class=\"blog-card-meta\">Josh Simmons &middot; August 2026 &middot; 4 min read</div>\n"
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
