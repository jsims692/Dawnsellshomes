<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Per-room bath flag (MRED MRD_Bath) — powers the master-bath filter. */
    public function up(): void
    {
        if (! Schema::hasColumn('listing_rooms', 'bath')) {
            Schema::table('listing_rooms', fn (Blueprint $t) => $t->string('bath', 20)->nullable()->after('flooring'));
        }
    }

    public function down(): void
    {
        Schema::table('listing_rooms', fn (Blueprint $t) => $t->dropColumn('bath'));
    }
};
