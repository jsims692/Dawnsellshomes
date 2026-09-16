<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes the private-room party venues guide — harvested from a BG
     * baby-shower thread's venue consensus (Continental, Saranello's,
     * Cooper's Hawk, Walker Brothers, the Italian-room circuit, park-
     * district budget option). Approved by Josh 9/16, shipped as-is.
     */
    private const PATH = 'blog/private-room-party-venues-buffalo-grove';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'The Private-Room List: Where Locals Actually Host Showers & Parties Around Buffalo Grove';
        $desc = "The Continental, Saranello's brunch spread, Cooper's Hawk, Walker Brothers, the Italian-room circuit, and the park-district budget trick — where the Buffalo Grove area actually hosts its showers, graduations, and party brunches.";

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Local Life &bull; Honest Answers</div>
 <h1>The Private-Room List: Where Locals Actually Host Showers &amp; Parties Around Buffalo Grove</h1>
 <div class="meta">By Josh Simmons · September 2026 · 4 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Ask a Buffalo Grove Facebook group where to host a baby shower and you'll get fifty answers in an hour &mdash; which is exactly what happened, so I kept the tally. If you're planning a shower, graduation party, birthday brunch, or any 30-people-and-a-cake event around here, this is the town's collective answer, organized.</p>
 <h2>The repeat winners</h2>
 <p><strong>The Continental</strong> in Buffalo Grove came up over and over &mdash; &ldquo;they do great with parties,&rdquo; &ldquo;you would not be disappointed.&rdquo; <strong>Saranello's</strong> in Wheeling is the brunch-spread favorite (locals quoted about $43 a head, all-inclusive, as of this fall). <strong>Cooper's Hawk</strong> and <strong>Wildfire</strong> in Lincolnshire both earn &ldquo;amazing to work with&rdquo; reviews for their party rooms, and <strong>Walker Brothers</strong> in Lincolnshire is the sentimental favorite &mdash; multiple people said &ldquo;mine was there.&rdquo;</p>
 <h2>The pleasant surprises</h2>
 <p><strong>Biaggi's</strong> in Deer Park &mdash; &ldquo;nice room, not too expensive, classy, great to work with.&rdquo; <strong>Lou Malnati's</strong> in BG has a proper private room that's hosted everything from bridal showers on down. <strong>Prairie House</strong> gets praise for room <em>and</em> prices. And <strong>Original Bagel &amp; Bialy</strong> hosts casual private-room brunches that one host called flawless &mdash; the sleeper budget pick.</p>
 <h2>The Italian-room circuit</h2>
 <p>This corner of the suburbs does the private-room-at-an-Italian-place genre exceptionally well: <strong>Enzo &amp; Lucia</strong> and <strong>Sorelle</strong> in Long Grove, <strong>Gianni's</strong> in Palatine (wonderful brunch), <strong>Francesca's</strong> in Arlington Heights, and <strong>Osteria Trulli Pugliese</strong> at Hintz and Buffalo Grove Road &mdash; &ldquo;like an escape to Italy.&rdquo;</p>
 <h2>The budget playbook locals use</h2>
 <p>Park-district banquet rooms. <strong>Plum Grove Banquet Hall</strong> through the Rolling Meadows Park District rents an open room where you bring your own food and decorations &mdash; simple and genuinely affordable. Most area park districts have a version; call yours first if the budget's tight.</p>
 <h2>Something different</h2>
 <p><strong>Broken Earth Winery</strong> in Long Grove, for when you want the photos to look like nobody else's shower.</p>
 <p>New to the area and collecting places like these? That's basically our job description. Start with our <a href='/cities/buffalo-grove'>Buffalo Grove guide</a> &mdash; yes, the town U.S. News just ranked #1 in Illinois &mdash; and <a href='/listings?city%5B%5D=Buffalo+Grove'>everything for sale nearby</a>. We'll handle the house; the Continental can handle the shower.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'private-room-party-venues-buffalo-grove',
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
            ." <div class=\"blog-card-cat\">Local Life &bull; Honest Answers</div>\n"
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
