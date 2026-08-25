<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Visual-sweep fixes: remaining var(--gold) text accents by context.
     * Dark-surface accents (hero ems, breadcrumbs, badges, cta phone links,
     * dark-band labels) get the red-soft dark accent; big numerals on dark
     * cards go white, as in the v2 system. Light-context uses stay brand red.
     */
    private const DARK = ['.hero h1 em', '.city-hero h1 em', '.breadcrumb a', '.city-hero .breadcrumb a',
        '.badge', '.hero-badge', '.cta-phone', '.team-title', '.team-bg .sec-label',
        '.hero-team figcaption span', '.hero-photos figcaption span'];

    private const WHITE = ['.stat-num', '.result-total'];

    public function up(): void
    {
        foreach (DB::table('page_styles')->get() as $row) {
            $css = preg_replace_callback('/([^{}]+)(\{[^}]*var\(--gold\)[^}]*\})/',
                function ($m) {
                    $sel = trim(preg_replace('/\s+/', ' ', $m[1]));
                    if (in_array($sel, self::DARK, true)) {
                        return $m[1].str_replace('var(--gold)', '#F1637C', $m[2]);
                    }
                    if (in_array($sel, self::WHITE, true)) {
                        return $m[1].str_replace('var(--gold)', '#fff', $m[2]);
                    }

                    return $m[0];
                }, $row->css);
            if ($css !== $row->css) {
                DB::table('page_styles')->where('id', $row->id)->update(['css' => $css]);
            }
        }
    }

    public function down(): void
    {
        // page_styles_navy_backup remains the restore point.
    }
};
