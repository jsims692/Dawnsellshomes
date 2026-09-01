<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Page row for /who-we-use (the vendors-we-actually-use page — body
     * rendered by the pages/who-we-use Blade view; this row supplies the
     * route + SEO head). Names transcribed from Josh's printed "Our
     * Trusted Contacts" sheet, 9/1/2026; no phone numbers by his call —
     * every referral flows through the team.
     */
    private const PATH = 'who-we-use';

    public function up(): void
    {
        if (DB::table('pages')->where('path', self::PATH)->exists()) {
            return;
        }

        $donor = DB::table('pages')->where('path', 'blog/first-time-homebuyer-guide-northwest-suburbs')->first();
        if (! $donor) {
            return;
        }

        $title = 'Who We Use: Our Trusted Lenders, Attorneys & Inspectors';
        $desc = 'The attorneys, lenders, inspectors, and tradespeople the Dawn Simmons Team personally uses after 25 years of northwest-suburbs closings. Nobody pays to be on this list.';

        $head = (string) $donor->head_html;
        $head = preg_replace('/<title>.*?<\/title>/s', '<title>'.$title.' | Dawn Simmons Team</title>', $head);
        $head = preg_replace('/(<meta name="description" content=")[^"]*(")/', '$1'.$desc.'$2', $head);
        $head = preg_replace('/(<link rel="canonical" href=")[^"]*(")/', '$1https://dawnsellshomes.com/'.self::PATH.'$2', $head);

        DB::table('pages')->insert([
            'path' => self::PATH,
            'slug' => self::PATH,
            'type' => 'root',
            'title' => $title.' | Dawn Simmons Team',
            'meta_description' => $desc,
            'head_html' => $head,
            'body_html' => '',
            'in_sitemap' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('pages')->where('path', self::PATH)->delete();
    }
};
