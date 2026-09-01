<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes "Just Moved to Arlington Heights? Where to Eat — and How to
     * Actually Meet People" — first of the Facebook-thread-sourced local
     * pieces (knowledge harvested, zero sentences copied; Josh's own
     * Johnny's Beef / Shakou / Naomi endorsements are the first-person
     * spine). Approved with additions 8/31.
     */
    private const PATH = 'blog/just-moved-to-arlington-heights';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'Just Moved to Arlington Heights? Where to Eat — and How to Actually Meet People';
        $desc = "Johnny's Beef confessions, the Asian-market secret, the great Greek debate, both sushi moods, and where people in their 20s and 30s actually meet — from a team that never left.";

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Honest Answers &bull; New in Town</div>
 <h1>Just Moved to Arlington Heights? Where to Eat &mdash; and How to Actually Meet People</h1>
 <div class="meta">By Josh Simmons · September 2026 · 4 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>Let me start with my own confession: I cannot drive past <strong>Johnny's Beef</strong> without stopping. Beef wet, sweet peppers, and a fry &mdash; that's the order, don't overthink it. And when you're done, their Italian ice is about the cheapest dessert in town and they hand it to you in a serving the size of a paint can. If you just moved here, go today.</p>
 <p>Now the rest &mdash; because we get this question from almost every buyer we help relocate, and the town's answers are remarkably consistent.</p>
 <h2>Breakfast</h2>
 <p><strong>Mr. Allison's.</strong> Big portions, no fuss, and the locals treat it like a membership club.</p>
 <h2>The secret weapon nobody expects out here</h2>
 <p>The Asian markets. <strong>Mitsuwa</strong> and <strong>Tensuke Market</strong> both have food courts that would embarrass most sit-down restaurants &mdash; ramen, katsu, sushi &mdash; attached to groceries you'll come back for weekly. This is the recommendation that makes people from the city stop feeling smug.</p>
 <h2>Sushi, both moods</h2>
 <p><strong>Shakou</strong> when it's a proper night out &mdash; upscale, and the cocktails earn their price. <strong>Naomi</strong> when the fancy drinks aren't necessary and you just want my go-to sushi. I keep both in rotation and refuse to apologize.</p>
 <h2>The great Greek debate</h2>
 <p>Downtown has <strong>Nostimo</strong> and <strong>Parea</strong>, and locals will argue loyalty like it's a sports rivalry. Honest answer: one's quick counter-service, one's the fancier sit-down &mdash; they're both good, and which you prefer says more about the night you're having than the food.</p>
 <h2>Beyond that</h2>
 <p><strong>Ttowa</strong> for Korean, <strong>Salsa 17</strong> for Mexican downtown, <strong>Cooper's Hawk</strong> or <strong>Felini's</strong> when someone's parents are visiting, and Lou Malnati's because you live here now and it's the law. Worth the short drive: Marino's in Elk Grove for pizza, <strong>Durty Nellie's</strong> in Palatine for drinks and live shows, and a Polish food truck that parks on Rand Road in Mount Prospect most of the week &mdash; worth hunting down. Sweet tooth: <strong>Jarosch Bakery</strong>, a legitimate institution.</p>
 <h2>Meeting people in your 20s and 30s</h2>
 <p>The part nobody puts on TikTok: the <strong>Arlington Heights Memorial Library</strong> runs meetups specifically for that age group (check ahml.info's events calendar). <strong>Hey Nonny</strong> does live music with real food. <strong>The Ale House</strong> above the Metropolis draws that crowd. <strong>Kahala Koa</strong> is a tiki bar that's family-friendly early, cocktail-serious late. And from spring through summer, downtown closes its streets for <strong>Arlington Al Fresco</strong> &mdash; restaurants spill their tables into the street, and it's when this town is at its absolute best. You'll accidentally meet half your neighbors.</p>
 <h2>Why a real estate team is writing about beef sandwiches</h2>
 <p>Because this is the stuff that never shows up in a listing. Two towns can look identical on paper and feel completely different at dinner. It's half the reason <a href='/cities/arlington-heights'>people pick Arlington Heights</a> &mdash; and if you're still weighing towns, <a href='/compare/arlington-heights-vs-mount-prospect'>our live comparisons</a> cover the numbers half. The food half, you now know.</p>
 <p>New to the area and want the version of this conversation about <em>your</em> situation? That's literally our job.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'just-moved-to-arlington-heights',
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
            ." <div class=\"blog-card-cat\">Honest Answers &bull; New in Town</div>\n"
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
