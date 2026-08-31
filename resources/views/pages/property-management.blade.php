<x-site.layout :page="$page" :head="$head">
<style>
  .pm-check { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: .55rem 1.6rem; margin-top: 1.8rem; }
  .pm-check li { display: flex; gap: .6rem; align-items: baseline; font-size: .95rem; color: var(--slate); }
  .pm-check li::before { content: "✓"; color: var(--red); font-weight: 700; flex: none; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Property Management</p>
    <p class="eyebrow">Property Management &middot; Northwest Suburbs IL</p>
    <h1>Hands-off rental management. <em>We handle everything.</em></h1>
    <p class="lead">From finding great tenants to collecting rent and coordinating repairs &mdash; the Dawn Simmons Team manages your rental property so you don&rsquo;t have to.</p>
    <div class="hero-ctas" style="margin-top:1.6rem">
      <a class="btn btn--primary" href="#pm-contact">Get a free consultation</a>
      <a class="btn btn--ghost" href="tel:8477381884">Call (847) 738-1884</a>
    </div>
  </div>
</section>

<section class="stats section--ink">
  <div class="wrap">
    <div class="stats-grid">
      <div><div class="stat-num">26+</div><div class="stat-label">Years in NW suburbs</div></div>
      <div><div class="stat-num">{{ \App\Support\TeamStats::soldTotal() }}</div><div class="stat-label">Properties sold</div></div>
      <div><div class="stat-num">3</div><div class="stat-label">Airbnbs personally operated</div></div>
      <div><div class="stat-num">Flat rate</div><div class="stat-label">Transparent pricing</div></div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">What we offer</p>
      <h2 class="h2">Two ways we can help you.</h2>
      <p class="lead">Whether you own a single-family home, condo, or short-term rental, we have a management solution that fits.</p>
    </div>
    <div class="cards3" style="grid-template-columns:repeat(auto-fit,minmax(320px,1fr))">
      <div class="c-card">
        <h3>Long-term rental management</h3>
        <p>We handle the full lifecycle of your rental &mdash; advertising, showing, screening applicants, drafting leases, collecting rent, coordinating maintenance, and managing tenant relationships. You get a monthly statement and a check. That&rsquo;s it.</p>
      </div>
      <div class="c-card">
        <h3>Short-term / Airbnb management</h3>
        <p>Josh personally operates Airbnb properties in the northwest suburbs, so he knows exactly what it takes to run them profitably. We can manage listings, guest communication, pricing, turnover coordination, and more for your short-term rental.</p>
      </div>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">What&rsquo;s included</p>
      <h2 class="h2">Full-service management &mdash; no surprises.</h2>
      <p class="lead">Every property we manage gets the full package. Here&rsquo;s what that looks like:</p>
    </div>
    <ul class="pm-check">
      <li>Rental market analysis &amp; pricing</li>
      <li>Professional listing on Zillow, Realtor.com, MLS &amp; more</li>
      <li>Tenant screening (credit, background, income)</li>
      <li>Lease drafting &amp; signing</li>
      <li>Rent collection &amp; direct deposit</li>
      <li>Maintenance coordination</li>
      <li>Move-in / move-out inspections</li>
      <li>Monthly owner statements</li>
      <li>Lease renewals &amp; rent increases</li>
      <li>Eviction coordination if needed</li>
    </ul>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Pricing</p>
      <h2 class="h2">Simple, flat-rate pricing.</h2>
    </div>
    <div class="price-card">
      <div class="num">$100<small>per month &middot; starting at</small></div>
      <p>Per property &middot; no percentage of rent &middot; no surprise fees. Most property managers charge 8&ndash;10% of monthly rent &mdash; on a $1,800/month unit that&rsquo;s $180/month or more. We start at a simple flat monthly rate, so your cost stays predictable no matter what your rent is.</p>
      <a class="btn btn--light" href="#pm-contact">Get started today</a>
    </div>
  </div>
</section>

<section class="section section--mist">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Why choose us</p>
      <h2 class="h2">A property manager who also knows real estate.</h2>
      <p class="lead">Most property managers are operators, not agents. Josh and Dawn are both &mdash; which means when it&rsquo;s time to sell your investment property, refinance, or upgrade to a bigger portfolio, you have an expert already in your corner.</p>
    </div>
    <div class="grid-4">
      <div class="why-card">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></div>
        <h3>Local expertise</h3>
        <p>We know the northwest suburbs inside and out &mdash; what rents for what, what tenants are looking for, and which maintenance vendors are reliable.</p>
      </div>
      <div class="why-card">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 4.5 19.5 9.5 8.5 20.5H3.5v-5Z"></path><path d="m13 6 5 5"></path></svg></div>
        <h3>Owner-operated Airbnbs</h3>
        <p>Josh personally runs short-term rentals. That&rsquo;s real experience, not theory &mdash; your Airbnb is in hands that have dealt with every guest scenario.</p>
      </div>
      <div class="why-card">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19V10M10 19V5M16 19v-8M20 19H4"></path></svg></div>
        <h3>Agent-level market knowledge</h3>
        <p>With {{ \App\Support\TeamStats::soldTotal() }} transactions, we can price your rental accurately and spot when it&rsquo;s time to sell, hold, or refinance.</p>
      </div>
      <div class="why-card">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 12a8 8 0 1 0-3.5 6.6L20 20l-1-3.6A7.9 7.9 0 0 0 20 12Z"></path></svg></div>
        <h3>Responsive communication</h3>
        <p>You&rsquo;ll always know what&rsquo;s happening with your property. No voicemail black holes, no waiting weeks for a callback.</p>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Service area</p>
      <h2 class="h2">We manage properties across the northwest suburbs.</h2>
      <p class="lead">Our primary management territory covers Prospect Heights, Mount Prospect, Arlington Heights, Rolling Meadows, Buffalo Grove, Palatine, Des Plaines, Wheeling, Elk Grove Village, Schaumburg, Hoffman Estates, Inverness, South Barrington, and surrounding communities. Not sure if we cover your area? Just ask &mdash; we&rsquo;re happy to discuss.</p>
    </div>
    <div class="pm">
      <div>
        <h3>Join our monthly investor meetup</h3>
        <p>Own rentals (or want to)? The Chicago Suburban Real Estate Group meets monthly &mdash; local investors, honest numbers, no sales pitch.</p>
      </div>
      <div class="pm-actions">
        <a class="btn btn--primary" href="/chicago-suburban-real-estate-group">About the meetup</a>
      </div>
    </div>
  </div>
</section>

<section class="section section--ink" id="pm-contact">
  <div class="wrap" style="max-width:820px">
    <div class="sec-head" style="text-align:center;max-width:none">
      <p class="eyebrow" style="justify-content:center">Get in touch</p>
      <h2 class="h2">Ready to stop self-managing?</h2>
      <p class="lead" style="margin:.9rem auto 0">Tell us about your property and we&rsquo;ll reach out within 24 hours to discuss your management options.</p>
    </div>
    {{-- PM lead form: POSTs to "/" with form-name=property-management; the extra
         property_address + rental_type fields are folded into the lead message
         server-side. Field names are part of the lead-pipeline contract. --}}
    <div class="form-card" id="pmCard" x-data="pmForm()" :class="{ done: sent }">
      <form method="POST" action="/" @submit.prevent="send()">
        <input type="hidden" name="form-name" value="property-management">
        <div class="form-grid">
          <div class="field"><label for="pm-name">Your name *</label><input id="pm-name" name="name" type="text" required x-model="f.name"></div>
          <div class="field"><label for="pm-phone">Phone number *</label><input id="pm-phone" name="phone" type="tel" required x-model="f.phone"></div>
          <div class="field full"><label for="pm-email">Email address *</label><input id="pm-email" name="email" type="email" required x-model="f.email"></div>
          <div class="field full"><label for="pm-addr">Property address</label><input id="pm-addr" name="property_address" type="text" placeholder="Address of the property to be managed" x-model="f.property_address"></div>
          <div class="field full"><label for="pm-type">Type of rental</label><select id="pm-type" name="rental_type" x-model="f.rental_type"><option value="">Select one&hellip;</option><option value="long-term">Long-term rental (12+ month lease)</option><option value="short-term">Short-term / Airbnb</option><option value="both">Both / not sure yet</option></select></div>
          <div class="field full"><label for="pm-msg">Anything else we should know?</label><textarea id="pm-msg" name="message" rows="4" x-model="f.message" placeholder="Current rent, tenant situation, timeline&hellip;"></textarea></div>
        </div>
        <div style="margin-top:1.15rem">
          <p style="position:absolute;left:-9999px" aria-hidden="true"><label>Don't fill this out: <input name="bot-field" x-model="f.bot" tabindex="-1"></label></p>
          <input type="hidden" name="form_ts" :value="f.ts">
          <button class="btn btn--primary" type="submit" x-text="busy ? 'Sending…' : 'Request my free consultation'" :disabled="busy">Request my free consultation</button>
        </div>
        <p class="form-note">By submitting this form you agree to be contacted by The Dawn Simmons Team. We never share your information.</p>
      </form>
      <div class="success" role="status">
        <div class="mark" aria-hidden="true"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></div>
        <h3>Request received!</h3>
        <p>Thanks &mdash; Dawn or Josh will reach out within 24 hours to talk through your property.</p>
      </div>
    </div>
  </div>
</section>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('pmForm', () => ({
    f: { name:'', phone:'', email:'', property_address:'', rental_type:'', message:'', bot:'', ts: Date.now() }, busy:false, sent:false,
    async send() {
      this.busy = true;
      const d = new URLSearchParams({ 'form-name':'property-management', name:this.f.name, phone:this.f.phone, email:this.f.email, property_address:this.f.property_address, rental_type:this.f.rental_type, message:this.f.message, 'bot-field':this.f.bot, form_ts:this.f.ts });
      try { await fetch('/', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body:d.toString() }); } catch(e) {}
      this.busy = false; this.sent = true;
    },
  }));
});
</script>
</x-site.layout>
