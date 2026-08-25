{{-- Per-listing payment calculator: price, ACTUAL tax bill, and ACTUAL HOA
     prefill from the listing (the two numbers generic calculators guess).
     Same math as /mortgage-calculator; estimates only, not a loan offer. --}}
@php
  $mhoa = 0;
  if ($l->hoa_fee) {
      $mhoa = (int) round(match (strtolower((string) $l->hoa_fee_freq)) {
          'quarterly' => $l->hoa_fee / 3,
          'annually', 'yearly' => $l->hoa_fee / 12,
          'not applicable', 'not required' => 0,
          default => $l->hoa_fee,
      });
  }
  $taxes = $l->tax_annual ?: (int) round($l->list_price * 0.024);
  $ins = max(75, (int) (round($l->list_price * 0.0035 / 12 / 5) * 5));
@endphp
<div class="ldc" x-data="listingCalc()">
<style>
  .ldc { background:#F2F5F9; border-radius:12px; padding:20px 22px; margin-top:30px; font-family:'Archivo',Arial,sans-serif; }
  .ldc h2 { font-family:Georgia,serif; font-size:20px; color:#0F1E2E; margin:0 0 4px; }
  .ldc-sub { font-size:12.5px; color:#48586B; margin:0 0 16px; }
  .ldc-grid { display:grid; grid-template-columns:1.2fr 1fr; gap:22px; align-items:start; }
  @media (max-width:760px) { .ldc-grid { grid-template-columns:1fr; } }
  .ldc-fields { display:grid; grid-template-columns:1fr 1fr; gap:10px 14px; }
  .ldc-fields label { display:block; font-size:10.5px; font-weight:700; letter-spacing:.6px; text-transform:uppercase; color:#48586B; margin-bottom:4px; }
  .ldc-fields input { width:100%; padding:9px 10px; border:1px solid #DEE6EE; border-radius:8px; font-size:14.5px; font-family:inherit; }
  .ldc-fields input:focus { outline:2px solid #C8102E; border-color:#C8102E; }
  .ldc-hint { grid-column:1/-1; font-size:11.5px; color:#8A99AA; margin-top:-4px; }
  .ldc-result { background:#0F1E2E; border-radius:10px; color:#fff; padding:18px 20px; }
  .ldc-total { font-size:30px; font-weight:800; line-height:1.1; }
  .ldc-lbl { font-size:11px; letter-spacing:1px; text-transform:uppercase; color:rgba(255,255,255,.65); margin-bottom:6px; }
  .ldc-row { display:flex; justify-content:space-between; gap:10px; font-size:13px; padding:6px 0; border-bottom:1px solid rgba(255,255,255,.14); }
  .ldc-row:last-child { border-bottom:0; }
  .ldc-row b { white-space:nowrap; }
  .ldc-note { font-size:11px; color:#8A99AA; margin-top:12px; }
  .ldc-note a { color:#C8102E; font-weight:700; }
</style>
  <h2>What would this home cost per month?</h2>
  <p class="ldc-sub">Using this listing's actual {{ $l->tax_annual ? 'tax bill'.($l->tax_year ? ' ('.$l->tax_year.')' : '') : 'estimated taxes' }}{{ $mhoa ? ' and actual HOA assessment' : '' }} — adjust anything.</p>
  <div class="ldc-grid">
    <div class="ldc-fields">
      <div><label for="lc-price">Price</label><input id="lc-price" type="number" x-model.number="price" @input="syncFromPct" min="0" step="5000"></div>
      <div><label for="lc-down">Down payment %</label><input id="lc-down" type="number" x-model.number="downPct" @input="syncFromPct" min="0" max="100" step="0.5"></div>
      <div><label for="lc-rate">Rate %</label><input id="lc-rate" type="number" x-model.number="rate" min="0" max="20" step="0.125"></div>
      <div><label for="lc-term">Term (years)</label><input id="lc-term" type="number" x-model.number="term" min="5" max="40" step="5"></div>
      <div><label for="lc-tax">Taxes ($/yr)</label><input id="lc-tax" type="number" x-model.number="taxAnnual" min="0" step="100"></div>
      <div><label for="lc-ins">Insurance ($/mo)</label><input id="lc-ins" type="number" x-model.number="ins" min="0" step="10"></div>
      <div><label for="lc-hoa">HOA ($/mo)</label><input id="lc-hoa" type="number" x-model.number="hoa" min="0" step="10"></div>
      <p class="ldc-hint">Rates move daily — edit to today's quote. Under 20% down adds PMI automatically.</p>
    </div>
    <div class="ldc-result">
      <div class="ldc-lbl">Estimated monthly payment</div>
      <div class="ldc-total" x-text="fmt(total) + '/mo'">$0</div>
      <div style="margin-top:12px">
        <div class="ldc-row"><span>Principal &amp; interest</span><b x-text="fmt(pi)"></b></div>
        <div class="ldc-row"><span>Property taxes</span><b x-text="fmt(num(taxAnnual)/12)"></b></div>
        <div class="ldc-row"><span>Insurance</span><b x-text="fmt(num(ins))"></b></div>
        <div class="ldc-row" x-show="pmi > 0"><span>PMI (under 20% down)</span><b x-text="fmt(pmi)"></b></div>
        <div class="ldc-row" x-show="num(hoa) > 0"><span>HOA / assessments</span><b x-text="fmt(num(hoa))"></b></div>
      </div>
    </div>
  </div>
  <p class="ldc-note">Estimates only — not a loan offer or quote; your rate, insurance and PMI depend on your credit and lender. Run other scenarios on the <a href="/mortgage-calculator">full mortgage calculator</a>, or ask us to connect you with lenders our clients actually close with.</p>
</div>
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('listingCalc', () => ({
    price: {{ (int) $l->list_price }}, downPct: 10, downAmt: {{ (int) round($l->list_price * 0.10) }},
    rate: 6.5, term: 30, taxAnnual: {{ (int) $taxes }}, ins: {{ (int) $ins }}, hoa: {{ (int) $mhoa }},

    num(v, fallback = 0) { return +v || fallback },
    fmt(n) { return '$' + Math.round(n).toLocaleString('en-US') },
    syncFromPct() { this.downAmt = Math.round(this.num(this.price) * this.num(this.downPct) / 100) },

    get loan() { return Math.max(this.num(this.price) - this.num(this.downAmt), 0) },
    get downFrac() { const p = this.num(this.price); return p > 0 ? this.num(this.downAmt) / p : 0 },
    get pi() {
      const r = this.num(this.rate) / 100 / 12
      const n = this.num(this.term, 30) * 12
      return r > 0 ? this.loan * (r * Math.pow(1 + r, n)) / (Math.pow(1 + r, n) - 1) : (n > 0 ? this.loan / n : 0)
    },
    get pmi() { return (this.downFrac < 0.20 && this.loan > 0) ? this.loan * 0.006 / 12 : 0 },
    get total() { return this.pi + this.num(this.taxAnnual) / 12 + this.num(this.ins) + this.pmi + this.num(this.hoa) },
  }))
})
</script>
