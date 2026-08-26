<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Launch night: crawlers fanning across 2,145 subdivision pages ran
     * LOWER(TRIM(...)) matches that bypass every index — full scans of the
     * listings table per request, 504s under load. Values are TRIMmed once
     * here (and at sync writes going forward); queries switch to plain
     * equality, which the utf8mb4 _ci collation already makes
     * case-insensitive, served by this composite index.
     */
    public function up(): void
    {
        DB::statement('UPDATE listings SET city = TRIM(city), subdivision = TRIM(subdivision)');
        Schema::table('listings', function ($table) {
            $table->index(['city', 'subdivision', 'status'], 'listings_city_subdivision_status');
        });
    }

    public function down(): void
    {
        Schema::table('listings', fn ($table) => $table->dropIndex('listings_city_subdivision_status'));
    }
};
