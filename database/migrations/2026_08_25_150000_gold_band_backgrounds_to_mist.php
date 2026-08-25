<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Final gold correction: sections whose BACKGROUND was the old gold
     * (stats strips, search bands) had become solid brand-red slabs. A light
     * band with dark text maps to the v2 mist, not to an accent color.
     * Accent uses of --gold (text, borders, buttons) stay brand red.
     */
    public function up(): void
    {
        foreach (DB::table('page_styles')->get() as $row) {
            $css = str_replace(['background:var(--gold)', 'background: var(--gold)'],
                ['background:#F2F5F9', 'background:#F2F5F9'], $row->css);
            if ($css !== $row->css) {
                DB::table('page_styles')->where('id', $row->id)->update(['css' => $css]);
            }
        }
        DB::table('pages')->where('body_html', 'like', '%background:var(--gold)%')
            ->orderBy('id')->chunkById(50, function ($pages) {
                foreach ($pages as $p) {
                    DB::table('pages')->where('id', $p->id)->update([
                        'body_html' => str_replace('background:var(--gold)', 'background:#F2F5F9', $p->body_html),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // pages_navy_body_backup / page_styles_navy_backup remain the restore point.
    }
};
