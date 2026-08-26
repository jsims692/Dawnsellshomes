<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Design v2 aesthetic for the 500+ imported legacy pages WITHOUT touching
     * a word of content: pure color-token and font-stack substitution across
     * the shared stylesheets and inline styles. Backups taken; down() restores.
     */
    private const COLOR_MAP = [
        '1B3A6B' => '0F1E2E', '1b3a6b' => '0f1e2e',   // navy -> ink
        '0D2349' => '0B1622', '0d2349' => '0b1622',   // navy-dark -> ink-deep
        'CC0000' => 'C8102E', 'cc0000' => 'c8102e',   // old red -> v2 red
        'C8A84B' => 'F1637C', 'c8a84b' => 'f1637c',   // gold -> red-soft accent
        '1E4080' => '182B42', '1e4080' => '182b42',   // gradient navies -> ink tints
        '2A5298' => '223A54', '2a5298' => '223a54',
        'A80000' => 'A50D24', 'a80000' => 'a50d24',   // hover red -> red-deep
        'rgba(27,58,107' => 'rgba(15,30,46', 'rgba(27, 58, 107' => 'rgba(15, 30, 46',
        'rgba(13,35,73' => 'rgba(11,22,34', 'rgba(13, 35, 73' => 'rgba(11, 22, 34',
        'rgba(200,168,75' => 'rgba(241,99,124', 'rgba(200, 168, 75' => 'rgba(241, 99, 124',
    ];

    private const FONT_MAP = [
        'Georgia,serif' => "'Fraunces',Georgia,serif",
        'Georgia, serif' => "'Fraunces',Georgia,serif",
        'Arial,sans-serif' => "'Archivo',Arial,sans-serif",
        'Arial, sans-serif' => "'Archivo',Arial,sans-serif",
    ];

    public function up(): void
    {
        DB::statement('CREATE TABLE IF NOT EXISTS page_styles_navy_backup LIKE page_styles');
        DB::statement('INSERT INTO page_styles_navy_backup SELECT * FROM page_styles');
        DB::statement('CREATE TABLE IF NOT EXISTS pages_navy_body_backup (id BIGINT UNSIGNED PRIMARY KEY, body_html LONGTEXT) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        DB::statement('INSERT INTO pages_navy_body_backup SELECT id, body_html FROM pages');

        foreach (DB::table('page_styles')->get() as $row) {
            $css = $this->transform($row->css);
            if ($css !== $row->css) {
                DB::table('page_styles')->where('id', $row->id)->update(['css' => $css]);
            }
        }
        DB::table('pages')->orderBy('id')->chunkById(50, function ($pages) {
            foreach ($pages as $p) {
                $body = $this->transform($p->body_html);
                if ($body !== $p->body_html) {
                    DB::table('pages')->where('id', $p->id)->update(['body_html' => $body]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::statement('UPDATE page_styles ps JOIN page_styles_navy_backup b ON b.id = ps.id SET ps.css = b.css');
        DB::statement('UPDATE pages p JOIN pages_navy_body_backup b ON b.id = p.id SET p.body_html = b.body_html');
        DB::statement('DROP TABLE IF EXISTS page_styles_navy_backup');
        DB::statement('DROP TABLE IF EXISTS pages_navy_body_backup');
    }

    private function transform(?string $s): ?string
    {
        if ($s === null || str_contains($s, "'Archivo',Arial")) {
            return $s; // already themed (idempotent)
        }
        $s = strtr($s, self::COLOR_MAP);

        return strtr($s, self::FONT_MAP);
    }
};
