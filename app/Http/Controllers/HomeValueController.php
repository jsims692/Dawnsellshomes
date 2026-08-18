<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;

class HomeValueController extends Controller
{
    /**
     * Nearby-sales snapshot for the homepage "What is your home worth?" widget.
     * Uses the team's own closed sales (real, verifiable comps) — not a modeled
     * AVM. Radius widens automatically until enough comps are found.
     */
    public function nearby(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:41,43',
            'lng' => 'required|numeric|between:-89,-87',
        ]);
        $lat = (float) $data['lat'];
        $lng = (float) $data['lng'];

        // Haversine in miles, evaluated in SQL; consider only recent-ish sales for relevance.
        $distance = "(3958.8 * ACOS(LEAST(1, COS(RADIANS(?)) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(lat)))))";
        $recentYear = (int) date('Y') - 6;

        $comps = collect();
        $radius = 0.0;
        foreach ([0.5, 1.0, 2.0, 3.5, 5.0] as $r) {
            $comps = Sale::public()->mapped()
                ->where('sold_year', '>=', $recentYear)
                ->selectRaw("sales.*, {$distance} AS miles", [$lat, $lng, $lat])
                ->havingRaw('miles <= ?', [$r])
                ->orderBy('miles')
                ->limit(40)
                ->get();
            $radius = $r;
            if ($comps->count() >= 6) {
                break;
            }
        }

        if ($comps->count() < 3) {
            return response()->json(['ok' => false, 'reason' => 'too_few_comps']);
        }

        $prices = $comps->pluck('sold_price')->sort()->values();
        $n = $prices->count();
        $median = $n % 2 ? $prices[intdiv($n, 2)] : (int) (($prices[$n / 2 - 1] + $prices[$n / 2]) / 2);
        // Trim the extremes for the displayed range so one outlier doesn't distort it.
        $lo = $prices[(int) floor(($n - 1) * 0.15)];
        $hi = $prices[(int) ceil(($n - 1) * 0.85)];

        $cities = $comps->groupBy('city')->map->count()->sortDesc();

        return response()->json([
            'ok' => true,
            'count' => $n,
            'radius_miles' => $radius,
            'median' => $median,
            'low' => $lo,
            'high' => $hi,
            'years' => [$comps->min('sold_year'), $comps->max('sold_year')],
            'top_city' => $cities->keys()->first(),
            'city_count' => $cities->count(),
            'sample' => $comps->take(5)->map(fn ($s) => [
                'address' => preg_replace('/^\d+\s+/', '', $s->address), // street only, no house number
                'city' => $s->city,
                'price' => $s->sold_price,
                'year' => $s->sold_year,
                'type' => $s->property_type,
                'miles' => round($s->miles, 1),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }
}
