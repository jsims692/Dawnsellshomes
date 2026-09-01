<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes "The Palatine Food Scene Nobody Puts in the Brochures" —
     * second Facebook-thread-sourced piece (four Palatine threads: the
     * sushi allegiances, the Target-lot tamale truck, the Mexico Uno
     * owner-answers story, the Durty Nellie's zoning news, all rewritten
     * fresh). Josh's Napoli's-since-freshman-year vouch is the opener.
     */
    private const PATH = 'blog/palatine-food-scene';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'The Palatine Food Scene Nobody Puts in the Brochures';
        $desc = "A twenty-year pizza loyalty, the suburbs' fiercest sushi rivalry, a tamale truck people set alarms for, and a restaurant owner who answers custom requests personally — what eating in Palatine is actually like.";

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Honest Answers &bull; Local Life</div>
 <h1>The Palatine Food Scene Nobody Puts in the Brochures</h1>
 <div class="meta">By Josh Simmons · September 2026 · 4 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Let me open with my own bias: <strong>Napoli's</strong> has been my slice since freshman year of high school. We drove there every single day back then &mdash; it was a $2 slice, and no, it isn't anymore, but I still stop in on the way home from <a href='/chicago-suburban-real-estate-group'>my monthly investor meetup</a>. Thick, doughy, unapologetic. Some staples you don't outgrow.</p>
 <p>Now, ask a Palatine Facebook group where to eat and you won't get recommendations &mdash; you'll get <em>allegiances</em>. Here's what the town actually argues about, and what we tell buyers who ask what living here is like.</p>
 <h2>The sushi capital of the northwest suburbs</h2>
 <p>I'm prepared to defend this: for a town its size, Palatine's sushi gravity is absurd. <strong>Sushi Para</strong> is the all-you-can-eat institution people drive in from Skokie for &mdash; the spicy tuna has actual cut tuna in it, which regulars mention like a badge of honor. Challengers get named fast: <strong>Sushi Grove</strong> (also all-you-can-eat), <strong>Sushi Asahi</strong> on Northwest Highway, and just over the borders, <strong>Sushi Edo</strong> in Rolling Meadows, <strong>Sushi Susie</strong> in Wheeling, and <strong>Kaido</strong> in Arlington Heights for the &ldquo;felt like I was in Japan&rdquo; experience. The debate never resolves. That's the point.</p>
 <h2>The tamale truck</h2>
 <p>Most Sunday mornings, a food truck sets up in the <strong>Target parking lot off Rand Road at 8 a.m. and sells homemade tamales until they're gone</strong> &mdash; usually by mid-morning. Locals set alarms for this. There's also <strong>La Casita</strong> in Mount Prospect (call ahead; they sell out) and <strong>Tamaleria y Antojitos Chepis</strong> in Arlington Heights. If you moved here from the city thinking you left great tamales behind: no.</p>
 <h2>The thing that tells you what kind of town this is</h2>
 <p>Someone recently asked the Palatine group whether any restaurant would recreate a very specific San Francisco-style quesadilla he'd been missing &mdash; photos, ingredients, and instructions in hand. Within hours, the owner of <strong>Mexico Uno</strong> replied personally: <em>come by, ask for me, we'll make it happen.</em> That's not a restaurant review. That's a town where the owner answers.</p>
 <h2>And downtown is evolving</h2>
 <p>Plans discussed at a recent Planning &amp; Zoning meeting would bring big changes to the longtime <strong>Durty Nellie's</strong> space &mdash; possibly a major new brewery tenant. Locals are split between mourning a live-music institution and excitement about what's next. Either way, it's a sign of a downtown people care enough about to argue over &mdash; which, if you're deciding where to buy, is worth more than any amenity list.</p>
 <h2>What all this appetite means for your house</h2>
 <p>A food scene like this is downtown vitality you can taste &mdash; and it shows up in demand. <a href='/market/palatine'>Palatine's live market numbers are here</a>, the <a href='/compare/arlington-heights-vs-palatine'>Arlington Heights comparison is here</a>, and <a href='/cities/palatine'>the full town rundown is here</a>. The food, you now have.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'palatine-food-scene',
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
            ." <div class=\"blog-card-cat\">Honest Answers &bull; Local Life</div>\n"
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
