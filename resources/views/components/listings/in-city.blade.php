{{-- Live MLS band injected into legacy city/neighborhood/condo pages by
     PageController::injectListingsBand. Fully self-contained styling + vanilla
     JS (host pages carry their own imported CSS and may not load Alpine).
     City pages show a Detached panel by default with an Attached toggle;
     subdivision-matched pages show a single panel. Cards are Rule-10
     thumbnails (≤8 fields, linked to the full compliant display); the stats
     strip is market analytics, attributed via the compliance block below. --}}
<section class="dshl{{ ($embedded ?? false) ? ' dshl--embed' : '' }}" id="live-listings">
<style>
  .dshl { font-family:'Archivo',Arial,sans-serif; background:#F2F5F9; padding:52px 24px; }
  .dshl--embed { background:transparent; padding:8px 0 0; }
  .dshl-in { max-width:1180px; margin:0 auto; }
  .dshl-eyebrow { font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:#C8102E; margin-bottom:10px; }
  .dshl h2 { font-family:Georgia,'Fraunces',serif; font-size:clamp(22px,3vw,32px); color:#0F1E2E; margin:0 0 6px; }
  .dshl-asof { font-size:12.5px; color:#8A99AA; margin:0 0 18px; }
  .dshl-tabs { display:flex; gap:8px; margin:0 0 20px; flex-wrap:wrap; }
  .dshl-tab { border:1px solid #DEE6EE; background:#fff; color:#48586B; font-family:inherit; font-size:13.5px; font-weight:700; padding:9px 18px; border-radius:999px; cursor:pointer; }
  .dshl-tab[aria-selected="true"] { background:#0F1E2E; border-color:#0F1E2E; color:#fff; }
  .dshl-panel[hidden] { display:none; }
  .dshl-stats { display:flex; flex-wrap:wrap; gap:12px; margin:0 0 26px; }
  .dshl-stat { background:#fff; border:1px solid #DEE6EE; border-radius:12px; padding:14px 18px; min-width:130px; }
  .dshl-stat b { display:block; font-size:24px; color:#0F1E2E; line-height:1.15; }
  .dshl-stat span { font-size:11.5px; letter-spacing:.6px; text-transform:uppercase; color:#48586B; }
  .dshl-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:16px; }
  .dshl-card { display:block; background:#fff; border:1px solid #DEE6EE; border-radius:12px; overflow:hidden; text-decoration:none; color:#0F1E2E; transition:box-shadow .15s ease; }
  .dshl-card:hover { box-shadow:0 10px 28px rgba(15,30,46,.14); }
  .dshl-photo { aspect-ratio:3/2; background:#E9EFF3 center/cover no-repeat; position:relative; }
  .dshl-status { position:absolute; top:10px; left:10px; background:#0F1E2E; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; }
  .dshl-body { padding:14px 16px 16px; }
  .dshl-price { font-size:20px; font-weight:800; color:#0F1E2E; }
  .dshl-facts { font-size:13px; color:#48586B; margin-top:3px; }
  .dshl-addr { font-size:13px; color:#48586B; margin-top:6px; line-height:1.45; }
  .dshl-all { display:inline-block; margin-top:22px; background:#C8102E; color:#fff; font-weight:700; font-size:14.5px; padding:12px 22px; border-radius:999px; text-decoration:none; }
  .dshl-all:hover { background:#A50D24; }
  .dshl-sold-note { margin-top:18px; font-size:13.5px; color:#48586B; }
  .dshl-subs { margin-top:34px; padding-top:24px; border-top:1px solid #DEE6EE; }
  .dshl-subs h3 { font-family:Georgia,'Fraunces',serif; font-size:19px; color:#0F1E2E; margin:0 0 14px; }
  .dshl-subchips { display:flex; flex-wrap:wrap; gap:8px; }
  .dshl-subchips a { display:inline-flex; align-items:center; gap:7px; background:#fff; border:1px solid #DEE6EE; border-radius:999px; padding:7px 14px; font-size:13px; font-weight:600; color:#0F1E2E; text-decoration:none; }
  .dshl-subchips a:hover { border-color:#C8102E; }
  .dshl-subchips a span { font-size:10.5px; font-weight:800; color:#C8102E; background:#FDECEF; border-radius:999px; padding:2px 7px; }
  .dshl-subs-all { display:inline-block; margin-top:14px; font-size:13.5px; font-weight:700; color:#C8102E; text-decoration:none; }
</style>
  <div class="dshl-in">
    @unless($embedded ?? false)
    <div class="dshl-eyebrow">Live from the MLS</div>
    <h2>Homes for sale in {{ $title }}</h2>
    @endunless
    <p class="dshl-asof">Updated {{ $dataAsOf instanceof \Carbon\CarbonInterface ? $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') : $dataAsOf }} &middot; Listings courtesy of MRED as distributed by MLS GRID</p>

    @if(count($panels) > 1)
    <div class="dshl-tabs" role="tablist" aria-label="Home type">
      @foreach($panels as $i => $panel)
      <button type="button" class="dshl-tab" role="tab" id="dshl-tab-{{ $panel['key'] }}"
              aria-selected="{{ $i === 0 ? 'true' : 'false' }}" aria-controls="dshl-panel-{{ $panel['key'] }}">{{ $panel['label'] }}</button>
      @endforeach
    </div>
    @endif

    @foreach($panels as $i => $panel)
    <div class="dshl-panel" id="dshl-panel-{{ $panel['key'] }}" role="tabpanel" @if($i > 0) hidden @endif>
      <div class="dshl-stats">
        <div class="dshl-stat"><b>{{ number_format($panel['stats']['active']) }}</b><span>Active listings</span></div>
        <div class="dshl-stat"><b>{{ number_format($panel['stats']['underContract']) }}</b><span>Under contract</span></div>
        <div class="dshl-stat"><b>{{ number_format($panel['stats']['closed6mo']) }}</b><span>Sold, last {{ $panel['stats']['closedMonths'] }} months</span></div>
        @if($panel['stats']['medianClose'])
        <div class="dshl-stat"><b>${{ number_format($panel['stats']['medianClose']) }}</b><span>Median sale price ({{ $panel['stats']['closedMonths'] }} mo)</span></div>
        @endif
        @if($panel['stats']['avgDom'])
        <div class="dshl-stat"><b>{{ $panel['stats']['avgDom'] }}</b><span>Avg days on market</span></div>
        @endif
        @if($panel['stats']['saleListRatio'])
        <div class="dshl-stat"><b>{{ $panel['stats']['saleListRatio'] }}%</b><span>Sale-to-list ratio</span></div>
        @endif
      </div>

      @if($panel['listings']->isNotEmpty())
      <div class="dshl-grid">
        @foreach($panel['listings'] as $l)
        <a class="dshl-card" href="/listings/{{ $l->listing_id }}">
          <div class="dshl-photo" style="background-image:url('{{ $l->photoUrl() ?? '' }}')"><span class="dshl-status">{{ $l->status }}</span></div>
          <div class="dshl-body">
            <div class="dshl-price">${{ number_format($l->list_price) }}</div>
            <div class="dshl-facts">{{ $l->beds }} bd &middot; {{ $l->baths() }} ba @if($l->sqft) &middot; {{ number_format($l->sqft) }} sqft @endif</div>
            <div class="dshl-addr">{{ $l->displayAddress() }}</div>
          </div>
        </a>
        @endforeach
      </div>
      @endif

      @php $noun = match ($panel['key']) { 'detached' => 'detached home', 'attached' => 'attached home', default => 'home' }; @endphp
      @if($panel['total'] > 0)
      <a class="dshl-all" href="{{ $panel['allUrl'] }}">See all {{ number_format($panel['total']) }} {{ $noun }}{{ $panel['total'] === 1 ? '' : 's' }} for sale in {{ $panel['allLabel'] }} &rarr;</a>
      @endif
      @if($panel['stats']['closed6mo'] > 0)
      <p class="dshl-sold-note">Thinking of selling here? {{ number_format($panel['stats']['closed6mo']) }} {{ $noun }}{{ $panel['stats']['closed6mo'] === 1 ? '' : 's' }} closed in {{ $panel['title'] }} in the last {{ $panel['stats']['closedMonths'] }} months{{ $panel['stats']['medianClose'] ? ' at a median of $'.number_format($panel['stats']['medianClose']) : '' }}. <a href="/sell" style="color:#C8102E;font-weight:700;">Get your free valuation &rarr;</a></p>
      @endif
    </div>
    @endforeach

    @if(!empty($subdivisions ?? []))
    <div class="dshl-subs">
      <h3>Neighborhoods &amp; subdivisions in {{ $cityLabel }}</h3>
      <div class="dshl-subchips">
        @foreach(array_slice($subdivisions, 0, 30) as $s)
        <a href="{{ $s['url'] }}">{{ $s['label'] }}@if(($s['active'] ?? 0) > 0)<span>{{ $s['active'] }} for sale</span>@endif</a>
        @endforeach
      </div>
      @if(count($subdivisions) > 30)
      <a class="dshl-subs-all" href="/neighborhoods#{{ $citySlug }}">See all {{ count($subdivisions) }} {{ $cityLabel }} communities &rarr;</a>
      @endif
    </div>
    @endif
  </div>
  @include('listings._compliance', ['dataAsOf' => $dataAsOf])
</section>
@if(count($panels) > 1)
<script>
(function () {
  var tabs = document.querySelectorAll('#live-listings .dshl-tab');
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
})();
</script>
@endif
