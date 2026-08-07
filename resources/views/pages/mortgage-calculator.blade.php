<x-site.layout :page="$page" :head="$head">

<div class="hero">
  <div class="breadcrumb"><a href="/">Home</a> &rsaquo; Mortgage Calculator</div>
  <h1>What Will Your Payment <em>Really</em> Be?</h1>
  <p>Most calculators show you principal and interest and call it a day. In the Northwest Suburbs, property taxes can be a third of your payment &mdash; so this one includes everything: taxes, insurance, PMI, and HOA.</p>
</div>

<section x-data="mortgageCalc">
  <div class="inner">
    <div class="calc-wrap">
      <div class="calc-card">
        <div class="field">
          <label for="price">Home Price</label>
          <input type="number" id="price" x-model.number="price" @input="syncFromPct" min="0" step="5000">
        </div>
        <div class="two-col">
          <div class="field">
            <label for="downPct">Down Payment %</label>
            <input type="number" id="downPct" x-model.number="downPct" @input="syncFromPct" min="0" max="100" step="0.5">
          </div>
          <div class="field">
            <label for="downAmt">Down Payment $</label>
            <input type="number" id="downAmt" x-model.number="downAmt" @input="syncFromAmt" min="0" step="1000">
          </div>
        </div>
        <div class="two-col">
          <div class="field">
            <label for="rate">Interest Rate %</label>
            <input type="number" id="rate" x-model.number="rate" min="0" max="20" step="0.125">
            <div class="hint">Edit to today's quote &mdash; rates move daily</div>
          </div>
          <div class="field">
            <label for="term">Loan Term (years)</label>
            <input type="number" id="term" x-model.number="term" min="5" max="40" step="5">
          </div>
        </div>
        <div class="field">
          <label for="taxRate">Property Tax Rate % (of home value, per year)</label>
          <input type="number" id="taxRate" x-model.number="taxRate" min="0" max="6" step="0.1">
          <div class="hint">Northwest Suburbs typically run 2&ndash;3%+. Ask us for the actual tax bill on any home &mdash; it varies a lot house to house.</div>
        </div>
        <div class="two-col">
          <div class="field">
            <label for="ins">Home Insurance ($/mo)</label>
            <input type="number" id="ins" x-model.number="ins" min="0" step="10">
          </div>
          <div class="field">
            <label for="hoa">HOA / Assessments ($/mo)</label>
            <input type="number" id="hoa" x-model.number="hoa" min="0" step="10">
            <div class="hint">Condos: this matters &mdash; a lot</div>
          </div>
        </div>
      </div>
      <div class="result-card">
        <div class="result-lbl">Estimated Monthly Payment</div>
        <div class="result-total" x-text="fmt(total) + '/mo'">$0</div>
        <div class="result-sub" x-text="`${fmt(loan)} loan • ${(downFrac * 100).toFixed(1)}% down • ${num(rate)}% • ${num(term, 30)} yrs`"></div>
        <div class="bar">
          <template x-for="p in parts" :key="p.color">
            <div :style="`width:${p.amt / total * 100}%;background:${p.color}`"></div>
          </template>
        </div>
        <div class="legend">
          <span><span class="dot" style="background:#C8A84B;"></span>P&amp;I</span>
          <span><span class="dot" style="background:#7FA3D8;"></span>Taxes</span>
          <span><span class="dot" style="background:#5BC49A;"></span>Insurance</span>
          <span><span class="dot" style="background:#E08A8A;"></span>PMI</span>
          <span><span class="dot" style="background:#B79ADB;"></span>HOA</span>
        </div>
        <div class="break-row"><span>Principal &amp; Interest</span><span class="amt" x-text="fmt(pi)">$0</span></div>
        <div class="break-row"><span>Property Taxes</span><span class="amt" x-text="fmt(tax)">$0</span></div>
        <div class="break-row"><span>Home Insurance</span><span class="amt" x-text="fmt(num(ins))">$0</span></div>
        <div class="break-row" x-show="pmi > 0"><span>PMI (under 20% down)</span><span class="amt" x-text="fmt(pmi)">$0</span></div>
        <div class="break-row" x-show="num(hoa) > 0"><span>HOA / Assessments</span><span class="amt" x-text="fmt(num(hoa))">$0</span></div>
      </div>
    </div>
    <p style="font-family:Arial,sans-serif;font-size:12px;color:#999;margin-top:18px;">Estimates only, for planning purposes &mdash; not a loan offer or quote. Your actual rate, taxes, insurance and PMI depend on your credit, the specific property, and your lender. We're Realtors, not lenders &mdash; but we'll happily connect you with lenders our clients have had good experiences with.</p>
  </div>
</section>

<section class="alt">
  <div class="inner">
    <h2>Three Things Buyers Get Wrong About Payments Here</h2>
    <div class="fp-grid">
      <div class="fp-card"><h3>The tax bill is the plot twist</h3><p>Two identical $400K houses in the same town can carry tax bills thousands of dollars apart. We pull the actual bill on every home before you offer &mdash; never trust a listing's estimate.</p></div>
      <div class="fp-card"><h3>PMI isn't forever &mdash; or scary</h3><p>Under 20% down, you'll usually pay PMI, but it drops off once you hit enough equity. Waiting years to save 20% often costs more than just buying with PMI now. Run both versions above.</p></div>
      <div class="fp-card"><h3>Condo math is different math</h3><p>A cheaper condo with a $450 assessment can cost more per month than a pricier townhome with none. Always add the HOA line &mdash; and read what it covers, because sometimes it includes heat and water and is actually a bargain.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="inner" style="text-align:center;">
    <h2>Want a Real Number, Not an Estimate?</h2>
    <p style="max-width:600px;margin:0 auto 20px;">Tell us what you're hoping to spend per month and we'll work backwards to the towns, neighborhoods, and homes that actually fit &mdash; taxes and all. That's the part a calculator can't do.</p>
    <a class="search-btn" href="/#contact">Contact Dawn &amp; Josh</a>
    <a class="outline-btn" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    <p style="margin-top:18px;font-size:14px;">First-time buyer? Start with our guide: <a href="/blog/best-northwest-suburbs-first-time-buyers">The Best Northwest Suburbs for First-Time Buyers</a>. Selling too? See what you&rsquo;ll walk away with on our <a href="/seller-net-sheet">seller net sheet</a>.</p>
  </div>
</section>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('mortgageCalc', () => ({
    price: 400000, downPct: 10, downAmt: 40000,
    rate: 6.5, term: 30, taxRate: 2.4, ins: 175, hoa: 0,

    num(v, fallback = 0) { return +v || fallback },
    fmt(n) { return '$' + Math.round(n).toLocaleString('en-US') },

    syncFromPct() { this.downAmt = Math.round(this.num(this.price) * this.num(this.downPct) / 100) },
    syncFromAmt() {
      const price = this.num(this.price)
      this.downPct = price > 0 ? +((this.num(this.downAmt) / price * 100).toFixed(1)) : 0
    },

    get loan() { return Math.max(this.num(this.price) - this.num(this.downAmt), 0) },
    get downFrac() { const p = this.num(this.price); return p > 0 ? this.num(this.downAmt) / p : 0 },
    get pi() {
      const r = this.num(this.rate) / 100 / 12
      const n = this.num(this.term, 30) * 12
      return r > 0 ? this.loan * (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1) : (n > 0 ? this.loan / n : 0)
    },
    get tax() { return this.num(this.price) * (this.num(this.taxRate) / 100) / 12 },
    get pmi() { return (this.downFrac < 0.20 && this.loan > 0) ? this.loan * 0.006 / 12 : 0 },
    get total() { return this.pi + this.tax + this.num(this.ins) + this.pmi + this.num(this.hoa) },
    get parts() {
      return [
        { amt: this.pi, color: '#C8A84B' },
        { amt: this.tax, color: '#7FA3D8' },
        { amt: this.num(this.ins), color: '#5BC49A' },
        { amt: this.pmi, color: '#E08A8A' },
        { amt: this.num(this.hoa), color: '#B79ADB' },
      ].filter(p => p.amt > 0)
    },
  }))
})
</script>

</x-site.layout>
