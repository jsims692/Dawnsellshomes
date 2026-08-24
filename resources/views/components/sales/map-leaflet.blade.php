@props([
    'height' => '560px',
    'compact' => false,   {{-- homepage variant: no filters/list wiring, just the map --}}
])
{{--
  Interactive sales map. City-level pulsing bubbles (sized by homes sold) that
  drill down into individual sale markers on click/zoom; red = we listed it,
  gold = we represented the buyer. Data comes from /sold/map-data (DB-backed).
  Emits filter state via the shared Alpine store `salesMap` so a page can wire
  its own filter controls, stat counters, and card list to the same state.
--}}
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

<div
    x-data="salesMap({ compact: @js($compact) })"
    x-init="init()"
    class="dsm-wrap"
    style="position:relative; height: {{ $height }}; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(27,58,107,.12); background:#e9edf3;"
>
    <div x-ref="map" style="position:absolute; inset:0;"></div>

    {{-- loading veil --}}
    <div x-show="loading" x-transition.opacity style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(244,246,251,.85); font-family:Arial,sans-serif; color:#0F1E2E; font-weight:700; letter-spacing:.5px; z-index:500;">
        Loading {{ $compact ? '' : '550+ ' }}sales…
    </div>

    {{-- legend + view toggle --}}
    <div style="position:absolute; left:12px; bottom:12px; z-index:500; background:rgba(255,255,255,.95); border-radius:8px; padding:10px 12px; font-family:Arial,sans-serif; font-size:12px; color:#333; box-shadow:0 2px 10px rgba(0,0,0,.15); line-height:1.7;">
        <div><span class="dsm-dot" style="background:#C8102E"></span> We listed &amp; sold it</div>
        <div><span class="dsm-dot" style="background:#E8B93B"></span> We represented the buyer</div>
        <div x-show="mode === 'cities'" style="color:#666; margin-top:2px;">Click a city bubble to see every home</div>
        <button x-show="mode === 'homes'" @click="showCities()" type="button" style="margin-top:6px; background:#0F1E2E; color:#fff; border:0; border-radius:6px; padding:6px 10px; font-weight:700; font-size:12px; cursor:pointer;">← Back to all cities</button>
    </div>

    {{-- hover/selection card --}}
    <template x-if="active">
        <div style="position:absolute; right:12px; top:12px; z-index:500; background:#fff; border-radius:10px; padding:14px 16px; width:250px; font-family:Arial,sans-serif; box-shadow:0 6px 24px rgba(0,0,0,.18); border-top:4px solid #0F1E2E;">
            <div style="font-size:20px; font-weight:800; color:#0F1E2E;" x-text="fmt(active.price)"></div>
            <div style="font-size:14px; font-weight:700; margin-top:2px;" x-text="active.address"></div>
            <div style="font-size:13px; color:#555;" x-text="active.city + ', IL'"></div>
            <div style="font-size:12px; color:#666; margin-top:8px;">
                <span x-text="active.type"></span> · Sold <span x-text="active.year"></span><br>
                <span :style="`color:${active.side==='listing' ? '#C8102E' : '#9a6e00'}; font-weight:700;`" x-text="active.side==='listing' ? 'Our listing' : 'Our buyer'"></span>
            </div>
            <button @click="active=null" type="button" aria-label="Close" style="position:absolute; top:6px; right:8px; border:0; background:none; font-size:18px; color:#999; cursor:pointer;">×</button>
        </div>
    </template>
</div>

<style>
    .dsm-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; border:2px solid #fff; box-shadow:0 0 0 1px rgba(0,0,0,.15); vertical-align:-1px; }
    /* pulsing city bubbles */
    .dsm-bubble { position:relative; display:flex; align-items:center; justify-content:center; border-radius:50%; background:rgba(27,58,107,.78); color:#fff; font:700 12px/1 Arial,sans-serif; border:2px solid #fff; box-shadow:0 2px 10px rgba(13,35,73,.35); cursor:pointer; transition:transform .15s; }
    .dsm-bubble:hover { transform:scale(1.08); background:rgba(204,0,0,.85); }
    .dsm-bubble::after { content:""; position:absolute; inset:-6px; border-radius:50%; border:2px solid rgba(27,58,107,.45); animation:dsm-pulse 2.4s ease-out infinite; }
    .dsm-bubble small { display:block; font-size:9px; font-weight:400; opacity:.85; margin-top:1px; text-align:center; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    @keyframes dsm-pulse { 0% { transform:scale(.85); opacity:.9; } 100% { transform:scale(1.55); opacity:0; } }
    /* home markers */
    .dsm-pin { width:12px; height:12px; border-radius:50%; border:2px solid #fff; box-shadow:0 1px 5px rgba(0,0,0,.45); cursor:pointer; transition:transform .12s; }
    .dsm-pin:hover { transform:scale(1.5); }
    .dsm-pin.listing { background:#C8102E; }
    .dsm-pin.buyside { background:#E8B93B; }
    .leaflet-container { font-family:Arial,sans-serif; touch-action:none; }
    .leaflet-control-attribution { font-size:9px !important; }
</style>

<script>
document.addEventListener('alpine:init', () => {
    // Shared filter state so page-level controls and the map stay in sync.
    Alpine.store('salesFilters', { side: '', type: '', city: '', year: '' });

    Alpine.data('salesMap', ({ compact }) => ({
        map: null, loading: true, mode: 'cities', active: null,
        all: [], cities: [], cityLayer: null, homeLayer: null,

        fmt(n) { return '$' + Math.round(n).toLocaleString('en-US'); },

        get filters() { return Alpine.store('salesFilters'); },
        get filtered() {
            const f = this.filters;
            return this.all.filter(s =>
                (!f.side || s.side === f.side) &&
                (!f.type || s.type === f.type) &&
                (!f.city || s.city === f.city) &&
                (!f.year || String(s.year) === String(f.year))
            );
        },

        async init() {
            this.map = L.map(this.$refs.map, { scrollWheelZoom: !compact, zoomControl: true, attributionControl: true })
                .setView([42.10, -87.98], compact ? 10 : 10);
            // Muted, brand-friendly basemap (free, no key). Swap to a keyed
            // provider later without touching the rest of this component.
            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '&copy; OpenStreetMap &copy; CARTO', subdomains: 'abcd', maxZoom: 19,
            }).addTo(this.map);
            this.cityLayer = L.layerGroup().addTo(this.map);
            this.homeLayer = L.layerGroup().addTo(this.map);

            const res = await fetch('/sold/map-data');
            const data = await res.json();
            this.all = data.sales; this.cities = data.cities;
            this.loading = false;
            this.showCities();

            // Re-render whenever page-level filters change.
            this.$watch('filters.side', () => this.refresh());
            this.$watch('filters.type', () => this.refresh());
            this.$watch('filters.year', () => this.refresh());
            this.$watch('filters.city', (city) => city ? this.showHomes(city) : this.showCities());

            // Zooming in far enough on the city view reveals individual homes.
            this.map.on('zoomend', () => {
                if (this.mode === 'cities' && this.map.getZoom() >= 13) this.showHomes(null, false);
                if (this.mode === 'homes' && !this.filters.city && this.map.getZoom() <= 10) this.showCities(false);
            });
        },

        refresh() { this.mode === 'cities' ? this.showCities(false) : this.showHomes(this.filters.city || null, false); },

        // ---- city bubble layer ----
        showCities(fit = true) {
            this.mode = 'cities'; this.active = null;
            this.homeLayer.clearLayers(); this.cityLayer.clearLayers();
            const rows = this.filtered;
            const byCity = {};
            rows.forEach(s => { (byCity[s.city] ||= { city: s.city, count: 0, lat: 0, lng: 0 }); const c = byCity[s.city]; c.count++; c.lat += s.lat; c.lng += s.lng; });
            const list = Object.values(byCity).map(c => ({ ...c, lat: c.lat / c.count, lng: c.lng / c.count }));
            const max = Math.max(1, ...list.map(c => c.count));
            list.forEach(c => {
                const size = 34 + Math.sqrt(c.count / max) * 46; // 34–80px
                const icon = L.divIcon({ className: '', iconSize: [size, size], iconAnchor: [size / 2, size / 2],
                    html: `<div class="dsm-bubble" style="width:${size}px;height:${size}px;" title="${c.city}: ${c.count} sold"><div>${c.count}<small>${size > 52 ? c.city : ''}</small></div></div>` });
                L.marker([c.lat, c.lng], { icon }).on('click', () => { this.filters.city = c.city; }).addTo(this.cityLayer);
            });
            if (fit && list.length) this.map.fitBounds(L.latLngBounds(list.map(c => [c.lat, c.lng])).pad(0.15), { maxZoom: 11 });
        },

        // ---- individual home layer ----
        showHomes(city = null, fit = true) {
            this.mode = 'homes'; this.active = null;
            this.cityLayer.clearLayers(); this.homeLayer.clearLayers();
            const rows = city ? this.filtered.filter(s => s.city === city) : this.filtered;
            rows.forEach(s => {
                const icon = L.divIcon({ className: '', iconSize: [12, 12], iconAnchor: [6, 6], html: `<div class="dsm-pin ${s.side}" title="${s.address}, ${s.city} — ${this.fmt(s.price)}"></div>` });
                L.marker([s.lat, s.lng], { icon }).on('click', () => { this.active = s; }).addTo(this.homeLayer);
            });
            if (fit && rows.length) this.map.fitBounds(L.latLngBounds(rows.map(s => [s.lat, s.lng])).pad(0.12), { maxZoom: 15 });
        },
    }));
});
</script>
