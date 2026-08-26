{{-- Living market report for one city: computed from replicated MLS data,
     refreshed continuously, attributed as market analytics. --}}
<x-site.layout :head="$head">
<style>
  .mr-stats { display:flex; flex-wrap:wrap; gap:12px; margin:8px 0 4px; }
  .mr-stat { background:#fff; border:1px solid #DEE6EE; border-radius:12px; padding:16px 20px; min-width:150px; }
  .mr-stat b { display:block; font-size:26px; color:#0F1E2E; line-height:1.2; }
  .mr-stat span { font-size:11.5px; letter-spacing:.6px; text-transform:uppercase; color:#48586B; }
  .mr-trend { font-size:15px; margin-top:18px; max-width:680px; line-height:1.7; color:#333; }
  .mr-links { display:flex; flex-wrap:wrap; gap:10px; margin-top:22px; }
  .mr-links a { border:1px solid #DEE6EE; background:#fff; border-radius:999px; padding:9px 18px; font-size:13.5px; font-weight:700; color:#0F1E2E; text-decoration:none; }
  .mr-links a:hover { border-color:#C8102E; color:#C8102E; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; <a href="/market">Market Reports</a> &rsaquo; {{ $city }}</p>
    <p class="eyebrow">{{ now()->format('F Y') }} &middot; live data</p>
    <h1>{{ $city }} housing market <em>right now.</em></h1>
    <p class="lead">Every number on this page is computed from the MLS feed and refreshes throughout the day — not a quarterly PDF. Updated {{ $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') }}.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <div class="sec-head"><p class="eyebrow">On the market</p><h2 class="h2">Inventory today.</h2></div>
    <div class="mr-stats">
      <div class="mr-stat"><b>{{ number_format($m['active']) }}</b><span>Active listings</span></div>
      <div class="mr-stat"><b>{{ number_format($m['newWeek']) }}</b><span>New this week</span></div>
      <div class="mr-stat"><b>{{ number_format($m['underContract']) }}</b><span>Under contract</span></div>
      @if($m['medianList'])<div class="mr-stat"><b>${{ number_format($m['medianList']) }}</b><span>Median list price</span></div>@endif
    </div>

    <div class="sec-head" style="margin-top:40px"><p class="eyebrow">Last 30 days</p><h2 class="h2">What actually sold.</h2></div>
    <div class="mr-stats">
      <div class="mr-stat"><b>{{ number_format($m['sold30']) }}</b><span>Homes closed</span></div>
      @if($m['medianClose30'])<div class="mr-stat"><b>${{ number_format($m['medianClose30']) }}</b><span>Median sale price</span></div>@endif
      @if($m['dom30'])<div class="mr-stat"><b>{{ $m['dom30'] }}</b><span>Avg days on market</span></div>@endif
      @if($m['ratio30'])<div class="mr-stat"><b>{{ $m['ratio30'] }}%</b><span>Sale-to-list ratio</span></div>@endif
    </div>

    @if($m['medianClose30'] && $m['medianClosePrior'])
    @php $delta = round(($m['medianClose30'] - $m['medianClosePrior']) / $m['medianClosePrior'] * 100, 1); @endphp
    <p class="mr-trend">
      The median {{ $city }} sale price over the last 30 days is
      <strong>{{ $delta >= 0 ? 'up' : 'down' }} {{ abs($delta) }}%</strong>
      versus the 30 days before it{{ $m['ratio30'] ? ', and homes are closing at '.$m['ratio30'].'% of their original asking price' : '' }}{{ $m['dom30'] ? ' after about '.$m['dom30'].' days on market' : '' }}.
      Thirty-day windows in a single town swing on mix — read direction, not gospel, and
      <a href="/contact" style="color:#C8102E;font-weight:700">ask us what it means for your street</a>.
    </p>
    @endif

    <div class="mr-links">
      <a href="/listings?city%5B%5D={{ urlencode($city) }}">Browse {{ $city }} listings &rarr;</a>
      <a href="/cities/{{ $citySlug }}">About {{ $city }} &rarr;</a>
      <a href="/neighborhoods#{{ $citySlug }}">{{ $city }} subdivisions &rarr;</a>
      <a href="/sell">What's my home worth? &rarr;</a>
      <a href="/compare">Compare {{ $city }} with another town &rarr;</a>
    </div>
  </div>
</section>
@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
