<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $path = '')
    {
        $page = Page::where('path', $path)->firstOrFail();

        $css = $page->css_override ?? $page->style()?->css;
        $styleTag = $css !== null ? '<style>'.$css.'</style>' : '';
        $head = str_contains($page->head_html, '<!--STYLE-->')
            ? str_replace('<!--STYLE-->', $styleTag, $page->head_html)
            : $page->head_html.$styleTag;

        // Homepage: replace the static 555-record Leaflet block with the DB-driven
        // interactive sales map component (same visual slot, live data).
        if ($path === '') {
            $page->body_html = $this->swapHomepageMap($page->body_html);
        }

        // Progressive-rewrite hook: a Blade view at pages/{path} takes over rendering
        // for that URL (it receives the same DB-backed SEO head); everything else
        // falls back to the verbatim imported content.
        $override = 'pages.'.($path === '' ? 'home' : str_replace('/', '.', $path));
        if (view()->exists($override)) {
            return view($override, ['page' => $page, 'head' => $head]);
        }

        return view('page', [
            'page' => $page,
            'head' => $head,
            // Imported pages are plain HTML; only load Alpine/Livewire when a
            // component was injected (currently: the homepage sales map).
            'needsAlpine' => str_contains($page->body_html, 'x-data='),
        ]);
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
