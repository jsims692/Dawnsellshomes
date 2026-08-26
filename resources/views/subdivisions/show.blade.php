{{-- Auto-generated subdivision page: rendered for any MLS-tagged subdivision
     in a service-area city that has no hand-built neighborhood page. All the
     listing display + stats come from the shared in-city band (Rule-10 cards,
     attributed analytics). --}}
<x-site.layout :head="$head">
@php
  $built = $profile['yearLo'] ? ($profile['yearLo'] == $profile['yearHi'] ? ' built in '.$profile['yearLo'] : ' built between '.$profile['yearLo'].' and '.$profile['yearHi']) : '';
  $sizes = $profile['sqftLo'] ? number_format($profile['sqftLo']).'–'.number_format($profile['sqftHi']).' sqft' : null;
@endphp
<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; <a href="/neighborhoods">Neighborhoods</a> &rsaquo; {{ $entry['name'] }}</p>
    <p class="eyebrow">{{ $entry['city'] }}, Illinois</p>
    <h1>{{ $entry['name'] }}</h1>
    <p class="lead">{{ $entry['name'] }} is a {{ $profile['phrase'] }} in <a href="/cities/{{ $entry['citySlug'] }}" style="color:inherit">{{ $entry['city'] }}</a>, Illinois{{ $built }}.
      Everything below comes straight from the MLS and updates hourly.</p>
  </div>
</section>

<section class="section section--tight">
  <div class="wrap">
    <div class="sub-facts">
      @if($built)<div class="sub-fact"><b>{{ $profile['yearLo'] == $profile['yearHi'] ? $profile['yearLo'] : $profile['yearLo'].'–'.$profile['yearHi'] }}</b><span>Years built</span></div>@endif
      @if($sizes)<div class="sub-fact"><b>{{ $sizes }}</b><span>Home sizes</span></div>@endif
      @if($profile['avgTax'])<div class="sub-fact"><b>${{ number_format($profile['avgTax']) }}</b><span>Avg annual property tax</span></div>@endif
      @if($profile['high'])<div class="sub-fact"><b>{{ $profile['high'] }}</b><span>High school</span></div>@endif
    </div>
    @if($profile['elementary'] || $profile['middle'] || $profile['high'])
    <p class="sub-schools">Schools typically serving {{ $entry['name'] }}:
      @foreach(array_filter(['elementary' => $profile['elementary'], 'middle' => $profile['middle'], 'high' => $profile['high']]) as $lvl => $name)
        <a href="/listings?school={{ urlencode($name) }}">{{ $name }}</a>{{ ! $loop->last ? ' · ' : '' }}
      @endforeach
      <span>(assignments can vary by address &mdash; always verify with the district)</span>
    </p>
    @endif
  </div>
</section>
<style>
  .sub-facts { display:flex; flex-wrap:wrap; gap:12px; }
  .sub-fact { background:#fff; border:1px solid #DEE6EE; border-radius:12px; padding:14px 18px; min-width:150px; }
  .sub-fact b { display:block; font-size:19px; color:#0F1E2E; line-height:1.25; }
  .sub-fact span { font-size:11.5px; letter-spacing:.6px; text-transform:uppercase; color:#48586B; }
  .sub-schools { font-size:14px; color:#48586B; margin:16px 0 0; }
  .sub-schools a { color:#C8102E; font-weight:600; text-decoration:none; }
  .sub-schools span { font-size:12px; color:#8A99AA; }
  .sub-solds { width:100%; border-collapse:collapse; font-size:14px; max-width:760px; }
  .sub-solds th { text-align:left; font-size:11.5px; letter-spacing:.8px; text-transform:uppercase; color:#48586B; border-bottom:2px solid #DEE6EE; padding:8px 10px; }
  .sub-solds td { border-bottom:1px solid #E9EFF3; padding:9px 10px; color:#333; }
  .sub-solds a { color:#0F1E2E; font-weight:600; text-decoration:none; }
  .sub-solds a:hover { color:#C8102E; }
</style>

@include('components.listings.in-city', [
    'embedded' => false,
    'title' => $entry['name'].', '.$entry['city'],
    'panels' => $panels,
    'dataAsOf' => $dataAsOf,
])

@if(!empty($profile['solds']))
<section class="section section--tight">
  <div class="wrap">
    <h2 class="h2" style="font-size:24px;margin-bottom:14px">Recent sales in {{ $entry['name'] }}</h2>
    <table class="sub-solds">
      <tr><th>Address</th><th>Beds / Baths</th><th>Sold</th><th>Price</th></tr>
      @foreach($profile['solds'] as $s)
      <tr>
        <td><a href="/listings/{{ $s['id'] }}">{{ $s['address'] ?? 'Address withheld' }}</a></td>
        <td>{{ $s['beds'] }} bd / {{ $s['baths'] }} ba</td>
        <td>{{ $s['when'] }}</td>
        <td>${{ number_format($s['price']) }}</td>
      </tr>
      @endforeach
    </table>
    <p style="font-size:12px;color:#8A99AA;margin-top:10px">Sold data courtesy of MRED as distributed by MLS GRID; properties may be listed or sold by various participants in the MLS.</p>
  </div>
</section>
@endif

<section class="section">
  <div class="wrap" style="max-width:860px">
    <div class="callout">
      &#128269; <strong>Heads up:</strong> Listing agents don&rsquo;t always tag a home with its subdivision in the MLS, so some {{ $entry['name'] }} homes may only appear in the broader {{ $entry['city'] }} results.
      <a class="link-arrow" href="/listings?city={{ urlencode($entry['city']) }}">See every {{ $entry['city'] }} listing &rarr;</a>
    </div>
    <div class="callout" style="margin-top:14px">
      &#127968; Thinking about {{ $entry['name'] }}? We&rsquo;ve been selling here for 26 years &mdash; including off-market and Private Listing Network homes that never reach public sites.
      <a class="link-arrow" href="/contact">Talk to Dawn &amp; Josh &rarr;</a>
    </div>
  </div>
</section>
</x-site.layout>
