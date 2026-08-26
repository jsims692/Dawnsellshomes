<x-site.layout :head="$head">
<style>
  .mi-chips { display:flex; flex-wrap:wrap; gap:8px; }
  .mi-chips a { display:inline-flex; gap:7px; align-items:center; background:#fff; border:1px solid #DEE6EE; border-radius:999px; padding:8px 15px; font-size:13.5px; font-weight:600; color:#0F1E2E; text-decoration:none; }
  .mi-chips a:hover { border-color:#C8102E; }
  .mi-chips a span { font-size:10.5px; font-weight:800; color:#C8102E; background:#FDECEF; border-radius:999px; padding:2px 7px; }
</style>
<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Market Reports</p>
    <p class="eyebrow">Live market data</p>
    <h1>Housing market reports, <em>town by town.</em></h1>
    <p class="lead">Pick a town for its live report — current inventory, what's new this week, and what actually closed in the last 30 days. Computed from the MLS, updated all day. Data as of {{ $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') }}.</p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <div class="mi-chips">
      @foreach($cities as $c)
      <a href="/market/{{ $c['slug'] }}">{{ $c['city'] }}@if($c['active'] > 0)<span>{{ $c['active'] }} active</span>@endif</a>
      @endforeach
    </div>
  </div>
</section>
@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
