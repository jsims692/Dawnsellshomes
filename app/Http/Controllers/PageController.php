<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $path = '')
    {
        // Match production behavior: trailing-slash URLs 301 to the canonical form
        // (Netlify has always redirected /x/ -> /x; serving both would be duplicate content).
        if ($path !== '' && str_ends_with($path, '/')) {
            return redirect('/'.rtrim($path, '/'), 301);
        }

        $page = Page::where('path', $path)->firstOrFail();

        $css = $page->css_override ?? $page->style()?->css;
        $styleTag = $css !== null ? '<style>'.$css.'</style>' : '';
        $head = str_contains($page->head_html, '<!--STYLE-->')
            ? str_replace('<!--STYLE-->', $styleTag, $page->head_html)
            : $page->head_html.$styleTag;

        // Progressive-rewrite hook: a Blade view at pages/{path} takes over rendering
        // for that URL (it receives the same DB-backed SEO head); everything else
        // falls back to the verbatim imported content.
        $override = 'pages.'.($path === '' ? 'home' : str_replace('/', '.', $path));
        if (view()->exists($override)) {
            return view($override, ['page' => $page, 'head' => $head]);
        }

        return view('page', ['page' => $page, 'head' => $head]);
    }
}
