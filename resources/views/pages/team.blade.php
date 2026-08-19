<x-site.layout :page="$page" :head="$head">
<style>
  .sec-label { font-family:Arial,sans-serif; font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:var(--red); margin-bottom:12px; }
  .tm-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:28px; margin-top:32px; }
  .tm-card { background:#fff; border:1px solid #e0e4ed; border-radius:12px; overflow:hidden; border-top:4px solid var(--navy); }
  .tm-photo { aspect-ratio:4/5; background:#eef1f6; }
  .tm-photo img { width:100%; height:100%; object-fit:cover; display:block; }
  .tm-info { padding:24px 26px 28px; }
  .tm-name { font-family:Georgia,serif; font-size:24px; color:var(--navy); margin:0 0 4px; }
  .tm-title { font-family:Arial,sans-serif; font-size:12px; font-weight:700; letter-spacing:1px; text-transform:uppercase; color:var(--gold); margin-bottom:14px; }
  .tm-bio { font-size:15.5px; line-height:1.75; color:#444; margin:0 0 16px; }
  .tm-contact { font-family:Arial,sans-serif; font-size:14px; line-height:1.9; }
  .tm-contact a { color:var(--navy); text-decoration:none; font-weight:700; }
  .tm-story { background:var(--navy); color:#fff; border-radius:12px; padding:40px 36px; border-left:4px solid var(--gold); margin-top:40px; }
  .tm-story .sec-label { color:var(--gold); }
  .tm-story p { color:rgba(255,255,255,.88); font-size:16px; line-height:1.9; }
  .tm-facts { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-top:26px; }
  .tm-fact { background:#fff; border:1px solid #e0e4ed; border-radius:8px; padding:16px; text-align:center; }
  .tm-fact b { display:block; font-family:Arial,sans-serif; font-size:26px; font-weight:900; color:var(--navy); }
  .tm-fact span { font-family:Arial,sans-serif; font-size:11px; letter-spacing:1px; text-transform:uppercase; color:#777; }
</style>

<div class="hero">
  <div class="breadcrumb"><a href="/">Home</a> &rsaquo; Our Team</div>
  <h1>Born and Raised Here. <em>Still Here.</em></h1>
  <p>The Dawn Simmons Team isn't a marketing name &mdash; it's literally a mother and son who grew up in these neighborhoods, raised a family here, and now help other families do the same.</p>
</div>

<section>
  <div class="inner">
    <div class="sec-label">The Family Behind The Team</div>
    <h2>Meet Dawn &amp; Josh</h2>
    <p>Two full-time agents, one family, every deal worked together. You get Dawn's 26 years of experience and Josh's energy and hustle &mdash; never a hand-off to an assistant.</p>

    <div class="tm-grid">
      <div class="tm-card" id="dawn">
        <div class="tm-photo"><img src="/images/dawn-simmons.jpg" srcset="/images/dawn-simmons.jpg 800w, /images/dawn-simmons@2x.jpg 1600w" sizes="(max-width: 700px) 92vw, 440px" width="800" height="1000" alt="Dawn Simmons, REALTOR and Broker, RE/MAX Suburban"></div>
        <div class="tm-info">
          <h3 class="tm-name">Dawn Simmons</h3>
          <div class="tm-title">REALTOR&reg; &middot; Broker &middot; RE/MAX Hall of Fame</div>
          <p class="tm-bio">Dawn grew up in Chicago and moved to Prospect Heights in 1988 &mdash; drawn by the large lots, great schools, and the kind of neighborhood where you actually know your neighbors. She raised three boys here, served as the local Cub Scout master, and watched her family grow roots in the same community she now helps others find. She got her real estate license in 2001 and has been selling homes in the northwest suburbs ever since. 550+ transactions. RE/MAX Hall of Fame. But what she's most proud of is that all three of her sons still call Prospect Heights home.</p>
          <div class="tm-contact"><a href="tel:8477381884">(847) 738-1884</a><br><a href="mailto:simsre2000@yahoo.com">simsre2000@yahoo.com</a></div>
        </div>
      </div>
      <div class="tm-card" id="josh">
        <div class="tm-photo"><img src="/images/josh-simmons.jpg" srcset="/images/josh-simmons.jpg 800w, /images/josh-simmons@2x.jpg 1600w" sizes="(max-width: 700px) 92vw, 440px" width="800" height="1000" alt="Josh Simmons, Broker Associate, RE/MAX Suburban" loading="lazy"></div>
        <div class="tm-info">
          <h3 class="tm-name">Josh Simmons</h3>
          <div class="tm-title">REALTOR&reg; &middot; Broker Associate</div>
          <p class="tm-bio">Josh is the middle of Dawn's three boys &mdash; all of whom still live in Prospect Heights. He grew up here, went to school here, and watched his mom build her business from the inside. During college he started helping with rentals, and by the time he had his Business Management degree from DePaul University, he knew exactly what he wanted to do. He skipped grad school and joined Dawn full time &mdash; and hasn't looked back. He brings the energy, the hustle, and the local knowledge that only comes from literally growing up in the neighborhoods he now helps people buy and sell.</p>
          <div class="tm-contact"><a href="tel:2246284013">(224) 628-4013</a> &middot; <a href="sms:2246284013">Text Josh</a><br><a href="mailto:jsims692@gmail.com">jsims692@gmail.com</a></div>
        </div>
      </div>
    </div>

    <div class="tm-facts">
      <div class="tm-fact"><b>550+</b><span>Homes sold</span></div>
      <div class="tm-fact"><b>26+</b><span>Years experience</span></div>
      <div class="tm-fact"><b>4.9&#9733;</b><span>62+ Google reviews</span></div>
      <div class="tm-fact"><b>10M+</b><span>Views on one home tour</span></div>
    </div>

    <div class="tm-story">
      <div class="sec-label">Our Story</div>
      <p>In 1988, Dawn moved from the city to Prospect Heights for the large lots and the sense of community she wanted for her family. She raised three boys there &mdash; her husband coached their soccer teams, she led the Cub Scouts, and they were embedded in every corner of neighborhood life. In 2001, she turned that love of the community into a real estate career, helping other families find what she'd already found.</p>
      <p style="margin:14px 0 0;">When Josh was in college, he started working alongside Dawn on rentals &mdash; and fell in love with it. After earning his degree from DePaul, he joined her full time. Today all three of Dawn's sons still live in Prospect Heights. This isn't a job for them. It's the community they grew up in and chose to stay in &mdash; and they want to help you find yours.</p>
    </div>
  </div>
</section>

<section class="alt">
  <div class="inner">
    <h2>How We Work</h2>
    <div class="fp-grid">
      <div class="fp-card"><h3>7 days a week, day or night</h3><p>You get our personal cell numbers. Deals don't happen 9-to-5 and neither do we.</p></div>
      <div class="fp-card"><h3>We fight for every dollar</h3><p>Pricing strategy and hard negotiation are where money is actually made or lost. Ask our sellers about &ldquo;sold in 2 days.&rdquo;</p></div>
      <div class="fp-card"><h3>Marketing that reaches millions</h3><p>One of Josh's video walkthroughs topped 10 million views. Your listing gets that same energy.</p></div>
      <div class="fp-card"><h3>We actually grew up here</h3><p>Not &ldquo;serving the area&rdquo; &mdash; living in it. We know which blocks flood, which streets are quiet, and what a home is really worth.</p></div>
      <div class="fp-card"><h3>Verifiable track record</h3><p><a href="/sold">550+ closed sales</a>, mapped, and <a href="/reviews">62+ public Google reviews</a>. Nothing to take on faith.</p></div>
      <div class="fp-card"><h3>Two full-time agents</h3><p>Every client works with both of us. Two sets of eyes, two networks, one team.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="inner" style="text-align:center;">
    <h2>Let's Talk About Your Next Move</h2>
    <p style="max-width:600px;margin:0 auto 20px;">Buying, selling, or just wondering what your home is worth &mdash; start with a free, no-pressure conversation with the two of us.</p>
    <a class="search-btn" href="/#contact">Contact Dawn &amp; Josh</a>
    <a class="outline-btn" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    <p style="margin-top:18px;font-size:14px;">See <a href="/reviews">what clients say</a> &middot; Browse <a href="/sold">homes we've sold</a> &middot; New to the area? <a href="/moving-to-northwest-suburbs">Start here</a>.</p>
  </div>
</section>
</x-site.layout>
