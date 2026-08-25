<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Article-page fixes surfaced by visual review:
     * - CTA buttons: `.article-body a` outranked `.cta-btn`, leaving red-on-red
     *   labels; the old blanket `!important` white also blanked outline
     *   buttons. Win by specificity instead, no !important.
     * - `.sub` hero subtitles had no rule at all (dark text on the dark hero).
     * - On-dark hero kickers use the v2 dark-surface accent (red-soft), not
     *   the solid brand red.
     */
    public function up(): void
    {
        foreach (DB::table('page_styles')->get() as $row) {
            $css = str_replace(
                '.article-body .cta-box .cta-btn { color:#fff !important;',
                '.article-body a.cta-btn { color:#fff;',
                $row->css);
            $css = str_replace(
                'text-transform:uppercase; color:var(--gold); margin-bottom:14px; }',
                'text-transform:uppercase; color:#F1637C; margin-bottom:14px; }',
                $css);
            if (str_contains($css, '.article-hero') && ! str_contains($css, '.article-hero .sub')) {
                $css .= "\n.article-hero .sub { color:rgba(255,255,255,.78); font-family:'Archivo',Arial,sans-serif; font-size:17px; line-height:1.6; max-width:680px; margin:0 auto; }";
            }
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
