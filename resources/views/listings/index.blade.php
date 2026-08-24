<x-site.layout :page="null" :head="null" title="Homes for Sale — Northwest Suburbs | The Dawn Simmons Team">
<x-slot:headExtra>
<title>Homes for Sale in the Northwest Suburbs | The Dawn Simmons Team – RE/MAX Suburban</title>
<meta name="description" content="Search homes for sale across Chicago's northwest suburbs with the Dawn Simmons Team, RE/MAX Suburban. Listings courtesy of MRED as distributed by MLS GRID, updated throughout the day.">
<meta name="robots" content="{{ config('services.mlsgrid.token') ? 'index,follow' : 'noindex,nofollow' }}">
</x-slot:headExtra>
<style>
  .li-wrap { max-width:1180px; margin:0 auto; padding:32px 24px; font-family:Arial,sans-serif; }
  .li-hero { background:linear-gradient(140deg,#0D2349 0%,#1e4080 60%,#2a5298 100%); color:#fff; text-align:center; padding:52px 24px 40px; }
  .li-hero h1 { font-family:Georgia,serif; font-size:clamp(26px,4vw,42px); margin:0 0 10px; }
  .li-hero p { color:rgba(255,255,255,.8); margin:0; }
  .li-filters { display:flex; flex-wrap:wrap; gap:10px; align-items:end; background:#fff; border:1px solid #e0e4ed; border-radius:10px; padding:16px; margin:-28px auto 28px; max-width:1000px; box-shadow:0 6px 24px rgba(13,35,73,.12); position:relative; }
  .li-filters label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#1B3A6B; display:block; }
  .li-filters select, .li-filters input { padding:9px 10px; border:1px solid #c9d2e3; border-radius:6px; font-size:14px; min-width:120px; }
  .li-filters button { background:#CC0000; color:#fff; border:0; border-radius:6px; padding:11px 22px; font-weight:700; cursor:pointer; }
  .li-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }
  .li-card { display:block; background:#fff; border:1px solid #e0e4ed; border-radius:10px; overflow:hidden; text-decoration:none; color:#222; transition:box-shadow .15s; }
  .li-card:hover { box-shadow:0 8px 28px rgba(13,35,73,.16); }
  .li-photo { aspect-ratio:3/2; background:#e9edf3 center/cover no-repeat; position:relative; }
  .li-status { position:absolute; top:10px; left:10px; background:#1B3A6B; color:#fff; font-size:11px; font-weight:700; padding:4px 10px; border-radius:4px; }
  .li-body { padding:16px; }
  .li-price { font-size:22px; font-weight:800; color:#1B3A6B; }
  .li-addr { font-size:14px; color:#444; margin:4px 0 8px; }
  .li-meta { font-size:13px; color:#666; }
  .li-office { font-size:11.5px; color:#888; margin-top:8px; border-top:1px dashed #e0e4ed; padding-top:8px; }
  .demo-banner { background:#fff7e0; border:1px solid #e2cd86; color:#7a5d12; border-radius:8px; padding:12px 16px; margin:0 auto 22px; max-width:1000px; font-size:14px; }
</style>

<div class="li-hero">
  <h1>Homes for Sale in the Northwest Suburbs</h1>
  <p>Every active listing in the communities we serve &mdash; updated throughout the day from the MLS via MLS GRID.</p>
</div>

<div class="li-wrap">
  @if($demo)
    <div class="demo-banner"><strong>Display preview:</strong> the listings below are clearly-marked sample records used to preview this page's layout. Live MRED listing data activates upon MLS GRID approval.</div>
  @endif

  <form class="li-filters" method="get" action="/listings">
    <div><label for="f-city">City</label><select id="f-city" name="city"><option value="">All cities</option>@foreach($cities as $c)<option value="{{ $c }}" @selected(($filters['city'] ?? '') === $c)>{{ $c }}</option>@endforeach</select></div>
    <div><label for="f-min">Min Price</label><select id="f-min" name="min"><option value="">No min</option>@foreach([200000,300000,400000,500000,750000,1000000] as $v)<option value="{{ $v }}" @selected(($filters['min'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-max">Max Price</label><select id="f-max" name="max"><option value="">No max</option>@foreach([300000,400000,500000,750000,1000000,2000000] as $v)<option value="{{ $v }}" @selected(($filters['max'] ?? '') == $v)>${{ number_format($v/1000) }}K</option>@endforeach</select></div>
    <div><label for="f-beds">Beds</label><select id="f-beds" name="beds"><option value="">Any</option>@foreach([1,2,3,4,5] as $v)<option value="{{ $v }}" @selected(($filters['beds'] ?? '') == $v)>{{ $v }}+</option>@endforeach</select></div>
    <div><button type="submit">Search</button></div>
  </form>

  <p style="font-size:14px;color:#666;margin:0 0 18px;">{{ number_format($total) }} {{ Str::plural('listing', $total) }} found{{ ($filters['city'] ?? null) ? ' in '.$filters['city'] : '' }}.</p>

  <div class="li-grid">
    @foreach($listings as $l)
    {{-- Thumbnail: ≤8 objective fields, no site branding, links to the fully compliant detail page (Rules 10, 13, 22 exemptions) --}}
    <a class="li-card" href="/listings/{{ $l->listing_id }}">
      <div class="li-photo" style="background-image:url('{{ $l->media[0]['url'] ?? '' }}')"><span class="li-status">{{ $l->status }}</span></div>
      <div class="li-body">
        <div class="li-price">${{ number_format($l->list_price) }}</div>
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
      <a href="{{ request()->fullUrlWithQuery(['page' => $i]) }}" style="display:inline-block;margin:2px;padding:8px 13px;border-radius:6px;font-weight:700;text-decoration:none;{{ $i === $page ? 'background:#1B3A6B;color:#fff;' : 'background:#fff;border:1px solid #c9d2e3;color:#1B3A6B;' }}">{{ $i }}</a>
    @endfor
  </div>
  @endif
</div>

@include('listings._compliance')
</x-site.layout>
