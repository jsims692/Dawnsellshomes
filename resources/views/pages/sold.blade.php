<x-site.layout :page="$page" :head="$head">
<style>
    /* Design v2 re-theme: same shell, ink/red tokens (--gold doubles as the
       plat-map buyer-side yellow). Alpine logic + sales map untouched. */
    :root { --navy:#0F1E2E; --navy-dark:#0B1622; --red:#C8102E; --gold:#E8B93B; }
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: 'Archivo', Arial, sans-serif; background: #F2F5F9; color: #0F1E2E; }

    .hero { background: linear-gradient(135deg, var(--navy) 0%, var(--navy-dark) 100%); color: #fff; padding: 60px 40px; text-align: center; }
    .hero h1 { font-family: 'Fraunces', Georgia, serif; font-weight: 600; letter-spacing: -.01em; font-size: clamp(28px,4vw,48px); margin-bottom: 12px; }
    .hero p { font-size: 18px; opacity: .85; max-width: 600px; margin: 0 auto; }
    .stats-bar { display: flex; justify-content: center; gap: 48px; margin-top: 36px; flex-wrap: wrap; }
    .stat { text-align: center; }
    .stat-num { font-family: 'Fraunces', Georgia, serif; font-size: 36px; font-weight: 600; color: #fff; }
    .stat-label { font-size: 13px; opacity: .75; margin-top: 4px; }

    .filters { background: #fff; padding: 20px 40px; border-bottom: 1px solid #e0e4ed; display: flex; gap: 16px; flex-wrap: wrap; align-items: center; box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .filters label { font-size: 13px; font-weight: 700; color: var(--navy); text-transform: uppercase; letter-spacing: .4px; }
    .filters select, .filters input { padding: 8px 12px; border: 1.5px solid #dde2ee; border-radius: 6px; font-size: 14px; font-family: inherit; background: #fff; }
    .filters select:focus, .filters input:focus { outline: none; border-color: var(--navy); }
    #result-count { margin-left: auto; font-size: 13px; color: #888; font-weight: 600; }

    .content { max-width: 1200px; margin: 0 auto; padding: 40px 24px; }
    
    #map { width: 100%; height: 460px; border-radius: 12px; margin-bottom: 40px; box-shadow: 0 4px 24px rgba(27,58,107,.12); }

    .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; }
    .card { background: #fff; border-radius: 10px; padding: 20px; border-top: 3px solid var(--navy); box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .card.buyside { border-top-color: var(--gold); }
    .card-price { font-size: 22px; font-weight: 800; color: var(--navy); }
    .card-addr { font-size: 14px; color: #444; margin: 6px 0 4px; font-weight: 600; }
    .card-city { font-size: 13px; color: #888; }
    .card-meta { display: flex; gap: 8px; margin-top: 12px; flex-wrap: wrap; }
    .tag { font-size: 11px; padding: 3px 8px; border-radius: 4px; font-weight: 700; text-transform: uppercase; letter-spacing: .3px; }
    .tag-type { background: #f0f4ff; color: var(--navy); }
    .tag-listing { background: #fff0f0; color: var(--red); }
    .tag-buyside { background: #fff8e6; color: #9a6e00; }
    .tag-year { background: #f0f0f0; color: #555; }

    .no-results { text-align: center; padding: 60px; color: #888; font-size: 16px; }

    @media (max-width: 700px) {
      .filters { padding: 16px 20px; }
      .stats-bar { gap: 24px; }
    }
    /* server-rendered card list is filtered client-side via data attributes */
    .card[hidden] { display:none; }
    .stat-num { font-variant-numeric: tabular-nums; }
    [x-cloak] { display:none !important; }
</style>

<div x-data="soldPage()">
<div class="hero">
  <div style="font-size:13px; font-weight:700; text-transform:uppercase; letter-spacing:1px; color:var(--gold); margin-bottom:12px;">Track Record</div>
  <h1>{{ \App\Support\TeamStats::soldTotal() }} Homes Sold Across<br>the Northwest Suburbs</h1>
  <p>Every pin on this map is a family we helped buy or sell a home. Real deals. Real results — {{ number_format($stats['cities']) }} towns, ${{ number_format($stats['volume'] / 1e6, 0) }}M+ in closed volume. The map plots our {{ number_format($stats['total']) }} closings since {{ $stats['first_year'] }}; our earliest deals predate digital mapping.</p>
  <div class="stats-bar">
    <div class="stat"><div class="stat-num" x-text="counts.total.toLocaleString()">{{ $stats['total'] }}</div><div class="stat-label">Total Transactions</div></div>
    <div class="stat"><div class="stat-num" x-text="counts.listing.toLocaleString()">{{ $stats['listing'] }}</div><div class="stat-label">Listing Side</div></div>
    <div class="stat"><div class="stat-num" x-text="counts.buyside.toLocaleString()">{{ $stats['buyside'] }}</div><div class="stat-label">Buyer Side</div></div>
    <div class="stat"><div class="stat-num">{{ $stats['years'] }}</div><div class="stat-label">Years Active</div></div>
  </div>
</div>

<div class="filters">
  <label for="filter-side">Side:</label>
  <select id="filter-side" x-model="$store.salesFilters.side">
    <option value="">All Transactions</option>
    <option value="listing">Listing Side</option>
    <option value="buyside">Buyer Side</option>
  </select>
  <label for="filter-type">Type:</label>
  <select id="filter-type" x-model="$store.salesFilters.type">
    <option value="">All Types</option>
    @foreach($types as $type)<option value="{{ $type }}">{{ $type === 'Condo/Townhome' ? 'Condo / Townhome' : $type }}</option>@endforeach
  </select>
  <label for="filter-city">City:</label>
  <select id="filter-city" x-model="$store.salesFilters.city">
    <option value="">All Cities</option>
    @foreach($cities as $city)<option value="{{ $city }}">{{ $city }}</option>@endforeach
  </select>
  <label for="filter-year">Year:</label>
  <select id="filter-year" x-model="$store.salesFilters.year">
    <option value="">All Years</option>
    @foreach($years as $year)<option value="{{ $year }}">{{ $year }}</option>@endforeach
  </select>
  <span id="result-count" x-text="counts.total === {{ $stats['total'] }} ? 'Showing all {{ number_format($stats['total']) }} sales' : 'Showing ' + counts.total.toLocaleString() + ' of {{ number_format($stats['total']) }}'"></span>
  <button type="button" x-show="hasFilters" @click="reset()" style="background:none; border:1.5px solid var(--red); color:var(--red); border-radius:6px; padding:7px 12px; font-weight:700; cursor:pointer;">Clear filters</button>
</div>

<div class="content">
  <x-sales.map height="520px" />

  {{-- Server-rendered so search engines index every sale; Alpine only toggles visibility. --}}
  <div class="grid" id="grid" style="margin-top:40px;">
    @foreach($sales as $sale)
    <div class="card {{ $sale->side }}" data-side="{{ $sale->side }}" data-type="{{ $sale->property_type }}" data-city="{{ $sale->city }}" data-year="{{ $sale->sold_year }}"
         x-show="matches($el.dataset)">
      <div class="card-price">{{ $sale->sold_price ? '$'.number_format($sale->sold_price) : 'Price undisclosed' }}</div>
      <div class="card-addr">{{ $sale->address }}</div>
      <div class="card-city">{{ $sale->city }}, IL</div>
      <div class="card-meta">
        <span class="tag">{{ $sale->property_type }}</span>
        <span class="tag">{{ $sale->sold_year }}</span>
        <span class="tag {{ $sale->side === 'listing' ? 'tag-listing' : 'tag-buy' }}">{{ $sale->side === 'listing' ? 'Our Listing' : 'Our Buyer' }}</span>
      </div>
    </div>
    @endforeach
  </div>
  <div class="no-results" x-show="counts.total === 0" x-cloak>No sales match those filters — try widening your search.</div>
</div>



<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('soldPage', () => ({
    // Card visibility + live counters derive from the same store the map uses.
    get f() { return Alpine.store('salesFilters'); },
    get hasFilters() { return !!(this.f.side || this.f.type || this.f.city || this.f.year); },
    matches(d) {
      return (!this.f.side || d.side === this.f.side) && (!this.f.type || d.type === this.f.type)
          && (!this.f.city || d.city === this.f.city) && (!this.f.year || d.year === String(this.f.year));
    },
    get counts() {
      const cards = Array.from(document.querySelectorAll('#grid .card')).filter(c => this.matches(c.dataset));
      return { total: cards.length, listing: cards.filter(c => c.dataset.side === 'listing').length, buyside: cards.filter(c => c.dataset.side === 'buyside').length };
    },
    reset() { this.f.side = ''; this.f.type = ''; this.f.city = ''; this.f.year = ''; },
  }));
});
</script>
</div>
</x-site.layout>
