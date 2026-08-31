<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Identity-preserving email rebrand across stored content: the personal
     * addresses baked into 311 legacy pages become the new branded ones,
     * which forward to the very same inboxes (ImprovMX) — so routing changes
     * for no one while every public surface shows @dawnsellshomes.com.
     */
    private const MAP = [
        'simsre2000@yahoo.com' => 'dawn@dawnsellshomes.com',
        'jsims692@gmail.com' => 'josh@dawnsellshomes.com',
    ];

    public function up(): void
    {
        DB::table('pages')->orderBy('id')->chunkById(50, function ($pages) {
            foreach ($pages as $p) {
                $body = strtr((string) $p->body_html, self::MAP);
                $head = strtr((string) $p->head_html, self::MAP);
                if ($body !== $p->body_html || $head !== $p->head_html) {
                    DB::table('pages')->where('id', $p->id)
                        ->update(['body_html' => $body, 'head_html' => $head]);
                }
            }
        });
    }

    public function down(): void
    {
        DB::table('pages')->orderBy('id')->chunkById(50, function ($pages) {
            $reverse = array_flip(self::MAP);
            foreach ($pages as $p) {
                $body = strtr((string) $p->body_html, $reverse);
                $head = strtr((string) $p->head_html, $reverse);
                if ($body !== $p->body_html || $head !== $p->head_html) {
                    DB::table('pages')->where('id', $p->id)
                        ->update(['body_html' => $body, 'head_html' => $head]);
                }
            }
        });
    }
};
