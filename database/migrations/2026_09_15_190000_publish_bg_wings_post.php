<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes the Buffalo Grove wings/food article — harvested wing-thread
     * consensus with Josh's Nino's Oreganato vouch as the spine (Chicken La
     * Nino, baked clams, pizza; Buffalo Grove Road location corrected by
     * Josh 9/15). Ties into the same-week U.S. News #1-in-Illinois moment.
     */
    private const PATH = 'blog/buffalo-grove-wings-and-ninos';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'The Buffalo Grove Wing Debate, Settled — Plus My Favorite Dish on Earth';
        $desc = "Gators won the town's wing vote in a landslide, but the real headline is Nino's Oreganato on Buffalo Grove Road: the Chicken La Nino, baked clams, and the rest of Buffalo Grove's honest food map from the Dawn Simmons Team.";

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Local Eats &bull; Honest Answers</div>
 <h1>The Buffalo Grove Wing Debate, Settled &mdash; Plus My Favorite Dish on Earth</h1>
 <div class="meta">By Josh Simmons · September 2026 · 4 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Buffalo Grove just got named the #1 place to live in Illinois, and I can report the town celebrated the way it does everything: by arguing about chicken wings on Facebook. A neighbor asked &ldquo;best wings?&rdquo; and the town delivered a full ranked ballot. Here's the tally &mdash; plus the thing I actually need to tell you about.</p>
 <h2>Start with my confession</h2>
 <p>Before the wings: <strong>Nino's Oreganato</strong> on Buffalo Grove Road is home to my favorite dish of all time. Not favorite-in-Buffalo-Grove. Favorite, period. The <strong>Chicken La Nino</strong> is a plate I would put up against restaurants with stars in the window, and I've been chasing its equal for years without success. While you're there: the pizza is great and the <strong>baked clams are to die for</strong>. Locals also swear by their Vesuvio wings &mdash; &ldquo;unique and delicious&rdquo; was the exact phrase in the thread, and they're right. If you take one recommendation from this entire article, it's Nino's.</p>
 <h2>The wing vote itself was a landslide</h2>
 <p><strong>Gators Wing Shack</strong> on Rand Road (between Dundee and Lake-Cook) took it by a mile &mdash; when the original poster called it, her literal words were &ldquo;majority rules, Gators it is.&rdquo; It's the default answer for a reason.</p>
 <h2>The challengers with real constituencies</h2>
 <p><strong>Buffalo Joe's</strong> (&ldquo;amazing&rdquo;), <strong>Sports Page</strong> on Rand, <strong>Penguino's</strong> right in BG, and &mdash; the vote nobody saw coming &mdash; <strong>Lou Malnati's</strong>, whose extra-crispy BBQ wings and gorgonzola dipping sauce have a devoted following among people who came for deep dish and stayed for the wings. <strong>Prairie House</strong> earned points for doing it the hard way: fresh never frozen, hand-breaded, homemade ranch. And the sleeper pick: <strong>Lucky Monk</strong> in Barrington &mdash; mostly known as a burger joint, quietly excellent wings.</p>
 <h2>Worth the drive</h2>
 <p>The thread's most repeated out-of-town answer was <strong>Franks for the Memories</strong> in Mundelein (&ldquo;1000% recommend&rdquo;), with honorable mentions for <strong>Philly Special</strong> in Vernon Hills and <strong>Old Munich</strong> in Wheeling &mdash; because no wing conversation in this corner of the suburbs stays inside one town's borders.</p>
 <p>This is part of how we work: we live here, we eat here, and we argue about wings here. If you're thinking about calling the #1 town in Illinois home, start with <a href='/listings?city%5B%5D=Buffalo+Grove'>everything for sale in Buffalo Grove</a> &mdash; real monthly payments, actual tax bills &mdash; and our full <a href='/cities/buffalo-grove'>Buffalo Grove guide</a>. And if you tour a home with us on a Saturday, there's a decent chance I'll route us past Nino's.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'buffalo-grove-wings-and-ninos',
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
            ." <div class=\"blog-card-cat\">Local Eats &bull; Honest Answers</div>\n"
            ." <h3>{$title}</h3>\n"
            ." <p>{$desc}</p>\n"
            ." <div class=\"blog-card-meta\">Josh Simmons &middot; September 2026 &middot; 4 min read</div>\n"
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
