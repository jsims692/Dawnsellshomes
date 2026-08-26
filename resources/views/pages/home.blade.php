{{-- Homepage, design v2 (Pat's redesign, promoted from /preview/home).
     The legacy one-page homepage stays reachable at /old-home. --}}
<x-site.layout :page="$page" :head="$head">
<style>
.hero-tabs{display:flex;gap:.4rem;margin:1.4rem 0 .7rem}
.hero-tabs button{border:1px solid var(--line);background:#fff;color:var(--slate);font-family:inherit;font-size:.85rem;font-weight:700;padding:.5rem 1rem;border-radius:999px;cursor:pointer;transition:all .15s ease}
.hero-tabs button.on{background:var(--ink);border-color:var(--ink);color:#fff}
.val-card.hero-search{display:flex;gap:.5rem}
.val-card.hero-search input{flex:1;min-width:0;border:0;outline:none;font:inherit;padding:.55rem .8rem;color:var(--ink)}
.val-card.hero-search select{border:1px solid var(--line);border-radius:10px;background:#fff;font:inherit;font-size:.88rem;color:var(--slate);padding:.45rem .5rem}
@media(max-width:560px){.val-card.hero-search{flex-wrap:wrap}.val-card.hero-search input{flex-basis:100%}}
/* Homepage-only styles (photo card, wired-widget results, result photos) —
   everything else comes from site-v2.css. */
.ph .photo-card{position:relative;border-radius:20px;overflow:hidden;box-shadow:0 30px 70px rgba(15,30,46,.25)}
.ph .photo-card img{display:block;width:100%;height:auto;aspect-ratio:4/5;object-fit:cover}
.ph .photo-cap{position:absolute;left:14px;right:14px;bottom:14px;background:rgba(255,255,255,.96);border-radius:14px;padding:.8rem 1rem;font-family:'Archivo',system-ui,sans-serif}
.ph .photo-cap strong{font-weight:700;color:var(--ink)}
.ph .photo-cap span{display:block;font-size:.75rem;color:var(--slate);letter-spacing:.04em;text-transform:uppercase;margin-top:2px}
.ph .photo-cap em{display:block;font-style:normal;font-size:.8rem;color:var(--faint);margin-top:4px}
.hv-preds{position:absolute;left:0;right:0;top:calc(100% + 6px);margin:0;padding:.4rem 0;list-style:none;background:#fff;border:1px solid var(--line);border-radius:12px;box-shadow:0 18px 40px rgba(15,30,46,.16);z-index:60;max-height:280px;overflow:auto;font-family:'Archivo',system-ui,sans-serif;font-size:.9rem;text-align:left}
.hv-preds li{padding:.55rem .9rem;cursor:pointer}
.hv-preds li strong{color:var(--ink)} .hv-preds li span{color:var(--faint);font-size:.8rem}
.hv-preds li.active,.hv-preds li:hover{background:var(--mist)}
.hv-preds li.hv-attrib{font-size:.65rem;color:var(--faint);text-align:right;cursor:default;padding:.2rem .8rem 0}
.hv-result{background:#fff;border:1px solid var(--line);border-radius:16px;padding:1.1rem 1.25rem;margin-top:.9rem;font-family:'Archivo',system-ui,sans-serif;text-align:left}
.hv-kicker{font-size:.7rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:var(--red)}
.hv-row{display:flex;flex-wrap:wrap;align-items:baseline;gap:.7rem;margin:.35rem 0 .3rem}
.hv-median{font-family:'Fraunces',serif;font-size:1.55rem;font-weight:600;color:var(--ink)}
.hv-range{font-size:.85rem;color:var(--slate)}
.hv-note{font-size:.85rem;color:var(--slate);margin:.3rem 0 .7rem}
.hv-sample{list-style:none;margin:.5rem 0 .2rem;padding:0;font-size:.85rem;color:var(--slate)}
.hv-sample li{display:flex;justify-content:space-between;gap:12px;padding:.32rem 0;border-bottom:1px dashed var(--line)}
.hv-sample li a{color:var(--ink);text-decoration:underline;text-underline-offset:2px}
.hv-sample li a:hover{color:var(--red)}
.hv-sample li span:last-child{color:var(--ink);font-weight:700;white-space:nowrap}
.res-photo{display:block;aspect-ratio:3/2;border-radius:12px;background:var(--mist) center/cover no-repeat;margin-bottom:.9rem}
.form-ok{margin-top:.8rem;color:#177245;font-weight:600;font-family:'Archivo',system-ui,sans-serif}
html,body{overflow-x:hidden}
.hero{overflow:hidden;position:relative}
</style>


<!-- Hero -->
<section class="hero">
  <svg class="plat" viewBox="0 0 640 640" fill="none" aria-hidden="true">
    <g stroke="#0F1E2E" stroke-opacity=".08" stroke-width="1.5">
      <path d="M60 30 L600 55 L585 610 L35 585 Z"></path>
      <path d="M45 210 C 200 195, 430 228, 596 206"></path>
      <path d="M330 32 L318 606"></path>
      <path d="M150 36 L142 205"></path><path d="M245 42 L236 210"></path>
      <path d="M425 48 L432 214"></path><path d="M515 52 L522 212"></path>
      <path d="M55 330 L323 345"></path><path d="M52 455 L320 470"></path>
      <path d="M140 212 L128 583"></path><path d="M232 218 L224 588"></path>
      <path d="M330 350 L590 330"></path><path d="M326 480 L588 462"></path>
      <path d="M430 218 L438 600"></path><path d="M515 214 L524 604"></path>
      <path d="M60 95 L598 118" stroke-dasharray="3 6"></path>
    </g>
    <g>
      <path d="M143 347 L233 352 L226 470 L136 464 Z" fill="#C8102E" fill-opacity=".06" stroke="#C8102E" stroke-opacity=".45" stroke-width="1.5"></path>
      <circle cx="184" cy="400" r="4" fill="#C8102E"></circle>
      <text x="152" y="432" font-family="Archivo, sans-serif" font-size="10" letter-spacing="2" fill="#C8102E" fill-opacity=".75">SINCE 1988</text>
    </g>
  </svg>
  <div class="wrap">
    <div class="hero-grid">
      <div>
        <p class="eyebrow">Prospect Heights · Mount Prospect · Arlington Heights</p>
        <h1 class="display">A mother &amp; son who know these neighborhoods <em>by heart.</em></h1>
        <p class="lead hero-sub">For over 30 years, Dawn and her son Josh have lived here, raised a family here, and helped hundreds of neighbors move across the northwest suburbs. Two full-time local agents who treat your move like it’s their own.</p>
        <div class="hero-ctas">
          <a class="btn btn--primary" href="#contact">Get a free home valuation</a>
          <a class="btn btn--ghost" href="#team">Meet Dawn &amp; Josh</a>
        </div>
        <div x-data="{ heroTab: 'search' }" style="max-width:560px">
          <div class="hero-tabs">
            <button type="button" :class="heroTab==='search' ? 'on' : ''" @click="heroTab='search'">&#128269; Search homes</button>
            <button type="button" :class="heroTab==='value' ? 'on' : ''" @click="heroTab='value'">&#127968; What&rsquo;s my home worth?</button>
          </div>
          <div x-show="heroTab==='search'">
            <form class="val-card hero-search" action="/listings" method="get" aria-label="Search homes">
              <input type="text" name="city" list="dshCityList" placeholder="City — e.g. Mount Prospect" autocomplete="off" aria-label="City">
              <select name="max" aria-label="Max price"><option value="">Max $</option><option value="300000">$300K</option><option value="400000">$400K</option><option value="500000">$500K</option><option value="600000">$600K</option><option value="750000">$750K</option><option value="1000000">$1M</option><option value="2000000">$2M</option></select>
              <select name="beds" aria-label="Beds"><option value="">Beds</option><option value="1">1+</option><option value="2">2+</option><option value="3">3+</option><option value="4">4+</option><option value="5">5+</option></select>
              <button class="btn btn--primary" type="submit">Search</button>
            </form>
            <p style="font-size:.82rem;color:var(--slate);margin-top:.5rem">Every MLS listing in the northwest suburbs — updated all day. <a href="/listings" style="color:var(--red);font-weight:600">Advanced search &rarr;</a></p>
          </div>
          <div x-show="heroTab==='value'" x-cloak>
            <form class="val-card" id="valForm" aria-label="Home valuation" x-data="homeValue()" x-init="init()" @submit.prevent="submit()" style="position:relative">
          <input type="text" id="valAddress" placeholder="Enter your address — what's your home worth?" aria-label="Your home address" x-model="query" @input.debounce.220ms="suggest()" @keydown.down.prevent="move(1)" @keydown.up.prevent="move(-1)" @keydown.escape="preds=[]" autocomplete="off">
  <ul x-show="preds.length" x-cloak @mousedown.prevent class="hv-preds" role="listbox"><template x-for="(p,i) in preds" :key="p.id"><li :class="{active:i===hi}" @click="pick(p)" @mouseenter="hi=i" role="option"><strong x-text="p.main"></strong> <span x-text="p.secondary"></span></li></template><li class="hv-attrib">Powered by Google</li></ul>
          <button class="btn btn--primary" type="submit">Get value</button>

        <div x-show="result" x-cloak x-transition class="hv-result rv">
  <template x-if="result && result.ok"><div>
    <div class="hv-kicker">Estimated value range</div>
    <div class="hv-row"><span class="hv-median" x-text="fmt(result.low)+' – '+fmt(result.high)"></span><span class="hv-range">median <strong x-text="fmt(result.median)"></strong> · <span x-text="result.basis_short"></span></span></div>
    <p class="hv-note" style="margin:.1rem 0 .4rem"><span x-text="result.kicker"></span> <span x-text="shortAddr"></span>. An estimate from recent nearby sales &mdash; not an appraisal or CMA.</p>
    <ul class="hv-sample">
      <template x-for="s in result.sample" :key="s.address+s.year">
        <li>
          <a x-show="s.url" :href="s.url" target="_blank" rel="noopener" x-text="s.address+', '+s.city"></a>
          <span x-show="!s.url" x-text="s.address+', '+s.city"></span>
          <span x-text="fmt(s.price)+' · '+(s.when || s.year)"></span>
        </li>
      </template>
    </ul>
    <p class="hv-note" x-show="result.ours_line" x-text="result.ours_line" style="font-weight:600;margin:.2rem 0 0"></p>
    <p x-show="result.attribution" x-text="result.attribution" style="font-size:10px;color:var(--faint);margin:.3rem 0 0;line-height:1.5"></p>
    <p class="hv-note">That's the data — now get Josh's number for your specific house, free, usually within 24 hours.</p>
    <button type="button" class="btn btn--primary" @click="toContact()">Get my exact number →</button>
    <a href="/seller-net-sheet" style="display:inline-block;margin-left:10px;font-size:.85rem;font-weight:700;color:var(--red);text-decoration:none;">What would I actually walk away with? →</a>
  </div></template>
  <template x-if="result && !result.ok"><div>
    <p class="hv-note">We haven't closed enough sales right there for a fair snapshot — but we'll pull real comps and send you a free valuation within 24 hours.</p>
    <button type="button" class="btn btn--primary" @click="toContact()">Get my free valuation →</button>
  </div></template>
</div></form>
          </div>
        </div>

        <ul class="trust">
          <li>RE/MAX Hall of Fame</li>
          <li>{{ \App\Support\TeamStats::soldTotal() }} homes sold</li>
          <li>4.9★ · 62+ Google reviews</li>
        </ul>
      </div>
      <div class="ph">
  <div class="photo-card">
    <img src="/images/hero-team.jpg" srcset="/images/hero-team.jpg 1200w, /images/hero-team@2x.jpg 2000w" sizes="(max-width:900px) 92vw, 480px" width="1200" height="1500" alt="Dawn Simmons and Josh Simmons, the mother-and-son Dawn Simmons Team at RE/MAX Suburban" fetchpriority="high">
    <div class="photo-cap"><strong>Dawn &amp; Josh</strong><span>Mom · Broker &nbsp;&mdash;&nbsp; Son · Broker Associate</span><em>Born and raised in Prospect Heights, and still here.</em></div>
  </div>
</div>
    </div>
  </div>
</section>

<!-- Stats -->
<div class="section--ink stats">
  <div class="wrap">
    <div class="stats-grid">
      <div class="rv in"><div class="stat-num">{{ \App\Support\TeamStats::soldTotal() }}</div><div class="stat-label">Homes sold</div></div>
      <div class="rv in"><div class="stat-num">38</div><div class="stat-label">Combined years of experience</div></div>
      <div class="rv in"><div class="stat-num">4.9★</div><div class="stat-label">Across 62+ Google reviews</div></div>
      <div class="rv in"><div class="stat-num">10M+</div><div class="stat-label">Views on one home tour</div></div>
    </div>
  </div>
</div>

<!-- Why us -->
<section class="section" id="why">
  <div class="wrap">
    <div class="sec-head rv in">
      <p class="eyebrow">Why the Dawn Simmons Team</p>
      <h2 class="h2">What working with us actually gets you.</h2>
      <p class="lead">There are hundreds of agents in the northwest suburbs. Here’s the difference.</p>
    </div>
    <div class="grid-4">
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h3l1.5 4L7.5 9.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg></div>
        <h3>7 days a week, day or night</h3>
        <p>Call or text anytime — evenings, weekends, holidays. Real estate doesn’t keep business hours, and neither do we.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7.5v9M14.4 9.2c-.5-.8-1.4-1.2-2.4-1.2-1.4 0-2.5.8-2.5 2 0 2.6 5 1.5 5 4 0 1.2-1.1 2-2.5 2-1 0-1.9-.4-2.4-1.2"></path></svg></div>
        <h3>We fight for every dollar</h3>
        <p>Nobody negotiates harder. Bidding wars won for buyers, multiple offers pulled for sellers — {{ \App\Support\TeamStats::soldTotal() }} times and counting.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="6" width="13.5" height="12" rx="2.5"></rect><path d="M16.5 10.5 21 8v8l-4.5-2.5"></path></svg></div>
        <h3>Marketing that reaches millions</h3>
        <p>Professional photo and video on every listing. One of Josh’s home tours alone has topped 10 million views.</p>
      </div>
      <div class="why-card rv in">
        <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-6.5-5.2-6.5-10a6.5 6.5 0 0 1 13 0c0 4.8-6.5 10-6.5 10Z"></path><path d="M9.8 11.4 12 9.4l2.2 2M10.6 11.1V14h2.8v-2.9"></path></svg></div>
        <h3>We actually grew up here</h3>
        <p>Prospect Heights since 1988. We know which blocks flood, which schools feed where, and what your street really sells for.</p>
      </div>
    </div>
  </div>
</section>

<!-- Buy / Sell -->
<section class="section section--tight" id="services" style="padding-top:0">
  <div class="wrap">
    <div class="svc">
      <div class="svc-panel svc--sell rv">
        <p class="eyebrow">Selling</p>
        <h3>Sell your home for top dollar.</h3>
        <p>The right price, professional marketing, and sharp negotiation — a proven strategy that consistently gets sellers top dollar in Prospect Heights, Mount Prospect, and Arlington Heights. Many of our listings receive multiple offers.</p>
        <a class="link-arrow" href="#contact">Get a free home valuation →</a>
      </div>
      <div class="svc-panel svc--buy rv">
        <p class="eyebrow">Buying</p>
        <h3>Find your next home.</h3>
        <p>Full-time local agents with access to listings before they hit the market — and the relationships to help you win when it’s competitive. We’ll find the right home and fight for you through closing.</p>
        <a class="link-arrow" href="#search">Start your home search →</a>
      </div>
    </div>
  </div>
</section>

<!-- Results -->
<section class="section section--mist" id="results">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Recent results</p>
      <h2 class="h2">Homes are selling fast in your area.</h2>
      <p class="lead">Real results from your neighbors in Prospect Heights, Mount Prospect, and the surrounding suburbs.</p>
    </div>
    <div class="res-grid">
      @if(!empty($teamListings))
      @foreach($teamListings as $t)
      <a class="res-card rv" href="{{ $t['url'] }}"><span class="res-photo" style="background-image:url('{{ $t['photo'] }}')"></span>
        <span class="chip">{{ $t['chip'] }}</span>
        <div><div class="res-addr">{{ $t['addr'] }}</div><div class="res-city">{{ $t['city'] }}</div></div>
        <div class="res-price">{{ $t['price'] }}</div>
        <div class="res-meta">{{ $t['meta'] }}</div>
      </a>
      @endforeach
      @else
      <a class="res-card rv" href="/neighborhoods/prospect-manor-mount-prospect"><span class="res-photo" style="background-image:url('/images/417-prospect-manor.jpg')"></span>
        <span class="chip">Sold in 7 days</span>
        <div><div class="res-addr">417 N Prospect Manor Ave</div><div class="res-city">Mount Prospect, IL 60056</div></div>
        <div class="res-price">$449,900</div>
        <div class="res-meta">Multiple offers · Listed &amp; sold by our team</div>
      </a>
      <a class="res-card rv" href="/cities/round-lake-beach"><span class="res-photo" style="background-image:url('/images/29-glenwood.jpg')"></span>
        <span class="chip">Full price · Under a week</span>
        <div><div class="res-addr">29 Glenwood Dr</div><div class="res-city">Round Lake Beach, IL 60073</div></div>
        <div class="res-price">$265,000</div>
        <div class="res-meta">3 bd · 1 ba · Steps off the lake</div>
      </a>
      <a class="res-card rv" href="/cities/prospect-heights"><span class="res-photo" style="background-image:url('/images/2-marberry.jpg')"></span>
        <span class="chip">Closed</span>
        <div><div class="res-addr">2 Marberry Dr</div><div class="res-city">Prospect Heights, IL 60070</div></div>
        <div class="res-price">5 bd · 4 ba</div>
        <div class="res-meta">2,678 sqft · In-law suite</div>
      </a>
      @endif
    </div>
    <div class="rv" style="margin-top:2.2rem">
      <x-sales.map height="480px" :compact="true" />
      <p style="margin-top:1.2rem"><a class="link-arrow" href="/sold">See all 555 homes we've sold — with the full interactive map →</a></p>
    </div>
  </div>
</section>

<!-- Areas -->
<div id="neighborhoods"></div>
<section class="section" id="areas">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Explore by area</p>
      <h2 class="h2">Browse homes across Chicagoland.</h2>
      <p class="lead">From Chicago’s north side to Fox Lake — pick a community to see active MLS listings, updated daily.</p>
    </div>
    <div class="callout rv">🧭 New to the area? <a class="link-arrow" href="/moving-to-northwest-suburbs">Start here: the complete guide to moving to the northwest suburbs →</a> &nbsp;·&nbsp; Know the subdivision you want? <a class="link-arrow" href="/neighborhoods">Browse every community we cover →</a></div>
    <div class="tabs" role="tablist" aria-label="Regions">
      <button class="tab" role="tab" aria-selected="true" aria-controls="area-core" id="tab-core">Your core market</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-north" id="tab-north">North suburbs</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-nw" id="tab-nw">Northwest suburbs</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-fox" id="tab-fox">Fox River Valley</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-lake" id="tab-lake">Lake County</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-chi" id="tab-chi">Chicago</button>
      <button class="tab" role="tab" aria-selected="false" aria-controls="area-west" id="tab-west">West &amp; southwest</button>
    </div>
    <div id="area-core" role="tabpanel" aria-labelledby="tab-core">
      <div class="chips">
        <a class="pill" href="/cities/prospect-heights">Prospect Heights</a><a class="pill" href="/cities/mount-prospect">Mount Prospect</a><a class="pill" href="/cities/arlington-heights">Arlington Heights</a><a class="pill" href="/cities/palatine">Palatine</a><a class="pill" href="/cities/wheeling">Wheeling</a><a class="pill" href="/cities/des-plaines">Des Plaines</a><a class="pill" href="/cities/rolling-meadows">Rolling Meadows</a><a class="pill" href="/cities/inverness">Inverness</a><a class="pill" href="/cities/south-barrington">South Barrington</a><a class="pill" href="/cities/fox-lake">Fox Lake</a>
      </div>
    </div>
    <div id="area-north" role="tabpanel" aria-labelledby="tab-north" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/buffalo-grove">Buffalo Grove</a><a class="pill" href="/cities/northbrook">Northbrook</a><a class="pill" href="/cities/glenview">Glenview</a><a class="pill" href="/cities/deerfield">Deerfield</a><a class="pill" href="/cities/northfield">Northfield</a><a class="pill" href="/cities/winnetka">Winnetka</a><a class="pill" href="/cities/glencoe">Glencoe</a><a class="pill" href="/cities/highland-park">Highland Park</a><a class="pill" href="/cities/highwood">Highwood</a><a class="pill" href="/cities/lake-forest">Lake Forest</a><a class="pill" href="/cities/lake-bluff">Lake Bluff</a><a class="pill" href="/cities/north-chicago">North Chicago</a><a class="pill" href="/cities/waukegan">Waukegan</a>
      </div>
    </div>
    <div id="area-nw" role="tabpanel" aria-labelledby="tab-nw" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/barrington">Barrington</a><a class="pill" href="/cities/lake-barrington">Lake Barrington</a><a class="pill" href="/cities/north-barrington">North Barrington</a><a class="pill" href="/cities/barrington-hills">Barrington Hills</a><a class="pill" href="/cities/hoffman-estates">Hoffman Estates</a><a class="pill" href="/cities/schaumburg">Schaumburg</a><a class="pill" href="/cities/elk-grove-village">Elk Grove Village</a><a class="pill" href="/cities/streamwood">Streamwood</a><a class="pill" href="/cities/hanover-park">Hanover Park</a><a class="pill" href="/cities/roselle">Roselle</a><a class="pill" href="/cities/bloomingdale">Bloomingdale</a><a class="pill" href="/cities/itasca">Itasca</a><a class="pill" href="/cities/wood-dale">Wood Dale</a><a class="pill" href="/cities/bensenville">Bensenville</a><a class="pill" href="/cities/addison">Addison</a><a class="pill" href="/cities/villa-park">Villa Park</a>
      </div>
    </div>
    <div id="area-fox" role="tabpanel" aria-labelledby="tab-fox" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/algonquin">Algonquin</a><a class="pill" href="/cities/lake-in-the-hills">Lake in the Hills</a><a class="pill" href="/cities/cary">Cary</a><a class="pill" href="/cities/fox-river-grove">Fox River Grove</a><a class="pill" href="/cities/crystal-lake">Crystal Lake</a><a class="pill" href="/cities/carpentersville">Carpentersville</a><a class="pill" href="/cities/east-dundee">East Dundee</a><a class="pill" href="/cities/west-dundee">West Dundee</a><a class="pill" href="/cities/elgin">Elgin</a><a class="pill" href="/cities/south-elgin">South Elgin</a><a class="pill" href="/cities/st-charles">St. Charles</a><a class="pill" href="/cities/wayne">Wayne</a><a class="pill" href="/cities/bartlett">Bartlett</a>
      </div>
    </div>
    <div id="area-lake" role="tabpanel" aria-labelledby="tab-lake" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/libertyville">Libertyville</a><a class="pill" href="/cities/vernon-hills">Vernon Hills</a><a class="pill" href="/cities/mundelein">Mundelein</a><a class="pill" href="/cities/gurnee">Gurnee</a><a class="pill" href="/cities/grayslake">Grayslake</a><a class="pill" href="/cities/round-lake">Round Lake</a><a class="pill" href="/cities/round-lake-beach">Round Lake Beach</a><a class="pill" href="/cities/round-lake-heights">Round Lake Heights</a><a class="pill" href="/cities/round-lake-park">Round Lake Park</a><a class="pill" href="/cities/lake-villa">Lake Villa</a><a class="pill" href="/cities/lindenhurst">Lindenhurst</a><a class="pill" href="/cities/antioch">Antioch</a><a class="pill" href="/cities/ingleside">Ingleside</a><a class="pill" href="/cities/third-lake">Third Lake</a><a class="pill" href="/cities/wauconda">Wauconda</a><a class="pill" href="/cities/island-lake">Island Lake</a><a class="pill" href="/cities/volo">Volo</a><a class="pill" href="/cities/zion">Zion</a><a class="pill" href="/cities/beach-park">Beach Park</a><a class="pill" href="/cities/winthrop-harbor">Winthrop Harbor</a>
      </div>
    </div>
    <div id="area-chi" role="tabpanel" aria-labelledby="tab-chi" hidden="">
      <div class="chip-group">
        <h4>Northwest &amp; north side</h4>
        <div class="chips">
          <a class="pill" href="/cities/edison-park">Edison Park</a><a class="pill" href="/cities/norwood-park">Norwood Park</a><a class="pill" href="/cities/portage-park">Portage Park</a><a class="pill" href="/cities/jefferson-park">Jefferson Park</a><a class="pill" href="/cities/irving-park">Irving Park</a><a class="pill" href="/cities/forest-glen">Forest Glen</a><a class="pill" href="/cities/dunning">Dunning</a><a class="pill" href="/cities/montclare">Montclare</a><a class="pill" href="/cities/belmont-cragin">Belmont Cragin</a><a class="pill" href="/cities/hermosa">Hermosa</a><a class="pill" href="/cities/avondale">Avondale</a><a class="pill" href="/cities/logan-square">Logan Square</a><a class="pill" href="/cities/bucktown">Bucktown</a><a class="pill" href="/cities/wicker-park">Wicker Park</a><a class="pill" href="/cities/west-town">West Town</a><a class="pill" href="/cities/ukrainian-village">Ukrainian Village</a><a class="pill" href="/cities/humboldt-park">Humboldt Park</a><a class="pill" href="/cities/wrigleyville">Wrigleyville</a><a class="pill" href="/cities/roscoe-village">Roscoe Village</a><a class="pill" href="/cities/north-center">North Center</a><a class="pill" href="/cities/ravenswood">Ravenswood</a><a class="pill" href="/cities/lincoln-square">Lincoln Square</a><a class="pill" href="/cities/andersonville">Andersonville</a><a class="pill" href="/cities/edgewater">Edgewater</a><a class="pill" href="/cities/rogers-park">Rogers Park</a><a class="pill" href="/cities/west-ridge">West Ridge</a><a class="pill" href="/cities/uptown">Uptown</a><a class="pill" href="/cities/lakeview">Lakeview</a><a class="pill" href="/cities/lincoln-park">Lincoln Park</a><a class="pill" href="/cities/old-town">Old Town</a><a class="pill" href="/cities/river-north">River North</a><a class="pill" href="/cities/streeterville">Streeterville</a><a class="pill" href="/cities/gold-coast">Gold Coast</a><a class="pill" href="/cities/near-north-side">Near North Side</a>
        </div>
      </div>
      <div class="chip-group">
        <h4>West &amp; southwest side</h4>
        <div class="chips">
          <a class="pill" href="/cities/austin">Austin</a><a class="pill" href="/cities/west-garfield-park">West Garfield Park</a><a class="pill" href="/cities/east-garfield-park">East Garfield Park</a><a class="pill" href="/cities/east-humboldt-park">East Humboldt Park</a><a class="pill" href="/cities/west-loop">West Loop</a><a class="pill" href="/cities/fulton-market">Fulton Market</a><a class="pill" href="/cities/near-west-side">Near West Side</a><a class="pill" href="/cities/pilsen">Pilsen</a><a class="pill" href="/cities/little-village">Little Village</a><a class="pill" href="/cities/mckinley-park">McKinley Park</a><a class="pill" href="/cities/bridgeport">Bridgeport</a><a class="pill" href="/cities/back-of-the-yards">Back of the Yards</a><a class="pill" href="/cities/brighton-park">Brighton Park</a><a class="pill" href="/cities/clearing">Clearing</a><a class="pill" href="/cities/garfield-ridge">Garfield Ridge</a><a class="pill" href="/cities/archer-heights">Archer Heights</a><a class="pill" href="/cities/gage-park">Gage Park</a><a class="pill" href="/cities/west-elsdon">West Elsdon</a><a class="pill" href="/cities/west-lawn">West Lawn</a><a class="pill" href="/cities/chicago-lawn">Chicago Lawn</a><a class="pill" href="/cities/marquette-park">Marquette Park</a><a class="pill" href="/cities/ashburn">Ashburn</a><a class="pill" href="/cities/beverly">Beverly</a><a class="pill" href="/cities/morgan-park">Morgan Park</a><a class="pill" href="/cities/mount-greenwood">Mount Greenwood</a>
        </div>
      </div>
    </div>
    <div id="area-west" role="tabpanel" aria-labelledby="tab-west" hidden="">
      <div class="chips">
        <a class="pill" href="/cities/park-ridge">Park Ridge</a><a class="pill" href="/cities/niles">Niles</a><a class="pill" href="/cities/skokie">Skokie</a><a class="pill" href="/cities/evanston">Evanston</a><a class="pill" href="/cities/morton-grove">Morton Grove</a><a class="pill" href="/cities/lincolnwood">Lincolnwood</a><a class="pill" href="/cities/harwood-heights">Harwood Heights</a><a class="pill" href="/cities/norridge">Norridge</a><a class="pill" href="/cities/franklin-park">Franklin Park</a><a class="pill" href="/cities/rosemont">Rosemont</a><a class="pill" href="/cities/river-grove">River Grove</a><a class="pill" href="/cities/elmwood-park">Elmwood Park</a><a class="pill" href="/cities/melrose-park">Melrose Park</a><a class="pill" href="/cities/bellwood">Bellwood</a><a class="pill" href="/cities/hillside">Hillside</a><a class="pill" href="/cities/westchester">Westchester</a><a class="pill" href="/cities/la-grange-park">La Grange Park</a><a class="pill" href="/cities/western-springs">Western Springs</a><a class="pill" href="/cities/hinsdale">Hinsdale</a><a class="pill" href="/cities/clarendon-hills">Clarendon Hills</a><a class="pill" href="/cities/westmont">Westmont</a><a class="pill" href="/cities/downers-grove">Downers Grove</a><a class="pill" href="/cities/lisle">Lisle</a><a class="pill" href="/cities/lombard">Lombard</a><a class="pill" href="/cities/glen-ellyn">Glen Ellyn</a><a class="pill" href="/cities/wheaton">Wheaton</a><a class="pill" href="/cities/carol-stream">Carol Stream</a><a class="pill" href="/cities/glendale-heights">Glendale Heights</a><a class="pill" href="/cities/oakbrook-terrace">Oakbrook Terrace</a><a class="pill" href="/cities/oak-brook">Oak Brook</a><a class="pill" href="/cities/elmhurst">Elmhurst</a><a class="pill" href="/cities/villa-park">Villa Park</a>
      </div>
    </div>
  </div>
</section>

<!-- Condos -->
<section class="section section--mist" id="condos" style="padding-top:clamp(3rem,6vw,4.5rem)">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Condos &amp; townhomes</p>
      <h2 class="h2">Condo &amp; townhome communities.</h2>
      <p class="lead">Floor plans, amenities, and pricing for the top complexes across our core market. Pick a city to browse.</p>
    </div>
    <div class="condos">
      <details class="city rv" open="">
        <summary><span>Mount Prospect <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/colony-country-mount-prospect">Colony Country · $180K–$265K</a>
          <a class="pill" href="/condos/hunt-club-on-the-lake">Hunt Club on the Lake · $160K–$280K</a>
          <a class="pill" href="/condos/village-centre-mount-prospect">Village Centre · $250K–$390K</a>
          <a class="pill" href="/condos/evergreen-woods-mount-prospect">Evergreen Woods · $280K–$400K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Arlington Heights <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/lake-arlington-towne">Lake Arlington Towne · $280K–$420K</a>
          <a class="pill" href="/condos/arlington-glen">Arlington Glen · $160K–$270K</a>
          <a class="pill" href="/condos/stone-creek-arlington-heights">Stone Creek · $140K–$180K</a>
          <a class="pill" href="/condos/lexington-heritage-arlington-heights">Lexington Heritage · $520K–$600K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Palatine <em class="city-count">9 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/benchmark-palatine">Benchmark · $220K–$380K</a>
          <a class="pill" href="/condos/one-renaissance-place">One Renaissance Place · $200K–$360K</a>
          <a class="pill" href="/condos/knollwood-of-palatine">Knollwood · $290K–$340K</a>
          <a class="pill" href="/condos/palatine-commons">Palatine Commons · $400K–$480K</a>
          <a class="pill" href="/condos/auburn-woods-palatine">Auburn Woods · $340K–$420K</a>
          <a class="pill" href="/condos/heritage-of-palatine">Heritage of Palatine · $260K–$360K</a>
          <a class="pill" href="/condos/forest-edge-palatine">Forest Edge · $200K–$250K</a>
          <a class="pill" href="/condos/baybrook-palatine">Baybrook · $210K–$260K</a>
          <a class="pill" href="/condos/fox-cove-palatine">Fox Cove · $150K–$200K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Wheeling <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/one-milwaukee-place-wheeling">One Milwaukee Place · $220K–$360K</a>
          <a class="pill" href="/condos/astor-place-wheeling">Astor Place · $230K–$400K</a>
          <a class="pill" href="/condos/wolf-crossing-wheeling">Wolf Crossing · $380K–$520K</a>
          <a class="pill" href="/condos/millbrook-pointe-wheeling">Millbrook Pointe · $360K–$480K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Buffalo Grove <em class="city-count">4 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/the-wheatlands-buffalo-grove">The Wheatlands · $220K–$400K</a>
          <a class="pill" href="/condos/cambridge-on-the-lake-buffalo-grove">Cambridge on the Lake · $160K–$280K</a>
          <a class="pill" href="/condos/town-place-buffalo-grove">Town Place · $220K–$360K</a>
          <a class="pill" href="/condos/delacourte-condominiums-buffalo-grove">Delacourte · $240K–$380K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Des Plaines <em class="city-count">3 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/lexington-pointe-des-plaines">Lexington Pointe · $230K–$390K</a>
          <a class="pill" href="/condos/buckingham-place-des-plaines">Buckingham Place · $280K–$420K</a>
          <a class="pill" href="/condos/the-james-at-the-landings-des-plaines">The James at The Landings · $310K–$460K</a>
        </div>
      </details>
      <details class="city rv">
        <summary><span>Prospect Heights <em class="city-count">5 communities</em></span></summary>
        <div class="city-body">
          <a class="pill" href="/condos/rob-roy-country-club-village">Rob Roy Country Club Village · $180K–$380K</a>
          <a class="pill" href="/condos/quincy-park-prospect-heights">Quincy Park · $195K–$250K</a>
          <a class="pill" href="/condos/willow-heights-prospect-heights">Willow Heights · $145K–$200K</a>
          <a class="pill" href="/condos/lake-run-prospect-heights">Lake Run · $175K–$200K</a>
          <a class="pill" href="/condos/willow-woods-prospect-heights">Willow Woods · $150K–$210K</a>
        </div>
      </details>
    </div>
  </div>
</section>

<!-- Search -->
<section class="section" id="search">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">Find your home</p>
      <h2 class="h2">Search homes on the MLS.</h2>
      <p class="lead">Active listings across Prospect Heights, Mount Prospect, Arlington Heights, and beyond — updated daily.</p>
    </div>
    <form class="search-card rv" aria-label="Home search" action="/listings" method="get">
      <div class="field field--loc"><label for="s-loc">City or area</label><input id="s-loc" name="city" type="text" list="dshCityList" placeholder="e.g. Mount Prospect" autocomplete="off"><datalist id="dshCityList"><option value="Addison"></option><option value="Algonquin"></option><option value="Antioch"></option><option value="Arlington Heights"></option><option value="Barrington"></option><option value="Barrington Hills"></option><option value="Bartlett"></option><option value="Beach Park"></option><option value="Bellwood"></option><option value="Bensenville"></option><option value="Bloomingdale"></option><option value="Bridgeport"></option><option value="Buffalo Grove"></option><option value="Carol Stream"></option><option value="Carpentersville"></option><option value="Cary"></option><option value="Chicago"></option><option value="Clarendon Hills"></option><option value="Crystal Lake"></option><option value="Deerfield"></option><option value="Des Plaines"></option><option value="Downers Grove"></option><option value="East Dundee"></option><option value="Elgin"></option><option value="Elk Grove Village"></option><option value="Elmhurst"></option><option value="Elmwood Park"></option><option value="Evanston"></option><option value="Fox Lake"></option><option value="Fox River Grove"></option><option value="Franklin Park"></option><option value="Glen Ellyn"></option><option value="Glencoe"></option><option value="Glendale Heights"></option><option value="Glenview"></option><option value="Grayslake"></option><option value="Gurnee"></option><option value="Hanover Park"></option><option value="Harwood Heights"></option><option value="Highland Park"></option><option value="Highwood"></option><option value="Hillside"></option><option value="Hinsdale"></option><option value="Hoffman Estates"></option><option value="Ingleside"></option><option value="Inverness"></option><option value="Island Lake"></option><option value="Itasca"></option><option value="La Grange Park"></option><option value="Lake Barrington"></option><option value="Lake Bluff"></option><option value="Lake Forest"></option><option value="Lake Villa"></option><option value="Lake in the Hills"></option><option value="Libertyville"></option><option value="Lincolnwood"></option><option value="Lindenhurst"></option><option value="Lisle"></option><option value="Lombard"></option><option value="Melrose Park"></option><option value="Morton Grove"></option><option value="Mount Prospect"></option><option value="Mundelein"></option><option value="Niles"></option><option value="Norridge"></option><option value="North Barrington"></option><option value="North Chicago"></option><option value="Northbrook"></option><option value="Northfield"></option><option value="Norwood Park"></option><option value="Oak Brook"></option><option value="Oakbrook Terrace"></option><option value="Palatine"></option><option value="Park Ridge"></option><option value="Prospect Heights"></option><option value="River Grove"></option><option value="Rolling Meadows"></option><option value="Roselle"></option><option value="Rosemont"></option><option value="Round Lake"></option><option value="Round Lake Beach"></option><option value="Round Lake Heights"></option><option value="Round Lake Park"></option><option value="Saint Charles"></option><option value="Schaumburg"></option><option value="Skokie"></option><option value="South Barrington"></option><option value="South Elgin"></option><option value="Streamwood"></option><option value="Third Lake"></option><option value="Vernon Hills"></option><option value="Villa Park"></option><option value="Volo"></option><option value="Wauconda"></option><option value="Waukegan"></option><option value="Wayne"></option><option value="West Dundee"></option><option value="Westchester"></option><option value="Western Springs"></option><option value="Westmont"></option><option value="Wheaton"></option><option value="Wheeling"></option><option value="Winnetka"></option><option value="Winthrop Harbor"></option><option value="Wood Dale"></option><option value="Zion"></option></datalist></div>
      <div class="field"><label for="s-min">Min price</label><select id="s-min" name="min"><option value="">No min</option><option value="100000">$100K</option><option value="200000">$200K</option><option value="300000">$300K</option><option value="400000">$400K</option><option value="500000">$500K</option><option value="600000">$600K</option><option value="750000">$750K</option><option value="1000000">$1M</option></select></div>
      <div class="field"><label for="s-max">Max price</label><select id="s-max" name="max"><option value="">No max</option><option value="300000">$300K</option><option value="400000">$400K</option><option value="500000">$500K</option><option value="600000">$600K</option><option value="750000">$750K</option><option value="1000000">$1M</option><option value="2000000">$2M</option></select></div>
      <div class="field"><label for="s-bed">Beds</label><select id="s-bed" name="beds"><option value="">Any</option><option value="1">1+</option><option value="2">2+</option><option value="3">3+</option><option value="4">4+</option><option value="5">5+</option></select></div>
      <div class="field"><label for="s-bath">Baths</label><select id="s-bath" name="baths"><option value="">Any</option><option value="1">1+</option><option value="2">2+</option><option value="3">3+</option><option value="4">4+</option></select></div>
      <button class="btn btn--primary" type="submit">Search homes</button>
    </form>
  </div>
</section>

<!-- Team -->
<section class="section section--tight" id="team" style="padding-top:0">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">The family behind the team</p>
      <h2 class="h2">Born and raised here. Still here.</h2>
      <p class="lead">The Dawn Simmons Team isn’t a marketing name — it’s literally a mother and son who grew up in these neighborhoods and now help other families put down roots of their own.</p>
    </div>
    <div class="team-grid">
      <div class="member rv">
        <div class="ph"><div class="ph-frame">
          <img src="/images/dawn-simmons.jpg" alt="Dawn Simmons, REALTOR and Broker at RE/MAX Suburban" loading="lazy" onerror="this.closest(&#39;.ph&#39;).classList.add(&#39;noimg&#39;)">
          <div class="ph-fallback"><span class="mono">D</span><small>Dawn Simmons</small></div>
        </div></div>
        <div>
          <h3>Dawn Simmons</h3>
          <p class="role">REALTOR® · Broker · RE/MAX Hall of Fame</p>
          <p>Moved to Prospect Heights in 1988, raised three boys here, and has been selling homes in the northwest suburbs since 2001 — {{ \App\Support\TeamStats::soldTotal() }} transactions and counting. All three sons still call Prospect Heights home.</p>
          <a class="link-arrow" href="/team#dawn">Read Dawn’s story →</a>
        </div>
      </div>
      <div class="member rv">
        <div class="ph"><div class="ph-frame">
          <img src="/images/josh-simmons.jpg" alt="Josh Simmons, Broker Associate at RE/MAX Suburban" loading="lazy" onerror="this.closest(&#39;.ph&#39;).classList.add(&#39;noimg&#39;)">
          <div class="ph-fallback"><span class="mono">J</span><small>Josh Simmons</small></div>
        </div></div>
        <div>
          <h3>Josh Simmons</h3>
          <p class="role">REALTOR® · Broker Associate</p>
          <p>The middle of Dawn’s three boys, a DePaul grad, and full-time on the team since college. Brings the energy, the hustle, and the local knowledge that only comes from growing up here.</p>
          <a class="link-arrow" href="/team#josh">Read Josh’s story →</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Reviews -->
<section class="section section--mist" id="reviews">
  <div class="wrap">
    <div class="rev-head rv">
      <p class="eyebrow" style="justify-content:center">Client reviews</p>
      <div class="rev-score">4.9</div>
      <div class="rev-stars-lg" aria-hidden="true">★★★★★</div>
      <p class="lead" style="margin:0 auto">Across 62+ Google reviews. Don’t just take our word for it.</p>
    </div>
    <div class="rev-grid">
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Incredibly responsive, transparent, and always quick to answer questions. Made our first home purchase experience 11/10!”</p>
        <div class="rev-name">Mark Kegermann</div>
        <div class="rev-role">First-time buyer · Google review</div>
      </div>
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Dawn is the best Realtor out there. She is kind, efficient, hard working — and my house sold in 2 days.”</p>
        <div class="rev-name">Charles Boyle</div>
        <div class="rev-role">Seller · Google review</div>
      </div>
      <div class="rev-card rv">
        <div class="rev-stars" aria-hidden="true">★★★★★</div>
        <p class="rev-quote">“Dawn did us good again. She is the absolute best. Also Josh is super — he learned well from Mom.”</p>
        <div class="rev-name">Kurt Koziol</div>
        <div class="rev-role">Repeat client · Google review</div>
      </div>
    </div>
    <p style="margin-top:2rem" class="rv"><a class="link-arrow" href="/reviews">Read all client reviews →</a></p>
  </div>
</section>

<!-- Blog -->
<section class="section" id="blog">
  <div class="wrap">
    <div class="sec-head rv">
      <p class="eyebrow">From the blog</p>
      <h2 class="h2">Local insights, straight talk.</h2>
    </div>
    <div class="blog-grid">
      <a class="blog-card rv" href="/blog/2026-08-northwest-suburbs-market-update">
        <span class="blog-cat">Market update</span>
        <h3>Northwest Suburbs Market Update — August 2026</h3>
        <p>Palatine’s single-family median jumps 16.4% to $600K, Arlington Heights runs $315K to $2.65M, and Wheeling’s dozen-home squeeze grinds on.</p>
        <span class="link-arrow">Read the update →</span>
      </a>
      <a class="blog-card rv" href="/blog/prospect-heights-il-living-guide">
        <span class="blog-cat">Neighborhood guide</span>
        <h3>Is Prospect Heights a Good Place to Live?</h3>
        <p>A local’s honest guide from a family that’s been here since 1988.</p>
        <span class="link-arrow">Read the guide →</span>
      </a>
      <a class="blog-card rv" href="/blog/first-time-homebuyer-guide-northwest-suburbs">
        <span class="blog-cat">First-time buyers</span>
        <h3>First-Time Homebuyer Guide to the NW Suburbs</h3>
        <p>The practical advice we give every first-time buyer, start to finish.</p>
        <span class="link-arrow">Read the guide →</span>
      </a>
    </div>
  </div>
</section>

<!-- Video band -->
<section class="section section--ink video-band" id="videos">
  <div class="wrap">
    <div class="rv">
      <p class="eyebrow">Video walkthroughs</p>
      <h2 class="h2" style="color:#fff">See homes before you visit.</h2>
      <p class="lead" style="margin-top:.9rem">Real neighborhood and home tours from Josh, shot on location — so you can get a feel for a space before you ever step inside.</p>
    </div>
    <div class="views rv">
      <div><div class="stat-num">10M+</div><div class="stat-label">views on a single home tour</div></div>
      <a class="btn btn--light" href="https://www.instagram.com/joshsimmonsre/">Follow @joshsimmonsre →</a>
    </div>
  </div>
</section>

<!-- Property management -->
<section class="section section--tight">
  <div class="wrap">
    <div class="pm rv">
      <div>
        <h3>Own a rental? We’ll manage it for you.</h3>
        <p>Tenant screening, rent collection, and maintenance — handled start to finish. Flat-rate pricing from $100/month.</p>
      </div>
      <div class="pm-actions">
        <a class="btn btn--primary" href="/property-management">Learn about property management</a>
        <a class="btn btn--ghost" href="tel:8477381884">Call (847) 738-1884</a>
      </div>
    </div>
  </div>
</section>

<!-- Contact -->
<section class="section section--mist" id="contact">
  <div class="wrap">
    <div class="contact-grid">
      <div class="rv">
        <p class="eyebrow">Get in touch</p>
        <h2 class="h2">Let’s talk about your next move.</h2>
        <p class="lead" style="margin-top:.9rem">Buying, selling, or just exploring your options — reach out and we’ll get back to you within 24 hours.</p>
        <div class="direct">
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 4h3l1.5 4L7.5 9.5a11 11 0 0 0 5 5L14 12.5 18 14v3a2 2 0 0 1-2 2A13 13 0 0 1 3 6a2 2 0 0 1 2-2Z"></path></svg></div>
            <div><strong>Dawn Simmons</strong><a href="tel:8477381884">(847) 738-1884</a><br><span>REALTOR® · Broker · RE/MAX Suburban</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 4h10a1.5 1.5 0 0 1 1.5 1.5v13A1.5 1.5 0 0 1 17 20H7a1.5 1.5 0 0 1-1.5-1.5v-13A1.5 1.5 0 0 1 7 4Z"></path><path d="M10.5 17h3"></path></svg></div>
            <div><strong>Josh Simmons</strong><a href="tel:2246284013">(224) 628-4013</a><br><span>REALTOR® · Broker Associate</span></div>
          </div>
          <div class="direct-item">
            <div class="icon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3.5" y="5.5" width="17" height="13" rx="2"></rect><path d="m4.5 7 7.5 5.5L19.5 7"></path></svg></div>
            <div><strong>Email</strong><a href="mailto:simsre2000@yahoo.com">simsre2000@yahoo.com</a></div>
          </div>
        </div>
        <p class="direct-note">Those are our personal cells — call or text any day, 7 days a week.</p>
      </div>
      <x-site.contact-form />
    </div>
  </div>
</section>


<script>
// Area tabs
var tabs = document.querySelectorAll('.tab');
tabs.forEach(function (tab) {
  tab.addEventListener('click', function () {
    tabs.forEach(function (t) {
      t.setAttribute('aria-selected', 'false');
      document.getElementById(t.getAttribute('aria-controls')).hidden = true;
    });
    tab.setAttribute('aria-selected', 'true');
    document.getElementById(tab.getAttribute('aria-controls')).hidden = false;
  });
});

// Scroll reveal
if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); }
    });
  }, { threshold: 0.12 });
  document.querySelectorAll('.rv').forEach(function (el) { io.observe(el); });
} else {
  document.querySelectorAll('.rv').forEach(function (el) { el.classList.add('in'); });
}

</script>
@include('components.home.value-logic')
</x-site.layout>
