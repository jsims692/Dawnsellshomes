{{-- Specialty search landing page: real content + live counts + sample
     cards for one high-intent niche (55+/first-floor-master, new
     construction, waterfront). Cards are Rule-10 thumbnails. --}}
<x-site.layout :head="$head">
<style>
  .col-cities { display:flex; flex-wrap:wrap; gap:8px; margin:14px 0 0; }
  .col-cities a { display:inline-flex; gap:7px; align-items:center; background:#fff; border:1px solid #DEE6EE; border-radius:999px; padding:7px 14px; font-size:13px; font-weight:600; color:#0F1E2E; text-decoration:none; }
  .col-cities a:hover { border-color:#C8102E; }
  .col-cities a span { font-size:10.5px; font-weight:800; color:#C8102E; background:#FDECEF; border-radius:999px; padding:2px 7px; }
  .col-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:18px; margin-top:20px; }
  .col-card { display:block; background:#fff; border:1px solid #DEE6EE; border-radius:12px; overflow:hidden; text-decoration:none; color:#0F1E2E; }
  .col-card:hover { box-shadow:0 10px 28px rgba(15,30,46,.14); }
  .col-photo { aspect-ratio:3/2; background:#E9EFF3 center/cover no-repeat; position:relative; }
  .col-status { position:absolute; top:10px; left:10px; background:#0F1E2E; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; }
  .col-body { padding:13px 15px 15px; }
  .col-price { font-size:19px; font-weight:800; }
  .col-meta { font-size:13px; color:#48586B; margin-top:3px; }
  .col-note { background:#FDF6E7; border:1px solid #EAD9A9; border-radius:10px; padding:12px 16px; font-size:13.5px; color:#6b5310; margin-top:22px; max-width:720px; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; <a href="/listings">Homes for Sale</a> &rsaquo; {{ $c['title'] }}</p>
    <p class="eyebrow">{{ $c['eyebrow'] }}</p>
    <h1>{{ $c['h1'] }}</h1>
    <p class="lead">{{ $c['blurb'] }}</p>
    <div class="hero-ctas" style="margin-top:1.6rem">
      <a class="btn btn--primary" href="{{ $searchUrl }}">Browse all {{ number_format($total) }} homes &rarr;</a>
      <a class="btn btn--ghost" href="/contact">Ask Dawn &amp; Josh</a>
    </div>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Live from the MLS</p>
      <h2 class="h2">{{ number_format($total) }} homes right now.</h2>
      <p class="lead" style="font-size:.95rem">Updated {{ $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') }} &middot; Listings courtesy of MRED as distributed by MLS GRID.</p>
    </div>

    @if($cities)
    <h3 style="font-family:'Fraunces',Georgia,serif;font-size:19px;margin:0 0 4px">Where they are</h3>
    <div class="col-cities">
      @foreach($cities as $city => $n)
      <a href="{{ $searchUrl }}&city%5B%5D={{ urlencode($city) }}">{{ $city }}<span>{{ $n }}</span></a>
      @endforeach
    </div>
    @endif

    @if($cards)
    <div class="col-grid">
      @foreach($cards as $l)
      <a class="col-card" href="{{ $l['url'] ?? '/listings/'.$l['id'] }}">
        <div class="col-photo" style="background-image:url('{{ $l['photo'] ?? '' }}')"><span class="col-status">{{ $l['status'] }}</span></div>
        <div class="col-body">
          <div class="col-price">${{ number_format($l['price']) }}</div>
          <div class="col-meta">{{ $l['beds'] }} bd &middot; {{ $l['baths'] }} ba @if($l['sqft']) &middot; {{ number_format($l['sqft']) }} sqft @endif</div>
          <div class="col-meta">{{ $l['addr'] }}</div>
        </div>
      </a>
      @endforeach
    </div>
    <a class="btn btn--primary" style="margin-top:24px;display:inline-block" href="{{ $searchUrl }}">See every match &rarr;</a>
    @endif

    <div class="col-note">&#128161; {{ $c['note'] }}</div>
  </div>
</section>
@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
