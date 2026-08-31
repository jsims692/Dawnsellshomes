{{-- Sitewide contact form (design v2 .form-card). Contract is sacred: urlencoded
     POST to "/" with form-name=contact + honeypot bot-field and the field names
     name,email,phone,interest,message — this IS the lead pipeline. The non-JS
     fallback posts natively; Alpine takes over when available. Ids (formCard,
     contactForm, f-goal, f-msg, f-name) are shared contracts with heroSearch()
     and the home-value widget CTA. --}}
<div class="form-card" id="formCard" x-data="contactForm()" :class="{ done: sent }">
  <form id="contactForm" method="POST" action="/" @submit.prevent="send()">
    <input type="hidden" name="form-name" value="contact">
    <div class="form-grid">
      <div class="field"><label for="f-name">Full name *</label><input id="f-name" name="name" type="text" required x-model="f.name"></div>
      <div class="field"><label for="f-email">Email address *</label><input id="f-email" name="email" type="email" required x-model="f.email"></div>
      <div class="field"><label for="f-phone">Phone number</label><input id="f-phone" name="phone" type="tel" x-model="f.phone"></div>
      <div class="field"><label for="f-goal">I am looking to</label><select id="f-goal" name="interest" x-model="f.interest"><option value="">Select one&hellip;</option><option value="buy">Buy a home</option><option value="sell">Sell my home</option><option value="both">Buy &amp; sell</option><option value="value">Get a home valuation</option><option value="invest">Invest</option><option value="rent">Rent</option><option value="other">Something else</option></select></div>
      <div class="field full"><label for="f-msg">Message</label><textarea id="f-msg" name="message" rows="4" x-model="f.message" placeholder="Tell us a little about your plans&hellip;"></textarea></div>
    </div>
    <div style="margin-top:1.15rem">
      <p style="position:absolute;left:-9999px" aria-hidden="true"><label>Don't fill this out: <input name="bot-field" x-model="f.bot" tabindex="-1"></label></p>
      <input type="hidden" name="form_ts" :value="f.ts">
      <button class="btn btn--primary" type="submit" x-text="busy ? 'Sending…' : 'Send message'" :disabled="busy">Send message</button>
    </div>
    <p class="form-note">By submitting this form you agree to be contacted by The Dawn Simmons Team. We never share your information.</p>
  </form>
  <div class="success" role="status">
    <div class="mark" aria-hidden="true"><svg viewBox="0 0 24 24" width="26" height="26" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12.5l4.5 4.5L19 7.5"></path></svg></div>
    <h3>Message received!</h3>
    <p>Thanks for reaching out. Dawn or Josh will be in touch within 24 hours.</p>
  </div>
</div>

@once
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('contactForm', () => ({
    // ts: set the instant Alpine builds this component (≈ when the form
    // rendered) — a real visitor takes seconds to fill this out; a bot that
    // replays the endpoint directly never sends form_ts at all, and one
    // that scripts a headless browser still submits within a second or two.
    f: { name:'', email:'', phone:'', interest:'', message:'', bot:'', ts: Date.now() }, busy:false, sent:false,
    async send() {
      this.busy = true;
      const d = new URLSearchParams({ 'form-name':'contact', name:this.f.name, email:this.f.email, phone:this.f.phone, interest:this.f.interest, message:this.f.message, 'bot-field':this.f.bot, form_ts:this.f.ts });
      try { await fetch('/', { method:'POST', headers:{'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'}, body:d.toString() }); } catch(e) {}
      this.busy = false; this.sent = true;
    },
  }));
});
// heroSearch(): shared valuation CTA — prefill the form and jump to it.
// Reads the address from whichever valuation input the page has.
function heroSearch() {
  var el = document.getElementById('valAddress') || document.getElementById('heroAddrInput');
  var a = el ? el.value.trim() : '';
  var root = document.getElementById('formCard');
  if (root && window.Alpine) {
    var c = Alpine.$data(root);
    c.f.interest = 'value';
    c.f.message = a ? "I'd like a free home valuation for: " + a : "I'd like a free home valuation.";
  }
  location.hash = '#contact';
  setTimeout(function () { var n = document.getElementById('f-name'); if (n) n.focus(); }, 400);
}
// ?val= and ?pln= prefills (same contract as the homepage)
(function () {
  var q = new URLSearchParams(location.search);
  var val = q.get('val'), pln = q.get('pln');
  if (val === null && pln === null) return;
  document.addEventListener('alpine:initialized', function () {
    var root = document.getElementById('formCard'); if (!root) return;
    var c = Alpine.$data(root);
    if (val !== null) { c.f.interest = 'value'; c.f.message = val ? "I'd like a free home valuation for my home in " + val + "." : "I'd like a free home valuation."; }
    if (pln !== null) { c.f.interest = 'buy'; c.f.message = "I'm looking to buy in " + pln + " and want to hear about Private Listing Network / off-market matches. Here's what I'm looking for: "; }
    location.hash = '#contact';
  });
})();
</script>
@endonce
