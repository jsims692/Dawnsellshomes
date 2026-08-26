<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Support\MarketStats;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Town-vs-town comparisons from live MLS data. Curated rivalry pages are
 * indexed and in the sitemap; any other valid pair still renders for users
 * but carries noindex so the pair-space never floods the index.
 */
class CompareController extends Controller
{
    /** Curated, alphabetically-ordered pairs (the searches people actually make). */
    public const FEATURED = [
        ['arlington-heights', 'mount-prospect'],
        ['arlington-heights', 'palatine'],
        ['arlington-heights', 'buffalo-grove'],
        ['des-plaines', 'mount-prospect'],
        ['mount-prospect', 'prospect-heights'],
        ['prospect-heights', 'wheeling'],
        ['buffalo-grove', 'wheeling'],
        ['palatine', 'schaumburg'],
        ['des-plaines', 'park-ridge'],
        ['barrington', 'inverness'],
        ['fox-lake', 'mchenry'],
        ['antioch', 'fox-lake'],
        ['libertyville', 'vernon-hills'],
        ['cary', 'crystal-lake'],
        ['hoffman-estates', 'schaumburg'],
    ];

    public function index()
    {
        $featured = [];
        foreach (self::FEATURED as [$a, $b]) {
            $ca = MarketStats::resolveCity($a);
            $cb = MarketStats::resolveCity($b);
            if ($ca && $cb) {
                $featured[] = ['slug' => $a.'-vs-'.$b, 'label' => $ca.' vs '.$cb];
            }
        }
        $cities = cache()->remember('compare-cities', 1800, fn () => Listing::displayable()
            ->where('is_demo', false)
            ->selectRaw('city, COUNT(*) c')->groupBy('city')->having('c', '>=', 20)
            ->orderBy('city')->pluck('city')
            ->map(fn ($c) => ['city' => $c, 'slug' => Str::slug($c)])->all());

        return view('compare.index', [
            'featured' => $featured,
            'cities' => $cities,
            'head' => '<title>Compare Northwest Suburbs Town vs Town | Dawn Simmons Team</title>'
                .'<meta name="description" content="Side-by-side live housing market comparisons for Chicago\'s northwest suburbs — prices, taxes, speed of sale, and inventory, straight from the MLS.">'
                .'<link rel="canonical" href="https://dawnsellshomes.com/compare">',
        ]);
    }

    public function show(string $pair)
    {
        abort_unless(preg_match('/^([a-z0-9\-]+)-vs-([a-z0-9\-]+)$/', $pair, $m), 404);
        [, $a, $b] = $m;
        abort_if($a === $b, 404);

        // One canonical order per pair: alphabetical.
        if (strcmp($a, $b) > 0) {
            return redirect('/compare/'.$b.'-vs-'.$a, 301);
        }

        $cityA = MarketStats::resolveCity($a);
        $cityB = MarketStats::resolveCity($b);
        abort_unless($cityA && $cityB, 404);

        $sa = MarketStats::report($cityA);
        $sb = MarketStats::report($cityB);
        abort_unless(($sa['active'] + $sa['sold30']) > 3 && ($sb['active'] + $sb['sold30']) > 3, 404);

        $featured = in_array([$a, $b], self::FEATURED, true);
        $asOf = Listing::max('mls_modified_at');
        $title = $cityA.' vs '.$cityB;

        return view('compare.show', [
            'a' => ['name' => $cityA, 'slug' => $a, 's' => $sa],
            'b' => ['name' => $cityB, 'slug' => $b, 's' => $sb],
            'verdict' => $this->verdict($cityA, $sa, $cityB, $sb),
            'featured' => $featured,
            'dataAsOf' => $asOf ? Carbon::parse($asOf) : now(),
            'head' => '<title>'.e($title.': Home Prices, Taxes & Market Speed Compared | Dawn Simmons Team').'</title>'
                .'<meta name="description" content="'.e($title.' side by side, from live MLS data: median prices, property taxes, days on market, and inventory. Which northwest suburb fits you?').'">'
                .'<link rel="canonical" href="https://dawnsellshomes.com/compare/'.e($a.'-vs-'.$b).'">'
                .($featured ? '' : '<meta name="robots" content="noindex,follow">')
                .'<meta property="og:title" content="'.e($title.' — Live Market Comparison').'">'
                .'<meta property="og:image" content="https://dawnsellshomes.com/images/og-image-2.jpg">',
        ]);
    }

    /** Honest, data-derived comparison prose — direction, not gospel. */
    private function verdict(string $ca, array $sa, string $cb, array $sb): array
    {
        $lines = [];
        if (($sa['medianClose30'] ?? null) && ($sb['medianClose30'] ?? null)) {
            [$hi, $lo, $hiN, $loN] = $sa['medianClose30'] >= $sb['medianClose30']
                ? [$sa, $sb, $ca, $cb] : [$sb, $sa, $cb, $ca];
            $pct = round(($hi['medianClose30'] - $lo['medianClose30']) / $lo['medianClose30'] * 100);
            $lines[] = $pct < 5
                ? "On recent sales, {$ca} and {$cb} are priced within a few percent of each other — the real difference is what your money buys, not how much you need."
                : "{$hiN} has sold about {$pct}% higher than {$loN} over the last 30 days — roughly the premium you pay for the difference in housing stock and location.";
        }
        if (($sa['avgTax'] ?? null) && ($sb['avgTax'] ?? null) && abs($sa['avgTax'] - $sb['avgTax']) > 800) {
            [$hiN, $hiT, $loN, $loT] = $sa['avgTax'] >= $sb['avgTax']
                ? [$ca, $sa['avgTax'], $cb, $sb['avgTax']]
                : [$cb, $sb['avgTax'], $ca, $sa['avgTax']];
            $lines[] = 'Property taxes run about $'.number_format($hiT - $loT)." a year higher in {$hiN} on the homes we track — worth folding into any monthly-payment math.";
        }
        if (($sa['dom30'] ?? null) && ($sb['dom30'] ?? null) && abs($sa['dom30'] - $sb['dom30']) >= 5) {
            $fast = $sa['dom30'] <= $sb['dom30'] ? $ca : $cb;
            $lines[] = "Homes are moving faster in {$fast} right now — sellers there have more leverage, and buyers need to be readier.";
        }
        $lines[] = 'Thirty-day windows swing on mix — read direction, not gospel. The things the data can\'t compare (downtowns, commutes, how a block actually feels) are exactly what we\'re for.';

        return $lines;
    }
}
