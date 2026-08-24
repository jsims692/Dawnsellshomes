<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Http\Request;

class ListingController extends Controller
{
    /** Search results. Filters are objective criteria only (Rule 9). */
    public function index(Request $request)
    {
        $q = Listing::displayable()->orderByDesc('mls_modified_at');

        if ($city = $request->query('city')) {
            $q->where('city', $city);
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
        if ($type = $request->query('type')) {
            $q->where('property_type', $type);
        }

        // Rule 26: no artificial caps below min(500, 50%); pagination exposes
        // the complete result set. 2,500 hard ceiling per search.
        $total = min($q->count(), 2500);
        $listings = $q->limit(2500)->get()->take(2500);
        $page = max(1, (int) $request->query('page', 1));
        $perPage = 24;
        $slice = $listings->slice(($page - 1) * $perPage, $perPage);

        return view('listings.index', [
            'listings' => $slice,
            'total' => $total,
            'page' => $page,
            'pages' => (int) ceil($total / $perPage),
            'cities' => Listing::displayable()->distinct()->orderBy('city')->pluck('city'),
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
            'demo' => Listing::displayable()->where('is_demo', true)->exists(),
            'filters' => $request->only(['city', 'min', 'max', 'beds', 'type']),
        ]);
    }

    public function show(string $listingId)
    {
        $listing = Listing::displayable()->where('listing_id', $listingId)->firstOrFail();

        return view('listings.show', [
            'l' => $listing,
            'dataAsOf' => Listing::max('mls_modified_at') ?? now(),
        ]);
    }
}
