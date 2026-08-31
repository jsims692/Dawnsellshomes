<x-site.layout :page="$page" :head="$head">

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Our Team</p>
    <p class="eyebrow">The family behind the team</p>
    <h1>Born and raised here. <em>Still here.</em></h1>
    <p class="lead">The Dawn Simmons Team isn&rsquo;t a marketing name &mdash; it&rsquo;s literally a mother and son who grew up in these neighborhoods, raised a family here, and now help other families do the same.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Meet Dawn &amp; Josh</p>
      <h2 class="h2">Two full-time agents. One family. Every deal worked together.</h2>
      <p class="lead">You get Dawn&rsquo;s 26 years of experience and Josh&rsquo;s energy and hustle &mdash; never a hand-off to an assistant.</p>
    </div>
    <div class="team-grid">
      <div class="member" id="dawn">
        <div class="ph"><div class="ph-frame"><img src="/images/dawn-simmons.jpg" srcset="/images/dawn-simmons.jpg 800w, /images/dawn-simmons@2x.jpg 1600w" sizes="(max-width: 700px) 92vw, 340px" width="800" height="1000" alt="Dawn Simmons, REALTOR and Broker, RE/MAX Suburban"></div></div>
        <div>
          <h3>Dawn Simmons</h3>
          <div class="role">REALTOR&reg; &middot; Broker &middot; RE/MAX Hall of Fame</div>
          <p>Dawn grew up in Chicago and moved to Prospect Heights in 1988 &mdash; drawn by the large lots, great schools, and the kind of neighborhood where you actually know your neighbors. She raised three boys here, served as the local Cub Scout master, and watched her family grow roots in the same community she now helps others find. She got her real estate license in 2001 and has been selling homes in the northwest suburbs ever since. {{ \App\Support\TeamStats::soldTotal() }} transactions. RE/MAX Hall of Fame. But what she&rsquo;s most proud of is that all three of her sons still call Prospect Heights home.</p>
          <p><a class="link-arrow" href="tel:8477381884">(847) 738-1884</a><br><a href="mailto:dawn@dawnsellshomes.com">dawn@dawnsellshomes.com</a></p>
        </div>
      </div>
      <div class="member" id="josh">
        <div class="ph"><div class="ph-frame"><img src="/images/josh-simmons.jpg" srcset="/images/josh-simmons.jpg 800w, /images/josh-simmons@2x.jpg 1600w" sizes="(max-width: 700px) 92vw, 340px" width="800" height="1000" alt="Josh Simmons, Broker Associate, RE/MAX Suburban" loading="lazy"></div></div>
        <div>
          <h3>Josh Simmons</h3>
          <div class="role">REALTOR&reg; &middot; Broker Associate</div>
          <p>Josh is the middle of Dawn&rsquo;s three boys &mdash; all of whom still live in Prospect Heights. He grew up here, went to school here, and watched his mom build her business from the inside. During college he started helping with rentals, and by the time he had his Business Management degree from DePaul University, he knew exactly what he wanted to do. He skipped grad school and joined Dawn full time &mdash; and hasn&rsquo;t looked back. He brings the energy, the hustle, and the local knowledge that only comes from literally growing up in the neighborhoods he now helps people buy and sell.</p>
          <p><a class="link-arrow" href="tel:2246284013">(224) 628-4013</a> &middot; <a href="sms:2246284013">Text Josh</a><br><a href="mailto:josh@dawnsellshomes.com">josh@dawnsellshomes.com</a></p>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="stats section--ink">
  <div class="wrap">
    <div class="stats-grid">
      <div><div class="stat-num">{{ \App\Support\TeamStats::soldTotal() }}</div><div class="stat-label">Homes sold</div></div>
      <div><div class="stat-num">26+</div><div class="stat-label">Years experience</div></div>
      <div><div class="stat-num">4.9&#9733;</div><div class="stat-label">62+ Google reviews</div></div>
      <div><div class="stat-num">10M+</div><div class="stat-label">Views on one home tour</div></div>
    </div>
  </div>
</section>

<section class="section section--ink" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Our story</p>
      <h2 class="h2">From the city to the suburbs &mdash; and never leaving.</h2>
    </div>
    <p class="lead">In 1988, Dawn moved from the city to Prospect Heights for the large lots and the sense of community she wanted for her family. She raised three boys there &mdash; her husband coached their soccer teams, she led the Cub Scouts, and they were embedded in every corner of neighborhood life. In 2001, she turned that love of the community into a real estate career, helping other families find what she&rsquo;d already found.</p>
    <p class="lead" style="margin-top:1rem">When Josh was in college, he started working alongside Dawn on rentals &mdash; and fell in love with it. After earning his degree from DePaul, he joined her full time. Today all three of Dawn&rsquo;s sons still live in Prospect Heights. This isn&rsquo;t a job for them. It&rsquo;s the community they grew up in and chose to stay in &mdash; and they want to help you find yours.</p>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">How we work</p>
      <h2 class="h2">The things our clients keep mentioning.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>7 days a week, day or night</h3><p>You get our personal cell numbers. Deals don&rsquo;t happen 9-to-5 and neither do we.</p></div>
      <div class="c-card"><h3>We fight for every dollar</h3><p>Pricing strategy and hard negotiation are where money is actually made or lost. Ask our sellers about &ldquo;sold in 2 days.&rdquo;</p></div>
      <div class="c-card"><h3>Marketing that reaches millions</h3><p>One of Josh&rsquo;s video walkthroughs topped 10 million views. Your listing gets that same energy.</p></div>
      <div class="c-card"><h3>We actually grew up here</h3><p>Not &ldquo;serving the area&rdquo; &mdash; living in it. We know which blocks flood, which streets are quiet, and what a home is really worth.</p></div>
      <div class="c-card"><h3>Verifiable track record</h3><p><a href="/sold">{{ \App\Support\TeamStats::soldTotal() }} closed sales</a>, mapped, and <a href="/reviews">62+ public Google reviews</a>. Nothing to take on faith.</p></div>
      <div class="c-card"><h3>Two full-time agents</h3><p>Every client works with both of us. Two sets of eyes, two networks, one team.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap pg-cta">
    <h2 class="h2">Let&rsquo;s talk about your next move.</h2>
    <p class="lead" style="margin:.9rem auto 0;max-width:52ch">Buying, selling, or just wondering what your home is worth &mdash; start with a free, no-pressure conversation with the two of us.</p>
    <div class="btns">
      <a class="btn btn--primary" href="/contact">Contact Dawn &amp; Josh</a>
      <a class="btn btn--ghost" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    </div>
    <p style="margin-top:1.2rem;font-size:.9rem;color:var(--slate)">See <a href="/reviews" class="link-arrow" style="display:inline">what clients say</a> &middot; Browse <a href="/sold" class="link-arrow" style="display:inline">homes we&rsquo;ve sold</a> &middot; New to the area? <a href="/moving-to-northwest-suburbs" class="link-arrow" style="display:inline">Start here</a>.</p>
  </div>
</section>
</x-site.layout>
