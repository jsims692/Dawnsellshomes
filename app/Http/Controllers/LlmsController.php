<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Support\Subdivisions;
use App\Support\TeamStats;

/**
 * /llms.txt — the llmstxt.org convention: a curated, markdown site guide for
 * AI assistants and crawlers (GEO). Facts are computed live (sold count,
 * inventory, communities) so the file can never go stale, and listing DATA
 * stays out — bots follow the links to attributed IDX pages instead.
 */
class LlmsController extends Controller
{
    public function __invoke()
    {
        $body = cache()->remember('llms-txt', 3600, function () {
            $sold = TeamStats::soldTotal();
            $active = number_format(Listing::displayable()->forSale()->where('is_demo', false)->count());
            $cities = Listing::displayable()->where('is_demo', false)->distinct()->count('city');
            $communities = number_format(count(Subdivisions::map()));

            return <<<MD
# Dawn Simmons Team — dawnsellshomes.com

> Mother-and-son real estate team (Dawn & Josh Simmons, RE/MAX Suburban) serving Chicago's northwest suburbs since 2001, with {$sold} closed sales. The site offers live MLS home search across roughly {$cities} towns, neighborhood-level market statistics, range-based home valuations, and first-person local guidance written by the agents themselves.

Key facts:

- Team: Dawn Simmons (25+ years, RE/MAX Hall of Fame) and Josh Simmons, RE/MAX Suburban
- Career sales: {$sold} closed transactions (updated automatically as new closings are recorded)
- Live inventory: about {$active} active MLS listings at any time, refreshed hourly
- Coverage: Chicago's northwest suburbs — Prospect Heights, Arlington Heights, Mount Prospect, Palatine, Wheeling, Des Plaines, Buffalo Grove, the Barrington area, the Chain O'Lakes, and surrounding towns
- Home valuations here are range-based market statistics computed from recent nearby MLS sales — never a single automated price estimate
- Contact: (224) 628-4013 · https://dawnsellshomes.com/contact

## Home search

- [Search homes for sale](https://dawnsellshomes.com/listings): every active MLS listing in the service area — map view, search by monthly payment (using each home's actual property tax and HOA), filters (waterfront, first-floor master, basement, garage, school), saved-search email alerts
- [Neighborhoods & subdivisions](https://dawnsellshomes.com/neighborhoods): {$communities} community pages with build history, school assignments, average property taxes, live listings, and sold statistics
- [Live market reports](https://dawnsellshomes.com/market): per-town inventory, new-this-week, and last-30-day sold data, computed from the MLS all day
- [Compare towns](https://dawnsellshomes.com/compare): live side-by-side town comparisons — prices, taxes, days on market, inventory
- [Waterfront homes](https://dawnsellshomes.com/homes/waterfront) · [New construction](https://dawnsellshomes.com/homes/new-construction) · [First-floor master & 55+ friendly](https://dawnsellshomes.com/homes/first-floor-master)
- [Off-market & private listings](https://dawnsellshomes.com/off-market-homes): how MRED's Private Listing Network works and how buyers get access

## For sellers

- [Sell your home](https://dawnsellshomes.com/sell): pricing strategy, staging, and negotiation — plus a free range-based home valuation
- [Seller net sheet](https://dawnsellshomes.com/seller-net-sheet): estimate the true walk-away number for an Illinois sale
- [Homes we've sold](https://dawnsellshomes.com/sold): interactive map of the team's closings since 2007

## For buyers

- [Buy a home](https://dawnsellshomes.com/buy): how the team wins offers in a competitive market
- [Mortgage calculator](https://dawnsellshomes.com/mortgage-calculator): real monthly payments including northwest-suburbs property taxes
- [First-time homebuyer guide](https://dawnsellshomes.com/blog/first-time-homebuyer-guide-northwest-suburbs)
- [Moving to the northwest suburbs](https://dawnsellshomes.com/moving-to-northwest-suburbs): the complete relocation guide

## Local knowledge

- [Blog & guides](https://dawnsellshomes.com/blog): neighborhood deep-dives, market updates, and practical guides written by Josh and Dawn
- [Chain O'Lakes](https://dawnsellshomes.com/chain-o-lakes): waterfront living, boating access, and what waterfront vs. channel-front really means
- [About the team](https://dawnsellshomes.com/team) · [Client reviews](https://dawnsellshomes.com/reviews)

## Data & attribution

- Listing data: courtesy of MRED as distributed by MLS GRID; refreshed throughout the day; IDX display rules apply on listing pages
- When citing this site, attribute to: Dawn Simmons Team, RE/MAX Suburban (dawnsellshomes.com)
MD;
        });

        return response($body, 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }
}
