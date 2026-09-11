<x-site.layout :page="null" :head="null">
<x-slot:headExtra>
<title>Site Pulse | Dawn Simmons Team</title>
<meta name="robots" content="noindex,nofollow">
</x-slot:headExtra>
@php

$since = fn (int $d) => now()->subDays($d);
$uniq = fn ($q) => (clone $q)->distinct()->count('vhash');

$base = \Illuminate\Support\Facades\DB::table('site_events');
$days = collect(range(13, 0))->map(function ($d) {
    $day = now()->subDays($d)->toDateString();
    $q = \Illuminate\Support\Facades\DB::table('site_events')->whereDate('created_at', $day);
    return [
        'day' => now()->subDays($d)->format('D n/j'),
        'visitors' => (clone $q)->where('event', 'page')->distinct()->count('vhash'),
        'searches' => (clone $q)->where('event', 'search')->count(),
    ];
});
$maxV = max(1, $days->max('visitors'));

$feat = \Illuminate\Support\Facades\DB::table('site_events')->where('created_at', '>=', $since(7))
    ->whereNotIn('event', ['page'])
    ->selectRaw('event, COUNT(*) n, COUNT(DISTINCT vhash) u')->groupBy('event')->orderByDesc('n')->get();

$hours = \Illuminate\Support\Facades\DB::table('site_events')->where('event', 'page')->where('created_at', '>=', $since(14))
    ->selectRaw('HOUR(CONVERT_TZ(created_at, "+00:00", "-05:00")) h, COUNT(*) n')->groupBy('h')->orderBy('h')->pluck('n', 'h');
$maxH = max(1, $hours->max() ?: 1);

$cities = \Illuminate\Support\Facades\DB::table('site_events')->where('event', 'page')->where('created_at', '>=', $since(7))
    ->whereNotNull('city')->selectRaw('city, COUNT(DISTINCT vhash) u')->groupBy('city')->orderByDesc('u')->limit(10)->get();

$searchMeta = \Illuminate\Support\Facades\DB::table('site_events')->where('event', 'search')->where('created_at', '>=', $since(7))->whereNotNull('meta')->pluck('meta');
$fltCount = []; $cityCount = []; $payCount = 0;
foreach ($searchMeta as $m) {
    $m = json_decode($m, true) ?: [];
    foreach ($m['flt'] ?? [] as $f) $fltCount[$f] = ($fltCount[$f] ?? 0) + 1;
    foreach ($m['city'] ?? [] as $c) $cityCount[$c] = ($cityCount[$c] ?? 0) + 1;
    if ($m['pay'] ?? false) $payCount++;
}
arsort($fltCount); arsort($cityCount);

$topPages = \Illuminate\Support\Facades\DB::table('site_events')->where('event', 'page')->where('created_at', '>=', $since(7))
    ->selectRaw('path, COUNT(*) n')->groupBy('path')->orderByDesc('n')->limit(12)->get();

$mob = \Illuminate\Support\Facades\DB::table('site_events')->where('event', 'page')->where('created_at', '>=', $since(7));
$mobPct = ($t = (clone $mob)->count()) ? round((clone $mob)->where('mobile', true)->count() * 100 / $t) : 0;
@endphp
<style>
  .pu-wrap { max-width:1080px; margin:0 auto; padding:36px 24px 64px; font-family:'Archivo',Arial,sans-serif; color:#0F1E2E; }
  .pu-wrap h1 { font-family:'Fraunces',Georgia,serif; margin:0 0 4px; }
  .pu-sub { color:#48586B; font-size:13.5px; margin:0 0 26px; }
  .pu-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:18px; }
  .pu-card { background:#fff; border:1px solid #DEE6EE; border-radius:12px; padding:18px 20px; }
  .pu-card h2 { font-size:12px; letter-spacing:.12em; text-transform:uppercase; color:#C8102E; margin:0 0 12px; }
  .pu-row { display:flex; align-items:center; gap:10px; font-size:13px; padding:3px 0; }
  .pu-row b { min-width:86px; font-variant-numeric:tabular-nums; font-weight:600; color:#48586B; }
  .pu-bar { height:14px; background:#C8102E; border-radius:3px; min-width:2px; }
  .pu-bar--n { background:#0F1E2E; }
  .pu-n { color:#48586B; font-variant-numeric:tabular-nums; }
  table.pu { width:100%; border-collapse:collapse; font-size:13px; }
  table.pu td { padding:5px 6px; border-bottom:1px solid #Eef2F7; }
  table.pu td:last-child { text-align:right; font-variant-numeric:tabular-nums; color:#48586B; }
</style>
<div class="pu-wrap">
  <h1>Site pulse</h1>
  <p class="pu-sub">First-party, anonymous usage. Bots filtered. Times are Chicago. Mobile share of visits (7d): <strong>{{ $mobPct }}%</strong></p>

  <div class="pu-card" style="margin-bottom:18px;">
    <h2>Daily visitors (red) &amp; searches (navy) — last 14 days</h2>
    @foreach($days as $d)
    <div class="pu-row"><b>{{ $d['day'] }}</b>
      <div class="pu-bar" style="width:{{ round($d['visitors'] * 260 / $maxV) }}px"></div><span class="pu-n">{{ $d['visitors'] }}</span>
      <div class="pu-bar pu-bar--n" style="width:{{ round($d['searches'] * 130 / max(1, $days->max('searches'))) }}px"></div><span class="pu-n">{{ $d['searches'] }}</span>
    </div>
    @endforeach
  </div>

  <div class="pu-grid">
    <div class="pu-card">
      <h2>Feature use — last 7 days (uses / people)</h2>
      <table class="pu">
        @forelse($feat as $f)
        <tr><td>{{ ['search' => '🔍 Searches', 'value' => '🏠 Home-value lookups', 'heart' => '♥ Homes saved', 'carousel' => '🖼 Card photo swipes', 'gallery' => '📷 Galleries opened', 'map' => '🗺 Maps opened', 'satellite' => '🛰 Satellite flips', 'streetview' => '🚶 Street View walks', 'share' => '↗ Listing shares', 'saved' => '🔔 Search alerts saved'][$f->event] ?? $f->event }}</td><td>{{ $f->n }} / {{ $f->u }}</td></tr>
        @empty
        <tr><td colspan="2">Nothing yet — data starts collecting from today.</td></tr>
        @endforelse
        @if($payCount)<tr><td>💵 Payment-first searches</td><td>{{ $payCount }}</td></tr>@endif
      </table>
    </div>

    <div class="pu-card">
      <h2>When people browse (visits by hour, 14d)</h2>
      @foreach(range(6, 23) as $h)
      <div class="pu-row"><b>{{ $h % 12 ?: 12 }}{{ $h < 12 ? 'am' : 'pm' }}</b><div class="pu-bar pu-bar--n" style="width:{{ round(($hours[$h] ?? 0) * 240 / $maxH) }}px"></div><span class="pu-n">{{ $hours[$h] ?? 0 }}</span></div>
      @endforeach
    </div>

    <div class="pu-card">
      <h2>Searched cities (7d)</h2>
      <table class="pu">
        @forelse(array_slice($cityCount, 0, 10, true) as $c => $n)<tr><td>{{ $c }}</td><td>{{ $n }}</td></tr>@empty<tr><td colspan="2">—</td></tr>@endforelse
      </table>
      <h2 style="margin-top:16px;">Filters people touch (7d)</h2>
      <table class="pu">
        @forelse(array_slice($fltCount, 0, 8, true) as $f => $n)<tr><td>{{ $f }}</td><td>{{ $n }}</td></tr>@empty<tr><td colspan="2">—</td></tr>@endforelse
      </table>
    </div>

    <div class="pu-card">
      <h2>Where visitors are (IP guess, 7d)</h2>
      <table class="pu">
        @forelse($cities as $c)<tr><td>{{ $c->city }}</td><td>{{ $c->u }}</td></tr>@empty<tr><td colspan="2">—</td></tr>@endforelse
      </table>
    </div>

    <div class="pu-card">
      <h2>Most-viewed pages (7d)</h2>
      <table class="pu">
        @forelse($topPages as $p)<tr><td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">/{{ ltrim($p->path, '/') }}</td><td>{{ $p->n }}</td></tr>@empty<tr><td colspan="2">—</td></tr>@endforelse
      </table>
    </div>
  </div>
</div>
</x-site.layout>
