{{-- Live MLS band injected into legacy city/neighborhood pages by
     PageController::injectListingsBand. Fully self-contained styling (the host
     pages carry their own imported CSS). Cards are Rule-10 thumbnails (≤8
     fields, linked to the full compliant display); the stats strip is market
     analytics, attributed via the standard compliance block below. --}}
<section class="dshl" id="live-listings">
<style>
  .dshl { font-family:'Archivo',Arial,sans-serif; background:#F2F5F9; padding:52px 24px; }
  .dshl-in { max-width:1180px; margin:0 auto; }
  .dshl-eyebrow { font-size:11px; font-weight:800; letter-spacing:2px; text-transform:uppercase; color:#C8102E; margin-bottom:10px; }
  .dshl h2 { font-family:Georgia,'Fraunces',serif; font-size:clamp(22px,3vw,32px); color:#0F1E2E; margin:0 0 6px; }
  .dshl-asof { font-size:12.5px; color:#8A99AA; margin:0 0 22px; }
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
</style>
  <div class="dshl-in">
    <div class="dshl-eyebrow">Live from the MLS</div>
    <h2>Homes for sale in {{ $title }}</h2>
    <p class="dshl-asof">Updated {{ $dataAsOf instanceof \Carbon\CarbonInterface ? $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') : $dataAsOf }} &middot; Listings courtesy of MRED as distributed by MLS GRID</p>

    <div class="dshl-stats">
      <div class="dshl-stat"><b>{{ number_format($stats['active']) }}</b><span>Active listings</span></div>
      <div class="dshl-stat"><b>{{ number_format($stats['underContract']) }}</b><span>Under contract</span></div>
      <div class="dshl-stat"><b>{{ number_format($stats['closed6mo']) }}</b><span>Sold, last 6 months</span></div>
      @if($stats['medianClose'])
      <div class="dshl-stat"><b>${{ number_format($stats['medianClose']) }}</b><span>Median sale price (6 mo)</span></div>
      @endif
      @if($stats['avgDom'])
      <div class="dshl-stat"><b>{{ $stats['avgDom'] }}</b><span>Avg days on market</span></div>
      @endif
      @if($stats['saleListRatio'])
      <div class="dshl-stat"><b>{{ $stats['saleListRatio'] }}%</b><span>Sale-to-list ratio</span></div>
      @endif
    </div>

    @if($listings->isNotEmpty())
    <div class="dshl-grid">
      @foreach($listings as $l)
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

    @if($total > 0)
    <a class="dshl-all" href="{{ $allUrl }}">See all {{ number_format($total) }} home{{ $total === 1 ? '' : 's' }} for sale in {{ $allLabel }} &rarr;</a>
    @endif
    @if($stats['closed6mo'] > 0)
    <p class="dshl-sold-note">Thinking of selling here? {{ number_format($stats['closed6mo']) }} home{{ $stats['closed6mo'] === 1 ? '' : 's' }} closed in {{ $title }} in the last 6 months{{ $stats['medianClose'] ? ' at a median of $'.number_format($stats['medianClose']) : '' }}. <a href="/sell" style="color:#C8102E;font-weight:700;">Get your free valuation &rarr;</a></p>
    @endif
  </div>
  @include('listings._compliance', ['dataAsOf' => $dataAsOf])
</section>
