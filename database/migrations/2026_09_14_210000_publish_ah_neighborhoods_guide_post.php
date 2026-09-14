<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes the Arlington Heights neighborhoods walkability guide —
     * harvested from a resident thread (neighborhood character, pools/parks
     * facts, boundary caveats), fair-housing-scrubbed (architecture,
     * walkability, and amenities only), approved by Josh 9/14.
     */
    private const PATH = 'blog/arlington-heights-neighborhoods-guide';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'Which Arlington Heights Neighborhood Fits You? An Honest Walkability Guide';
        $desc = 'Recreation Park craftsmans, downtown condos, Stonegate Tudors, Volz Park value, Northgate and Terramere quiet — an honest local map of Arlington Heights neighborhoods by walkability, from the Dawn Simmons Team.';

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Neighborhood Guides &bull; Honest Answers</div>
 <h1>Which Arlington Heights Neighborhood Fits You? An Honest Walkability Guide</h1>
 <div class="meta">By Josh Simmons · September 2026 · 5 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Every week someone tells me the same dream: a street with sidewalks, a park you can walk to, and downtown restaurants close enough that the car stays home. Arlington Heights delivers that better than almost any town in the northwest suburbs &mdash; but <em>which part</em> of Arlington Heights matters enormously. Here's the honest map, the way locals actually describe it.</p>
 <h2>The walk-to-everything zone</h2>
 <p>Ring around downtown, and the closer you get, the more it feels like a city neighborhood that happens to have yards. <a href='/neighborhoods/recreation-park-arlington-heights'>Recreation Park</a> is the classic: 1920s craftsman homes and bungalows, real sidewalks, and you can walk to the shops, the library, the park, and the Metra. The historic district and the blocks just south of downtown have the same character. West of town, <strong>Volz Park</strong> is the value play locals mention quietly &mdash; mostly 1970s builds, about five blocks to the train, without the near-downtown premium.</p>
 <h2>Downtown itself</h2>
 <p>If your ideal Saturday is zero driving, the condo buildings around the station put you a block from the train, two from the farmers market, across from the Metropolis theater, and inside a ring of forty-some restaurants. In summer the town closes the streets for Arlington Al Fresco and the whole downtown turns into a patio &mdash; I've written about <a href='/blog/just-moved-to-arlington-heights'>my favorite eating in town</a> if you want the food tour.</p>
 <h2>Classic-suburban, still close</h2>
 <p><a href='/neighborhoods/stonegate-arlington-heights'>Stonegate</a> and Scarsdale are the postcard streets &mdash; Tudors and stately lots, fewer sidewalks, more quiet, yet still bikeable to town. This is the &ldquo;we want space <em>and</em> the downtown&rdquo; compromise, and it prices accordingly.</p>
 <h2>North Arlington Heights</h2>
 <p><a href='/neighborhoods/northgate-arlington-heights'>Northgate</a>, <a href='/neighborhoods/terramere-arlington-heights'>Terramere</a>, and the neighborhoods near Hersey trade the walk-to-downtown for quieter streets, newer stock, and their own walkable parks and pools. Different rhythm, not a lesser one.</p>
 <h2>What the whole town shares</h2>
 <p>Parks every few blocks, five outdoor community pools spread across town plus an indoor one off Euclid, and a library people genuinely brag about.</p>
 <h2>The fine print I make every buyer check</h2>
 <p>School assignments here shift block-by-block at the edges &mdash; areas near Dundee Road and north of Hintz feed different districts than the blocks beside them. Never assume from the town name; verify the exact address with the district. It's a two-minute check that prevents a very bad surprise.</p>
 <p>Want to see what's actually for sale in each of these? <a href='/listings?city%5B%5D=Arlington+Heights'>Every Arlington Heights listing, updated all day</a> &mdash; or <a href='/listings'>search by monthly payment</a> with each home's real tax bill. And when you're ready for the street-level truth &mdash; which blocks flood, which corners are loud &mdash; that part's a phone call.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'arlington-heights-neighborhoods-guide',
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
            ." <div class=\"blog-card-cat\">Neighborhood Guides &bull; Honest Answers</div>\n"
            ." <h3>{$title}</h3>\n"
            ." <p>{$desc}</p>\n"
            ." <div class=\"blog-card-meta\">Josh Simmons &middot; September 2026 &middot; 5 min read</div>\n"
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
