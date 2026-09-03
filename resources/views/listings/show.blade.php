<x-site.layout :page="null" :head="null" title="Listing Detail">
<x-slot:headExtra>
<title>{{ $l->address_public ? $l->street_address.', '.$l->city : 'Home for Sale in '.$l->city }} — MLS #{{ $l->listing_id }} | Dawn Simmons Team</title>
<meta name="description" content="{{ Str::limit(strip_tags((string) $l->remarks), 155) ?: 'Listing in '.$l->city.', IL — courtesy of '.$l->list_office_name.' via MRED / MLS GRID.' }}">
{{-- Sold pages stay out of the index: they purge at the 12-month retention
     boundary (mass-404 churn) and their on-demand galleries cost MLS budget
     per first view — for humans following comps, not crawler sweeps. --}}
<meta name="robots" content="{{ config('services.mlsgrid.token') && $l->isForSale() ? 'index,follow' : 'noindex,follow' }}">
<link rel="canonical" href="https://dawnsellshomes.com/listings/{{ $l->listing_id }}">
<meta property="og:title" content="{{ $l->address_public ? $l->street_address.', '.$l->city.', IL' : 'Home for Sale in '.$l->city.', IL' }}{{ $l->isForSale() && $l->list_price ? ' — $'.number_format($l->list_price) : '' }}">
<meta property="og:description" content="{{ $l->beds }} bed · {{ $l->baths() }} bath{{ $l->sqft ? ' · '.number_format($l->sqft).' sqft' : '' }} — listing courtesy of {{ $l->list_office_name }} via MRED / MLS GRID.">
<meta property="og:type" content="website">
<meta property="og:url" content="https://dawnsellshomes.com/listings/{{ $l->listing_id }}">
@if($l->photoUrl())
@php [$ogW, $ogH] = @getimagesize(public_path(ltrim((string) parse_url($l->photoUrl(), PHP_URL_PATH), '/'))) ?: [null, null]; @endphp
<meta property="og:image" content="{{ url($l->photoUrl()) }}">
@if($ogW)<meta property="og:image:width" content="{{ $ogW }}"><meta property="og:image:height" content="{{ $ogH }}">@endif
<meta property="og:image:alt" content="{{ $l->address_public && $l->street_address ? $l->street_address.', '.$l->city : 'Home in '.$l->city }}">
@endif
@php
  $ld = [
    '@context' => 'https://schema.org',
    '@type' => 'RealEstateListing',
    'name' => $l->address_public && $l->street_address ? $l->street_address.', '.$l->city.', IL' : 'Home in '.$l->city.', IL',
    'url' => 'https://dawnsellshomes.com/listings/'.$l->listing_id,
  ];
  if ($l->photoUrl()) $ld['image'] = url($l->photoUrl());
  if ($l->address_public && $l->street_address) {
    $ld['address'] = ['@type' => 'PostalAddress', 'streetAddress' => $l->street_address,
      'addressLocality' => $l->city, 'addressRegion' => 'IL', 'postalCode' => $l->zip];
  }
  if ($l->beds) $ld['numberOfBedrooms'] = (int) $l->beds;
  if ($l->sqft) $ld['floorSize'] = ['@type' => 'QuantitativeValue', 'value' => (int) $l->sqft, 'unitCode' => 'FTK'];
  if ($l->isForSale() && $l->list_price && ! $l->is_auction) {
    $ld['offers'] = ['@type' => 'Offer', 'price' => (int) $l->list_price, 'priceCurrency' => 'USD',
      'availability' => 'https://schema.org/InStock'];
  }
  $crumbs = ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => [
    ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => 'https://dawnsellshomes.com/'],
    ['@type' => 'ListItem', 'position' => 2, 'name' => 'Homes for Sale', 'item' => 'https://dawnsellshomes.com/listings'],
    ['@type' => 'ListItem', 'position' => 3, 'name' => $l->city, 'item' => 'https://dawnsellshomes.com/listings?city='.rawurlencode((string) $l->city)],
    ['@type' => 'ListItem', 'position' => 4, 'name' => $ld['name']],
  ]];
@endphp
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_SLASHES) !!}</script>
<script type="application/ld+json">{!! json_encode($crumbs, JSON_UNESCAPED_SLASHES) !!}</script>
</x-slot:headExtra>
<style>
  .ld-wrap { max-width:1100px; margin:0 auto; padding:32px 24px; font-family:'Archivo',Arial,sans-serif; }
  .ld-gallery { display:grid; grid-template-columns:repeat(3,1fr); gap:8px; border-radius:14px; overflow:hidden; }
  .ld-gallery a { display:block; aspect-ratio:3/2; background:#E9EFF3 center/cover no-repeat; }
  .ld-gallery a[hidden] { display:none !important; }
  .ld-gallery a:first-child { grid-column:1/-1; aspect-ratio:21/9; max-height:430px; }
  .ld-gallery.ld-few { grid-template-columns:repeat(2,1fr); }
  .ld-gallery.ld-few a:nth-child(2):last-child { grid-column:1/-1; aspect-ratio:5/2; }
  .ld-gallery.ld-open a:first-child { grid-column:auto; aspect-ratio:3/2; max-height:none; }
  .ld-morebtn { margin-top:10px; background:#fff; border:1px solid #c9d2e3; color:#0F1E2E; border-radius:6px; padding:9px 16px; font-weight:700; font-size:13px; cursor:pointer; }
  @media (max-width:700px) { .ld-gallery { grid-template-columns:1fr 1fr; } }
  .ld-head { display:flex; flex-wrap:wrap; justify-content:space-between; gap:16px; align-items:start; margin:24px 0 6px; }
  .ld-price { font-size:34px; font-weight:800; color:#0F1E2E; }
  .ld-status { display:inline-block; background:#0F1E2E; color:#fff; font-size:12px; font-weight:700; padding:5px 12px; border-radius:5px; }
  .ld-share { background:#fff; border:1.5px solid #c9d2e3; color:#0F1E2E; font-size:13px; font-weight:700; padding:6px 14px; border-radius:999px; cursor:pointer; font-family:inherit; }
  .ld-share:hover { border-color:#C8102E; color:#C8102E; }
  .ld-share--done { border-color:#1d6b35; color:#1d6b35; }
  .ld-facts { display:flex; flex-wrap:wrap; gap:22px; font-size:15px; color:#333; margin:14px 0 20px; }
  .ld-facts strong { color:#0F1E2E; }
  /* Rule 22: brokerage attribution immediately adjacent to the property information */
  .ld-attrib { background:#F2F5F9; border-left:4px solid #C8102E; border-radius:8px; padding:14px 18px; font-size:13.5px; color:#333; margin:0 0 22px; }
  .ld-remarks { font-family:Georgia,serif; font-size:16.5px; line-height:1.8; color:#333; max-width:820px; }
  .ld-tour { display:inline-block; margin:14px 0 0; color:#C8102E; font-weight:700; text-decoration:none; }
  .ld-h2 { font-family:'Fraunces',Georgia,serif; font-size:22px; color:#0F1E2E; margin:34px 0 14px; }
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
    <a href="{{ $p }}" data-i="{{ $i }}" style="background-image:url('{{ $p }}')"
       aria-label="Photo {{ $i + 1 }} of {{ count($photos) }}" @if($i > 3) hidden @endif></a>
    @empty
    <a style="pointer-events:none"></a><a style="pointer-events:none"></a><a style="pointer-events:none"></a>
    @endforelse
  </div>

  @if(count($photos) > 0)
  {{-- Fullscreen photo feed: tap any photo and it opens a vertically
       scrollable column of every photo, anchored at the one tapped — scroll
       through them all, no swiping or clicking picture-to-picture. Close
       with X, Esc, or the phone's back button. --}}
  <div class="lbx" id="lbx" hidden role="dialog" aria-label="Photo viewer">
    <button type="button" class="lbx-x" id="lbxClose" aria-label="Close">&times;</button>
    <div class="lbx-count" id="lbxCount"></div>
    <div class="lbx-scroll" id="lbxScroll" tabindex="-1"></div>
  </div>
  <style>
    .lbx { position:fixed; inset:0; z-index:10000; background:rgba(8,13,20,.97); }
    .lbx[hidden] { display:none; }
    .lbx-scroll { position:absolute; inset:0; overflow-y:auto; overscroll-behavior:contain; -webkit-overflow-scrolling:touch; padding:56px 0 34px; outline:none; }
    /* auto 3/2: real ratio once loaded, a stable placeholder box before —
       so anchoring to a tapped photo doesn't jump as neighbors load. */
    .lbx-scroll img { display:block; width:min(96vw,900px); height:auto; aspect-ratio:auto 3/2; margin:0 auto 10px; border-radius:6px; background:#16202c; }
    .lbx-x { position:fixed; top:10px; right:12px; background:rgba(8,13,20,.55); border:0; color:#fff; font-size:34px; line-height:1; cursor:pointer; padding:6px 13px; border-radius:10px; z-index:2; }
    .lbx-count { position:fixed; top:16px; left:50%; transform:translateX(-50%); background:rgba(8,13,20,.55); color:rgba(255,255,255,.92); font-size:13px; font-weight:700; letter-spacing:.5px; padding:6px 13px; border-radius:999px; z-index:2; }
  </style>
  <script>
  (function () {
    var pics = @json(array_values($photos));
    var box = document.getElementById('lbx'), scroller = document.getElementById('lbxScroll'),
        count = document.getElementById('lbxCount'), built = false, pushed = false;
    function build() {
      if (built) return; built = true;
      pics.forEach(function (p, i) {
        var im = document.createElement('img');
        im.dataset.src = p;
        im.alt = 'Photo ' + (i + 1) + ' of ' + pics.length;
        scroller.appendChild(im);
      });
      var io = new IntersectionObserver(function (entries) {
        entries.forEach(function (en) {
          if (!en.isIntersecting) return;
          if (en.target.dataset.src) { en.target.src = en.target.dataset.src; delete en.target.dataset.src; }
          io.unobserve(en.target);
        });
      }, { root: scroller, rootMargin: '150% 0px' });
      Array.prototype.forEach.call(scroller.children, function (im) { io.observe(im); });
      scroller.addEventListener('scroll', onScroll, { passive: true });
    }
    function onScroll() {
      var kids = scroller.children, mid = scroller.scrollTop + scroller.clientHeight / 2, i = 0;
      while (i < kids.length - 1 && kids[i].offsetTop + kids[i].offsetHeight < mid) i++;
      count.textContent = (i + 1) + ' / ' + pics.length;
    }
    function open(i) {
      build();
      box.hidden = false;
      document.body.style.overflow = 'hidden';
      var t = scroller.children[i];
      if (t.dataset.src) { t.src = t.dataset.src; delete t.dataset.src; }
      requestAnimationFrame(function () { t.scrollIntoView({ block: 'start' }); onScroll(); scroller.focus(); });
      try { history.pushState({ lbx: 1 }, ''); pushed = true; } catch (e) { pushed = false; }
    }
    function close(fromPop) {
      box.hidden = true;
      document.body.style.overflow = '';
      if (pushed && !fromPop) { pushed = false; history.back(); } else { pushed = false; }
    }
    document.getElementById('ldGallery').addEventListener('click', function (e) {
      var a = e.target.closest('a[data-i]');
      if (!a) return;
      e.preventDefault();
      open(parseInt(a.dataset.i, 10));
    });
    document.getElementById('lbxClose').addEventListener('click', function () { close(false); });
    window.addEventListener('popstate', function () { if (!box.hidden) close(true); });
    document.addEventListener('keydown', function (e) {
      if (!box.hidden && e.key === 'Escape') close(false);
    });
  })();
  </script>
  @endif
  {{-- Only announce the fetch when the page actually looks bare: with 5+
       photos already cached (hero + grid filled), a visible banner and a
       reload are worse than letting the tail arrive silently. --}}
  @if(($galleryFetching ?? false) && count($photos) < 5)
  <p style="margin:10px 0 0;font-size:13px;color:#48586B;background:#F2F5F9;border-radius:8px;padding:10px 14px;">
    &#128247; Retrieving this home's full photo gallery from the MLS &mdash; the page will refresh in a moment.
  </p>
  <script>
  (function () {
    var k = 'gref-{{ $l->listing_id }}', n = parseInt(sessionStorage.getItem(k) || '0', 10);
    if (n < 6) { sessionStorage.setItem(k, String(n + 1)); setTimeout(function () { location.reload(); }, 20000); }
  })();
  </script>
  @endif
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
      @if($l->status === 'Closed' && $l->close_price)
      <div class="ld-price">${{ number_format($l->close_price) }} <span style="font-size:14px;font-weight:800;color:#C8102E;vertical-align:middle;">SOLD {{ $l->close_date?->format('M Y') }}</span></div>
      <div style="font-size:13.5px;color:#48586B;">Listed at ${{ number_format($l->list_price) }}</div>
      @else
      <div class="ld-price">{{ $l->list_price ? '$'.number_format($l->list_price) : ($l->is_auction ? 'Auction' : 'Price on request') }}</div>
      @if($l->previous_price && $l->price_dropped_at && $l->previous_price > $l->list_price)
      <div style="font-size:13.5px;color:#1d6b35;font-weight:700;">&#8595; Reduced from ${{ number_format($l->previous_price) }} on {{ $l->price_dropped_at->format('M j') }}</div>
      @endif
      @endif
      <h1 style="font-family:'Fraunces',Georgia,serif;font-size:22px;color:#222;margin:6px 0 0;">{{ $l->displayAddress() }}</h1>
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
      <button type="button" id="ld-share" class="ld-share">&#8599; Share</button>
      <span class="ld-status">{{ $l->status }}</span>
    </div>
  </div>
  <script>
  (function () {
    var b = document.getElementById('ld-share');
    if (!b) return;
    var payload = {
      title: @json($l->address_public && $l->street_address ? $l->street_address.', '.$l->city : 'Home in '.$l->city.', IL'),
      text: @json(($l->address_public && $l->street_address ? $l->street_address.', '.$l->city : 'A home in '.$l->city).($l->isForSale() && $l->list_price ? ' — $'.number_format($l->list_price) : '').' (via Dawn Simmons Team)'),
      url: window.location.href
    };
    b.addEventListener('click', function () {
      if (navigator.share) {
        navigator.share(payload).catch(function () {});
      } else if (navigator.clipboard) {
        navigator.clipboard.writeText(payload.url).then(function () {
          var old = b.innerHTML;
          b.innerHTML = '&#10003; Link copied';
          b.classList.add('ld-share--done');
          setTimeout(function () { b.innerHTML = old; b.classList.remove('ld-share--done'); }, 2200);
        });
      }
    });
  })();
  </script>

  <div class="ld-facts">
    <span><strong>{{ $l->beds }}</strong> beds{{ $l->bedrooms_possible > $l->beds ? ' ('.$l->bedrooms_possible.' possible)' : '' }}</span>
    <span><strong>{{ $l->baths() }}</strong> baths</span>
    @if($l->sqft)<span><strong>{{ number_format($l->sqft) }}</strong> sqft{{ $l->living_area_source ? ' ('.strtolower($l->living_area_source).')' : '' }}</span>@endif
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
        'Entry level' => $l->entry_level,
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
        'Parcel number (PIN)' => $l->parcel_number,
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

  @include('listings._calculator')

  @if($subUrl && $l->subdivision)
  <p style="margin:22px 0 0;font-size:14.5px;">&#127968; This home is in <a href="{{ $subUrl }}" style="color:#C8102E;font-weight:700;">{{ $l->subdivision }}</a> &mdash; see the community's listings, sold prices, and market stats.</p>
  @endif

  @if(!empty($nearbySolds))
  <h2 class="ld-h2">Recently sold nearby</h2>
  <table class="ld-rooms" style="max-width:760px;">
    <tr><th>Address</th><th>Beds / Baths</th><th>Sold</th><th>Price</th></tr>
    @foreach($nearbySolds as $s)
    <tr>
      <td><a href="/listings/{{ $s['id'] }}" style="color:#0F1E2E;font-weight:600;text-decoration:none;">{{ $s['address'] ?? 'Address withheld' }}</a></td>
      <td>{{ $s['beds'] }} bd / {{ $s['baths'] }} ba</td>
      <td>{{ $s['when'] }}</td>
      <td>${{ number_format($s['price']) }}</td>
    </tr>
    @endforeach
  </table>
  <p style="font-size:12px;color:#8A99AA;margin-top:8px;">Sold data courtesy of MRED as distributed by MLS GRID; properties may be listed or sold by various participants in the MLS.</p>
  @endif

  @if($similar->isNotEmpty())
  <h2 class="ld-h2">Similar homes nearby</h2>
  <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px;">
    @foreach($similar as $s)
    <a href="/listings/{{ $s->listing_id }}" style="display:block;background:#fff;border:1px solid #DEE6EE;border-radius:12px;overflow:hidden;text-decoration:none;color:#0F1E2E;">
      <div style="aspect-ratio:3/2;background:#E9EFF3 center/cover no-repeat;{{ $s->photoUrl() ? 'background-image:url(\''.$s->photoUrl().'\');' : '' }}"></div>
      <div style="padding:13px 15px 15px;">
        <div style="font-size:19px;font-weight:800;">${{ number_format($s->list_price) }}</div>
        <div style="font-size:13px;color:#48586B;margin-top:2px;">{{ $s->beds }} bd &middot; {{ $s->baths() }} ba @if($s->sqft) &middot; {{ number_format($s->sqft) }} sqft @endif</div>
        <div style="font-size:13px;color:#48586B;margin-top:5px;line-height:1.45;">{{ $s->displayAddress() }}</div>
      </div>
    </a>
    @endforeach
  </div>
  @endif
  @endif

  <div class="ld-cta">
    <div style="font-family:Georgia,serif;font-size:20px;font-weight:700;">Want to see this home?</div>
    <p style="color:rgba(255,255,255,.85);margin:8px 0 0;">The Dawn Simmons Team (RE/MAX Suburban) can show you any listed property and give you a straight answer on what it's really worth. Call/text Josh at {{ config('site.josh_cell', '(224) 628-4013') }}.</p>
    <a href="/contact">Ask About This Home &rarr;</a>
  </div>
</div>

@include('listings._compliance', ['dataAsOf' => $dataAsOf])
</x-site.layout>
