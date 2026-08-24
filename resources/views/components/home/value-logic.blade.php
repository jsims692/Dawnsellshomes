{{-- Shared Alpine logic for the home-value widget (used by the live homepage widget and the redesigned homepage hero). Also ensures the Google Maps loader exists. --}}
@php $gkey = config('services.google.maps_key'); @endphp
@if($gkey)
<script>
window.__gmapsReady ||= new Promise((resolve) => {
    if (window.google?.maps?.importLibrary) return resolve();
    window.__gmapsInit = () => resolve();
    const s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key={{ $gkey }}&v=weekly&loading=async&callback=__gmapsInit';
    s.async = true; s.defer = true;
    document.head.appendChild(s);
});
</script>
@endif
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('homeValue', () => ({
        key: @js($gkey), query: '', preds: [], hi: -1, busy: false, result: null,
        place: null, token: null, shortAddr: '',
        fmt(n) { return '$' + Math.round(n).toLocaleString('en-US'); },

        async init() {
            if (!this.key || !window.__gmapsReady) return;
            try {
                await window.__gmapsReady;
                await google.maps.importLibrary('places');
                this.token = new google.maps.places.AutocompleteSessionToken();
            } catch (e) { /* autocomplete unavailable; widget still works as before */ }
        },

        async suggest() {
            this.hi = -1;
            if (!this.key || !window.google?.maps?.places || this.query.trim().length < 3) { this.preds = []; return; }
            try {
                const { suggestions } = await google.maps.places.AutocompleteSuggestion.fetchAutocompleteSuggestions({
                    input: this.query, sessionToken: this.token,
                    includedPrimaryTypes: ['street_address', 'premise', 'subpremise'],
                    includedRegionCodes: ['us'],
                    locationBias: { center: { lat: 42.08, lng: -87.98 }, radius: 45000 },
                });
                this.preds = suggestions.slice(0, 5).map(s => ({
                    id: s.placePrediction.placeId, main: s.placePrediction.mainText.text,
                    secondary: s.placePrediction.secondaryText?.text || '', pred: s.placePrediction,
                }));
            } catch (e) { this.preds = []; }
        },
        move(d) { if (!this.preds.length) return; this.hi = (this.hi + d + this.preds.length) % this.preds.length; },

        async pick(p) {
            this.preds = []; this.query = p.main + (p.secondary ? ', ' + p.secondary : '');
            try {
                const place = p.pred.toPlace();
                await place.fetchFields({ fields: ['location', 'formattedAddress'] });
                this.token = new google.maps.places.AutocompleteSessionToken(); // session ends on selection
                this.place = { lat: place.location.lat(), lng: place.location.lng(), address: place.formattedAddress || this.query };
                await this.lookup();
            } catch (e) { this.place = null; }
        },

        async submit() {
            if (this.hi >= 0 && this.preds[this.hi]) return this.pick(this.preds[this.hi]);
            if (this.preds.length === 1) return this.pick(this.preds[0]);
            if (this.place && this.query) return this.lookup();
            // No suggestion chosen: geocode the raw text if we can, else fall back to the old behavior.
            if (this.key && window.google?.maps && this.query.trim().length > 4) {
                try {
                    const { Geocoder } = await google.maps.importLibrary('geocoding');
                    const r = await new Geocoder().geocode({ address: this.query, region: 'us' });
                    const g = r.results?.[0];
                    if (g) { this.place = { lat: g.geometry.location.lat(), lng: g.geometry.location.lng(), address: g.formatted_address }; return this.lookup(); }
                } catch (e) {}
            }
            heroSearch();
        },

        async lookup() {
            if (!this.place) return;
            this.busy = true; this.result = null;
            this.shortAddr = this.place.address.replace(/, USA$/, '').replace(/, IL \d{5}$/, ', IL');
            try {
                const res = await fetch(`/home-value/nearby?lat=${this.place.lat}&lng=${this.place.lng}`);
                this.result = await res.json();
            } catch (e) { this.result = { ok: false }; }
            this.busy = false;
        },

        toContact() {
            const el = document.getElementById('heroAddrInput');
            if (el && this.place) el.value = this.place.address;
            heroSearch();
        },
    }));
});

</script>
