<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Sale;
use Illuminate\Http\Request;

class SoldController extends Controller
{
    /**
     * The "Homes We've Sold" page. SEO head comes from the imported page row;
     * the sales themselves are DB-driven: server-rendered list for crawlers,
     * JSON payload for the interactive map.
     */
    public function show()
    {
        $page = Page::where('path', 'sold')->firstOrFail();
        $head = str_replace('<!--STYLE-->', '', $page->head_html); // page ships its own styles

        $sales = Sale::public()->orderByDesc('sold_year')->orderByDesc('sold_price')->get();

        $stats = [
            'total' => $sales->count(),
            'listing' => $sales->where('side', 'listing')->count(),
            'buyside' => $sales->where('side', 'buyside')->count(),
            'volume' => $sales->sum('sold_price'),
            'cities' => $sales->pluck('city')->unique()->count(),
            'years' => Sale::max('sold_year') - Sale::min('sold_year') + 1,
            'first_year' => Sale::min('sold_year'),
        ];

        return view('pages.sold', [
            'page' => $page,
            'head' => $head,
            'sales' => $sales,
            'stats' => $stats,
            'cities' => $sales->pluck('city')->unique()->sort()->values(),
            'years' => $sales->pluck('sold_year')->unique()->sortDesc()->values(),
            'types' => $sales->pluck('property_type')->unique()->sort()->values(),
        ]);
    }

    /**
     * Map payload: individual sales (for markers) plus per-city aggregates
     * (for the bubble layer). Cached; invalidated whenever a sale changes.
     */
    public function data(Request $request)
    {
        $payload = cache()->remember('sales-map-payload', now()->addHours(6), function () {
            $sales = Sale::public()->mapped()->get();

            $cities = $sales->groupBy('city')->map(fn ($group, $city) => [
                'city' => $city,
                'count' => $group->count(),
                'volume' => (int) $group->sum('sold_price'),
                'lat' => round($group->avg('lat'), 5),
                'lng' => round($group->avg('lng'), 5),
            ])->values()->all();

            return [
                'sales' => $sales->map(fn ($s) => [
                    'id' => $s->id,
                    'address' => $s->address,
                    'city' => $s->city,
                    'price' => $s->sold_price,
                    'year' => $s->sold_year,
                    'type' => $s->property_type,
                    'side' => $s->side,
                    'lat' => $s->lat,
                    'lng' => $s->lng,
                ])->values()->all(),
                'cities' => $cities,
            ];
        });

        return response()->json($payload)->header('Cache-Control', 'public, max-age=3600');
    }
}
