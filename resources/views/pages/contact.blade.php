<x-site.layout :page="$page" :head="$head">
<style>
  .map-frame { border: 0; width: 100%; height: 420px; border-radius: var(--radius); box-shadow: var(--shadow-sm); display: block; background: var(--mist); }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Contact</p>
    <p class="eyebrow">Contact</p>
    <h1>Let&rsquo;s talk about <em>your move.</em></h1>
    <p class="lead">Buying, selling, renting out, or just wondering what your home is worth &mdash; start with a free, no-pressure conversation. You&rsquo;ll hear back from Dawn or Josh personally, usually within 24 hours.</p>
  </div>
</section>

<section class="section" id="contact">
  <div class="wrap">
    <div class="contact-grid">
      <div>
        <p class="eyebrow">Talk to us directly</p>
        <h2 class="h2">Two agents. Two personal cells.</h2>
        <div class="direct">
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h3l1.5 4L7.5 9.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg></div>
            <div><strong>Dawn Simmons</strong><a href="tel:8477381884">(847) 738-1884</a><br><span>REALTOR&reg; &middot; Broker &middot; RE/MAX Hall of Fame</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10a1.5 1.5 0 0 1 1.5 1.5v13A1.5 1.5 0 0 1 17 20H7a1.5 1.5 0 0 1-1.5-1.5v-13A1.5 1.5 0 0 1 7 4Z"></path><path d="M10.5 17h3"></path></svg></div>
            <div><strong>Josh Simmons</strong><a href="tel:2246284013">(224) 628-4013</a> &middot; <a href="sms:2246284013">text</a><br><span>REALTOR&reg; &middot; Broker Associate</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect><path d="m4.5 7 7.5 5.5L19.5 7"></path></svg></div>
            <div><strong>Email</strong><a href="mailto:dawn@dawnsellshomes.com">dawn@dawnsellshomes.com</a><br><span>or <a href="mailto:josh@dawnsellshomes.com">josh@dawnsellshomes.com</a></span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-7-5.5-7-11a7 7 0 0 1 14 0c0 5.5-7 11-7 11Z"></path><circle cx="12" cy="10" r="2.5"></circle></svg></div>
            <div><strong>RE/MAX Suburban</strong><a href="https://maps.google.com/maps?q=330%20E%20Northwest%20Hwy%2C%20Mount%20Prospect%2C%20IL%2060056" rel="noopener">330 E Northwest Hwy, Mount Prospect, IL 60056</a><br><span>Our home office &mdash; 11 offices across the suburbs</span></div>
          </div>
        </div>
        <p class="direct-note">Those are our personal cells &mdash; call or text any day, 7 days a week. Deals don&rsquo;t happen 9-to-5 and neither do we.</p>
      </div>
      <x-site.contact-form />
    </div>
  </div>
</section>

<section class="section--tight section--mist">
  <div class="wrap">
    <div class="sec-head" style="margin-bottom:1.6rem">
      <p class="eyebrow">Find us</p>
      <h2 class="h2">Our Mount Prospect office.</h2>
    </div>
    <iframe class="map-frame" title="Map to RE/MAX Suburban, 330 E Northwest Hwy, Mount Prospect IL"
            src="https://www.google.com/maps?q=RE%2FMAX%20Suburban%2C%20330%20E%20Northwest%20Hwy%2C%20Mount%20Prospect%2C%20IL%2060056&output=embed"
            loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
  </div>
</section>
</x-site.layout>
