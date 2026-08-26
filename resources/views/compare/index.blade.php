<x-site.layout :head="$head">
<style>
  .ci-chips { display:flex; flex-wrap:wrap; gap:8px; margin-bottom:36px; }
  .ci-chips a { background:#fff; border:1px solid #DEE6EE; border-radius:999px; padding:9px 17px; font-size:13.5px; font-weight:700; color:#0F1E2E; text-decoration:none; }
  .ci-chips a:hover { border-color:#C8102E; color:#C8102E; }
  .ci-pick { display:flex; flex-wrap:wrap; gap:10px; align-items:center; background:#fff; border:1px solid #DEE6EE; border-radius:14px; padding:18px 20px; max-width:640px; }
  .ci-pick select { padding:10px 12px; border:1px solid #c9d2e3; border-radius:8px; font-size:14.5px; font-family:inherit; }
  .ci-pick button { background:#C8102E; color:#fff; border:0; border-radius:999px; padding:11px 22px; font-weight:700; font-size:14px; cursor:pointer; }
</style>
<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; Compare Towns</p>
    <p class="eyebrow">Town vs town</p>
    <h1>Which suburb <em>actually</em> fits you?</h1>
    <p class="lead">Pick two towns and see the live numbers side by side — prices, taxes, speed of sale, inventory — straight from the MLS. Then ask us about everything the numbers can't show.</p>
  </div>
</section>
<section class="section">
  <div class="wrap">
    <div class="ci-pick" x-data="{ x: '', y: '' }">
      <select x-model="x"><option value="">First town…</option>@foreach($cities as $c)<option value="{{ $c['slug'] }}">{{ $c['city'] }}</option>@endforeach</select>
      <span style="font-weight:800;color:#48586B">vs</span>
      <select x-model="y"><option value="">Second town…</option>@foreach($cities as $c)<option value="{{ $c['slug'] }}">{{ $c['city'] }}</option>@endforeach</select>
      <button type="button" :disabled="!x || !y || x === y" @click="window.location = '/compare/' + (x < y ? x + '-vs-' + y : y + '-vs-' + x)">Compare &rarr;</button>
    </div>

    <div class="sec-head" style="margin-top:40px"><p class="eyebrow">Popular matchups</p><h2 class="h2">The comparisons buyers ask us about.</h2></div>
    <div class="ci-chips">
      @foreach($featured as $f)
      <a href="/compare/{{ $f['slug'] }}">{{ $f['label'] }}</a>
      @endforeach
    </div>
  </div>
</section>
</x-site.layout>
