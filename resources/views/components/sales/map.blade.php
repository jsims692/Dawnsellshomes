@props([
    'height' => '560px',
    'compact' => false,   {{-- homepage variant --}}
])
@php $gkey = config('services.google.maps_key'); @endphp
@if(!$gkey)
    {{-- No Google key configured: fall back to the Leaflet build. --}}
    <x-sales.map-leaflet :height="$height" :compact="$compact" />
@else
{{--
  Interactive sales map on Google Maps JS API. City-level pulsing bubbles
  (sized by homes sold) drill down into individual sale markers; red = we
  listed it, gold = we represented the buyer. Data from /sold/map-data.
  Filter state lives in the shared Alpine store `salesFilters` so page-level
  controls, stat counters, and the card list stay in sync with the map.
--}}
<div
    x-data="salesMap({ compact: @js($compact), key: @js($gkey) })"
    x-init="init()"
    class="dsm-wrap"
    style="position:relative; height: {{ $height }}; border-radius:12px; overflow:hidden; box-shadow:0 4px 24px rgba(27,58,107,.12); background:#e9edf3;"
>
    <div x-ref="map" style="position:absolute; inset:0;"></div>

    <div x-show="loading" x-transition.opacity style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; background:rgba(244,246,251,.85); font-family:Arial,sans-serif; color:#1B3A6B; font-weight:700; letter-spacing:.5px; z-index:5;">
        Loading {{ $compact ? '' : '550+ ' }}sales…
    </div>

    <div style="position:absolute; left:12px; bottom:28px; z-index:5; background:rgba(255,255,255,.95); border-radius:8px; padding:10px 12px; font-family:Arial,sans-serif; font-size:12px; color:#333; box-shadow:0 2px 10px rgba(0,0,0,.15); line-height:1.7;">
        <div><span class="dsm-dot" style="background:#CC0000"></span> We listed &amp; sold it</div>
        <div><span class="dsm-dot" style="background:#C8A84B"></span> We represented the buyer</div>
        <div x-show="mode === 'cities'" style="color:#666; margin-top:2px;">Click a city bubble to see every home</div>
        <button x-show="mode === 'homes'" @click="showCities()" type="button" style="margin-top:6px; background:#1B3A6B; color:#fff; border:0; border-radius:6px; padding:6px 10px; font-weight:700; font-size:12px; cursor:pointer;">← Back to all cities</button>
    </div>

    <template x-if="active">
        <div style="position:absolute; right:12px; top:12px; z-index:5; background:#fff; border-radius:10px; padding:14px 16px; width:250px; font-family:Arial,sans-serif; box-shadow:0 6px 24px rgba(0,0,0,.18); border-top:4px solid #1B3A6B;">
            <div style="font-size:20px; font-weight:800; color:#1B3A6B;" x-text="fmt(active.price)"></div>
            <div style="font-size:14px; font-weight:700; margin-top:2px;" x-text="active.address"></div>
            <div style="font-size:13px; color:#555;" x-text="active.city + ', IL'"></div>
            <div style="font-size:12px; color:#666; margin-top:8px;">
                <span x-text="active.type"></span> · Sold <span x-text="active.year"></span><br>
                <span :style="`color:${active.side==='listing' ? '#CC0000' : '#a8842b'}; font-weight:700;`" x-text="active.side==='listing' ? 'Our listing' : 'Our buyer'"></span>
            </div>
            <button @click="active=null" type="button" aria-label="Close" style="position:absolute; top:6px; right:8px; border:0; background:none; font-size:18px; color:#999; cursor:pointer;">×</button>
        </div>
    </template>
</div>

<style>
    .dsm-dot { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; border:2px solid #fff; box-shadow:0 0 0 1px rgba(0,0,0,.15); vertical-align:-1px; }
    .dsm-bubble { position:relative; display:flex; align-items:center; justify-content:center; border-radius:50%; background:rgba(27,58,107,.80); color:#fff; font:700 12px/1 Arial,sans-serif; border:2px solid #fff; box-shadow:0 2px 10px rgba(13,35,73,.35); cursor:pointer; transition:transform .15s, background .15s; }
    .dsm-bubble:hover { transform:scale(1.08); background:rgba(204,0,0,.85); }
    .dsm-bubble::after { content:""; position:absolute; inset:-6px; border-radius:50%; border:2px solid rgba(27,58,107,.45); animation:dsm-pulse 2.4s ease-out infinite; }
    .dsm-bubble small { display:block; font-size:9px; font-weight:400; opacity:.85; margin-top:1px; text-align:center; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    @keyframes dsm-pulse { 0% { transform:scale(.85); opacity:.9; } 100% { transform:scale(1.55); opacity:0; } }
    .dsm-pin { width:12px; height:12px; border-radius:50%; border:2px solid #fff; box-shadow:0 1px 5px rgba(0,0,0,.45); cursor:pointer; transition:transform .12s; }
    .dsm-pin:hover { transform:scale(1.5); }
    .dsm-pin.listing { background:#CC0000; }
    .dsm-pin.buyside { background:#C8A84B; }
</style>

<script>
// Load Google Maps once per page (component may be rendered more than once).
window.__gmapsReady ||= new Promise((resolve) => {
    if (window.google?.maps?.importLibrary) return resolve();
    window.__gmapsInit = () => resolve();
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key={{ $gkey }}&v=weekly&loading=async&callback=__gmapsInit';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
});

document.addEventListener('alpine:init', () => {
    Alpine.store('salesFilters', { side: '', type: '', city: '', year: '' });

    // Brand-styled basemap: soft greys, muted POIs, navy-tinted water.
    const STYLE = [
        { elementType: 'geometry', stylers: [{ color: '#f4f6f9' }] },
        { elementType: 'labels.text.fill', stylers: [{ color: '#4b5563' }] },
        { elementType: 'labels.text.stroke', stylers: [{ color: '#f4f6f9' }] },
        { featureType: 'administrative', elementType: 'geometry.stroke', stylers: [{ color: '#d1d5db' }] },
        { featureType: 'administrative.locality', elementType: 'labels.text.fill', stylers: [{ color: '#1B3A6B' }] },
        { featureType: 'poi', stylers: [{ visibility: 'off' }] },
        { featureType: 'poi.park', elementType: 'geometry', stylers: [{ color: '#e6efe3' }, { visibility: 'on' }] },
        { featureType: 'road', elementType: 'geometry', stylers: [{ color: '#ffffff' }] },
        { featureType: 'road', elementType: 'geometry.stroke', stylers: [{ color: '#e5e7eb' }] },
        { featureType: 'road', elementType: 'labels.text.fill', stylers: [{ color: '#6b7280' }] },
        { featureType: 'road.highway', elementType: 'geometry', stylers: [{ color: '#f1e9d6' }] },
        { featureType: 'road.highway', elementType: 'geometry.stroke', stylers: [{ color: '#e2d3ad' }] },
        { featureType: 'transit', stylers: [{ visibility: 'off' }] },
        { featureType: 'water', elementType: 'geometry', stylers: [{ color: '#c9d8ea' }] },
        { featureType: 'water', elementType: 'labels.text.fill', stylers: [{ color: '#1B3A6B' }] },
    ];

    Alpine.data('salesMap', ({ compact, key }) => ({
        map: null, loading: true, mode: 'cities', active: null,
        all: [], markers: [], AdvancedMarker: null,

        fmt(n) { return '$' + Math.round(n).toLocaleString('en-US'); },
        get filters() { return Alpine.store('salesFilters'); },
        get filtered() {
            const f = this.filters;
            return this.all.filter(s => (!f.side || s.side === f.side) && (!f.type || s.type === f.type)
                && (!f.city || s.city === f.city) && (!f.year || String(s.year) === String(f.year)));
        },

        async init() {
            await window.__gmapsReady;
            const { Map } = await google.maps.importLibrary('maps');
            const { AdvancedMarkerElement } = await google.maps.importLibrary('marker');
            this.AdvancedMarker = AdvancedMarkerElement;

            this.map = new Map(this.$refs.map, {
                center: { lat: 42.10, lng: -87.98 }, zoom: 10,
                // mapId is required for AdvancedMarkers; a plain DEMO_MAP_ID keeps
                // JSON styles working without a cloud-configured map style.
                mapId: 'DEMO_MAP_ID',
                styles: STYLE,
                mapTypeControl: false, streetViewControl: false, fullscreenControl: !compact,
                zoomControl: true, gestureHandling: compact ? 'cooperative' : 'greedy',
                minZoom: 8, maxZoom: 17, clickableIcons: false,
            });

            const res = await fetch('/sold/map-data');
            const data = await res.json();
            this.all = data.sales;
            this.loading = false;
            this.showCities();

            this.$watch('filters.side', () => this.refresh());
            this.$watch('filters.type', () => this.refresh());
            this.$watch('filters.year', () => this.refresh());
            this.$watch('filters.city', (city) => city ? this.showHomes(city) : this.showCities());

            this.map.addListener('zoom_changed', () => {
                const z = this.map.getZoom();
                if (this.mode === 'cities' && z >= 13) this.showHomes(null, false);
                if (this.mode === 'homes' && !this.filters.city && z <= 10) this.showCities(false);
            });
        },

        clear() { this.markers.forEach(m => m.map = null); this.markers = []; },
        refresh() { this.mode === 'cities' ? this.showCities(false) : this.showHomes(this.filters.city || null, false); },

        fitTo(points, maxZoom) {
            if (!points.length) return;
            const b = new google.maps.LatLngBounds();
            points.forEach(p => b.extend(p));
            this.map.fitBounds(b, 40);
            google.maps.event.addListenerOnce(this.map, 'idle', () => { if (this.map.getZoom() > maxZoom) this.map.setZoom(maxZoom); });
        },

        showCities(fit = true) {
            this.mode = 'cities'; this.active = null; this.clear();
            const byCity = {};
            this.filtered.forEach(s => { const c = (byCity[s.city] ||= { city: s.city, count: 0, lat: 0, lng: 0 }); c.count++; c.lat += s.lat; c.lng += s.lng; });
            const list = Object.values(byCity).map(c => ({ ...c, lat: c.lat / c.count, lng: c.lng / c.count }));
            const max = Math.max(1, ...list.map(c => c.count));
            list.forEach(c => {
                const size = 34 + Math.sqrt(c.count / max) * 46;
                const el = document.createElement('div');
                el.className = 'dsm-bubble'; el.style.width = el.style.height = size + 'px'; el.title = `${c.city}: ${c.count} sold`;
                el.innerHTML = `<div>${c.count}<small>${size > 52 ? c.city : ''}</small></div>`;
                const m = new this.AdvancedMarker({ map: this.map, position: { lat: c.lat, lng: c.lng }, content: el, zIndex: c.count });
                m.addListener('click', () => { this.filters.city = c.city; });
                this.markers.push(m);
            });
            if (fit) this.fitTo(list.map(c => ({ lat: c.lat, lng: c.lng })), 11);
        },

        showHomes(city = null, fit = true) {
            this.mode = 'homes'; this.active = null; this.clear();
            const rows = city ? this.filtered.filter(s => s.city === city) : this.filtered;
            rows.forEach(s => {
                const el = document.createElement('div');
                el.className = 'dsm-pin ' + s.side; el.title = `${s.address}, ${s.city} — ${this.fmt(s.price)}`;
                const m = new this.AdvancedMarker({ map: this.map, position: { lat: s.lat, lng: s.lng }, content: el });
                m.addListener('click', () => { this.active = s; });
                this.markers.push(m);
            });
            if (fit) this.fitTo(rows.map(s => ({ lat: s.lat, lng: s.lng })), 15);
        },
    }));
});
</script>
@endif
