<div style="max-width:600px;margin:0 auto;font-family:Arial,Helvetica,sans-serif;color:#0F1E2E;">
  <div style="padding:18px 0;border-bottom:3px solid #C8102E;">
    <strong style="font-size:17px;">DAWN SIMMONS TEAM</strong>
    <span style="color:#C8102E;font-size:11px;letter-spacing:2px;"> RE/MAX SUBURBAN</span>
  </div>
  <p style="font-size:15px;">{{ $search->name ? $search->name.', there' : 'There' }}&rsquo;s movement on your saved search:<br>
  <strong>{{ $search->summary() }}</strong></p>

  @if($listings->isNotEmpty())<p style="font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#C8102E;margin:18px 0 8px;">Just listed</p>@endif

  @foreach($listings as $l)
  <a href="{{ url($l->url()) }}" style="display:block;text-decoration:none;color:#0F1E2E;border:1px solid #DEE6EE;border-radius:10px;margin:0 0 14px;overflow:hidden;">
    @if($l->photoUrl())<img src="{{ $l->photoUrl() }}" alt="" width="600" style="width:100%;max-height:300px;object-fit:cover;display:block;">@endif
    <div style="padding:14px 16px;">
      <div style="font-size:20px;font-weight:800;">${{ number_format($l->list_price) }}</div>
      <div style="font-size:14px;color:#48586B;">{{ $l->displayAddress() }}</div>
      <div style="font-size:13px;color:#48586B;margin-top:4px;">{{ $l->beds }} bd &middot; {{ $l->baths() }} ba @if($l->sqft) &middot; {{ number_format($l->sqft) }} sqft @endif &middot; MLS #{{ $l->listing_id }}</div>
      <div style="font-size:11.5px;color:#8A99AA;margin-top:6px;">Listing courtesy of {{ $l->list_office_name }}</div>
      <div style="margin-top:10px;"><span style="background:#C8102E;color:#fff;font-weight:700;font-size:13px;padding:8px 16px;border-radius:999px;">See photos &amp; details &rarr;</span></div>
    </div>
  </a>
  @endforeach

  @if($drops->isNotEmpty())
  <p style="font-size:13px;font-weight:800;letter-spacing:1px;text-transform:uppercase;color:#C8102E;margin:18px 0 8px;">Price drops</p>
  @foreach($drops as $l)
  <a href="{{ url($l->url()) }}" style="display:block;text-decoration:none;color:#0F1E2E;border:1px solid #DEE6EE;border-radius:10px;margin:0 0 14px;overflow:hidden;">
    @if($l->photoUrl())<img src="{{ $l->photoUrl() }}" alt="" width="600" style="width:100%;max-height:300px;object-fit:cover;display:block;">@endif
    <div style="padding:14px 16px;">
      <div style="font-size:20px;font-weight:800;">${{ number_format($l->list_price) }}
        <span style="font-size:14px;color:#8A99AA;text-decoration:line-through;font-weight:600;">${{ number_format($l->previous_price) }}</span>
        <span style="background:#EAF7EF;color:#1d6b35;font-size:11px;font-weight:800;padding:3px 8px;border-radius:999px;vertical-align:middle;">&#8595; ${{ number_format($l->previous_price - $l->list_price) }}</span></div>
      <div style="font-size:14px;color:#48586B;">{{ $l->displayAddress() }}</div>
      <div style="font-size:13px;color:#48586B;margin-top:4px;">{{ $l->beds }} bd &middot; {{ $l->baths() }} ba @if($l->sqft) &middot; {{ number_format($l->sqft) }} sqft @endif &middot; MLS #{{ $l->listing_id }}</div>
      <div style="font-size:11.5px;color:#8A99AA;margin-top:6px;">Listing courtesy of {{ $l->list_office_name }}</div>
    </div>
  </a>
  @endforeach
  @endif

  <div style="background:#F2F5F9;border-radius:10px;padding:14px 16px;margin:6px 0 14px;font-size:13px;color:#0F1E2E;">
    &#128274; <strong>You&rsquo;re not seeing private listings.</strong> Some homes sell through MRED&rsquo;s Private Listing Network and never appear on any public website &mdash; this one, Zillow, any of them. We can watch the PLN for your criteria and send matches directly. <a href="{{ url('/contact?pln='.urlencode($search->criteria['city'] ?? 'the northwest suburbs')) }}" style="color:#C8102E;font-weight:700;">Ask us &rarr;</a>
  </div>

  <p style="font-size:13px;color:#48586B;">Questions, or want a showing? Call or text Josh: <a href="tel:2246284013" style="color:#C8102E;font-weight:700;">(224) 628-4013</a> — 7 days a week.</p>

  <div style="border-top:1px solid #DEE6EE;margin-top:18px;padding-top:12px;font-size:11px;color:#8A99AA;line-height:1.7;">
    <p>Listings courtesy of MRED as distributed by MLS GRID, as of {{ now()->timezone('America/Chicago')->format('n/j/Y g:i A T') }}. All data is deemed reliable but is not guaranteed. IDX information is provided exclusively for consumers' personal, non-commercial use. Properties may or may not be listed by the office/agent presenting the information.</p>
    <p>The Dawn Simmons Team &middot; RE/MAX Suburban &middot; 330 E Northwest Hwy, Mount Prospect IL 60056<br>
    You saved this search on dawnsellshomes.com. <a href="{{ url('/alerts/manage/'.$search->token) }}" style="color:#8A99AA;">Manage alerts</a> &middot; <a href="{{ url('/alerts/unsubscribe/'.$search->token) }}" style="color:#8A99AA;">Unsubscribe</a></p>
  </div>
</div>
