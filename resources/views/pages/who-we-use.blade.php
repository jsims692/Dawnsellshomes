<x-site.layout :page="$page" :head="$head">

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Who We Use</p>
    <p class="eyebrow">Our trusted contacts</p>
    <h1>The people we actually use &mdash; after {{ \App\Support\TeamStats::soldTotal() }} closings.</h1>
    <p class="lead">Twenty-five years of transactions builds a phone book money can't buy. When clients ask &ldquo;do you know a good&hellip;&rdquo; &mdash; this is who we call. Nobody pays to be on this list, and nobody pays us a dime for being on it. These are simply the people we use ourselves.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">The transaction team</p>
      <h2 class="h2">Deals live and die on these three phone calls.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>Real estate attorneys</h3><p><strong>Shawn Good</strong> and <strong>Marie Clear</strong> of The Good Law Group &mdash; our go-to counsel, on either side of the table. In Illinois your attorney matters more than most buyers realize; these two have saved deals we thought were dead.</p></div>
      <div class="c-card"><h3>Lenders</h3><p><strong>Domenic Noia</strong> at Neighborhood Loans and <strong>Jim Spallone</strong> at Guaranteed Rate. Pre-approvals that mean something, and closings that actually close on the date on the contract.</p></div>
      <div class="c-card"><h3>Home inspectors</h3><p><strong>Javier Cangiano</strong> &mdash; thorough, straight-talking, and worth following wherever he's working &mdash; and <strong>The BrickKicker</strong> when scheduling is tight. An honest inspection protects everyone.</p></div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">For the house itself</p>
      <h2 class="h2">The numbers we hand out most.</h2>
    </div>
    <div class="cards3">
      <div class="c-card"><h3>Plumbing &amp; sewer</h3><p><strong>A Right Price Plumbing &amp; Sewer Company Inc.</strong> &mdash; our call in the Fox Lake and Chain O'Lakes area, where plumbing surprises come with the territory.</p></div>
      <div class="c-card"><h3>Well service</h3><p><strong>Snelton</strong> &mdash; if you're buying on the Chain or anywhere on well water, save yourself the learning curve and use who we use.</p></div>
      <div class="c-card"><h3>Hauling &amp; cleanouts</h3><p><strong>Torvik Hauling</strong> &mdash; estate cleanouts, pre-listing purges, and moving-day surprises that need to disappear by the weekend.</p></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap pg-cta">
    <h2 class="h2">Need someone who isn&rsquo;t on this list?</h2>
    <p class="lead" style="margin:.9rem auto 0;max-width:52ch">Roofer, HVAC, electrician, stager, painter &mdash; ask us. If we don&rsquo;t have a name from {{ \App\Support\TeamStats::soldTotal() }} closings, we know someone who does. And mention us when you call: it tends to get the good calendar slots.</p>
    <div class="btns">
      <a class="btn btn--primary" href="/contact">Ask Dawn &amp; Josh for a name</a>
      <a class="btn btn--ghost" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    </div>
  </div>
</section>
</x-site.layout>
