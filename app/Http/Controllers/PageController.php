<?php

namespace App\Http\Controllers;

use App\Models\Page;

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
            'reviews', 'sell', 'buy' => ['reviews' => config('site.reviews', [])],
            'blog' => ['posts' => $this->blogPosts()],
            default => [],
        };
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
