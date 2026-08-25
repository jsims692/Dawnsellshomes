<x-site.layout :page="null" :head="null" title="Listing Detail">
<x-slot:headExtra>
<title>{{ $l->address_public ? $l->street_address.', '.$l->city : 'Home for Sale in '.$l->city }} — MLS #{{ $l->listing_id }} | Dawn Simmons Team</title>
<meta name="description" content="{{ Str::limit(strip_tags((string) $l->remarks), 155) ?: 'Listing in '.$l->city.', IL — courtesy of '.$l->list_office_name.' via MRED / MLS GRID.' }}">
<meta name="robots" content="{{ config('services.mlsgrid.token') ? 'index,follow' : 'noindex,nofollow' }}">
</x-slot:headExtra>
<style>
  .ld-wrap { max-width:1100px; margin:0 auto; padding:32px 24px; font-family:Arial,sans-serif; }
  .ld-gallery { display:grid; grid-template-columns:2fr 1fr; gap:8px; border-radius:12px; overflow:hidden; }
  .ld-gallery div { aspect-ratio:3/2; background:#e9edf3 center/cover no-repeat; }
  .ld-head { display:flex; flex-wrap:wrap; justify-content:space-between; gap:16px; align-items:start; margin:24px 0 6px; }
  .ld-price { font-size:34px; font-weight:800; color:#1B3A6B; }
  .ld-status { display:inline-block; background:#1B3A6B; color:#fff; font-size:12px; font-weight:700; padding:5px 12px; border-radius:5px; }
  .ld-facts { display:flex; flex-wrap:wrap; gap:22px; font-size:15px; color:#333; margin:14px 0 20px; }
  .ld-facts strong { color:#1B3A6B; }
  /* Rule 22: brokerage attribution immediately adjacent to the property information */
  .ld-attrib { background:#F0F2F5; border-left:4px solid #C8A84B; border-radius:8px; padding:14px 18px; font-size:13.5px; color:#333; margin:0 0 22px; }
  .ld-remarks { font-family:Georgia,serif; font-size:16.5px; line-height:1.8; color:#333; max-width:820px; }
  .ld-cta { background:#1B3A6B; border-radius:12px; color:#fff; padding:28px; margin-top:30px; }
  .ld-cta a { background:#CC0000; color:#fff; padding:12px 22px; border-radius:6px; font-weight:700; text-decoration:none; display:inline-block; margin-top:10px; }
</style>

<div class="ld-wrap">
  @if($l->is_demo)<div style="background:#fff7e0;border:1px solid #e2cd86;color:#7a5d12;border-radius:8px;padding:12px 16px;margin-bottom:18px;font-size:14px;"><strong>SAMPLE LISTING</strong> — for display review only; not a real property.</div>@endif

  {{-- Locally cached photo only: MLS GRID media URLs expire and rate-limit hotlinks --}}
  <div class="ld-gallery">
    <div style="background-image:url('{{ $l->photoUrl() ?? '' }}')"></div>
    <div style="background-image:url('{{ $l->photoUrl() ?? '' }}')"></div>
  </div>

  <div class="ld-head">
    <div>
      <div class="ld-price">${{ number_format($l->list_price) }}</div>
      <h1 style="font-family:Georgia,serif;font-size:22px;color:#222;margin:6px 0 0;">{{ $l->displayAddress() }}</h1>
    </div>
    <span class="ld-status">{{ $l->status }}</span>
  </div>

  <div class="ld-facts">
    <span><strong>{{ $l->beds }}</strong> beds</span>
    <span><strong>{{ $l->baths() }}</strong> baths</span>
    @if($l->sqft)<span><strong>{{ number_format($l->sqft) }}</strong> sqft</span>@endif
    @if($l->year_built)<span>Built <strong>{{ $l->year_built }}</strong></span>@endif
    <span>{{ $l->property_subtype ?: $l->property_type }}</span>
    <span>MLS #<strong>{{ $l->listing_id }}</strong></span>
  </div>

  {{-- Rule 22: listing brokerage, MLS number, provided contact, status — adjacent to property info --}}
  <div class="ld-attrib">
    <strong>Listing courtesy of {{ $l->list_office_name }}</strong>
    @if($l->list_office_phone) &middot; {{ $l->list_office_phone }} @endif
    @if($l->list_office_email) &middot; {{ $l->list_office_email }} @endif
    &middot; MLS #{{ $l->listing_id }} &middot; Status: {{ $l->status }}
  </div>

  @if($l->remarks)<div class="ld-remarks">{{ $l->remarks }}</div>@endif

  <div class="ld-cta">
    <div style="font-family:Georgia,serif;font-size:20px;font-weight:700;">Want to see this home?</div>
    <p style="color:rgba(255,255,255,.85);margin:8px 0 0;">The Dawn Simmons Team (RE/MAX Suburban) can show you any listed property and give you a straight answer on what it's really worth. Call/text Josh at {{ config('site.josh_cell', '(224) 628-4013') }}.</p>
    <a href="/#contact">Ask About This Home &rarr;</a>
  </div>
</div>

@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
