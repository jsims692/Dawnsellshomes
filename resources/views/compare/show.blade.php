{{-- Town vs town from live MLS data: stat table + honest data-derived
     verdict. Featured pairs are indexed; ad-hoc pairs carry noindex. --}}
<x-site.layout :head="$head">
<style>
  .cmp-table { width:100%; max-width:860px; border-collapse:collapse; font-size:15px; background:#fff; border:1px solid #DEE6EE; border-radius:14px; overflow:hidden; }
  .cmp-table th, .cmp-table td { padding:13px 18px; text-align:left; border-bottom:1px solid #E9EFF3; }
  .cmp-table thead th { background:#0F1E2E; color:#fff; font-family:'Fraunces',Georgia,serif; font-size:17px; }
  .cmp-table td:first-child { color:#48586B; font-size:12.5px; letter-spacing:.5px; text-transform:uppercase; font-weight:700; width:38%; }
  .cmp-table td:not(:first-child) { font-weight:700; color:#0F1E2E; font-size:16px; }
  .cmp-win { color:#C8102E !important; }
  .cmp-verdict { max-width:760px; }
  .cmp-verdict p { font-size:15.5px; line-height:1.75; color:#333; margin:0 0 14px; }
  .cmp-ctas { display:flex; flex-wrap:wrap; gap:10px; margin-top:8px; }
  .cmp-ctas a { border:1px solid #DEE6EE; background:#fff; border-radius:999px; padding:9px 18px; font-size:13.5px; font-weight:700; color:#0F1E2E; text-decoration:none; }
  .cmp-ctas a:hover { border-color:#C8102E; color:#C8102E; }
  .cmp-table-wrap { overflow-x:auto; }
</style>

<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; <a href="/compare">Compare Towns</a> &rsaquo; {{ $a['name'] }} vs {{ $b['name'] }}</p>
    <p class="eyebrow">Live market comparison</p>
    <h1>{{ $a['name'] }} <em>vs</em> {{ $b['name'] }}</h1>
    <p class="lead">Same data, side by side — computed from the MLS feed and refreshed all day. Updated {{ $dataAsOf->timezone('America/Chicago')->format('n/j/Y g:i A T') }}.</p>
  </div>
</section>

<section class="section">
  <div class="wrap">
    @php
      $rows = [
        ['Active listings', 'active', fn ($v) => number_format($v), 'hi'],
        ['New this week', 'newWeek', fn ($v) => number_format($v), null],
        ['Under contract', 'underContract', fn ($v) => number_format($v), null],
        ['Median list price', 'medianList', fn ($v) => '$'.number_format($v), null],
        ['Sold, last 30 days', 'sold30', fn ($v) => number_format($v), null],
        ['Median sale price (30d)', 'medianClose30', fn ($v) => '$'.number_format($v), null],
        ['Avg days on market', 'dom30', fn ($v) => $v, 'lo'],
        ['Sale-to-list ratio', 'ratio30', fn ($v) => $v.'%', 'hi'],
        ['Avg annual property tax', 'avgTax', fn ($v) => '$'.number_format($v), 'lo'],
      ];
    @endphp
    <div class="cmp-table-wrap">
    <table class="cmp-table">
      <thead><tr><th></th><th>{{ $a['name'] }}</th><th>{{ $b['name'] }}</th></tr></thead>
      @foreach($rows as [$label, $key, $fmt, $better])
      @php $va = $a['s'][$key] ?? null; $vb = $b['s'][$key] ?? null; @endphp
      @if($va !== null || $vb !== null)
      <tr>
        <td>{{ $label }}</td>
        <td class="{{ $better && $va !== null && $vb !== null && (($better === 'hi' && $va > $vb) || ($better === 'lo' && $va < $vb)) ? 'cmp-win' : '' }}">{{ $va !== null ? $fmt($va) : '—' }}</td>
        <td class="{{ $better && $va !== null && $vb !== null && (($better === 'hi' && $vb > $va) || ($better === 'lo' && $vb < $va)) ? 'cmp-win' : '' }}">{{ $vb !== null ? $fmt($vb) : '—' }}</td>
      </tr>
      @endif
      @endforeach
    </table>
    </div>

    <div class="sec-head" style="margin-top:44px"><p class="eyebrow">What the data says</p><h2 class="h2">Our read.</h2></div>
    <div class="cmp-verdict">
      @foreach($verdict as $line)<p>{{ $line }}</p>@endforeach
    </div>

    <div class="cmp-ctas">
      <a href="/listings?city%5B%5D={{ urlencode($a['name']) }}&city%5B%5D={{ urlencode($b['name']) }}">Search both towns at once &rarr;</a>
      <a href="/market/{{ $a['slug'] }}">{{ $a['name'] }} market report &rarr;</a>
      <a href="/market/{{ $b['slug'] }}">{{ $b['name'] }} market report &rarr;</a>
      <a href="/cities/{{ $a['slug'] }}">About {{ $a['name'] }} &rarr;</a>
      <a href="/cities/{{ $b['slug'] }}">About {{ $b['name'] }} &rarr;</a>
      <a href="/contact" style="background:#C8102E;border-color:#C8102E;color:#fff;">Ask us which fits you &rarr;</a>
    </div>
  </div>
</section>
@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
