<x-site.layout :page="$page" :head="$head">
<style>
  .contact-narrow { max-width: 760px; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Buy a Home</p>
    <p class="eyebrow">Buying in the Northwest Suburbs</p>
    <h1>Find the right home &mdash; <em>and actually win it.</em></h1>
    <p class="lead">In a market where good homes get multiple offers, you need more than a door-opener. You get two full-time agents who grew up in these neighborhoods, know what every block is really worth, and negotiate like it&rsquo;s their own money.</p>
    <div class="hero-ctas" style="margin-top:1.6rem">
      <a class="btn btn--primary" href="/#search">Browse available homes</a>
      <a class="btn btn--ghost" href="#contact">Talk to Dawn &amp; Josh</a>
    </div>
    <ul class="trust" style="color:rgba(255,255,255,.75)">
      <li>550+ closed sales</li>
      <li>Off-market &amp; PLN access</li>
      <li>4.9&#9733; &middot; 62+ Google reviews</li>
    </ul>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">How it works</p>
      <h2 class="h2">From first call to keys in hand.</h2>
    </div>
    <div class="steps">
      <div class="step">
        <div><h3>Strategy call</h3><p>Budget, towns, schools, commute, timeline &mdash; we start with what you actually need, then tell you honestly where it exists (and where it doesn&rsquo;t) in today&rsquo;s market.</p></div>
      </div>
      <div class="step">
        <div><h3>Get pre-approved</h3><p>Sellers here don&rsquo;t take offers without one. We&rsquo;ll connect you with lenders our clients actually close with, and our <a href="/mortgage-calculator">mortgage calculator</a> shows your real monthly payment &mdash; northwest-suburbs property taxes included.</p></div>
      </div>
      <div class="step">
        <div><h3>Tour smart &mdash; including off-market homes</h3><p>We flag the flood-prone blocks, the loud corners, and the overpriced flips before you fall in love. Through our network and the Private Listing Network, you&rsquo;ll also see <a href="/off-market-homes">homes that never hit Zillow</a>.</p></div>
      </div>
      <div class="step">
        <div><h3>Win the offer</h3><p>Price, terms, timing, escalation &mdash; there&rsquo;s more than one way to win a bidding war, and overpaying is only the worst one. This is where 550+ closed deals of negotiating experience earns its keep.</p></div>
      </div>
      <div class="step">
        <div><h3>Close with confidence</h3><p>Inspection, attorney review, appraisal, final walkthrough &mdash; we manage every deadline and every renegotiation until the keys are in your hand.</p></div>
      </div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Why buyers choose us</p>
      <h2 class="h2">Local isn&rsquo;t a marketing word for us.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>We grew up on these streets</h3><p>Dawn moved here in 1988; Josh was raised here and never left. When we say a block is quiet or a basement is a flood risk, it&rsquo;s from living here &mdash; not from a report.</p></div>
      <div class="c-card"><h3>You see more than Zillow shows</h3><p>Off-market and Private Listing Network homes, plus listings our network surfaces before they go live. <a href="/off-market-homes">Here&rsquo;s how that works</a>.</p></div>
      <div class="c-card"><h3>Two agents on every deal</h3><p>Dawn&rsquo;s 26 years of contract savvy plus Josh&rsquo;s energy and speed. Two sets of eyes on every listing, two networks working for you, one team.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Where we work</p>
      <h2 class="h2">Explore the towns we know by heart.</h2>
      <p class="lead">Every guide below was written by us &mdash; real sold data, real neighborhood knowledge, no filler.</p>
    </div>
    <div class="chip-group">
      <h4>Core northwest suburbs</h4>
      <div class="chips">
        <a href="/cities/prospect-heights">Prospect Heights</a>
        <a href="/cities/mount-prospect">Mount Prospect</a>
        <a href="/cities/arlington-heights">Arlington Heights</a>
        <a href="/cities/palatine">Palatine</a>
        <a href="/cities/wheeling">Wheeling</a>
        <a href="/cities/buffalo-grove">Buffalo Grove</a>
        <a href="/cities/des-plaines">Des Plaines</a>
        <a href="/cities/rolling-meadows">Rolling Meadows</a>
        <a href="/cities/elk-grove-village">Elk Grove Village</a>
        <a href="/cities/schaumburg">Schaumburg</a>
        <a href="/cities/hoffman-estates">Hoffman Estates</a>
      </div>
    </div>
    <div class="chip-group">
      <h4>Lake country &amp; the Chain O&rsquo;Lakes</h4>
      <div class="chips">
        <a href="/chain-o-lakes">Chain O&rsquo;Lakes guide</a>
        <a href="/cities/antioch">Antioch</a>
        <a href="/cities/fox-lake">Fox Lake</a>
      </div>
    </div>
    <div class="chip-group">
      <h4>Start here</h4>
      <div class="chips">
        <a href="/moving-to-northwest-suburbs">Moving to the northwest suburbs</a>
        <a href="/blog/best-northwest-suburbs-first-time-buyers">Best towns for first-time buyers</a>
        <a href="/#neighborhoods">All neighborhoods</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--ink">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">From our buyers</p>
      <h2 class="h2">What winning feels like.</h2>
    </div>
    <div class="rev-grid">
      @foreach(array_slice($reviews, 3, 3) as [$author, $role, $text])
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
      <p class="eyebrow">Buying FAQ</p>
      <h2 class="h2">The questions every buyer asks.</h2>
    </div>
    <div class="faq">
      <details>
        <summary>How much do I need for a down payment?</summary>
        <p>Less than most people think &mdash; conventional loans go as low as 3&ndash;5% down and FHA 3.5%, though a bigger down payment strengthens your offer. We&rsquo;ll connect you with a lender who lays out your real options before you start touring.</p>
      </details>
      <details>
        <summary>Do I need to be pre-approved before we look at homes?</summary>
        <p>To tour, no. To offer, yes &mdash; sellers here won&rsquo;t take you seriously without one, and in a multiple-offer weekend there&rsquo;s no time to get one after you find the house. It costs nothing and takes about a day.</p>
      </details>
      <details>
        <summary>What does a buyer&rsquo;s agent cost me?</summary>
        <p>Agent compensation is negotiated up front in your buyer agreement, and in many of our deals the seller ends up covering some or all of it. We&rsquo;ll walk you through exactly how it works before you sign anything.</p>
      </details>
      <details>
        <summary>How do I win in a multiple-offer situation?</summary>
        <p>Price is only one lever. Terms, timing, inspection strategy, and knowing what this seller actually cares about often beat a higher number. We&rsquo;ve been on both sides of hundreds of these &mdash; we know what wins.</p>
      </details>
      <details>
        <summary>Which towns should we even be looking at?</summary>
        <p>That&rsquo;s our favorite question. Start with the <a href="/moving-to-northwest-suburbs">moving guide</a>, browse the town guides above, and then let&rsquo;s talk &mdash; a 15-minute call about your budget and priorities usually narrows it to two or three towns.</p>
      </details>
    </div>
  </div>
</section>

<section class="section section--mist" id="contact">
  <div class="wrap contact-narrow" style="margin-inline:auto">
    <div class="sec-head" style="text-align:center;max-width:none">
      <p class="eyebrow" style="justify-content:center">Get started</p>
      <h2 class="h2">Tell us what you&rsquo;re looking for.</h2>
      <p class="lead" style="margin:.9rem auto 0">Towns, budget, must-haves &mdash; whatever you know so far. We&rsquo;ll come back with an honest read on the market and a plan, usually within 24 hours.</p>
    </div>
    <x-site.contact-form />
  </div>
</section>
</x-site.layout>
