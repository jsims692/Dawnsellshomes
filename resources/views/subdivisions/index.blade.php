{{-- /neighborhoods directory: hand-built neighborhood/condo pages merged with
     MLS-derived subdivision pages, grouped by city. "N for sale" counts are
     market analytics (attributed below); hand pages without MLS data show no
     count rather than a zero. --}}
<x-site.layout :head="$head">
<style>
  .nb-city { margin: 0 0 34px; }
  .nb-city h2 { font-family:'Fraunces',Georgia,serif; font-size:22px; margin:0 0 12px; }
  .nb-city h2 a { color:inherit; text-decoration:none; }
  .nb-city h2 a:hover { color:#C8102E; }
  .nb-chips { display:flex; flex-wrap:wrap; gap:8px; }
  .nb-chip { display:inline-flex; align-items:center; gap:7px; background:#fff; border:1px solid #DEE6EE;
             border-radius:999px; padding:8px 15px; font-size:13.5px; font-weight:600; color:#0F1E2E;
             text-decoration:none; transition:border-color .15s ease, box-shadow .15s ease; }
  .nb-chip:hover { border-color:#C8102E; box-shadow:0 4px 14px rgba(15,30,46,.10); }
  .nb-count { font-size:11px; font-weight:800; color:#C8102E; background:#FDECEF; border-radius:999px; padding:2px 8px; }
  .nb-note { font-size:13.5px; color:#48586B; max-width:760px; margin:0 0 30px; }
  .nb-attrib { font-size:12px; color:#8A99AA; margin-top:36px; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Neighborhoods</p>
    <p class="eyebrow">Explore by community</p>
    <h1>Every neighborhood &amp; subdivision <em>we cover.</em></h1>
    <p class="lead">Pick a community to see its live MLS listings, recent sales, and market stats. Counts update hourly.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    <p class="nb-note">Listing agents don&rsquo;t always tag a home with its subdivision in the MLS, so a community&rsquo;s page may not show every home for sale there &mdash; the city search always does. Missing your subdivision? <a class="link-arrow" href="/contact">Tell us and we&rsquo;ll add it &rarr;</a></p>
    @foreach($groups as $group)
    <div class="nb-city" id="{{ $group['slug'] }}">
      <h2><a href="/cities/{{ $group['slug'] }}">{{ $group['name'] }}</a></h2>
      <div class="nb-chips">
        @foreach($group['items'] as $item)
        <a class="nb-chip" href="{{ $item['url'] }}">{{ $item['label'] }}@if(($item['active'] ?? 0) > 0)<span class="nb-count">{{ $item['active'] }} for sale</span>@endif</a>
        @endforeach
      </div>
    </div>
    @endforeach
    <p class="nb-attrib">For-sale counts courtesy of MRED as distributed by MLS GRID as of {{ $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') }}.</p>
  </div>
</section>
</x-site.layout>
