<x-site.layout :page="$page" :head="$head">
<style>
  .sec-label { font-family:Arial,sans-serif; font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:var(--red); margin-bottom:12px; }
  .rv-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-top:28px; }
  .rv-card { background:#fff; border:1px solid #e0e4ed; border-radius:10px; padding:26px 26px 22px; border-top:4px solid var(--gold); display:flex; flex-direction:column; }
  .rv-stars { color:#e6b400; font-size:18px; letter-spacing:2px; margin-bottom:10px; }
  .rv-text { font-size:15.5px; line-height:1.7; color:#333; margin:0 0 16px; flex:1; }
  .rv-author { font-family:Arial,sans-serif; font-weight:700; color:var(--navy); font-size:14px; }
  .rv-role { font-family:Arial,sans-serif; font-size:12px; color:#888; margin-top:2px; }
  .rv-summary { display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:28px; margin:8px auto 0; }
  .rv-big { font-family:Arial,sans-serif; font-size:56px; font-weight:900; color:var(--gold); line-height:1; }
  .rv-big small { display:block; font-size:12px; font-weight:700; letter-spacing:1.5px; text-transform:uppercase; color:rgba(255,255,255,.7); margin-top:6px; }
</style>

<div class="hero">
  <div class="breadcrumb"><a href="/">Home</a> &rsaquo; Client Reviews</div>
  <h1>Don't Just Take <em>Our</em> Word for It.</h1>
  <p>Real reviews from real buyers and sellers across Prospect Heights, Mount Prospect, Arlington Heights and the northwest suburbs.</p>
  <div class="rv-summary">
    <div class="rv-big">4.9 &#9733;<small>Google rating</small></div>
    <div class="rv-big">62+<small>Google reviews</small></div>
    <div class="rv-big">550+<small>Homes sold</small></div>
  </div>
</div>

<section>
  <div class="inner">
    <h2>What Clients Say About Working With Dawn &amp; Josh</h2>
    <p>Every review below is a real, public Google review from a client we helped buy or sell a home. We didn't cherry-pick the glowing ones &mdash; this is what people consistently say: fast, honest, responsive, and there when it matters.</p>
    <div class="rv-grid">
      @foreach($reviews as [$author, $role, $text])
      <div class="rv-card">
        <div class="rv-stars" aria-label="5 out of 5 stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
        <p class="rv-text">&ldquo;{{ $text }}&rdquo;</p>
        <div class="rv-author">{{ $author }}</div>
        <div class="rv-role">{{ $role }} &middot; Google Review</div>
      </div>
      @endforeach
    </div>
    <p style="margin-top:28px;font-family:Arial,sans-serif;font-size:14px;color:#666;">Want to read all 62+? <a href="https://www.google.com/search?q=Dawn+Simmons+Team+RE%2FMAX+Suburban+reviews" target="_blank" rel="noopener">See our reviews on Google &rarr;</a></p>
  </div>
</section>

<section class="alt">
  <div class="inner">
    <h2>Why the Reviews Keep Saying the Same Things</h2>
    <div class="fp-grid">
      <div class="fp-card"><h3>&ldquo;Responsive&rdquo; isn't marketing &mdash; it's two cell phones</h3><p>Dawn and Josh give every client their personal cells. Call, text, 7 days a week. That's why &ldquo;always quick to answer&rdquo; shows up in review after review.</p></div>
      <div class="fp-card"><h3>Sold in 2 days. Won the bidding war.</h3><p>Pricing right and negotiating hard are the two things that actually move money in a sale. 26+ years and 550+ transactions of practice.</p></div>
      <div class="fp-card"><h3>&ldquo;He learned well from Mom.&rdquo;</h3><p>Two full-time agents from one family, working every deal together. You get Dawn's experience and Josh's hustle &mdash; not a hand-off to an assistant.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="inner" style="text-align:center;">
    <h2>Ready to Be Our Next Review?</h2>
    <p style="max-width:600px;margin:0 auto 20px;">Whether you're buying your first home or selling a longtime family house, we'd love to earn your five stars. Start with a free, no-pressure conversation.</p>
    <a class="search-btn" href="/#contact">Contact Dawn &amp; Josh</a>
    <a class="outline-btn" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    <p style="margin-top:18px;font-size:14px;">Curious who we are? <a href="/team">Meet the mother-and-son team</a> &middot; See <a href="/sold">550+ homes we've sold</a>.</p>
  </div>
</section>


</x-site.layout>
