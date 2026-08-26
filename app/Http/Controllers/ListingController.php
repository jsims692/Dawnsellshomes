<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /** The shared search query: every filter, applied identically for the list and the map. */
    private function applyFilters(Request $request)
    {
        $q = Listing::displayable()->forSale();

        $cities = array_values(array_filter(array_map(
            fn ($c) => mb_strtolower(trim((string) $c)), (array) $request->query('city'))));
        if ($cities !== []) {
            $q->whereIn('city', $cities);
        }
        if ($min = (int) $request->query('min')) {
            $q->where('list_price', '>=', $min);
        }
        if ($max = (int) $request->query('max')) {
            $q->where('list_price', '<=', $max);
        }
        if ($beds = (int) $request->query('beds')) {
            $q->where('beds', '>=', $beds);
        }
        if ($baths = (int) $request->query('baths')) {
            $q->where('baths_full', '>=', $baths);
        }
        if ($type = $request->query('type')) {
            $q->where('property_type', $type);
        }
        if (in_array($d = $request->query('dwelling'), ['detached', 'attached', 'multi', 'multi5'], true)) {
            $q->where('dwelling', $d);
        }
        if ($request->boolean('waterfront')) {
            $q->where('waterfront', true);
        }
        if ($request->boolean('basement')) {
            $q->whereHas('features', fn ($f) => $f->where('category', 'basement')->where('value', '!=', 'None'));
        }
        if ($garage = (int) $request->query('garage')) {
            $q->where('garage_spaces', '>=', $garage);
        }
        if ($request->boolean('ffmaster')) {
            $q->whereHas('rooms', fn ($r) => $r->where('name', 'Master Bedroom')->where('level', 'Main'));
        }
        if ($request->boolean('masterbath')) {
            $q->whereHas('rooms', fn ($r) => $r->where('name', 'Master Bedroom')->where('bath', 'like', '%Full%'));
        }
        if ($request->boolean('ranch')) {
            $q->where('stories', 1);
        }
        if ($request->boolean('nohoa')) {
            $q->where(fn ($w) => $w->whereNull('hoa_fee')->orWhere('hoa_fee', 0));
        }
        if ($built = (int) $request->query('built')) {
            $q->where('year_built', '>=', $built);
        }
        if ($request->boolean('reduced')) {
            $q->whereNotNull('price_dropped_at');
        }
        if ($school = trim((string) $request->query('school'))) {
            $q->where(fn ($w) => $w->where('elementary_school', $school)
                ->orWhere('middle_school', $school)->orWhere('high_school', $school));
        }
        if ($request->query('basement') === 'finished') {
            $q->whereHas('features', fn ($f) => $f->where('category', 'basement')->where('value', 'Finished'));
        }

        return $q;
    }

    /** Search results. Filters are objective criteria only (Rule 9). */
    public function index(Request $request)
    {
        $q = $this->applyFilters($request);
        match ($request->query('sort')) {
            'price' => $q->orderBy('list_price'),
            'price-desc' => $q->orderByDesc('list_price'),
            'new' => $q->orderByDesc('listing_contract_date')->orderByDesc('mls_modified_at'),
            default => $q->orderByDesc('mls_modified_at'),
        };

        // Rule 26: no artificial caps below min(500, 50%); pagination exposes
        // the complete result set. 2,500 hard ceiling per search.
        $total = min($q->count(), 2500);
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 24;
        // Page in the database, and leave the bulky `raw` RESO blob (and long
        // remarks) unselected — loading thousands of full rows to show 24 OOMs.
        $offset = ($page - 1) * $perPage;
        $slice = $offset >= $total ? collect() : $q
            ->select(['id', 'listing_key', 'listing_id', 'status', 'list_price', 'street_address',
                'city', 'state', 'zip', 'address_public', 'display_public', 'beds', 'baths_full',
                'baths_half', 'sqft', 'property_type', 'property_subtype', 'year_built', 'subdivision',
                'list_office_name', 'lat', 'lng', 'mls_modified_at', 'is_demo', 'is_auction'])
            ->skip($offset)->take(min($perPage, $total - $offset))->get();

        return view('listings.index', [
            'listings' => $slice,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $perPage),
            'cities' => Listing::displayable()->forSale()->distinct()->orderBy('city')->pluck('city'),
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
            'demo' => Listing::displayable()->where('is_demo', true)->exists(),
            'filters' => ['city' => array_values(array_filter((array) $request->query('city')))] + $request->only(['min', 'max', 'beds', 'baths', 'type', 'dwelling', 'waterfront', 'basement', 'garage', 'ffmaster', 'masterbath', 'ranch', 'nohoa', 'built', 'reduced', 'sort', 'school']),
        ]);
    }

    /**
     * Pins for the map view: same filters as the list, coordinates only.
     * Rule-10 thumbnails in the popups (price, beds/baths, address, photo),
     * each linked to the fully compliant detail page.
     */
    public function mapData(Request $request)
    {
        $pins = $this->applyFilters($request)
            ->whereNotNull('lat')->whereNotNull('lng')
            ->limit(1500)
            ->get(['listing_id', 'listing_key', 'status', 'list_price', 'street_address', 'city',
                'state', 'zip', 'address_public', 'beds', 'baths_full', 'baths_half', 'lat', 'lng', 'is_auction'])
            ->map(fn ($l) => [
                'id' => $l->listing_id,
                'lat' => (float) $l->lat,
                'lng' => (float) $l->lng,
                'p' => $l->is_auction ? null : $l->list_price,
                'a' => $l->displayAddress(),
                'b' => (int) $l->beds,
                'ba' => $l->baths(),
                's' => $l->status,
                'ph' => $l->photoUrl(),
            ])->values();

        return response()->json($pins)->header('Cache-Control', 'public, max-age=120');
    }

    public function show(string $listingId)
    {
        $listing = Listing::displayable()->with(['rooms', 'features'])
            ->where('listing_id', $listingId)->firstOrFail();

        // Sold pages fetch their full gallery on first view via a detached
        // worker (budget-guarded; photos appear in ~30-60s). Pre-downloading
        // every sold gallery would be ~60GB+ at full scale — demand-driven.
        $galleryFetching = false;
        if (! $listing->isForSale()) {
            $lockKey = 'gallery-fetch:'.$listing->listing_id;
            $cached = count($listing->photoUrls());
            $expected = (int) @file_get_contents(storage_path('app/public/listings/'.$listing->listing_key.'.count'));
            $incomplete = $cached <= 1
                || ($expected > 0 && $cached < min($expected, \App\Console\Commands\MlsMedia::PHOTOS_MAX));
            if ($incomplete && cache()->add($lockKey, 1, 600)) {
                // First view (or an abandoned partial fetch): pull the gallery.
                exec(sprintf('%s %s mls:media --listing=%s --all >> %s 2>&1 &',
                    escapeshellarg(PHP_BINARY),
                    escapeshellarg(base_path('artisan')),
                    escapeshellarg($listing->listing_id),
                    escapeshellarg(storage_path('logs/gallery-fetch.log'))));
                $galleryFetching = true;
            } elseif ($incomplete && cache()->has($lockKey)) {
                $galleryFetching = true; // a fetch is in flight — keep the page refreshing
            }
        }

        // Similar homes: nearest active listings in a comparable price band
        // (falls back to same-city when the listing lacks coordinates).
        $similar = collect();
        if ($listing->isForSale()) {
            $similar = Listing::displayable()->forSale()->where('is_auction', false)
                ->where('id', '!=', $listing->id)
                ->when($listing->list_price, fn ($q) => $q->whereBetween('list_price',
                    [(int) ($listing->list_price * 0.7), (int) ($listing->list_price * 1.3)]))
                ->when($listing->lat && $listing->lng,
                    fn ($q) => $q->whereNotNull('lat')
                        ->whereBetween('lat', [$listing->lat - 0.05, $listing->lat + 0.05])
                        ->whereBetween('lng', [$listing->lng - 0.07, $listing->lng + 0.07])
                        ->orderByRaw('POW(lat - ?, 2) + POW(lng - ?, 2)', [$listing->lat, $listing->lng]),
                    fn ($q) => $q->where('city', $listing->city)
                        ->orderByDesc('mls_modified_at'))
                ->limit(3)
                ->get(['id', 'listing_key', 'listing_id', 'status', 'list_price', 'street_address',
                    'city', 'state', 'zip', 'address_public', 'beds', 'baths_full', 'baths_half', 'sqft']);
        }

        // Community link + nearby sold comps (for-sale pages only): the
        // context Zillow gives, from data we already replicate.
        $nearbySolds = [];
        if ($listing->isForSale() && $listing->lat && $listing->lng) {
            $nearbySolds = Listing::displayable()->where('is_demo', false)
                ->where('status', 'Closed')->whereNotNull('close_price')
                ->where('id', '!=', $listing->id)
                ->whereBetween('lat', [$listing->lat - 0.015, $listing->lat + 0.015])
                ->whereBetween('lng', [$listing->lng - 0.02, $listing->lng + 0.02])
                ->orderByRaw('POW(lat - ?, 2) + POW(lng - ?, 2)', [$listing->lat, $listing->lng])
                ->limit(4)
                ->get(['listing_id', 'street_address', 'address_public', 'close_price', 'close_date', 'beds', 'baths_full', 'baths_half'])
                ->map(fn ($s) => [
                    'id' => $s->listing_id,
                    'address' => $s->address_public && $s->street_address ? $s->street_address : null,
                    'beds' => $s->beds, 'baths' => $s->baths(),
                    'when' => $s->close_date?->format('M Y'), 'price' => $s->close_price,
                ])->all();
        }

        return view('listings.show', [
            'l' => $listing,
            'subUrl' => $listing->isForSale()
                ? \App\Support\Subdivisions::urlFor($listing->subdivision, $listing->city) : null,
            'nearbySolds' => $nearbySolds,
            'similar' => $similar,
            'galleryFetching' => $galleryFetching,
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
        ]);
    }
}
