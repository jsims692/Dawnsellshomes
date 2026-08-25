<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /** Search results. Filters are objective criteria only (Rule 9). */
    public function index(Request $request)
    {
        $q = Listing::displayable()->forSale()->orderByDesc('mls_modified_at');

        if ($city = trim((string) $request->query('city'))) {
            $q->whereRaw('LOWER(city) = ?', [mb_strtolower($city)]);
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
                'list_office_name', 'lat', 'lng', 'mls_modified_at', 'is_demo'])
            ->skip($offset)->take(min($perPage, $total - $offset))->get();

        return view('listings.index', [
            'listings' => $slice,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $perPage),
            'cities' => Listing::displayable()->forSale()->distinct()->orderBy('city')->pluck('city'),
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
            'demo' => Listing::displayable()->where('is_demo', true)->exists(),
            'filters' => $request->only(['city', 'min', 'max', 'beds', 'baths', 'type', 'dwelling']),
        ]);
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

        return view('listings.show', [
            'l' => $listing,
            'galleryFetching' => $galleryFetching,
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
        ]);
    }
}
