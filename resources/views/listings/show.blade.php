<x-site.layout :page="null" :head="null" title="Listing Detail">
<x-slot:headExtra>
<title>{{ $l->address_public ? $l->street_address.', '.$l->city : 'Home for Sale in '.$l->city }} — MLS #{{ $l->listing_id }} | Dawn Simmons Team</title>
<meta name="description" content="{{ Str::limit(strip_tags((string) $l->remarks), 155) ?: 'Listing in '.$l->city.', IL — courtesy of '.$l->list_office_name.' via MRED / MLS GRID.' }}">
<meta name="robots" content="{{ config('services.mlsgrid.token') ? 'index,follow' : 'noindex,nofollow' }}">
</x-slot:headExtra>
<style>
  .ld-wrap { max-width:1100px; margin:0 auto; padding:32px 24px; font-family:'Archivo',Arial,sans-serif; }
  .ld-gallery { display:grid; grid-template-columns:3fr 1fr; gap:8px; border-radius:14px; overflow:hidden; }
  .ld-gallery a { display:block; aspect-ratio:3/2; background:#E9EFF3 center/cover no-repeat; }
  .ld-gallery a:first-child { grid-row:span 3; aspect-ratio:auto; min-height:280px; max-height:430px; }
  .ld-gallery.ld-few { grid-template-columns:1fr 1fr; }
  .ld-gallery.ld-few a:first-child { grid-row:auto; grid-column:1/-1; aspect-ratio:2/1; min-height:0; max-height:none; }
  .ld-gallery.ld-open { grid-template-columns:repeat(3,1fr); }
  .ld-gallery.ld-open a:first-child { grid-row:auto; aspect-ratio:3/2; min-height:0; max-height:none; }
  .ld-morebtn { margin-top:10px; background:#fff; border:1px solid #c9d2e3; color:#0F1E2E; border-radius:6px; padding:9px 16px; font-weight:700; font-size:13px; cursor:pointer; }
  @media (max-width:700px) { .ld-gallery { grid-template-columns:1fr 1fr; } }
  .ld-head { display:flex; flex-wrap:wrap; justify-content:space-between; gap:16px; align-items:start; margin:24px 0 6px; }
  .ld-price { font-size:34px; font-weight:800; color:#0F1E2E; }
  .ld-status { display:inline-block; background:#0F1E2E; color:#fff; font-size:12px; font-weight:700; padding:5px 12px; border-radius:5px; }
  .ld-facts { display:flex; flex-wrap:wrap; gap:22px; font-size:15px; color:#333; margin:14px 0 20px; }
  .ld-facts strong { color:#0F1E2E; }
  /* Rule 22: brokerage attribution immediately adjacent to the property information */
  .ld-attrib { background:#F2F5F9; border-left:4px solid #C8102E; border-radius:8px; padding:14px 18px; font-size:13.5px; color:#333; margin:0 0 22px; }
  .ld-remarks { font-family:Georgia,serif; font-size:16.5px; line-height:1.8; color:#333; max-width:820px; }
  .ld-tour { display:inline-block; margin:14px 0 0; color:#C8102E; font-weight:700; text-decoration:none; }
  .ld-h2 { font-family:Georgia,serif; font-size:22px; color:#0F1E2E; margin:34px 0 14px; }
  .ld-rooms { width:100%; border-collapse:collapse; font-size:14px; }
  .ld-rooms th { text-align:left; font-size:11.5px; letter-spacing:.8px; text-transform:uppercase; color:#48586B; border-bottom:2px solid #DEE6EE; padding:8px 10px; }
  .ld-rooms td { border-bottom:1px solid #E9EFF3; padding:9px 10px; color:#333; }
  .ld-sections { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:16px; margin-top:8px; }
  .ld-card { background:#fff; border:1px solid #DEE6EE; border-radius:12px; padding:18px 20px; }
  .ld-card h3 { font-size:12px; letter-spacing:1.2px; text-transform:uppercase; color:#C8102E; margin:0 0 10px; }
  .ld-card dl { margin:0; font-size:13.5px; }
  .ld-card dt { color:#48586B; float:left; clear:left; width:42%; padding:4px 0; }
  .ld-card dd { margin:0 0 0 44%; padding:4px 0; color:#0F1E2E; }
  .ld-cta { background:#0F1E2E; border-radius:12px; color:#fff; padding:28px; margin-top:30px; }
  .ld-cta a { background:#C8102E; color:#fff; padding:12px 22px; border-radius:999px; font-weight:700; text-decoration:none; display:inline-block; margin-top:10px; }
</style>

<div class="ld-wrap">
  {{-- Locally cached gallery: MLS GRID media URLs expire and rate-limit hotlinks.
       Collapsed = hero + 4; the button reveals every cached photo. --}}
  @php $photos = $l->photoUrls(); @endphp
  <div class="ld-gallery{{ count($photos) < 4 ? ' ld-few' : '' }}" id="ldGallery">
    @forelse($photos as $i => $p)
    <a href="{{ $p }}" target="_blank" rel="noopener" style="background-image:url('{{ $p }}')"
       aria-label="Photo {{ $i + 1 }} of {{ count($photos) }}" @if($i > 3) hidden @endif></a>
    @empty
    <a style="pointer-events:none"></a><a style="pointer-events:none"></a><a style="pointer-events:none"></a>
    @endforelse
  </div>
  @if(count($photos) > 4)
  <button type="button" class="ld-morebtn" id="ldMoreBtn">See all {{ count($photos) }} photos</button>
  <script>
  document.getElementById('ldMoreBtn').addEventListener('click', function () {
    document.querySelectorAll('#ldGallery a[hidden]').forEach(function (a) { a.hidden = false; });
    document.getElementById('ldGallery').classList.add('ld-open');
    this.remove();
  });
  </script>
  @endif

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
    @if($l->days_on_market !== null)<span><strong>{{ $l->days_on_market }}</strong> days on market</span>@endif
  </div>

  {{-- Rule 22: listing brokerage, MLS number, provided contact, status — adjacent to property info --}}
  <div class="ld-attrib">
    <strong>Listing courtesy of {{ $l->list_office_name }}</strong>
    @if($l->list_office_phone) &middot; {{ $l->list_office_phone }} @endif
    @if($l->list_office_email) &middot; {{ $l->list_office_email }} @endif
    &middot; MLS #{{ $l->listing_id }} &middot; Status: {{ $l->status }}
  </div>

  @if($l->remarks)<div class="ld-remarks">{{ $l->remarks }}</div>@endif
  @if($l->virtual_tour_url)<a class="ld-tour" href="{{ $l->virtual_tour_url }}" rel="noopener nofollow" target="_blank">&#127909; Virtual tour &rarr;</a>@endif

  @if($l->rooms->isNotEmpty())
  <h2 class="ld-h2">Rooms</h2>
  <table class="ld-rooms">
    <thead><tr><th>Room</th><th>Size</th><th>Level</th><th>Flooring</th></tr></thead>
    <tbody>
      @foreach($l->rooms as $room)
      <tr><td>{{ $room->name }}</td><td>{{ $room->dimensions }}</td><td>{{ $room->level }}</td><td>{{ $room->flooring }}</td></tr>
      @endforeach
    </tbody>
  </table>
  @endif

  @php
    $fmtMoney = fn ($v) => $v !== null ? '$'.number_format($v) : null;
    $yn = fn ($v) => $v ? 'Yes' : null;
    $sections = [
      'Interior' => [
        'Appliances' => $l->featureList('appliances'),
        'Interior features' => $l->featureList('interior'),
        'Flooring' => $l->featureList('flooring'),
        'Fireplaces' => $l->fireplaces,
        'Fireplace details' => $l->featureList('fireplace'),
        'Fireplace location' => $l->featureList('fireplace_location'),
        'Basement' => $l->featureList('basement'),
        'Laundry' => $l->featureList('laundry'),
        'Additional rooms' => $l->featureList('additional_rooms'),
        'Total rooms' => $l->rooms_total,
      ],
      'Building & exterior' => [
        'Style' => $l->featureList('style'),
        'Age' => $l->age_range,
        'Stories' => $l->stories,
        'Exterior' => $l->featureList('construction'),
        'Exterior features' => $l->featureList('exterior'),
        'Roof' => $l->featureList('roof'),
        'Foundation' => $l->featureList('foundation'),
        'Windows' => $l->featureList('windows'),
        'Doors' => $l->featureList('doors'),
        'Patio & porch' => $l->featureList('patio'),
        'Other structures' => $l->featureList('structures'),
        'New construction' => $yn($l->new_construction),
        'Exposure' => $l->exposure,
      ],
      'Parking' => [
        'Garage spaces' => $l->garage_spaces,
        'Total spaces' => $l->parking_total,
        'Parking details' => $l->featureList('parking'),
      ],
      'Lot & location' => [
        'Lot dimensions' => $l->lot_dimensions,
        'Lot description' => $l->featureList('lot'),
        'Township' => $l->township,
        'County' => $l->county,
        'Waterfront' => $yn($l->waterfront),
        'Water body' => $l->water_body,
      ],
      'Utilities' => [
        'Heating' => $l->featureList('heating'),
        'Cooling' => $l->featureList('cooling'),
        'Water' => $l->featureList('water'),
        'Sewer' => $l->featureList('sewer'),
        'Electricity' => $l->featureList('electric'),
        'Equipment' => $l->featureList('equipment'),
      ],
      'HOA & community' => [
        'Assessment' => $l->hoa_fee ? $fmtMoney($l->hoa_fee).($l->hoa_fee_freq ? ' / '.strtolower($l->hoa_fee_freq) : '') : null,
        'Assessment includes' => $l->featureList('hoa_includes'),
        'Amenities' => $l->featureList('amenities'),
        'Community features' => $l->featureList('community'),
        'Pets' => $l->featureList('pets'),
      ],
      'Schools' => [
        'Elementary' => trim(($l->elementary_school ?? '').($l->elementary_district ? ' ('.$l->elementary_district.')' : '')) ?: null,
        'Junior high' => trim(($l->middle_school ?? '').($l->middle_district ? ' ('.$l->middle_district.')' : '')) ?: null,
        'High school' => trim(($l->high_school ?? '').($l->high_district ? ' ('.$l->high_district.')' : '')) ?: null,
      ],
      'Taxes & terms' => [
        'Annual taxes' => $l->tax_annual ? $fmtMoney($l->tax_annual).($l->tax_year ? ' ('.$l->tax_year.')' : '') : null,
        'PIN' => $l->parcel_number,
        'Ownership' => $l->ownership,
        'Possession' => $l->featureList('possession'),
        'Special conditions' => $l->featureList('conditions'),
        'Listed' => $l->listing_contract_date?->format('n/j/Y'),
      ] + ($l->status === 'Closed'
        ? ['Sold' => $l->close_date?->format('n/j/Y'), 'Sold price' => $fmtMoney($l->close_price)]
        : []),
    ];
    $sections = array_filter(array_map(fn ($rows) => array_filter($rows, fn ($v) => $v !== null && $v !== ''), $sections));
  @endphp

  @if($sections)
  <h2 class="ld-h2">Property details</h2>
  <div class="ld-sections">
    @foreach($sections as $heading => $rows)
    <div class="ld-card">
      <h3>{{ $heading }}</h3>
      <dl>
        @foreach($rows as $label => $value)
        <dt>{{ $label }}</dt><dd>{{ $value }}</dd>
        @endforeach
      </dl>
    </div>
    @endforeach
  </div>
  @endif

  @if($l->status !== 'Closed')
  @include('listings._calculator')
  @endif

  <div class="ld-cta">
    <div style="font-family:Georgia,serif;font-size:20px;font-weight:700;">Want to see this home?</div>
    <p style="color:rgba(255,255,255,.85);margin:8px 0 0;">The Dawn Simmons Team (RE/MAX Suburban) can show you any listed property and give you a straight answer on what it's really worth. Call/text Josh at {{ config('site.josh_cell', '(224) 628-4013') }}.</p>
    <a href="/contact">Ask About This Home &rarr;</a>
  </div>
</div>

@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
