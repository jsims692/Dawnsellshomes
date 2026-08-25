<?php

namespace App\Http\Controllers;

use App\Models\Listing;
use App\Models\Page;
use Illuminate\Support\Carbon;

class PageController extends Controller
{
    public function show(string $path = '')
    {
        $page = Page::where('path', $path)->firstOrFail();

        // Progressive-rewrite hook: a Blade view at pages/{path} takes over rendering
        // for that URL. It gets the DB-backed SEO head but styles itself (design v2),
        // so the imported stylesheet slot is dropped rather than filled.
        $override = 'pages.'.($path === '' ? 'home' : str_replace('/', '.', $path));
        if (view()->exists($override)) {
            $head = str_replace('<!--STYLE-->', '', $page->head_html);

            return view($override, ['page' => $page, 'head' => $head] + $this->extraData($path));
        }

        // Homepage (legacy fallback): replace the static 555-record Leaflet block with
        // the DB-driven interactive sales map component (same visual slot, live data).
        if ($path === '') {
            $page->body_html = $this->swapHomepageMap($page->body_html);
            $page->body_html = $this->swapHomeValueWidget($page->body_html);
        }

        // City, neighborhood and condo pages get a live MLS band (inventory,
        // sold stats, listing cards) injected into the imported markup.
        if (in_array($page->type, ['city', 'neighborhood', 'condo'], true)) {
            $this->injectListingsBand($page);
        }

        return view('page', [
            'page' => $page,
            'head' => $this->legacyHead($page),
            // Imported pages are plain HTML; only load Alpine/Livewire when a
            // component was injected (currently: the homepage sales map).
            'needsAlpine' => str_contains($page->body_html, 'x-data='),
        ]);
    }

    /**
     * The old one-page homepage, parked at /old-home while design v2 beds in:
     * same imported markup, injected widget + map, noindexed.
     */
    public function legacyHome()
    {
        $page = Page::where('path', '')->firstOrFail();

        $page->body_html = $this->swapHomepageMap($page->body_html);
        $page->body_html = $this->swapHomeValueWidget($page->body_html);

        return view('page', [
            'page' => $page,
            'head' => '<meta name="robots" content="noindex,nofollow">'.$this->legacyHead($page),
            'needsAlpine' => str_contains($page->body_html, 'x-data='),
        ]);
    }

    /** Imported-page head: fill the stylesheet slot with the page's own CSS. */
    private function legacyHead(Page $page): string
    {
        $css = $page->css_override ?? $page->style()?->css;
        $styleTag = $css !== null ? '<style>'.$css.'</style>' : '';

        return str_contains($page->head_html, '<!--STYLE-->')
            ? str_replace('<!--STYLE-->', $styleTag, $page->head_html)
            : $page->head_html.$styleTag;
    }

    /** Extra view data for specific Blade-rendered pages. */
    private function extraData(string $path): array
    {
        return match ($path) {
            '' => ['teamListings' => $this->teamListings()],
            'reviews', 'sell', 'buy' => ['reviews' => config('site.reviews', [])],
            'blog' => ['posts' => $this->blogPosts()],
            default => [],
        };
    }

    /**
     * The team's own listings for the homepage "recent results" cards:
     * on-market first, then freshest closings. Empty until TEAM_AGENT_MLS_IDS
     * is configured (the view falls back to its static cards).
     */
    private function teamListings(): array
    {
        if (config('site.team_agent_ids', []) === []) {
            return [];
        }

        return cache()->remember('home-team-listings', 300, fn () => Listing::displayable()
            ->where('is_demo', false)->where('is_team', true)->where('is_auction', false)
            ->orderByRaw("FIELD(status, 'Active', 'Active Under Contract', 'Pending', 'Closed')")
            ->orderByRaw('COALESCE(close_date, mls_modified_at) DESC')
            ->limit(3)->get()
            ->map(fn ($l) => [
                'url' => '/listings/'.$l->listing_id,
                'photo' => $l->photoUrl() ?? '',
                'chip' => $l->status === 'Closed'
                    ? 'Sold '.$l->close_date?->format('M Y')
                    : ($l->status === 'Active' ? 'For sale' : 'Under contract'),
                'addr' => $l->address_public ? $l->street_address : 'Address undisclosed',
                'city' => $l->city.', '.$l->state.' '.$l->zip,
                'price' => '$'.number_format($l->close_price ?: $l->list_price),
                'meta' => trim(($l->beds ? $l->beds.' bd · '.$l->baths().' ba' : '')
                    .($l->sqft ? ' · '.number_format($l->sqft).' sqft' : '')).' · '
                    .($l->teamSide() === 'buyer' ? 'We represented the buyer' : 'Listed by our team'),
            ])->all());
    }

    /**
     * Cards for the blog index. The curated list (category, title, excerpt,
     * order) lives in the imported blog page's body_html, so parse it from
     * there; fall back to the blog/* rows if the markup ever changes.
     */
    private function blogPosts(): array
    {
        $body = (string) Page::where('path', 'blog')->value('body_html');
        preg_match_all(
            "/<a class='blog-card' href='([^']+)'>\s*<div class=\"blog-card-cat\">(.*?)<\/div>\s*<h3>(.*?)<\/h3>\s*<p>(.*?)<\/p>/s",
            $body,
            $matches,
            PREG_SET_ORDER
        );

        if ($matches !== []) {
            return array_map(fn ($m) => [
                'href' => $m[1], 'cat' => trim($m[2]), 'title' => trim($m[3]), 'excerpt' => trim($m[4]),
            ], $matches);
        }

        return Page::where('path', 'like', 'blog/%')
            ->orderByDesc('path')
            ->get(['path', 'title', 'meta_description'])
            ->map(fn ($p) => [
                'href' => '/'.$p->path,
                'cat' => 'From the blog',
                'title' => e(preg_replace('/\s*\|\s*Dawn Simmons Team\s*$/', '', (string) $p->title)),
                'excerpt' => e((string) $p->meta_description),
            ])->all();
    }

    /**
     * Inject the live-MLS band (stats + listing cards) into a legacy city or
     * neighborhood page. Rendered HTML is cached briefly so the 400+ imported
     * pages stay fast; insertion goes after the city-search section when the
     * page has one, otherwise just above the footer.
     */
    private function injectListingsBand(Page $page): void
    {
        if (! config('site.listings_enabled')) {
            return;
        }

        $body = $page->body_html;
        // Pages with the old IDX Broker carousel ("Active Listings in …") get
        // the live cards swapped into that slot; everywhere else the full band
        // is inserted after the search section (fallback: above the footer).
        $embedded = str_contains($body, 'idx-widget-wrap');

        $html = cache()->remember('listings-band:'.$page->path.($embedded ? ':embed' : ''), 300,
            fn () => $this->renderListingsBand($page, $embedded) ?? '');
        if ($html === '') {
            return;
        }

        if ($embedded) {
            // preg_replace_callback: the band HTML contains "$123,456" prices,
            // which a plain replacement string would treat as backreferences.
            $page->body_html = preg_replace_callback(
                '/<div class="idx-widget-wrap"[^>]*>\s*<script[^>]*><\/script>\s*<\/div>/',
                fn () => $html, $body, 1);

            return;
        }

        $pos = strpos($body, 'id="city-search"');
        if ($pos !== false && ($end = strpos($body, '</section>', $pos)) !== false) {
            $page->body_html = substr_replace($body, "\n".$html, $end + strlen('</section>'), 0);

            return;
        }
        $pos = strripos($body, '<footer');
        $page->body_html = $pos === false ? $body.$html : substr_replace($body, $html."\n", $pos, 0);
    }

    private function renderListingsBand(Page $page, bool $embedded = false): ?string
    {
        [$cityName, $subdivision] = $this->listingScope($page);
        if (! $cityName) {
            return null;
        }

        $cityBase = Listing::displayable()->where('is_demo', false)
            ->whereRaw('LOWER(city) = ?', [$cityName]);
        if (! (clone $cityBase)->exists()) {
            return null;
        }
        $cityLabel = (clone $cityBase)->value('city') ?? ucwords($cityName);

        // Neighborhood/condo pages narrow to the subdivision when the MLS field
        // matches (one panel, no toggle — a complex is what it is). City pages
        // get a detached panel (default) and an attached panel behind a toggle.
        $panels = [];
        $title = $cityLabel;
        $narrowed = false;
        if ($subdivision) {
            $sub = (clone $cityBase)->whereRaw('LOWER(subdivision) = ?', [$subdivision]);
            if ((clone $sub)->exists()) {
                $narrowed = true;
                $title = ucwords($subdivision).', '.$cityLabel;
                $panels[] = $this->bandPanel($sub, 'All homes', $title, $cityLabel,
                    '/listings?city='.urlencode($cityLabel));
            }
        }
        if (! $narrowed) {
            $panels[] = $this->bandPanel((clone $cityBase)->where('dwelling', 'detached'),
                'Detached homes', $cityLabel, $cityLabel,
                '/listings?city='.urlencode($cityLabel).'&dwelling=detached', 'detached');
            $panels[] = $this->bandPanel((clone $cityBase)->where('dwelling', 'attached'),
                'Attached living', $cityLabel, $cityLabel,
                '/listings?city='.urlencode($cityLabel).'&dwelling=attached', 'attached');
        }
        $panels = array_values(array_filter($panels));
        if ($panels === []) {
            return null;
        }

        $asOf = Listing::max('mls_modified_at');

        return view('components.listings.in-city', [
            'embedded' => $embedded,
            'title' => $title,
            'panels' => $panels,
            'dataAsOf' => $asOf ? Carbon::parse($asOf) : now(),
        ])->render();
    }

    /** Stats + cards for one band panel (a dwelling type, or a subdivision). */
    private function bandPanel($base, string $label, string $title, string $allLabel, string $allUrl, ?string $dwelling = null): ?array
    {
        // Auctions stay out of the band entirely (stats included): teaser
        // prices distort medians and their "photos" are badge graphics.
        $base = (clone $base)->where('is_auction', false);

        $active = (clone $base)->where('status', 'Active')->count();
        // "Under contract" the way consumers mean it: contingent + pending.
        $underContract = (clone $base)->whereIn('status', ['Active Under Contract', 'Pending'])->count();
        // Sold window: 6 months, widened to 12 when the 6-month bucket is empty.
        $closedMonths = 6;
        $closed = (clone $base)->where('status', 'Closed')->where('close_date', '>=', now()->subMonths(6));
        $closedCount = (clone $closed)->count();
        if ($closedCount === 0) {
            $closedMonths = 12;
            $closed = (clone $base)->where('status', 'Closed')->where('close_date', '>=', now()->subMonths(12));
            $closedCount = (clone $closed)->count();
        }
        if ($active + $underContract + $closedCount === 0) {
            return null;
        }

        $prices = (clone $closed)->whereNotNull('close_price')->orderBy('close_price')->pluck('close_price');
        $avgDom = (clone $closed)->whereNotNull('days_on_market')->avg('days_on_market');
        $ratio = (clone $closed)->whereNotNull('close_price')
            ->where('original_list_price', '>', 0)
            ->selectRaw('AVG(close_price / original_list_price * 100) AS r')->value('r');

        $cards = (clone $base)->forSale()
            ->orderByRaw("FIELD(status, 'Active', 'Active Under Contract')")
            ->orderByDesc('mls_modified_at')->limit(6)
            ->get(['id', 'listing_key', 'listing_id', 'status', 'list_price', 'street_address',
                'city', 'state', 'zip', 'address_public', 'beds', 'baths_full', 'baths_half', 'sqft']);

        return [
            'key' => $dwelling ?? 'all',
            'label' => $label,
            'title' => $title,
            'listings' => $cards,
            'stats' => [
                'active' => $active,
                'underContract' => $underContract,
                'closed6mo' => $closedCount,
                'closedMonths' => $closedMonths,
                'medianClose' => $prices->isEmpty() ? null : $prices[(int) floor(($prices->count() - 1) / 2)],
                'avgDom' => $avgDom ? (int) round($avgDom) : null,
                'saleListRatio' => $ratio ? round($ratio, 1) : null,
            ],
            'total' => (clone $base)->forSale()->count(),
            'allLabel' => $allLabel,
            'allUrl' => $allUrl,
        ];
    }

    /** [city name (lowercase, spaces), subdivision or null] for a city/neighborhood page. */
    private function listingScope(Page $page): array
    {
        $slug = $page->slug ?: basename($page->path);
        if ($page->type === 'city') {
            return [str_replace('-', ' ', $slug), null];
        }

        // Neighborhood/condo slugs end in their city's slug: "{subdivision}-{city}".
        $citySlugs = cache()->remember('city-page-slugs', now()->addDay(),
            fn () => Page::where('type', 'city')->pluck('slug')
                ->sortByDesc(fn ($s) => strlen($s))->values()->all());
        foreach ($citySlugs as $citySlug) {
            if (str_ends_with($slug, '-'.$citySlug)) {
                $subdivision = str_replace('-', ' ', substr($slug, 0, -strlen($citySlug) - 1));

                return [str_replace('-', ' ', $citySlug), $subdivision ?: null];
            }
        }

        return [null, null];
    }

    /**
     * Replace the static "What is your home worth?" box with the interactive
     * widget (Places autocomplete + nearby-sales snapshot). The Google loader
     * is defined once here so both this widget and the map below share it.
     */
    private function swapHomeValueWidget(string $body): string
    {
        $start = strpos($body, '<div class="value-widget">');
        if ($start === false) {
            return $body;
        }
        // The static widget closes right after its "search available homes" line.
        $marker = 'search available homes →</a></p>';
        $end = strpos($body, $marker, $start);
        if ($end === false) {
            return $body;
        }
        $end = strpos($body, '</div>', $end + strlen($marker)) + strlen('</div>');

        $key = config('services.google.maps_key');
        $loader = $key ? '<script>window.__gmapsReady ||= new Promise((resolve) => { if (window.google?.maps?.importLibrary) return resolve(); window.__gmapsInit = () => resolve(); const s = document.createElement("script"); s.src = "https://maps.googleapis.com/maps/api/js?key='.$key.'&v=weekly&loading=async&callback=__gmapsInit"; s.async = true; s.defer = true; document.head.appendChild(s); });</script>'."
" : '';

        return substr($body, 0, $start).$loader.view('components.home.value-widget')->render().substr($body, $end);
    }

    private function swapHomepageMap(string $body): string
    {
        $start = strpos($body, '<div id="homeMap"');
        if ($start === false) {
            return $body;
        }
        $dealsPos = strpos($body, 'var DEALS = [', $start);
        $end = $dealsPos !== false ? strpos($body, '</script>', $dealsPos) : false;
        if ($end === false) {
            return $body;
        }
        $end += strlen('</script>');

        // The removed block spans the map div, the section's closing tags, and
        // the two script tags — so re-emit the closers around the component.
        $replacement = view('components.sales.map', ['height' => '480px', 'compact' => true])->render()
            ."\n  </div>\n</section>\n";

        return substr($body, 0, $start).$replacement.substr($body, $end);
    }
}
