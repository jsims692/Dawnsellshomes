<x-site.layout :page="$page" :head="$head">

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Client Reviews</p>
    <p class="eyebrow">Client reviews</p>
    <h1>Don&rsquo;t just take <em>our</em> word for it.</h1>
    <p class="lead">Real reviews from real buyers and sellers across Prospect Heights, Mount Prospect, Arlington Heights and the northwest suburbs.</p>
  </div>
</section>

<section class="stats section--ink">
  <div class="wrap">
    <div class="stats-grid" style="grid-template-columns:repeat(3,1fr)">
      <div><div class="stat-num">4.9&#9733;</div><div class="stat-label">Google rating</div></div>
      <div><div class="stat-num">62+</div><div class="stat-label">Google reviews</div></div>
      <div><div class="stat-num">{{ \App\Support\TeamStats::soldTotal() }}</div><div class="stat-label">Homes sold</div></div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">In their words</p>
      <h2 class="h2">What clients say about working with Dawn &amp; Josh.</h2>
      <p class="lead">Every review below is a real, public Google review from a client we helped buy or sell a home. We didn&rsquo;t cherry-pick the glowing ones &mdash; this is what people consistently say: fast, honest, responsive, and there when it matters.</p>
    </div>
    <div class="rev-grid">
      @foreach($reviews as [$author, $role, $text])
      <div class="rev-card">
        <div class="rev-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p class="rev-quote">&ldquo;{{ $text }}&rdquo;</p>
        <div class="rev-name">{{ $author }}</div>
        <div class="rev-role">{{ $role }} &middot; Google Review</div>
      </div>
      @endforeach
    </div>
    <p style="margin-top:1.8rem;font-size:.92rem;color:var(--slate)">Want to read all 62+? <a class="link-arrow" style="display:inline" href="https://www.google.com/search?q=Dawn+Simmons+Team+RE%2FMAX+Suburban+reviews" target="_blank" rel="noopener">See our reviews on Google &rarr;</a></p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">The pattern</p>
      <h2 class="h2">Why the reviews keep saying the same things.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>&ldquo;Responsive&rdquo; isn&rsquo;t marketing &mdash; it&rsquo;s two cell phones</h3><p>Dawn and Josh give every client their personal cells. Call, text, 7 days a week. That&rsquo;s why &ldquo;always quick to answer&rdquo; shows up in review after review.</p></div>
      <div class="c-card"><h3>Sold in 2 days. Won the bidding war.</h3><p>Pricing right and negotiating hard are the two things that actually move money in a sale. 26+ years and {{ \App\Support\TeamStats::soldTotal() }} transactions of practice.</p></div>
      <div class="c-card"><h3>&ldquo;He learned well from Mom.&rdquo;</h3><p>Two full-time agents from one family, working every deal together. You get Dawn&rsquo;s experience and Josh&rsquo;s hustle &mdash; not a hand-off to an assistant.</p></div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap pg-cta">
    <h2 class="h2">Ready to be our next review?</h2>
    <p class="lead" style="margin:.9rem auto 0;max-width:52ch">Whether you&rsquo;re buying your first home or selling a longtime family house, we&rsquo;d love to earn your five stars. Start with a free, no-pressure conversation.</p>
    <div class="btns">
      <a class="btn btn--primary" href="/contact">Contact Dawn &amp; Josh</a>
      <a class="btn btn--ghost" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    </div>
    <p style="margin-top:1.2rem;font-size:.9rem;color:var(--slate)">Curious who we are? <a class="link-arrow" style="display:inline" href="/team">Meet the mother-and-son team</a> &middot; See <a class="link-arrow" style="display:inline" href="/sold">{{ \App\Support\TeamStats::soldTotal() }} homes we&rsquo;ve sold</a>.</p>
  </div>
</section>
</x-site.layout>
