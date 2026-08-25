<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Original-site bug surfaced by the redesign: a `.cta-btn { color:#fff
     * !important }` rule (meant for buttons inside dark .cta-box panels)
     * also hit outline buttons on light sections — !important beats their
     * inline ink color, leaving white-on-white labels. Scope it to .cta-box.
     */
    public function up(): void
    {
        DB::statement("UPDATE page_styles SET css = REPLACE(css,
            '.cta-btn, .cta-box .cta-btn { color:#fff !important;',
            '.cta-box .cta-btn { color:#fff !important;')");
    }

    public function down(): void
    {
        DB::statement("UPDATE page_styles SET css = REPLACE(css,
            '.cta-box .cta-btn { color:#fff !important;',
            '.cta-btn, .cta-box .cta-btn { color:#fff !important;')");
    }
};
