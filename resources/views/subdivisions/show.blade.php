{{-- Auto-generated subdivision page: rendered for any MLS-tagged subdivision
     in a service-area city that has no hand-built neighborhood page. All the
     listing display + stats come from the shared in-city band (Rule-10 cards,
     attributed analytics). --}}
<x-site.layout :head="$head">
<section class="page-hero">
  <div class="wrap">
    <p class="crumb"><a href="/">Home</a> &rsaquo; <a href="/neighborhoods">Neighborhoods</a> &rsaquo; {{ $entry['name'] }}</p>
    <p class="eyebrow">{{ $entry['city'] }}, Illinois</p>
    <h1>{{ $entry['name'] }}</h1>
    <p class="lead">Live listings, recent sales, and market stats for the {{ $entry['name'] }} subdivision of <a href="/cities/{{ $entry['citySlug'] }}" style="color:inherit">{{ $entry['city'] }}</a> &mdash; straight from the MLS, updated hourly.</p>
  </div>
</section>

@include('components.listings.in-city', [
    'embedded' => false,
    'title' => $entry['name'].', '.$entry['city'],
    'panels' => $panels,
    'dataAsOf' => $dataAsOf,
])

<section class="section">
  <div class="wrap" style="max-width:860px">
    <div class="callout">
      &#128269; <strong>Heads up:</strong> Listing agents don&rsquo;t always tag a home with its subdivision in the MLS, so some {{ $entry['name'] }} homes may only appear in the broader {{ $entry['city'] }} results.
      <a class="link-arrow" href="/listings?city={{ urlencode($entry['city']) }}">See every {{ $entry['city'] }} listing &rarr;</a>
    </div>
    <div class="callout" style="margin-top:14px">
      &#127968; Thinking about {{ $entry['name'] }}? We&rsquo;ve been selling here for 26 years &mdash; including off-market and Private Listing Network homes that never reach public sites.
      <a class="link-arrow" href="/contact">Talk to Dawn &amp; Josh &rarr;</a>
    </div>
  </div>
</section>
</x-site.layout>
