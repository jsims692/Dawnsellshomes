<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Publishes "Buying on the Chain? Let's Talk About Flooding." — the
     * moat piece: honest flood guidance for Chain O'Lakes buyers, built
     * from harvested flood-thread knowledge (pier physics, the community
     * safety net, county gauge links) with Josh's Mineola Bay standing and
     * his Allan Lukasz vouch. Voice-passed 9/1: heart-of-the-Chain opener,
     * Allan named with approval.
     */
    private const PATH = 'blog/buying-on-the-chain-flooding';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = "Buying on the Chain? Let's Talk About Flooding.";
        $desc = 'The honest Chain O\'Lakes flood guide from an owner on Mineola Bay: what high water actually looks like, the tricks old-timers know, and the five things to check before you buy on the water.';

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        $body = <<<'HTML'
<div class="article-hero">
 <div class="category">Life on the Chain &bull; Honest Answers</div>
 <h1>Buying on the Chain? Let's Talk About Flooding.</h1>
 <div class="meta">By Josh Simmons · September 2026 · 5 min read</div>
</div>
<div class="article-body">
 <a class='back-link' href='/blog'> Back to all articles</a>
 <p>I keep a place on <strong>Mineola Bay in Fox Lake</strong> &mdash; the heart of the Chain &mdash; so I'm writing this as a neighbor, not a tourist. And here's the sentence every Chain O'Lakes veteran eventually says with a shrug: <em>not the first flood, won't be the last.</em> If you're thinking about buying on the water here, you deserve the honest version &mdash; because the Chain is the best-kept lifestyle secret in Illinois, <em>and</em> it floods. Both things are true.</p>
 <h2>What high water actually looks like</h2>
 <p>In a big flood year, the river and lakes come up for weeks, not days. Pier sections get flipped and carried off. Sandbag lines appear around houses and businesses. Some folks reach their front door in waders for a stretch. The county issues gauge alerts, and everyone refreshes the forecast like it's a playoff bracket. Then the water goes down, the community digs out, and summer resumes.</p>
 <h2>What the old-timers know (that I'll save you a flood's tuition on)</h2>
 <p><strong>Weigh your pier down before high water.</strong> The local trick is garbage cans filled with water on the decking. Cheap insurance against watching your sections float away.</p>
 <p><strong>Know your bottom.</strong> The lake bed changes lot to lot &mdash; hard sand in one spot, quicksand-grade silt fifty yards away. It decides whether a flipped pier is a Saturday with friends or a job for the barge crew.</p>
 <p><strong>Sediment moves.</strong> Locals below a recently removed dam say sand and silt are shifting like never before. Channels change; ask about yours.</p>
 <p><strong>The community is the safety net.</strong> Washed-away pier sections routinely get pulled from the water by strangers hunting for the owner. Work parties assemble for the price of a case of beer. And when a job is beyond a Saturday with friends, the Chain has its own MacGyver: <strong>Allan Lukasz</strong>. I grew up with him, half the Chain tags him by name when something goes sideways, and he's saved my own place more times than I can count. He's <a href='/who-we-use'>on our trusted-contacts list</a> for a reason.</p>
 <h2>What I check before a client buys on the water &mdash; and you should too</h2>
 <p><strong>1. The FEMA flood zone and a real flood-insurance quote, before you offer.</strong> Not after. A flood premium can move your monthly payment meaningfully &mdash; and our <a href='/listings'>payment-first search</a> estimates standard insurance, <em>not</em> flood premiums, so on the Chain this is the number you add yourself. Get the quote early; sometimes it's pleasant, sometimes it changes the negotiation.</p>
 <p><strong>2. Elevation and history.</strong> How high is the living space relative to the water &mdash; and what happened here in the big years? Sellers must disclose, but the right questions get better answers.</p>
 <p><strong>3. The equipment.</strong> Sump, battery backup, seawall condition, pier condition, where the mechanicals sit. Water finds the unprepared.</p>
 <p><strong>4. Lakefront vs. channel-front.</strong> Different flood behavior, different price, different life. <a href='/blog/waterfront-vs-channel-front-chain-o-lakes'>We wrote the honest comparison here.</a></p>
 <p><strong>5. Watch the water yourself.</strong> The <a href='https://water.noaa.gov/gauges/FLVI2' rel='noopener' target='_blank'>NOAA Chain O'Lakes gauge</a> is the same one the county uses. Bookmark it and you'll know more than most sellers.</p>
 <h2>Why almost nobody leaves</h2>
 <p>I've watched floods come and go, and the docks fill right back up. The <a href='/chain-o-lakes'>Chain lifestyle</a> &mdash; boats to dinner, sunsets off the pier, the whole culture &mdash; is worth the occasional wet spring to the people who live it. The job isn't to talk you out of the water. It's to make sure you buy the <em>right spot</em> on it &mdash; and after decades here, we know which ones stayed dry. That part's a phone call.</p>
 <p><a class='cta-btn' href='/contact'>Talk to Dawn &amp; Josh &rarr;</a></p>
</div>
HTML;

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => 'buying-on-the-chain-flooding',
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
            ." <div class=\"blog-card-cat\">Life on the Chain &bull; Honest Answers</div>\n"
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
