<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PageStyle;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ImportStaticSite extends Command
{
    protected $signature = 'import:static
        {source=/home/jsims/dawnsellshomes : Path to the static site tree}
        {--fresh : Truncate pages and page_styles before importing}';

    protected $description = 'Import the static HTML site into the pages table (pass 1: verbatim head/body slices, deduped CSS, parsed SEO fields)';

    public function handle(): int
    {
        $source = rtrim($this->argument('source'), '/');

        if ($this->option('fresh')) {
            DB::statement('PRAGMA foreign_keys = OFF');
            Page::truncate();
            PageStyle::truncate();
            DB::statement('PRAGMA foreign_keys = ON');
        }

        $sitemapPaths = $this->sitemapPaths($source);

        $files = collect(glob($source.'/*.html'));
        foreach (['blog', 'cities', 'condos', 'neighborhoods', 'schools'] as $sub) {
            $files = $files->merge(glob($source.'/'.$sub.'/*.html'));
        }
        $files = $files->sort()->values();

        $this->info("Importing {$files->count()} pages…");
        $styles = []; // sha1 => key
        $bar = $this->output->createProgressBar($files->count());

        foreach ($files as $file) {
            $rel = substr($file, strlen($source) + 1);
            $path = preg_replace('/\.html$/', '', $rel);
            if ($path === 'index') {
                $path = '';
            }
            $dir = str_contains($path, '/') ? explode('/', $path)[0] : null;
            $type = match ($dir) {
                'cities' => 'city',
                'neighborhoods' => 'neighborhood',
                'condos' => 'condo',
                'schools' => 'school',
                'blog' => 'blog',
                default => 'root',
            };
            $slug = $path === '' ? 'index' : basename($path);

            $html = file_get_contents($file);

            // --- verbatim slices (string-based; no DOM round-trip) ---
            $headStart = strpos($html, '<head>') + 6;
            $headEnd = strpos($html, '</head>');
            $head = substr($html, $headStart, $headEnd - $headStart);

            $bodyStart = strpos($html, '<body>', $headEnd) + 6;
            $bodyEnd = strrpos($html, '</body>');
            $body = substr($html, $bodyStart, $bodyEnd - $bodyStart);

            // --- extract only the FIRST head <style> block (the shared template CSS) for
            // dedupe; later small page-specific blocks stay verbatim in place so head
            // element order is preserved exactly ---
            $css = '';
            $head = preg_replace_callback('#<style[^>]*>(.*?)</style>#s', function ($m) use (&$css) {
                $css = $m[1];

                return '<!--STYLE-->';
            }, $head, 1);

            // Live-site parity fix the local tree is missing (Aug 6 deploy):
            // .fp-grid minmax(260px,…) -> minmax(210px,…), only inside .fp-grid rules.
            $fpFix = fn ($s) => preg_replace_callback(
                '/\.fp-grid\s*\{[^}]*\}/s',
                fn ($m) => str_replace('minmax(260px', 'minmax(210px', $m[0]),
                $s
            );
            $css = $fpFix($css);
            $body = $fpFix($body);
            $head = $fpFix($head);

            // --- parsed reference fields ---
            $title = $this->match('#<title>(.*?)</title>#s', $head);
            $metaDesc = $this->metaContent($head, 'name', 'description');
            $metaKeys = $this->metaContent($head, 'name', 'keywords');
            $canonical = $this->match('#<link\s+rel="canonical"\s+href="([^"]+)"#', $head);

            $og = [];
            foreach (['property', 'name'] as $attr) {
                if (preg_match_all('#<meta\s+'.$attr.'="((?:og|twitter):[^"]+)"\s+content="([^"]*)"#', $head, $m, PREG_SET_ORDER)) {
                    foreach ($m as $tag) {
                        $og[$tag[1]] = $tag[2];
                    }
                }
            }

            $jsonLd = [];
            if (preg_match_all('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m)) {
                $jsonLd = $m[1];
            }

            // --- dedupe CSS across pages ---
            $cssKey = null;
            if (trim($css) !== '') {
                $hash = sha1($css);
                if (! isset($styles[$hash])) {
                    $styles[$hash] = $type.'-'.substr($hash, 0, 8);
                    PageStyle::create(['key' => $styles[$hash], 'css' => $css]);
                }
                $cssKey = $styles[$hash];
            }

            Page::create([
                'path' => $path,
                'type' => $type,
                'slug' => $slug,
                'title' => $title ?? $slug,
                'meta_description' => $metaDesc,
                'meta_keywords' => $metaKeys,
                'canonical' => $canonical,
                'og_tags' => $og ?: null,
                'json_ld' => $jsonLd ?: null,
                'head_html' => $head,
                'body_html' => $body,
                'css_key' => $cssKey,
                'in_sitemap' => in_array($path, $sitemapPaths, true),
            ]);

            $bar->advance();
        }
        $bar->finish();
        $this->newLine();

        // --- link neighborhoods/condos to their parent city via breadcrumb ---
        $cityIds = Page::where('type', 'city')->pluck('id', 'slug');
        $linked = 0;
        foreach (Page::whereIn('type', ['neighborhood', 'condo'])->get() as $page) {
            if (preg_match('#href="/cities/([a-z0-9-]+)"#', $page->body_html, $m) && isset($cityIds[$m[1]])) {
                $page->update(['city_page_id' => $cityIds[$m[1]]]);
                $linked++;
            }
        }

        $this->info(sprintf(
            'Done: %d pages, %d shared styles, %d child pages linked to cities, %d in sitemap.',
            Page::count(), PageStyle::count(), $linked, Page::where('in_sitemap', true)->count()
        ));

        return self::SUCCESS;
    }

    private function match(string $pattern, string $subject): ?string
    {
        return preg_match($pattern, $subject, $m) ? $m[1] : null;
    }

    private function metaContent(string $head, string $attr, string $value): ?string
    {
        return $this->match('#<meta\s+'.$attr.'="'.$value.'"\s+content="([^"]*)"#s', $head);
    }

    private function sitemapPaths(string $source): array
    {
        $paths = [];
        if (preg_match_all('#<loc>https://dawnsellshomes\.com/?([^<]*)</loc>#', file_get_contents($source.'/sitemap.xml'), $m)) {
            $paths = $m[1];
        }

        return $paths;
    }
}
