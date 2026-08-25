<x-site.layout :page="null" :head="null" title="Homes for Sale — Northwest Suburbs | The Dawn Simmons Team">
@php
  $dwLabel = ['detached' => 'Detached Homes', 'attached' => 'Condos & Townhomes', 'multi' => '2–4 Unit Buildings', 'multi5' => '5+ Unit Buildings'][$filters['dwelling'] ?? ''] ?? 'Homes';
  $cityList = array_map(fn ($c) => \Illuminate\Support\Str::title($c), (array) ($filters['city'] ?? []));
  $cityDisplay = match (true) { count($cityList) === 1 => $cityList[0], count($cityList) === 2 => implode(' & ', $cityList), count($cityList) > 2 => count($cityList).' Cities', default => null };
  $placeLabel = $cityDisplay ? ($cityDisplay.(count($cityList) === 1 ? ', IL' : '')) : 'the Northwest Suburbs';
  $pageHeading = "{$dwLabel} for Sale in {$placeLabel}";
@endphp
<x-slot:headExtra>
<title>{{ $pageHeading }} | The Dawn Simmons Team – RE/MAX Suburban</title>
<meta name="description" content="Search homes for sale across Chicago's northwest suburbs with the Dawn Simmons Team, RE/MAX Suburban. Listings courtesy of MRED as distributed by MLS GRID, updated throughout the day.">
<meta name="robots" content="{{ config('services.mlsgrid.token') ? 'index,follow' : 'noindex,nofollow' }}">
</x-slot:headExtra>
<style>
  .li-wrap { max-width:1180px; margin:0 auto; padding:32px 24px; font-family:Arial,sans-serif; }
  .li-hero { background:linear-gradient(180deg,#0B1622,#0F1E2E); color:#fff; text-align:center; padding:52px 24px 40px; }
  .li-hero h1 { font-family:'Fraunces',Georgia,serif; font-size:clamp(26px,4vw,42px); margin:0 0 10px; }
  .li-hero p { color:rgba(255,255,255,.8); margin:0; }
  .li-filters { display:flex; flex-wrap:wrap; gap:10px; align-items:end; background:#fff; border:1px solid #e0e4ed; border-radius:10px; padding:16px; margin:-28px auto 28px; max-width:1000px; box-shadow:0 6px 24px rgba(15,30,46,.12); position:relative; }
  .li-filters label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#0F1E2E; display:block; }
  .li-filters select, .li-filters input { padding:9px 10px; border:1px solid #c9d2e3; border-radius:6px; font-size:14px; min-width:120px; }
  .li-filters button { background:#C8102E; color:#fff; border:0; border-radius:999px; padding:11px 22px; font-weight:700; cursor:pointer; }
  .li-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
  .li-card { display:block; background:#fff; border:1px solid #e0e4ed; border-radius:10px; overflow:hidden; text-decoration:none; color:#222; transition:box-shadow .15s; }
  .li-card:hover { box-shadow:0 8px 28px rgba(15,30,46,.16); }
  .li-photo { aspect-ratio:3/2; background:#e9edf3 center/cover no-repeat; position:relative; }
  .li-status { position:absolute; top:10px; left:10px; background:#0F1E2E; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; }
  .li-body { padding:16px; }
  .li-price { font-size:22px; font-weight:800; color:#0F1E2E; }
  .li-addr { font-size:14px; color:#444; margin:4px 0 8px; }
  .li-meta { font-size:13px; color:#666; }
  .li-office { font-size:11.5px; color:#888; margin-top:8px; border-top:1px dashed #e0e4ed; padding-top:8px; }
  .li-filters .fl-pop-btn, .fl-pop-btn { padding:9px 12px; border:1px solid #c9d2e3; border-radius:6px; font-size:14px; background:#fff; cursor:pointer; font-family:inherit; color:#0F1E2E; min-width:130px; text-align:left; }
  .fl-pop { position:absolute; top:100%; left:0; z-index:30; background:#fff; border:1px solid #c9d2e3; border-radius:10px; padding:10px 14px; box-shadow:0 12px 32px rgba(15,30,46,.16); max-height:280px; overflow:auto; min-width:230px; }
  .fl-check { display:flex; gap:8px; align-items:center; font-size:13.5px; padding:4px 0; text-transform:none; letter-spacing:0; font-weight:500; color:#0F1E2E; cursor:pointer; }
  .demo-banner { background:#fff7e0; border:1px solid #e2cd86; color:#7a5d12; border-radius:8px; padding:12px 16px; margin:0 auto 22px; max-width:1000px; font-size:14px; }
</style>

<div class="li-hero">
  <h1>{{ $pageHeading }}</h1>
  <p>{{ ($filters['city'] ?? null) ? 'Every active '.strtolower($dwLabel === 'Homes' ? 'listing' : $dwLabel).' in '.$cityDisplay : 'Every active listing in the communities we serve' }} &mdash; updated throughout the day from the MLS via MLS GRID.</p>
</div>

<div class="li-wrap">
  @if($demo)
    <div class="demo-banner"><strong>Display preview:</strong> the listings below are clearly-marked sample records used to preview this page's layout. Live MRED listing data activates upon MLS GRID approval.</div>
  @endif

  <form class="li-filters" method="get" action="/listings">
    @php $selCities = array_map('mb_strtolower', (array) ($filters['city'] ?? [])); @endphp
    <div x-data="{open:false}" style="position:relative"><label>Cities</label>
      <button type="button" class="fl-pop-btn" @click="open=!open">{{ count($selCities) ? count($selCities).' selected' : 'All cities' }} &#9662;</button>
      <div class="fl-pop" x-show="open" x-cloak @click.outside="open=false">
        @foreach($cities as $c)<label class="fl-check"><input type="checkbox" name="city[]" value="{{ $c }}" @checked(in_array(mb_strtolower($c), $selCities))> {{ $c }}</label>@endforeach
      </div>
    </div>
    <div><label for="f-min">Min Price</label><select id="f-min" name="min"><option value="">No min</option>@foreach([200000,300000,400000,500000,750000,1000000] as $v)<option value="{{ $v }}" @selected(($filters['min'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-max">Max Price</label><select id="f-max" name="max"><option value="">No max</option>@foreach([300000,400000,500000,750000,1000000,2000000] as $v)<option value="{{ $v }}" @selected(($filters['max'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-beds">Beds</label><select id="f-beds" name="beds"><option value="">Any</option>@foreach([1,2,3,4,5] as $v)<option value="{{ $v }}" @selected(($filters['beds'] ?? '') == $v)>{{ $v }}+</option>@endforeach</select></div>
    <div><label for="f-dwelling">Home type</label><select id="f-dwelling" name="dwelling"><option value="">All types</option>@foreach(['detached' => 'Detached homes', 'attached' => 'Attached (condo/townhome)', 'multi' => '2–4 unit buildings', 'multi5' => '5+ unit buildings'] as $v => $label)<option value="{{ $v }}" @selected(($filters['dwelling'] ?? '') === $v)>{{ $label }}</option>@endforeach</select></div>
    <div x-data="{open:false}" style="position:relative"><label>More</label>
      <button type="button" class="fl-pop-btn" @click="open=!open">More filters &#9662;</button>
      <div class="fl-pop" x-show="open" x-cloak @click.outside="open=false">
        <label class="fl-check"><input type="checkbox" name="waterfront" value="1" @checked($filters['waterfront'] ?? false)> &#127754; Waterfront only</label>
        <label class="fl-check"><input type="checkbox" name="basement" value="1" @checked($filters['basement'] ?? false)> Has basement</label>
        <label class="fl-check" style="justify-content:space-between">Garage spaces
          <select name="garage" style="padding:5px 8px;border:1px solid #c9d2e3;border-radius:6px;font-size:13px;"><option value="">Any</option>@foreach([1,2,3] as $g)<option value="{{ $g }}" @selected(($filters['garage'] ?? '') == $g)>{{ $g }}+</option>@endforeach</select>
        </label>
      </div>
    </div>
    <div><button type="submit">Search</button></div>
  </form>

  @if(session('alert_saved'))
  <div style="background:#EAF7EF;border:1px solid #b7dfc3;color:#1d6b35;border-radius:10px;padding:12px 16px;margin:0 0 18px;font-size:14px;">
    &#10003; <strong>Search saved!</strong> We'll email you when new homes match: {{ session('alert_saved') }}. Unsubscribe anytime from the email.
  </div>
  @endif

  <div style="display:flex;flex-wrap:wrap;gap:14px;align-items:center;justify-content:space-between;background:#F2F5F9;border:1px solid #DEE6EE;border-radius:10px;padding:14px 16px;margin:0 0 18px;">
    <div style="font-size:14px;color:#0F1E2E;"><strong>&#128276; Never miss a listing.</strong>
      <span style="color:#48586B;">Save this search and we'll email you the moment new matches hit the MLS.</span></div>
    <form method="post" action="/listings/alerts" style="display:flex;flex-wrap:wrap;gap:8px;align-items:center;margin:0;">
      @csrf
      @foreach(array_filter($filters ?? []) as $k => $v)@foreach((array) $v as $vv)<input type="hidden" name="{{ $k }}{{ is_array($v) ? '[]' : '' }}" value="{{ $vv }}">@endforeach @endforeach
      <input type="text" name="name" placeholder="First name (optional)" style="padding:9px 12px;border:1px solid #c9d2e3;border-radius:6px;font-size:14px;width:150px;">
      <input type="email" name="email" required placeholder="you@email.com" style="padding:9px 12px;border:1px solid #c9d2e3;border-radius:6px;font-size:14px;width:190px;">
      <button type="submit" style="background:#C8102E;color:#fff;border:0;border-radius:999px;padding:10px 18px;font-weight:700;font-size:13.5px;cursor:pointer;">Save search + get alerts</button>
    </form>
  </div>

  <div style="background:#0F1E2E;color:#fff;border-radius:10px;padding:16px 18px;margin:0 0 18px;font-size:14px;display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;">
    <div style="max-width:640px;line-height:1.6;">&#128274; <strong>What you can&rsquo;t see here: private listings.</strong>
      <span style="color:rgba(255,255,255,.75);">Some homes sell through MRED&rsquo;s Private Listing Network and never appear on any public site &mdash; not here, not Zillow. Agents can only share them directly. Tell us what you&rsquo;re looking for and we&rsquo;ll watch the PLN for you.</span></div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <a href="/contact?pln={{ urlencode($cityList[0] ?? 'the northwest suburbs') }}" style="background:#C8102E;color:#fff;font-weight:700;font-size:13.5px;padding:10px 18px;border-radius:999px;text-decoration:none;white-space:nowrap;">Get private matches &rarr;</a>
      <a href="/off-market-homes" style="border:1px solid rgba(255,255,255,.4);color:#fff;font-weight:700;font-size:13.5px;padding:10px 18px;border-radius:999px;text-decoration:none;white-space:nowrap;">How it works</a>
    </div>
  </div>

  <p style="font-size:14px;color:#666;margin:0 0 18px;">{{ number_format($total) }} {{ Str::plural('listing', $total) }} found{{ $cityDisplay ? ' in '.$cityDisplay : '' }}.</p>

  <div class="li-grid">
    @foreach($listings as $l)
    {{-- Thumbnail: ≤8 objective fields, no site branding, links to the fully compliant detail page (Rules 10, 13, 22 exemptions) --}}
    <a class="li-card" href="/listings/{{ $l->listing_id }}">
      <div class="li-photo" style="background-image:url('{{ $l->photoUrl() ?? '' }}')"><span class="li-status">{{ $l->status }}</span></div>
      <div class="li-body">
        <div class="li-price">{{ $l->list_price ? '$'.number_format($l->list_price) : ($l->is_auction ? 'Auction — see details' : 'Price on request') }}</div>
        <div class="li-addr">{{ $l->displayAddress() }}</div>
        <div class="li-meta">{{ $l->beds }} bd &middot; {{ $l->baths() }} ba &middot; {{ $l->sqft ? number_format($l->sqft).' sqft' : '—' }} &middot; MLS #{{ $l->listing_id }}</div>
        <div class="li-office">Listing courtesy of {{ $l->list_office_name }}</div>
      </div>
    </a>
    @endforeach
  </div>

  @if($pages > 1)
  <div style="text-align:center;margin-top:28px;">
    @for($i = 1; $i <= $pages; $i++)
      <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" style="display:inline-block;margin:2px;padding:8px 13px;border-radius:6px;font-weight:700;text-decoration:none;{{ $i === $page ? 'background:#0F1E2E;color:#fff;' : 'background:#fff;border:1px solid #c9d2e3;color:#0F1E2E;' }}">{{ $i }}</a>
    @endfor
  </div>
  @endif
</div>

@include('listings._compliance')
</x-site.layout>
