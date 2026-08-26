<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Living market reports: /market/{city} pages computed fresh from the
 * replicated MLS data — inventory, what's new this week, last-30-day sold
 * stats, and a trend read. Every stat is attributed market analytics;
 * no single-property valuations.
 */
class MarketController extends Controller
{
    /** Cities need this many rows before a report is worth indexing. */
    private const MIN_ROWS = 20;

    public function index()
    {
        $cities = cache()->remember('market-cities', 1800, fn () => Listing::displayable()
            ->where('is_demo', false)
            ->selectRaw('city, COUNT(*) c, SUM(status IN (\'Active\', \'Active Under Contract\')) a')
            ->groupBy('city')->having('c', '>=', self::MIN_ROWS)->orderBy('city')
            ->get()->map(fn ($r) => ['city' => $r->city, 'slug' => Str::slug($r->city), 'active' => (int) $r->a])
            ->all());

        $asOf = Listing::max('mls_modified_at');

        return view('market.index', [
            'cities' => $cities,
            'dataAsOf' => $asOf ? Carbon::parse($asOf) : now(),
            'head' => '<title>Housing Market Reports — Northwest Suburbs of Chicago | Dawn Simmons Team</title>'
                .'<meta name="description" content="Live housing market reports for every town we serve: current inventory, new listings this week, and last-30-day sold statistics straight from the MLS.">'
                .'<link rel="canonical" href="https://dawnsellshomes.com/market">',
        ]);
    }

    public function show(string $citySlug)
    {
        $city = collect(cache()->get('market-cities', []))->firstWhere('slug', $citySlug)['city']
            ?? Listing::displayable()->where('is_demo', false)->distinct()->pluck('city')
                ->first(fn ($c) => Str::slug($c) === $citySlug);
        abort_unless($city, 404);

        $m = cache()->remember('market-report:'.$citySlug, 1800, function () use ($city) {
            $base = fn () => Listing::displayable()->where('is_demo', false)->where('is_auction', false)
                ->whereRaw('LOWER(city) = ?', [mb_strtolower($city)]);

            $median = function ($q, string $col) {
                $vals = $q->whereNotNull($col)->orderBy($col)->pluck($col);

                return $vals->isEmpty() ? null : $vals[(int) floor(($vals->count() - 1) / 2)];
            };

            $active = $base()->where('status', 'Active')->count();
            $newWeek = $base()->where('status', 'Active')->where('days_on_market', '<=', 7)->count();
            $underContract = $base()->whereIn('status', ['Active Under Contract', 'Pending'])->count();
            $medianList = $median($base()->where('status', 'Active'), 'list_price');

            $sold30 = $base()->where('status', 'Closed')->where('close_date', '>=', now()->subDays(30));
            $sold30Count = (clone $sold30)->count();
            $medianClose30 = $median(clone $sold30, 'close_price');
            $dom30 = (clone $sold30)->whereNotNull('days_on_market')->avg('days_on_market');
            $ratio30 = (clone $sold30)->where('original_list_price', '>', 0)->whereNotNull('close_price')
                ->selectRaw('AVG(close_price / original_list_price * 100) r')->value('r');

            $prior = $base()->where('status', 'Closed')
                ->whereBetween('close_date', [now()->subDays(60), now()->subDays(30)]);
            $medianClosePrior = $median($prior, 'close_price');

            return [
                'active' => $active, 'newWeek' => $newWeek, 'underContract' => $underContract,
                'medianList' => $medianList, 'sold30' => $sold30Count, 'medianClose30' => $medianClose30,
                'dom30' => $dom30 ? (int) round($dom30) : null,
                'ratio30' => $ratio30 ? round($ratio30, 1) : null,
                'medianClosePrior' => $medianClosePrior,
            ];
        });

        $asOf = Listing::max('mls_modified_at');

        return view('market.show', [
            'city' => $city,
            'citySlug' => $citySlug,
            'm' => $m,
            'dataAsOf' => $asOf ? Carbon::parse($asOf) : now(),
            'head' => '<title>'.e($city.', IL Housing Market Report '.now()->format('F Y').' | Dawn Simmons Team').'</title>'
                .'<meta name="description" content="'.e('Live '.$city.' housing market data: '.number_format($m['active']).' active listings, '.$m['sold30'].' closings in the last 30 days'.($m['medianClose30'] ? ' at a $'.number_format($m['medianClose30']).' median' : '').'. Updated from the MLS.').'">'
                .'<link rel="canonical" href="https://dawnsellshomes.com/market/'.e($citySlug).'">'
                .'<meta property="og:title" content="'.e($city.', IL Housing Market Report — '.now()->format('F Y')).'">'
                .'<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg">',
        ]);
    }
}
