<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Support\Str;

/**
 * Specialty search landing pages ("collections"): high-intent queries that
 * deserve their own indexable page — 55+/single-level living, new
 * construction, waterfront. Each is a real page with live counts, city
 * breakdown, sample cards, and a link into the filtered search.
 */
class CollectionController extends Controller
{
    public const COLLECTIONS = [
        'first-floor-master' => [
            'title' => 'First-Floor Master & 55+ Friendly Homes',
            'h1' => 'Single-level living, without the compromise.',
            'eyebrow' => 'First-floor master · 55+ friendly',
            'blurb' => 'The single most requested feature we hear — a real primary suite on the main level, no stairs between you and your bedroom. These homes suit empty nesters, anyone planning ahead, and buyers who simply prefer one-story living. Many are in quiet, low-maintenance communities; some are true ranches, others two-story homes designed with the master down.',
            'params' => ['ffmaster' => 1],
            'note' => 'Tip: pair this with the Ranch / single-story filter on the search page for strictly one-level homes.',
        ],
        'new-construction' => [
            'title' => 'New & Recently Built Homes',
            'h1' => 'New construction & nearly-new homes.',
            'eyebrow' => 'Built 2024 or later',
            'blurb' => 'Homes built in the last two years across the northwest suburbs — modern layouts, current building code, full-warranty mechanicals, and none of the surprise projects that come with older houses. Inventory here moves fast: builders release in phases and the best lots go first.',
            'params' => ['built' => 2024],
            'note' => 'We also track pre-construction releases that never hit public sites — ask us what\'s coming.',
        ],
        'waterfront' => [
            'title' => 'Waterfront Homes',
            'h1' => 'Wake up on the water.',
            'eyebrow' => 'Waterfront living',
            'blurb' => 'True waterfront across our service area — from Chain O\'Lakes homes with private piers to lakefront and channel-front properties in the suburbs. Waterfront is its own market: values follow frontage, water depth, and boating access as much as the house itself, and we\'ve sold here for 25 years.',
            'params' => ['waterfront' => 1],
            'note' => 'Chain O\'Lakes buyers: channel-front and lakefront price very differently — read our guide before you offer.',
        ],
    ];

    public function show(string $slug)
    {
        $c = self::COLLECTIONS[$slug] ?? abort(404);

        $data = cache()->remember('collection:'.$slug, 1800, function () use ($c) {
            $base = fn () => $this->query($c['params']);
            $total = $base()->count();
            $cities = $base()->selectRaw('city, COUNT(*) c')->groupBy('city')
                ->orderByDesc('c')->limit(18)->pluck('c', 'city')->all();
            $cards = $base()->orderByDesc('mls_modified_at')->limit(6)
                ->get(['id', 'listing_key', 'listing_id', 'status', 'list_price', 'street_address',
                    'city', 'state', 'zip', 'address_public', 'beds', 'baths_full', 'baths_half', 'sqft'])
                ->map(fn ($l) => [
                    'id' => $l->listing_id, 'url' => $l->url(), 'price' => $l->list_price, 'status' => $l->status,
                    'addr' => $l->displayAddress(), 'beds' => $l->beds, 'baths' => $l->baths(),
                    'sqft' => $l->sqft, 'photo' => $l->photoUrl(),
                ])->all();

            return ['total' => $total, 'cities' => $cities, 'cards' => $cards];
        });

        $asOf = Listing::max('mls_modified_at');

        return view('collections.show', [
            'slug' => $slug,
            'c' => $c,
            'total' => $data['total'],
            'cities' => $data['cities'],
            'cards' => $data['cards'],
            'searchUrl' => '/listings?'.http_build_query($c['params']),
            'dataAsOf' => $asOf ? \Illuminate\Support\Carbon::parse($asOf) : now(),
            'head' => '<title>'.e($c['title'].' — Northwest Suburbs of Chicago | Dawn Simmons Team').'</title>'
                .'<meta name="description" content="'.e(Str::limit($c['blurb'], 150).' Live MLS inventory, updated hourly.').'">'
                .'<link rel="canonical" href="https://dawnsellshomes.com/homes/'.e($slug).'">'
                .'<meta property="og:title" content="'.e($c['title']).'">'
                .'<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg">',
        ]);
    }

    private function query(array $params)
    {
        $q = Listing::displayable()->forSale()->where('is_demo', false)->where('is_auction', false);
        if ($params['ffmaster'] ?? false) {
            $q->whereHas('rooms', fn ($r) => $r->where('name', 'Master Bedroom')->where('level', 'Main'));
        }
        if ($params['built'] ?? false) {
            $q->where('year_built', '>=', $params['built']);
        }
        if ($params['waterfront'] ?? false) {
            $q->where('waterfront', true);
        }

        return $q;
    }
}
