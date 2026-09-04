<x-site.layout :page="null" :head="null" title="Homes for Sale — Northwest Suburbs | The Dawn Simmons Team">
@php
  $dwLabel = ['detached' => 'Detached Homes', 'attached' => 'Condos & Townhomes', 'multi' => '2–4 Unit Buildings', 'multi5' => '5+ Unit Buildings'][$filters['dwelling'] ?? ''] ?? 'Homes';
  $cityList = array_map(fn ($c) => \Illuminate\Support\Str::title($c), (array) ($filters['city'] ?? []));
  $cityDisplay = match (true) { count($cityList) === 1 => $cityList[0], count($cityList) === 2 => implode(' & ', $cityList), count($cityList) > 2 => count($cityList).' Cities', default => null };
  $placeLabel = $cityDisplay ? ($cityDisplay.(count($cityList) === 1 ? ', IL' : '')) : 'the Northwest Suburbs';
  $pageHeading = "{$dwLabel} for Sale in {$placeLabel}";
@endphp
<x-slot:headExtra>
<title>{{ $pageHeading }} | The Dawn Simmons Team – RE/MAX Suburban</title>
<meta name="description" content="Search homes for sale across Chicago's northwest suburbs with the Dawn Simmons Team, RE/MAX Suburban. Listings courtesy of MRED as distributed by MLS GRID, updated throughout the day.">
<meta name="robots" content="{{ config('services.mlsgrid.token') ? 'index,follow' : 'noindex,nofollow' }}">
</x-slot:headExtra>
<style>
  .li-wrap { max-width:1180px; margin:0 auto; padding:32px 24px; font-family:Arial,sans-serif; }
  .li-hero { background:linear-gradient(180deg,#0B1622,#0F1E2E); color:#fff; text-align:center; padding:52px 24px 40px; }
  .li-hero h1 { font-family:'Fraunces',Georgia,serif; font-size:clamp(26px,4vw,42px); margin:0 0 10px; }
  .li-hero p { color:rgba(255,255,255,.8); margin:0; }
  .li-filters { display:flex; flex-wrap:wrap; gap:10px; align-items:end; background:#fff; border:1px solid #e0e4ed; border-radius:10px; padding:16px; margin:-28px auto 28px; max-width:1000px; box-shadow:0 6px 24px rgba(15,30,46,.12); position:relative; }
  .li-filters label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#0F1E2E; display:block; }
  .li-filters select, .li-filters input { padding:9px 10px; border:1px solid #c9d2e3; border-radius:6px; font-size:14px; min-width:120px; }
  .li-filters button { background:#C8102E; color:#fff; border:0; border-radius:999px; padding:11px 22px; font-weight:700; cursor:pointer; }
  .li-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
  .li-card { display:block; background:#fff; border:1px solid #e0e4ed; border-radius:10px; overflow:hidden; text-decoration:none; color:#222; transition:box-shadow .15s; }
  .li-card:hover { box-shadow:0 8px 28px rgba(15,30,46,.16); }
  .li-photo { aspect-ratio:3/2; background:#e9edf3 center/cover no-repeat; position:relative; }
  .li-status { position:absolute; top:10px; left:10px; background:#0F1E2E; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; z-index:2; }
  /* Card photo carousel: swipe through cached photos without opening the
     listing. Only the first image ships a src; the rest hydrate on first
     touch/scroll so a page of cards doesn't load 200+ images. */
  .li-strip { position:relative; }
  .li-track { display:flex; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; aspect-ratio:3/2; background:#e9edf3; }
  .li-track::-webkit-scrollbar { display:none; }
  .li-track img { flex:0 0 100%; width:100%; height:100%; object-fit:cover; scroll-snap-align:start; scroll-snap-stop:always; }
  .li-count { position:absolute; right:8px; bottom:8px; background:rgba(8,13,20,.62); color:#fff; font-size:11px; font-weight:700; padding:3px 9px; border-radius:999px; pointer-events:none; }
  .li-arr { position:absolute; top:50%; transform:translateY(-50%); width:30px; height:44px; border:0; border-radius:6px; background:rgba(255,255,255,.88); color:#0F1E2E; font-size:22px; line-height:1; cursor:pointer; display:none; z-index:2; padding:0; }
  .li-arr-l { left:6px; } .li-arr-r { right:6px; }
  @media (hover:hover) { .li-strip:hover .li-arr:not([hidden]) { display:block; } }
  .li-body { padding:16px; }
  .li-price { font-size:22px; font-weight:800; color:#0F1E2E; }
  .li-pay { font-size:13.5px; font-weight:800; color:#C8102E; background:#FDECEF; border-radius:6px; padding:2px 8px; vertical-align:2px; white-space:nowrap; }
  .li-addr { font-size:14px; color:#444; margin:4px 0 8px; }
  .li-meta { font-size:13px; color:#666; }
  .li-office { font-size:11.5px; color:#888; margin-top:8px; border-top:1px dashed #e0e4ed; padding-top:8px; }
  .li-filters .fl-pop-btn, .fl-pop-btn { padding:9px 12px; border:1px solid #c9d2e3; border-radius:6px; font-size:14px; background:#fff; cursor:pointer; font-family:inherit; color:#0F1E2E; min-width:130px; text-align:left; }
  .fl-pop { position:absolute; top:100%; left:0; z-index:30; background:#fff; border:1px solid #c9d2e3; border-radius:10px; padding:12px 16px; box-shadow:0 12px 32px rgba(15,30,46,.16); max-height:300px; overflow:auto; min-width:310px; }
  .fl-pop--right { left:auto; right:0; }
  .li-filters .fl-pop label.fl-check, .fl-check { display:flex; gap:9px; align-items:center; justify-content:flex-start; font-size:13.5px; padding:5px 0; text-transform:none; letter-spacing:0; font-weight:500; color:#0F1E2E; cursor:pointer; white-space:nowrap; }
  .li-filters .fl-pop label.fl-check input[type=checkbox] { margin:0; flex:none; width:15px; height:15px; min-width:0; padding:0; border:0; }
  .li-filters .fl-pop input.fl-city-q { display:block; width:100%; min-width:0; margin:0 0 8px; padding:8px 10px; border:1px solid #c9d2e3; border-radius:6px; font-size:13.5px; position:sticky; top:-12px; background:#fff; box-shadow:0 4px 8px #fff; }
  .fl-pop-actions { display:flex; gap:8px; position:sticky; bottom:-12px; background:#fff; padding:10px 0 12px; border-top:1px solid #E9EFF3; margin-top:8px; }
  .li-filters .fl-pop-actions button { min-width:0; }
  .fl-pop-actions .fl-apply { background:#C8102E; color:#fff; border:0; border-radius:999px; padding:8px 18px; font-weight:700; font-size:13px; cursor:pointer; }
  .fl-pop-actions .fl-clear { background:none; border:1px solid #c9d2e3; color:#48586B; border-radius:999px; padding:8px 14px; font-weight:600; font-size:13px; cursor:pointer; }
  .demo-banner { background:#fff7e0; border:1px solid #e2cd86; color:#7a5d12; border-radius:8px; padding:12px 16px; margin:0 auto 22px; max-width:1000px; font-size:14px; }
  .lv-toggle { display:flex; border:1px solid #c9d2e3; border-radius:999px; overflow:hidden; background:#fff; }
  .li-filters ~ * .lv-toggle button, .lv-toggle button { background:#fff; color:#48586B; border:0; border-radius:0; padding:8px 18px; font-size:13.5px; font-weight:700; cursor:pointer; min-width:0; }
  .lv-toggle button.on { background:#0F1E2E; color:#fff; }
  #lmap { height:72vh; min-height:420px; border-radius:12px; border:1px solid #DEE6EE; background:#eef1f6; }
  .lmap-key { display:inline-block; width:10px; height:10px; border-radius:50%; vertical-align:-1px; }
  /* Map pins — same dot language as the sales map (.dsm-pin). */
  .lmk { box-sizing:border-box; width:13px; height:13px; border-radius:50%; background:#0F1E2E; border:2px solid #fff; box-shadow:0 1px 5px rgba(0,0,0,.45); cursor:pointer; transition:transform .12s; }
  .lmk:hover { transform:scale(1.5); }
  .lmk--a { background:#C8102E; }
  #lmap .gm-style-iw-d { overflow:auto !important; }
  /* Mobile default: one view at a time, driven by the List|Map toggle */
  .li-mapcol { display:none; }
  .is-map .li-mapcol { display:block; }
  .is-map .li-results { display:none; }
  /* Desktop: portal split — sticky map beside the scrolling results */
  @media (min-width:1000px) {
    .lv-toggle { display:none; }
    .li-split { display:grid; grid-template-columns:minmax(0,1fr) minmax(0,1fr); gap:20px; align-items:start; }
    .li-results { display:block !important; }
    .li-mapcol { display:block !important; position:sticky; top:76px; }
    #lmap { height:calc(100vh - 165px); }
    .li-results .li-grid { grid-template-columns:repeat(auto-fill,minmax(235px,1fr)); }
  }
</style>

<div class="li-hero">
  <h1>{{ $pageHeading }}</h1>
  <p>{{ ($filters['city'] ?? null) ? 'Every active '.strtolower($dwLabel === 'Homes' ? 'listing' : $dwLabel).' in '.$cityDisplay : 'Every active listing in the communities we serve' }} &mdash; updated throughout the day from the MLS via MLS GRID.</p>
</div>

<div class="li-wrap">
  @if($demo)
    <div class="demo-banner"><strong>Display preview:</strong> the listings below are clearly-marked sample records used to preview this page's layout. Live MRED listing data activates upon MLS GRID approval.</div>
  @endif

  <form class="li-filters" method="get" action="/listings">
    @php $selCities = array_map('mb_strtolower', (array) ($filters['city'] ?? [])); @endphp
    <div x-data="{open:false, q:'', n:{{ count($selCities) }}}" style="position:relative"><label>Cities</label>
      <button type="button" class="fl-pop-btn" @click="open=!open"><span x-text="n ? n+' selected' : 'All cities'">{{ count($selCities) ? count($selCities).' selected' : 'All cities' }}</span> &#9662;</button>
      <div class="fl-pop" x-show="open" x-cloak @click.outside="open=false" x-ref="pop"
           @change="n = $refs.pop.querySelectorAll('input[type=checkbox]:checked').length">
        <input type="search" class="fl-city-q" placeholder="Type a city&hellip;" x-model="q" @keydown.enter.prevent>
        @foreach($cities as $c)<label class="fl-check" x-show="!q || $el.textContent.toLowerCase().includes(q.toLowerCase())"><input type="checkbox" name="city[]" value="{{ $c }}" @checked(in_array(mb_strtolower($c), $selCities))> {{ $c }}</label>@endforeach
        <div class="fl-pop-actions">
          <button type="submit" class="fl-apply">Apply</button>
          <button type="button" class="fl-clear" x-show="n > 0" @click="$refs.pop.querySelectorAll('input[type=checkbox]').forEach(i => i.checked = false); n = 0">Clear all</button>
        </div>
      </div>
    </div>
    <div><label for="f-min">Min Price</label><select id="f-min" name="min"><option value="">No min</option>@foreach([200000,300000,400000,500000,750000,1000000] as $v)<option value="{{ $v }}" @selected(($filters['min'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-max">Max Price</label><select id="f-max" name="max"><option value="">No max</option>@foreach([300000,400000,500000,750000,1000000,2000000] as $v)<option value="{{ $v }}" @selected(($filters['max'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-beds">Beds</label><select id="f-beds" name="beds"><option value="">Any</option>@foreach([1,2,3,4,5] as $v)<option value="{{ $v }}" @selected(($filters['beds'] ?? '') == $v)>{{ $v }}+</option>@endforeach</select></div>
    <div><label for="f-dwelling">Home type</label><select id="f-dwelling" name="dwelling"><option value="">All types</option>@foreach(['detached' => 'Detached homes', 'attached' => 'Attached (condo/townhome)', 'multi' => '2–4 unit buildings', 'multi5' => '5+ unit buildings'] as $v => $label)<option value="{{ $v }}" @selected(($filters['dwelling'] ?? '') === $v)>{{ $label }}</option>@endforeach</select></div>
    <div x-data="{open:false}" style="position:relative"><label>More</label>
      <button type="button" class="fl-pop-btn" @click="open=!open">More filters &#9662;</button>
      <div class="fl-pop fl-pop--right" x-show="open" x-cloak @click.outside="open=false">
        <label class="fl-check"><input type="checkbox" name="ffmaster" value="1" @checked($filters['ffmaster'] ?? false)> First-floor master bedroom</label>
        <label class="fl-check"><input type="checkbox" name="masterbath" value="1" @checked($filters['masterbath'] ?? false)> Full master bath</label>
        <label class="fl-check"><input type="checkbox" name="ranch" value="1" @checked($filters['ranch'] ?? false)> Ranch / single story</label>
        <label class="fl-check"><input type="checkbox" name="waterfront" value="1" @checked($filters['waterfront'] ?? false)> &#127754; Waterfront only</label>
        <label class="fl-check"><input type="checkbox" name="nohoa" value="1" @checked($filters['nohoa'] ?? false)> No HOA</label>
        <label class="fl-check"><input type="checkbox" name="reduced" value="1" @checked($filters['reduced'] ?? false)> &#128201; Price reduced</label>
        <label class="fl-check"><input type="checkbox" name="available" value="1" @checked($filters['available'] ?? false)> &#9989; Available only (hide under contract)</label>
        <div style="border-top:1px solid #E9EFF3;margin:8px 0 4px"></div>
        <label class="fl-check" style="justify-content:space-between !important">Basement
          <select name="basement" style="padding:5px 8px;border:1px solid #c9d2e3;border-radius:6px;font-size:13px;"><option value="">Any</option><option value="1" @selected(($filters['basement'] ?? '') === '1')>Has basement</option><option value="finished" @selected(($filters['basement'] ?? '') === 'finished')>Finished</option></select>
        </label>
        <label class="fl-check" style="justify-content:space-between !important">Built after
          <select name="built" style="padding:5px 8px;border:1px solid #c9d2e3;border-radius:6px;font-size:13px;"><option value="">Any</option>@foreach([1980,1990,2000,2010,2020] as $y)<option value="{{ $y }}" @selected(($filters['built'] ?? '') == $y)>{{ $y }}+</option>@endforeach</select>
        </label>
        <label class="fl-check" style="justify-content:space-between !important">Garage spaces
          <select name="garage" style="padding:5px 8px;border:1px solid #c9d2e3;border-radius:6px;font-size:13px;"><option value="">Any</option>@foreach([1,2,3] as $g)<option value="{{ $g }}" @selected(($filters['garage'] ?? '') == $g)>{{ $g }}+</option>@endforeach</select>
        </label>
        <label class="fl-check" style="justify-content:space-between !important">Rate assumption %
          <input name="rate" inputmode="decimal" value="{{ $filters['rate'] ?? '' }}" placeholder="{{ number_format($payRate ?? 6.1, 2) }}" style="width:70px;padding:5px 8px;border:1px solid #c9d2e3;border-radius:6px;font-size:13px;min-width:0;">
        </label>

      </div>
    </div>
    <div><label for="f-down">Down payment</label><input id="f-down" name="down" inputmode="numeric" placeholder="$60,000" value="{{ ($filters['down'] ?? '') !== '' ? number_format((int) $filters['down']) : '' }}" style="width:120px" onchange="this.value=this.value.replace(/[^0-9]/g,'')"></div>
    <div><label for="f-pay">Max monthly</label><input id="f-pay" name="payment" inputmode="numeric" placeholder="$3,000/mo" value="{{ ($filters['payment'] ?? '') !== '' ? number_format((int) $filters['payment']) : '' }}" style="width:120px" onchange="this.value=this.value.replace(/[^0-9]/g,'')"></div>
    <div><label for="f-sort">Sort</label><select id="f-sort" name="sort" onchange="this.form.submit()"><option value="">{{ ($filters['payment'] ?? false) ? 'Most house for the budget' : 'Just updated' }}</option><option value="new" @selected(($filters['sort'] ?? '') === 'new')>Newest listed</option><option value="price" @selected(($filters['sort'] ?? '') === 'price')>Price: low to high</option><option value="price-desc" @selected(($filters['sort'] ?? '') === 'price-desc')>Price: high to low</option></select></div>
    <div><button type="submit">Search</button></div>
  </form>

  @if(session('alert_saved'))
  <div style="background:#EAF7EF;border:1px solid #b7dfc3;color:#1d6b35;border-radius:10px;padding:12px 16px;margin:0 0 18px;font-size:14px;">
    &#10003; <strong>Search saved!</strong> We'll email you when new homes match: {{ session('alert_saved') }}. Unsubscribe anytime from the email.
  </div>
  @endif

  <div style="display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:space-between;background:#F2F5F9;border:1px solid #DEE6EE;border-radius:10px;padding:14px 16px;margin:0 0 18px;">
    <div style="font-size:14px;color:#0F1E2E;"><strong>&#128276; Never miss a listing.</strong>
      <span style="color:#48586B;">Save this search and we'll email you the moment new matches hit the MLS.</span></div>
    <form method="post" action="/listings/alerts" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0;">
      @csrf
      @foreach(array_filter($filters ?? []) as $k => $v)@foreach((array) $v as $vv)<input type="hidden" name="{{ $k }}{{ is_array($v) ? '[]' : '' }}" value="{{ $vv }}">@endforeach @endforeach
      <input type="text" name="name" placeholder="First name (optional)" style="padding:9px 12px;border:1px solid #c9d2e3;border-radius:6px;font-size:14px;width:150px;">
      <input type="email" name="email" required placeholder="you@email.com" style="padding:9px 12px;border:1px solid #c9d2e3;border-radius:6px;font-size:14px;width:190px;">
      <button type="submit" style="background:#C8102E;color:#fff;border:0;border-radius:999px;padding:10px 18px;font-weight:700;font-size:13.5px;cursor:pointer;">Save search + get alerts</button>
    </form>
  </div>

  <div style="background:#0F1E2E;color:#fff;border-radius:10px;padding:16px 18px;margin:0 0 18px;font-size:14px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <div style="max-width:640px;line-height:1.6;">&#128274; <strong>What you can&rsquo;t see here: private listings.</strong>
      <span style="color:rgba(255,255,255,.75);">Some homes sell through MRED&rsquo;s Private Listing Network and never appear on any public site &mdash; not here, not Zillow. Agents can only share them directly. Tell us what you&rsquo;re looking for and we&rsquo;ll watch the PLN for you.</span></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="/contact?pln={{ urlencode($cityList[0] ?? 'the northwest suburbs') }}" style="background:#C8102E;color:#fff;font-weight:700;font-size:13.5px;padding:10px 18px;border-radius:999px;text-decoration:none;white-space:nowrap;">Get private matches &rarr;</a>
      <a href="/off-market-homes" style="border:1px solid rgba(255,255,255,.4);color:#fff;font-weight:700;font-size:13.5px;padding:10px 18px;border-radius:999px;text-decoration:none;white-space:nowrap;">How it works</a>
    </div>
  </div>

  {{-- Desktop: portal-style split (scrolling cards + sticky map, both always
       on). Mobile: the List|Map toggle, since side-by-side can't fit. --}}
  <div x-data="{ view: 'list' }" :class="{ 'is-map': view === 'map' }"
       x-init="window.matchMedia('(min-width:1000px)').matches && window.initListingsMap && initListingsMap()">
  <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin:0 0 18px;">
    <p style="font-size:14px;color:#666;margin:0;">{{ number_format($total) }} {{ Str::plural('listing', $total) }} found{{ $cityDisplay ? ' in '.$cityDisplay : '' }}{{ ($payMode ?? false) ? ' within your monthly budget' : '' }}.
      @if($payMode ?? false)<br><span style="font-size:12px;color:#8A99AA;">Payments assume {{ number_format($payRate, 2) }}% 30-yr fixed{{ (int) ($filters['down'] ?? 0) ? ' with $'.number_format((int) $filters['down']).' down' : '' }} + each home's <strong>actual property tax</strong> and HOA + est. insurance/PMI where applicable. An estimate, not a loan offer &mdash; verify with your lender.</span>@endif</p>
    <div class="lv-toggle" role="tablist" aria-label="Results view">
      <button type="button" :class="{ on: view === 'list' }" class="on" @click="view = 'list'">&#9776; List</button>
      <button type="button" :class="{ on: view === 'map' }" @click="view = 'map'; window.initListingsMap && initListingsMap()">&#128506; Map</button>
    </div>
  </div>

  <div class="li-split">
  <div class="li-mapcol">
    <div id="lmap"></div>
    <p style="font-size:12.5px;color:#8A99AA;margin:10px 0 0;">
      <span class="lmap-key" style="background:#C8102E"></span> Active &nbsp;
      <span class="lmap-key" style="background:#0F1E2E"></span> Under contract &nbsp;&middot;&nbsp;
      Up to 1,500 mapped listings for this search; homes without a mappable address appear in the list only.
    </p>
  </div>

  <div class="li-results">
  <div class="li-grid">
    @foreach($listings as $l)
    {{-- Thumbnail: ≤8 objective fields, no site branding, links to the fully compliant detail page (Rules 10, 13, 22 exemptions) --}}
    <a class="li-card" href="{{ $l->url() }}">
      @php $ph = array_slice($l->photoUrls(), 0, 10); @endphp
      @if(count($ph) > 1)
      <div class="li-strip">
        <div class="li-track" data-n="{{ count($ph) }}">
          @foreach($ph as $i => $p)
          <img @if($i === 0) src="{{ $p }}" loading="lazy" @else data-src="{{ $p }}" @endif alt="">
          @endforeach
        </div>
        <button type="button" class="li-arr li-arr-l" aria-label="Previous photo" hidden>&#8249;</button>
        <button type="button" class="li-arr li-arr-r" aria-label="Next photo">&#8250;</button>
        <span class="li-count">1/{{ count($ph) }}</span>
        <span class="li-status">{{ $l->status }}</span>
      </div>
      @else
      <div class="li-photo" style="background-image:url('{{ $l->photoUrl() ?? '' }}')"><span class="li-status">{{ $l->status }}</span></div>
      @endif
      <div class="li-body">
        <div class="li-price">{{ $l->list_price ? '$'.number_format($l->list_price) : ($l->is_auction ? 'Auction — see details' : 'Price on request') }}@if(isset($l->est_monthly)) <span class="li-pay">&asymp; ${{ number_format($l->est_monthly) }}/mo</span>@endif</div>
        <div class="li-addr">{{ $l->displayAddress() }}</div>
        <div class="li-meta">{{ $l->beds }} bd &middot; {{ $l->baths() }} ba &middot; {{ $l->sqft ? number_format($l->sqft).' sqft' : '—' }} &middot; MLS #{{ $l->listing_id }}</div>
        <div class="li-office">Listing courtesy of {{ $l->list_office_name }}</div>
      </div>
    </a>
    @endforeach
  </div>

  @if($pages > 1)
  <div style="text-align:center;margin-top:28px;">
    @for($i = 1; $i <= $pages; $i++)
      <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" style="display:inline-block;margin:2px;padding:8px 13px;border-radius:6px;font-weight:700;text-decoration:none;{{ $i === $page ? 'background:#0F1E2E;color:#fff;' : 'background:#fff;border:1px solid #c9d2e3;color:#0F1E2E;' }}">{{ $i }}</a>
    @endfor
  </div>
  @endif
  </div>{{-- /.li-results --}}
  </div>{{-- /.li-split --}}
  </div>{{-- /view state --}}
</div>

@php $gmapsKey = config('services.google.maps_key'); @endphp
<script>
// Map view on the same Google basemap as the homepage/sold sales map —
// one visual language for every map on the site. Pins come from
// /listings/map-data with the current search's query string, so map and
// list always agree. Loads on first open.
window.__gmapsReady ||= new Promise(function (resolve) {
  if (window.google && window.google.maps && window.google.maps.importLibrary) return resolve();
  window.__gmapsInit = function () { resolve(); };
  var s = document.createElement('script');
  s.src = 'https://maps.googleapis.com/maps/api/js?key={{ $gmapsKey }}&v=weekly&loading=async&callback=__gmapsInit';
  s.async = true; s.defer = true;
  document.head.appendChild(s);
});

window.initListingsMap = (function () {
  var started = false;
  return function () {
    if (started) return; started = true;
    build();
  };

  function esc(s) {
    return String(s == null ? '' : s).replace(/[&<>"']/g, function (c) {
      return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
    });
  }

  function card(p) {
    return '<a href="' + esc(p.u || '/listings/' + p.id) + '" style="display:block;width:210px;text-decoration:none;color:#0F1E2E;font-family:Archivo,Arial,sans-serif;">'
      + (p.ph ? '<div style="aspect-ratio:3/2;border-radius:8px;background:#E9EFF3 center/cover no-repeat;background-image:url(\'' + esc(p.ph) + '\');margin-bottom:8px;"></div>' : '')
      + '<b style="font-size:16px;">' + (p.p ? '$' + Number(p.p).toLocaleString() : 'Auction') + '</b>'
      + '<div style="font-size:12.5px;color:#48586B;">' + esc(p.b) + ' bd &middot; ' + esc(p.ba) + ' ba &middot; ' + esc(p.s) + '</div>'
      + '<div style="font-size:12.5px;color:#48586B;margin-top:3px;line-height:1.4;">' + esc(p.a) + '</div></a>';
  }

  async function build() {
    var pinsReq = fetch('/listings/map-data' + window.location.search).then(function (r) { return r.json(); });
    await window.__gmapsReady;
    var lib = await google.maps.importLibrary('maps');

    // Same HTML-overlay marker as components/sales/map.blade.php.
    function HtmlMarker(map, position, el, onClick) {
      var o = new lib.OverlayView();
      el.style.position = 'absolute';
      if (onClick) el.addEventListener('click', function (e) { e.stopPropagation(); onClick(); });
      o.onAdd = function () { o.getPanes().overlayMouseTarget.appendChild(el); };
      o.draw = function () {
        var p = o.getProjection() && o.getProjection().fromLatLngToDivPixel(new google.maps.LatLng(position));
        if (!p) return;
        var w = el.offsetWidth || 13, h = el.offsetHeight || 13;
        el.style.left = (p.x - w / 2) + 'px'; el.style.top = (p.y - h / 2) + 'px';
      };
      o.onRemove = function () { el.remove(); };
      o.setMap(map);
      return o;
    }

    // Keep in sync with STYLE in components/sales/map.blade.php — the one
    // brand basemap every map on the site shares.
    var STYLE = [
      { elementType: 'geometry', stylers: [{ color: '#f4f6fb' }] },
      { elementType: 'labels.text.fill', stylers: [{ color: '#4a5568' }] },
      { elementType: 'labels.text.stroke', stylers: [{ color: '#f4f6fb' }] },
      { elementType: 'labels.icon', stylers: [{ visibility: 'off' }] },
      { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#c9d1e0' }] },
      { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#0F1E2E' }, { weight: 0.5 }] },
      { featureType: 'administrative.neighborhood', stylers: [{ visibility: 'off' }] },
      { featureType: 'landscape.man_made', elementType: 'geometry', stylers: [{ color: '#f8f6f2' }] },
      { featureType: 'landscape.natural', elementType: 'geometry', stylers: [{ color: '#eef1f6' }] },
      { featureType: 'poi', stylers: [{ visibility: 'off' }] },
      { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#e3ebe0' }, { visibility: 'on' }] },
      { featureType: 'poi.park', elementType: 'labels.text.fill', stylers: [{ color: '#7a8f76' }] },
      { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
      { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#e1e6ef' }] },
      { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#7b8494' }] },
      { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#f3ead2' }] },
      { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#e6d6ab' }] },
      { featureType: 'road.highway', elementType: 'labels.text.fill', stylers: [{ color: '#8a7a4a' }] },
      { featureType: 'road.arterial', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
      { featureType: 'road.local', elementType: 'labels', stylers: [{ visibility: 'off' }] },
      { featureType: 'transit', stylers: [{ visibility: 'off' }] },
      { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c5d5ea' }] },
      { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#0F1E2E' }] },
    ];

    var map = new lib.Map(document.getElementById('lmap'), {
      center: { lat: 42.15, lng: -88.0 }, zoom: 10,
      styles: STYLE,
      backgroundColor: '#eef1f6',
      mapTypeControl: false, streetViewControl: false, fullscreenControl: true,
      zoomControl: true, gestureHandling: 'greedy',
      minZoom: 8, maxZoom: 17, clickableIcons: false,
    });

    var iw = new google.maps.InfoWindow({ disableAutoPan: true, pixelOffset: new google.maps.Size(0, -10) });
    var closeTimer;
    // Let the cursor travel into the card to click through (same feel as
    // the old popup): the card holds the window open while hovered.
    google.maps.event.addListener(iw, 'domready', function () {
      var box = document.querySelector('.gm-style-iw');
      if (!box) return;
      box.addEventListener('mouseenter', function () { clearTimeout(closeTimer); });
      box.addEventListener('mouseleave', function () {
        closeTimer = setTimeout(function () { iw.close(); }, 300);
      });
    });

    var pins = await pinsReq;
    var bounds = new google.maps.LatLngBounds();
    pins.forEach(function (p) {
      var el = document.createElement('div');
      el.className = 'lmk' + (p.s === 'Active' ? ' lmk--a' : '');
      var open = function () {
        clearTimeout(closeTimer);
        iw.setContent(card(p));
        iw.setPosition({ lat: p.lat, lng: p.lng });
        iw.open({ map: map });
      };
      el.addEventListener('mouseenter', open);
      el.addEventListener('mouseleave', function () {
        closeTimer = setTimeout(function () { iw.close(); }, 350);
      });
      HtmlMarker(map, { lat: p.lat, lng: p.lng }, el, open);
      bounds.extend({ lat: p.lat, lng: p.lng });
    });
    if (pins.length) {
      map.fitBounds(bounds, 30);
      google.maps.event.addListenerOnce(map, 'idle', function () {
        if (map.getZoom() > 15) map.setZoom(15);
      });
    }
  }
})();
</script>

<script>
// Card photo carousels: hydrate neighbors of the visible frame on demand,
// keep the counter honest, and let desktop hover-arrows page the strip
// without following the card link.
(function () {
  function hydrate(track, i) {
    for (var k = Math.max(0, i - 1); k <= Math.min(track.children.length - 1, i + 2); k++) {
      var im = track.children[k];
      if (im.dataset.src) { im.src = im.dataset.src; delete im.dataset.src; }
    }
  }
  document.querySelectorAll('.li-track').forEach(function (track) {
    var strip = track.parentNode,
        count = strip.querySelector('.li-count'),
        prev = strip.querySelector('.li-arr-l'),
        next = strip.querySelector('.li-arr-r'),
        n = parseInt(track.dataset.n, 10) || 1;
    function idx() { return Math.round(track.scrollLeft / track.clientWidth); }
    ['touchstart', 'mouseenter'].forEach(function (ev) {
      track.addEventListener(ev, function () { hydrate(track, idx()); }, { passive: true, once: true });
    });
    track.addEventListener('scroll', function () {
      var i = idx();
      hydrate(track, i);
      if (count) count.textContent = (i + 1) + '/' + n;
      if (prev) prev.hidden = i === 0;
      if (next) next.hidden = i >= n - 1;
    }, { passive: true });
    [[prev, -1], [next, 1]].forEach(function (pair) {
      if (!pair[0]) return;
      pair[0].addEventListener('click', function (e) {
        e.preventDefault();
        hydrate(track, idx() + pair[1]);
        track.scrollBy({ left: pair[1] * track.clientWidth, behavior: 'smooth' });
      });
    });
  });
})();
</script>

@include('listings._compliance')
</x-site.layout>
