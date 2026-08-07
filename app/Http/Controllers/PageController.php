<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(string $path = '')
    {
        $page = Page::where('path', rtrim($path, '/'))->firstOrFail();

        $css = $page->css_override ?? $page->style()?->css;
        $styleTag = $css !== null ? '<style>'.$css.'</style>' : '';
        $head = str_contains($page->head_html, '<!--STYLE-->')
            ? str_replace('<!--STYLE-->', $styleTag, $page->head_html)
            : $page->head_html.$styleTag;

        return view('page', ['page' => $page, 'head' => $head]);
    }
}
