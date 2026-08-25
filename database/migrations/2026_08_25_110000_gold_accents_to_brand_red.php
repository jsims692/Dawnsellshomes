<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Correction to the legacy re-theme: old gold mapped to red-soft (#F1637C)
     * turned gold-tinted panels pink. Solid accents now use the brand red;
     * low-alpha gold washes become the v2 neutral panel tones (mist/line).
     */
    private const MAP = [
        'F1637C' => 'C8102E', 'f1637c' => 'c8102e',
        'rgba(241,99,124' => 'rgba(222,230,238', 'rgba(241, 99, 124' => 'rgba(222, 230, 238',
    ];

    public function up(): void
    {
        foreach (DB::table('page_styles')->get() as $row) {
            $css = strtr($row->css, self::MAP);
            if ($css !== $row->css) {
                DB::table('page_styles')->where('id', $row->id)->update(['css' => $css]);
            }
        }
        DB::table('pages')->orderBy('id')->chunkById(50, function ($pages) {
            foreach ($pages as $p) {
                $body = strtr((string) $p->body_html, self::MAP);
                if ($body !== $p->body_html) {
                    DB::table('pages')->where('id', $p->id)->update(['body_html' => $body]);
                }
            }
        });
    }

    public function down(): void
    {
        // Reverse of an accent correction; the navy-era backups from the
        // previous migration remain the true restore point.
    }
};
