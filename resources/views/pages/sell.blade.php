<x-site.layout :page="$page" :head="$head">
<style>
  /* Widget panel (.sl-val): themes the shared home-value widget for the ink hero. */
  .sl-val { --gold: #E8B93B; margin-top: 1.6rem; max-width: 560px; }
  .sl-val .value-widget > p:first-child { font-size: .8rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--red-soft); margin-bottom: .6rem; }
  .sl-val .value-row { display: flex; gap: .6rem; }
  .sl-val .value-row input { flex: 1; min-width: 0; border: 0; border-radius: 12px; padding: .95rem 1.05rem; font-family: 'Archivo', system-ui, sans-serif; font-size: .95rem; color: var(--ink); }
  .sl-val .value-row button { background: var(--red); color: #fff; border: 0; border-radius: 999px; padding: .9rem 1.45rem; font-weight: 600; font-size: .95rem; cursor: pointer; white-space: nowrap; }
  .sl-val .value-row button:hover { background: var(--red-deep); }
  @media (max-width: 560px) { .sl-val .value-row { flex-direction: column; } .sl-val .value-row button { width: 100%; } }
  .contact-narrow { max-width: 760px; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Sell Your Home</p>
    <p class="eyebrow">Selling in the Northwest Suburbs</p>
    <h1>Sell your home for <em>every dollar</em> it&rsquo;s worth.</h1>
    <p class="lead">Pricing strategy and hard negotiation are where money is actually made or lost in a sale. You get two full-time agents &mdash; Dawn&rsquo;s 26 years and RE/MAX Hall of Fame record, Josh&rsquo;s marketing and hustle &mdash; on every single deal.</p>
    <div class="sl-val">
      <x-home.value-widget />
    </div>
    <ul class="trust" style="color:rgba(255,255,255,.75)">
      <li>RE/MAX Hall of Fame</li>
      <li>{{ \App\Support\TeamStats::soldTotal() }} homes sold</li>
      <li>4.9&#9733; &middot; 62+ Google reviews</li>
    </ul>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">How it works</p>
      <h2 class="h2">From first walkthrough to the wire hitting your account.</h2>
      <p class="lead">No mystery, no hand-offs to an assistant. Here&rsquo;s exactly what working with us looks like.</p>
    </div>
    <div class="steps">
      <div class="step">
        <div><h3>Walkthrough &amp; pricing strategy</h3><p>We tour your home, pull real comps from our own {{ \App\Support\TeamStats::soldTotal() }} closed sales, and land on a pricing strategy together. You get an honest number &mdash; not the inflated one that wins the listing and costs you months.</p></div>
      </div>
      <div class="step">
        <div><h3>Prep, staging &amp; photography</h3><p>We tell you which fixes actually pay back (and which to skip), then make the house shine: professional photos, and video that shows the home the way buyers want to see it.</p></div>
      </div>
      <div class="step">
        <div><h3>A marketing blitz that reaches millions</h3><p>MLS, Zillow, Realtor.com &mdash; and video marketing with real reach. One of Josh&rsquo;s home tours topped 10 million views. Your listing gets that same energy.</p></div>
      </div>
      <div class="step">
        <div><h3>Showings &amp; negotiation</h3><p>We fight for every dollar. Ask our sellers about &ldquo;sold in 2 days&rdquo; &mdash; pricing right and negotiating hard are the two things that actually move money in a sale.</p></div>
      </div>
      <div class="step">
        <div><h3>Contract to closing</h3><p>Inspections, appraisal, attorney review, tax prorations &mdash; we coordinate all of it and keep you ahead of every deadline until the deal is closed and funded.</p></div>
      </div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Why sellers choose us</p>
      <h2 class="h2">Two agents. One family. Zero hand-offs.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>7 days a week, day or night</h3><p>You get our personal cell numbers. Deals don&rsquo;t happen 9-to-5 and neither do we.</p></div>
      <div class="c-card"><h3>We actually grew up here</h3><p>Not &ldquo;serving the area&rdquo; &mdash; living in it. We know which blocks flood, which streets are quiet, and what a home is really worth.</p></div>
      <div class="c-card"><h3>A verifiable track record</h3><p><a href="/sold">{{ \App\Support\TeamStats::soldTotal() }} closed sales</a>, mapped, and <a href="/reviews">62+ public Google reviews</a>. Nothing to take on faith.</p></div>
    </div>

    <div class="wdd">
      <h3 class="wdd-h">And just as important &mdash; what we <em>don&rsquo;t</em> do.</h3>
      <ul class="wdd-list">
        <li><span aria-hidden="true">&#10007;</span> We don&rsquo;t overprice your house to win the listing &mdash; that inflated number costs you months.</li>
        <li><span aria-hidden="true">&#10007;</span> We don&rsquo;t hand you off to an assistant &mdash; you get Dawn and Josh, first walkthrough to wire transfer.</li>
        <li><span aria-hidden="true">&#10007;</span> We don&rsquo;t disappear once the sign is in the yard.</li>
        <li><span aria-hidden="true">&#10007;</span> We don&rsquo;t just put it on the MLS and hope &mdash; marketing with real reach is the whole job.</li>
      </ul>
    </div>
    <style>
      .wdd { max-width: 720px; margin: 2.6rem auto 0; border-top: 1px solid var(--line); padding-top: 1.9rem; }
      .wdd-h { font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.35rem; margin: 0 0 1rem; }
      .wdd-list { list-style: none; margin: 0; padding: 0; display: grid; gap: .7rem; }
      .wdd-list li { font-size: .98rem; line-height: 1.65; color: var(--slate); display: flex; gap: .7rem; }
      .wdd-list li span { color: var(--red); font-weight: 800; flex: none; }
    </style>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Seller tools</p>
      <h2 class="h2">Run your own numbers first.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>Seller net sheet</h3><p>The sale price is the headline &mdash; the wire to your bank is the real story. Estimate your true walk-away number, Illinois costs included. <a href="/seller-net-sheet">Open the net sheet &rarr;</a></p></div>
      <div class="c-card"><h3>Homes we&rsquo;ve sold</h3><p>Every pin is a family we helped. Browse {{ \App\Support\TeamStats::mappedSales() }} closed sales on an interactive map and see what we&rsquo;ve done on your street. <a href="/sold">See the map &rarr;</a></p></div>
      <div class="c-card"><h3>Free home valuation</h3><p>Enter your address above for an instant neighborhood snapshot from our own sales &mdash; then we&rsquo;ll follow up with a real number for your specific home, usually within 24 hours.</p></div>
    </div>
  </div>
</section>

<section class="section section--ink">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">From our sellers</p>
      <h2 class="h2">What clients say when it&rsquo;s done.</h2>
    </div>
    <div class="rev-grid">
      @foreach(array_slice($reviews, 0, 3) as [$author, $role, $text])
      <div class="rev-card">
        <div class="rev-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p class="rev-quote">&ldquo;{{ $text }}&rdquo;</p>
        <div class="rev-name">{{ $author }}</div>
        <div class="rev-role">{{ $role }} &middot; Google Review</div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Selling FAQ</p>
      <h2 class="h2">The questions every seller asks.</h2>
    </div>
    <div class="faq">
      <details>
        <summary>What is my home actually worth?</summary>
        <p>Start with the address lookup above &mdash; it shows real closed sales from our own record near you. Then we&rsquo;ll do it properly: a walkthrough, real comps, and a no-obligation valuation for your specific home, usually within 24 hours.</p>
      </details>
      <details>
        <summary>How fast will our home sell?</summary>
        <p>It depends on pricing, condition, and your street&rsquo;s market &mdash; and we&rsquo;ll give you an honest read on all three at the walkthrough. Priced right, homes here move fast; some of our listings have sold in 2 days with multiple offers.</p>
      </details>
      <details>
        <summary>Do we need to renovate before listing?</summary>
        <p>Usually not. Most big renovations don&rsquo;t pay back at sale. We&rsquo;ll walk the house with you and point out the handful of fixes that actually move the price &mdash; and tell you what to skip.</p>
      </details>
      <details>
        <summary>What will we actually walk away with?</summary>
        <p>Between commissions, tax prorations, transfer stamps, title, and attorney fees, Illinois closings have real costs most sellers don&rsquo;t see coming. Our <a href="/seller-net-sheet">seller net sheet</a> estimates your true net &mdash; calibrated against our real closing statements.</p>
      </details>
      <details>
        <summary>Who will we actually be working with?</summary>
        <p>Dawn and Josh. Both of us, full time, on every deal &mdash; with our personal cell numbers. No teams of assistants, no hand-offs, no voicemail black holes.</p>
      </details>
    </div>
  </div>
</section>

<section class="section--tight section--mist" id="search" style="padding-top:3rem;padding-bottom:3rem">
  <div class="wrap pg-cta">
    <p class="eyebrow" style="justify-content:center">Buying too?</p>
    <h2 class="h2">Selling and buying at the same time?</h2>
    <p class="lead" style="margin:.8rem auto 0;max-width:52ch">We coordinate both sides so you&rsquo;re never stuck between homes. Browse what&rsquo;s on the market, or read how we help buyers win.</p>
    <div class="btns">
      <a class="btn btn--primary" href="/#search">Browse available homes</a>
      <a class="btn btn--ghost" href="/buy">How we help buyers</a>
    </div>
  </div>
</section>

<section class="section" id="contact">
  <div class="wrap contact-narrow" style="margin-inline:auto">
    <div class="sec-head" style="text-align:center;max-width:none">
      <p class="eyebrow" style="justify-content:center">Get started</p>
      <h2 class="h2">Get your free, no-pressure valuation.</h2>
      <p class="lead" style="margin:.9rem auto 0">Tell us a little about your home and your timeline. We&rsquo;ll come back with a real number and a real plan &mdash; usually within 24 hours.</p>
    </div>
    <x-site.contact-form />
  </div>
</section>
</x-site.layout>
