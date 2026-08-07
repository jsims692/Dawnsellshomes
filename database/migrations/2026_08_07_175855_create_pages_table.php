<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            // URL path without leading slash or extension: '', 'blog', 'cities/arlington-heights', ...
            $table->string('path')->unique();
            // city | neighborhood | condo | school | blog | root
            $table->string('type')->index();
            $table->string('slug')->index();
            $table->string('title');
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical')->nullable();
            // og:*, twitter:* tags as {property: content}
            $table->json('og_tags')->nullable();
            // array of raw JSON-LD blocks (kept verbatim for SEO fidelity)
            $table->json('json_ld')->nullable();
            // verbatim <head> inner HTML with <style> blocks replaced by a <!--STYLE--> marker;
            // the rendering source of truth for v1 byte-fidelity (parsed columns above are
            // reference/flexibility data — editing them does not change output yet)
            $table->longText('head_html');
            // page content between shared chrome (nav/footer), verbatim
            $table->longText('body_html');
            // which shared per-type <style> block this page uses; null = uses css_override
            $table->string('css_key')->nullable()->index();
            $table->longText('css_override')->nullable();
            // parent city page for neighborhoods/condos (nullable FK to pages.id)
            $table->foreignId('city_page_id')->nullable()->constrained('pages')->nullOnDelete();
            $table->boolean('in_sitemap')->default(true);
            $table->timestamps();
        });

        // shared per-type CSS blocks extracted from the static templates
        Schema::create('page_styles', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('css');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_styles');
        Schema::dropIfExists('pages');
    }
};
