<x-site.layout :page="$page" :head="$head">

<div class="hero">
  <div class="breadcrumb"><a href="/">Home</a> &rsaquo; Seller Net Sheet</div>
  <h1>What Will You <em>Actually</em> Walk Away With?</h1>
  <p>The sale price is the headline &mdash; the wire to your bank is the real story. This Illinois-specific net sheet includes the costs most sellers don't see coming: tax prorations, transfer stamps, title, and the rest.</p>
</div>

<section x-data="netSheet">
  <div class="inner">
    <div class="calc-wrap">
      <div class="calc-card">
        <div class="group-lbl">The Big Numbers</div>
        <div class="field">
          <label for="price">Expected Sale Price</label>
          <input type="number" id="price" x-model.number="price" @input="onPrice" min="0" step="5000">
          <div class="hint">Not sure? <a href="/?val=#contact">Get our free valuation first</a> &mdash; it's the number everything else hangs on.</div>
        </div>
        <div class="field">
          <label for="payoff">Mortgage Payoff (all loans &amp; HELOCs)</label>
          <input type="number" id="payoff" x-model.number="payoff" min="0" step="1000">
          <div class="hint">Call your lender for the exact payoff &mdash; it runs a bit higher than your balance</div>
        </div>
        <div class="group-lbl">Commission</div>
        <div class="two-col">
          <div class="field">
            <label for="listPct">Listing Side %</label>
            <input type="number" id="listPct" x-model.number="listPct" min="0" max="10" step="0.25">
          </div>
          <div class="field">
            <label for="buyPct">Buyer Side %</label>
            <input type="number" id="buyPct" x-model.number="buyPct" min="0" max="10" step="0.25">
          </div>
        </div>
        <div class="field"><div class="hint" style="margin-top:-8px;">Commissions are negotiable and the buyer-side offer is your strategic call now &mdash; we'll walk you through what's winning in today's market.</div></div>
        <div class="group-lbl">Taxes &amp; Government</div>
        <div class="field">
          <label for="taxBill">Most Recent Annual Property Tax Bill</label>
          <input type="number" id="taxBill" x-model.number="taxBill" min="0" step="100">
        </div>
        <div class="two-col">
          <div class="field">
            <label for="months">Months of Unbilled Taxes at Closing</label>
            <input type="number" id="months" x-model.number="months" min="0" max="24" step="1">
            <div class="hint">Illinois pays taxes in arrears &mdash; sellers typically owe 6&ndash;14 months depending on closing date; a mid-year Cook County closing can owe 13+</div>
          </div>
          <div class="field">
            <label for="proration">Proration %</label>
            <input type="number" id="proration" x-model.number="proration" min="90" max="120" step="1">
            <div class="hint">100&ndash;110% is typical around here</div>
          </div>
        </div>
        <div class="field">
          <label for="muniStamp">Municipal Transfer Stamp ($ per $1,000)</label>
          <input type="number" id="muniStamp" x-model.number="muniStamp" min="0" max="12" step="0.5">
          <div class="hint">Many Northwest Suburbs charge $0 &mdash; but some towns do charge, and it varies a lot. State ($1/1,000) and county ($0.50/1,000) are added automatically. Ask us for your town's exact rate.</div>
        </div>
        <div class="group-lbl">Closing Costs</div>
        <div class="two-col">
          <div class="field">
            <label for="title">Owner's Title Policy</label>
            <input type="number" id="title" x-model.number="title" @input="titleTouched = true" min="0" step="50">
            <div class="hint">Seller pays in Illinois; scales with price</div>
          </div>
          <div class="field">
            <label for="attorney">Attorney Fee</label>
            <input type="number" id="attorney" x-model.number="attorney" min="0" step="50">
            <div class="hint">Illinois closings use attorneys</div>
          </div>
        </div>
        <div class="two-col">
          <div class="field">
            <label for="survey">Survey</label>
            <input type="number" id="survey" x-model.number="survey" min="0" step="50">
            <div class="hint">Single-family; usually $0 for condos</div>
          </div>
          <div class="field">
            <label for="misc">Misc / Recording / Compliance</label>
            <input type="number" id="misc" x-model.number="misc" min="0" step="50">
            <div class="hint">Water cert, zoning, payoff wires, etc.</div>
          </div>
        </div>
        <div class="field">
          <label for="credits">Repair Credits / Concessions to Buyer</label>
          <input type="number" id="credits" x-model.number="credits" min="0" step="500">
          <div class="hint">Whatever you agree to after inspection or negotiate up front</div>
        </div>
      </div>
      <div class="result-card">
        <div class="result-lbl">Estimated Net Proceeds</div>
        <div class="result-total" x-text="(net < 0 ? '–' : '') + fmt(net)">$0</div>
        <div class="result-sub" x-text="`Cost of sale ≈ ${num(price) > 0 ? (costs / num(price) * 100).toFixed(1) : '0'}% of sale price (before payoff)`"></div>
        <div class="break-head">From the Sale</div>
        <div class="break-row"><span>Sale Price</span><span class="amt" x-text="fmt(num(price))">$0</span></div>
        <div class="break-head">Comes Off the Top</div>
        <div class="break-row"><span>Mortgage Payoff</span><span class="amt" x-text="neg(num(payoff))">&ndash;$0</span></div>
        <div class="break-row"><span>Commission (<span x-text="num(listPct) + num(buyPct)">5</span>%)</span><span class="amt" x-text="neg(comm)">&ndash;$0</span></div>
        <div class="break-row"><span>Tax Proration Credit to Buyer</span><span class="amt" x-text="neg(pror)">&ndash;$0</span></div>
        <div class="break-row"><span>Transfer Taxes (state + county<span x-text="num(muniStamp) > 0 ? ' + municipal' : ''"></span>)</span><span class="amt" x-text="neg(xfer)">&ndash;$0</span></div>
        <div class="break-row"><span>Title, Attorney &amp; Survey</span><span class="amt" x-text="neg(closing)">&ndash;$0</span></div>
        <div class="break-row"><span>Misc Fees</span><span class="amt" x-text="neg(num(misc))">&ndash;$0</span></div>
        <div class="break-row" x-show="num(credits) > 0"><span>Credits to Buyer</span><span class="amt" x-text="neg(num(credits))">&ndash;$0</span></div>
        <div class="break-row" style="margin-top:8px;border-top:2px solid var(--gold);border-bottom:none;padding-top:12px;"><span style="font-weight:700;">Total Cost of Sale</span><span class="amt" x-text="neg(costs)">&ndash;$0</span></div>
      </div>
    </div>
    <p style="font-family:Arial,sans-serif;font-size:12px;color:#999;margin-top:18px;">Estimates for planning purposes only &mdash; not a closing statement, payoff quote, or legal or financial advice. Actual figures depend on your loan payoff, your town's transfer stamps, your contract terms, and your attorney's and title company's fees. Before you list, we prepare an exact net sheet for your specific home and town &mdash; free.</p>
  </div>
</section>

<section class="alt">
  <div class="inner">
    <h2>The Three Numbers That Surprise Illinois Sellers</h2>
    <div class="fp-grid">
      <div class="fp-card"><h3>The tax proration credit</h3><p>Illinois pays property taxes in arrears &mdash; this year you pay last year's bill. So at closing you credit the buyer for the months you owned but haven't been billed for yet, usually at 100&ndash;110% of the last bill. On a recent closing we were part of, the proration credit came to more than $13,000 &mdash; over thirteen months of taxes on a mid-year Cook County closing. It's not a fee &mdash; it's taxes you always owed &mdash; but nobody warns you it comes out at closing.</p></div>
      <div class="fp-card"><h3>Transfer stamps depend on your town</h3><p>State and county transfer taxes are fixed ($1.50 per $1,000 combined). Municipal stamps are the wildcard &mdash; many of our towns charge nothing, others charge real money, and a few make the <em>buyer</em> pay. We know every town's rules; one question to us saves you the surprise.</p></div>
      <div class="fp-card"><h3>The payoff is higher than the balance</h3><p>Your payoff includes interest through the closing date plus recording and wire fees &mdash; and if you have a HELOC, it must be paid and <em>closed</em>, not just zeroed. Order the payoff letter early; it's the number this whole sheet hangs on.</p></div>
    </div>
  </div>
</section>

<section>
  <div class="inner" style="text-align:center;">
    <h2>Want the Exact Number for Your Home?</h2>
    <p style="max-width:620px;margin:0 auto 20px;">Before you list, we run a real net sheet: your actual payoff, your town's actual stamps, your tax bill's actual proration &mdash; and a pricing strategy to push the top line higher. Free, no commitment, and it's the single most useful 20 minutes of your sale.</p>
    <a class="search-btn" href="/?val=#contact">Get My Free Valuation + Net Sheet</a>
    <a class="outline-btn" href="sms:2246284013">Text Josh: (224) 628-4013</a>
    <p style="margin-top:18px;font-size:14px;">Buying your next place too? Run the other side with our <a href="/mortgage-calculator">mortgage calculator</a>. And read why we think <a href="/off-market-homes">"quiet" off-market listings usually cost sellers money</a>.</p>
  </div>
</section>

<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('netSheet', () => ({
    price: 400000, payoff: 180000,
    listPct: 2.5, buyPct: 2.5,
    taxBill: 8500, months: 12, proration: 105, muniStamp: 0,
    title: 2500, attorney: 650, survey: 500, misc: 500, credits: 0,
    titleTouched: false,

    num(v) { return +v || 0 },
    fmt(n) { return '$' + Math.round(Math.abs(n)).toLocaleString('en-US') },
    neg(n) { return n > 0 ? '–' + this.fmt(n) : '$0' },

    // scale title default gently with price until user edits it
    onPrice() {
      if (!this.titleTouched) {
        this.title = Math.round((1200 + this.num(this.price) * 0.00325) / 50) * 50
      }
    },

    get comm() { return this.num(this.price) * (this.num(this.listPct) + this.num(this.buyPct)) / 100 },
    get pror() { return this.num(this.taxBill) / 12 * this.num(this.months) * (this.num(this.proration) / 100) },
    get xfer() { return this.num(this.price) / 1000 * (1.0 + 0.5 + this.num(this.muniStamp)) },
    get closing() { return this.num(this.title) + this.num(this.attorney) + this.num(this.survey) },
    get costs() { return this.comm + this.pror + this.xfer + this.closing + this.num(this.misc) + this.num(this.credits) },
    get net() { return this.num(this.price) - this.num(this.payoff) - this.costs },
  }))
})
</script>

</x-site.layout>
