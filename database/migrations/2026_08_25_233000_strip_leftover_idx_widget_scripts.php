<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Final IDX Broker eviction: the original rewire matched
     * https://search.dawnsellshomes.com URLs, but widget embeds use
     * protocol-relative src ("//search...") and slipped through — 26 pages
     * still carried live third-party <script> tags. Remove the scripts and
     * point any straggler search-subdomain URL at our own /listings.
     */
    public function up(): void
    {
        DB::table('pages')->orderBy('id')->chunkById(50, function ($pages) {
            foreach ($pages as $p) {
                $body = (string) $p->body_html;
                $body = preg_replace('/<script[^>]*idxwidgetsrc-\d+[^>]*>.*?<\/script>/s', '', $body);
                $body = preg_replace('/(?:https?:)?\/\/search\.dawnsellshomes\.com[^"\'\s<>]*/', '/listings', $body);
                if ($body !== $p->body_html) {
                    DB::table('pages')->where('id', $p->id)->update(['body_html' => $body]);
                }
            }
        });
    }

    public function down(): void
    {
        // Removal of dead third-party embeds; nothing to restore.
    }
};
