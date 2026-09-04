<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Sale;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeValueController extends Controller
{
    /**
     * Nearby-sales snapshot for the "What is your home worth?" widget.
     *
     * Primary source: closed sales from the live MLS feed (all brokerages,
     * last 12 months) presented as aggregate market analytics with MRED /
     * MLS GRID attribution — never as an automated value of the visitor's
     * property (the license treats a public automated estimate as a CMA/AVM,
     * which is broker-to-client only; the exact number stays a human follow-up).
     * The team's own closed sales blend in as a track-record line, and remain
     * the fallback where the MLS slice has no coverage.
     */
    public function nearby(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric|between:41,43',
            'lng' => 'required|numeric|between:-89,-87',
        ]);
        $lat = (float) $data['lat'];
        $lng = (float) $data['lng'];

        // Haversine in miles, evaluated in SQL.
        $distance = '(3958.8 * ACOS(LEAST(1, COS(RADIANS(?)) * COS(RADIANS(lat)) * COS(RADIANS(lng) - RADIANS(?)) + SIN(RADIANS(?)) * SIN(RADIANS(lat)))))';

        $comps = collect();
        $radius = 0.0;
        foreach ([0.5, 1.0, 2.0, 3.5, 5.0] as $r) {
            $comps = Listing::displayable()->where('is_demo', false)
                ->where('status', 'Closed')
                ->where('close_date', '>=', now()->subMonths(12))
                ->whereNotNull('close_price')->whereNotNull('lat')->whereNotNull('lng')
                ->selectRaw("listings.*, {$distance} AS miles", [$lat, $lng, $lat])
                ->havingRaw('miles <= ?', [$r])
                ->orderBy('miles')
                ->limit(40)
                ->get();
            $radius = $r;
            if ($comps->count() >= 6) {
                break;
            }
        }

        if ($comps->count() >= 3) {
            return $this->snapshot($comps, $radius, $lat, $lng, $distance);
        }

        return $this->teamFallback($lat, $lng, $distance);
    }

    /** Market-analytics snapshot from MLS closed sales (all brokerages). */
    private function snapshot($comps, float $radius, float $lat, float $lng, string $distance)
    {
        [$median, $lo, $hi] = $this->spread($comps->pluck('close_price'));

        $ours = Sale::public()->mapped()
            ->where('sold_year', '>=', (int) date('Y') - 6)
            ->selectRaw("sales.*, {$distance} AS miles", [$lat, $lng, $lat])
            ->havingRaw('miles <= ?', [$radius])
            ->get()->count();

        $asOf = Listing::max('mls_modified_at');
        $asOf = ($asOf ? Carbon::parse($asOf) : now())->timezone('America/Chicago')->format('n/j/Y g:i A T');

        $cities = $comps->groupBy('city')->map->count()->sortDesc();

        // Pre-warm the linked comps' galleries while the visitor reads the
        // range — by the time they tap one, its photos are on disk. Nearest
        // comp first; the worker cap quietly skips the rest when saturated.
        // (Plain foreach: Collection::each() treats warm()'s false — "gallery
        // already complete" — as break, which silently starved comps 2-5.)
        if (! \App\Support\GalleryWarmer::isBot(request()->userAgent())) {
            foreach ($comps->take(5) as $comp) {
                \App\Support\GalleryWarmer::warm($comp);
            }
        }

        return response()->json([
            'ok' => true,
            'source' => 'mls',
            'count' => $comps->count(),
            'radius_miles' => $radius,
            'median' => $median,
            'low' => $lo,
            'high' => $hi,
            'years' => [(int) $comps->min('close_date')?->format('Y'), (int) $comps->max('close_date')?->format('Y')],
            'top_city' => $cities->keys()->first(),
            'city_count' => $cities->count(),
            'kicker' => "Homes sold within {$radius} mi of",
            'basis' => 'Based on '.$comps->count().' closed sales reported to the MLS in the last 12 months — every brokerage, not just ours',
            'basis_short' => $comps->count().' MLS sales, last 12 mo',
            'ours_line' => $ours > 0 ? "The Dawn Simmons Team personally closed {$ours} of its own deals within {$radius} mi." : '',
            'attribution' => "Sold data courtesy of MRED as distributed by MLS GRID, as of {$asOf}. Deemed reliable but not guaranteed.",
            'sample' => $comps->take(5)->map(fn ($l) => [
                'address' => $l->address_public ? $l->street_address : 'Undisclosed address',
                'url' => $l->url(),
                'city' => $l->city,
                'price' => $l->close_price,
                'year' => (int) $l->close_date?->format('Y'),
                'when' => $l->close_date?->format('M Y'),
                'type' => $l->property_subtype ?: ucfirst((string) $l->dwelling),
                'miles' => round($l->miles, 1),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }

    /** Original behavior: the team's own closed sales, where MLS coverage is thin. */
    private function teamFallback(float $lat, float $lng, string $distance)
    {
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

        [$median, $lo, $hi] = $this->spread($comps->pluck('sold_price'));
        $cities = $comps->groupBy('city')->map->count()->sortDesc();

        return response()->json([
            'ok' => true,
            'source' => 'team',
            'count' => $comps->count(),
            'radius_miles' => $radius,
            'median' => $median,
            'low' => $lo,
            'high' => $hi,
            'years' => [$comps->min('sold_year'), $comps->max('sold_year')],
            'top_city' => $cities->keys()->first(),
            'city_count' => $cities->count(),
            'kicker' => "Homes we've sold within {$radius} mi of",
            'basis' => 'Based on '.$comps->count().' of our own closed sales, '.$comps->min('sold_year').'–'.$comps->max('sold_year'),
            'basis_short' => $comps->count().' of our own sales',
            'ours_line' => '',
            'attribution' => '',
            'sample' => $comps->take(5)->map(fn ($s) => [
                'address' => preg_replace('/^\d+\s+/', '', $s->address),
                'city' => $s->city,
                'price' => $s->sold_price,
                'year' => $s->sold_year,
                'when' => (string) $s->sold_year,
                'type' => $s->property_type,
                'miles' => round($s->miles, 1),
            ])->values(),
        ])->header('Cache-Control', 'no-store');
    }

    /** Median + outlier-trimmed range. */
    private function spread($prices): array
    {
        $prices = $prices->sort()->values();
        $n = $prices->count();
        $median = $n % 2 ? $prices[intdiv($n, 2)] : (int) (($prices[$n / 2 - 1] + $prices[$n / 2]) / 2);

        return [$median, $prices[(int) floor(($n - 1) * 0.15)], $prices[(int) ceil(($n - 1) * 0.85)]];
    }
}
