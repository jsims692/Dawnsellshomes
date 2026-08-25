<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** Audit gaps: full address line lives in components (dir/suffix/unit),
     *  plus PIN, bedrooms-possible, sqft source, condo entry level. */
    public function up(): void
    {
        // Idempotent: a first run applied only part of this (MySQL DDL is not
        // transactional) before failing.
        foreach ([
            'parcel_number' => fn (Blueprint $t) => $t->string('parcel_number', 30)->nullable()->after('lot_dimensions'),
            'bedrooms_possible' => fn (Blueprint $t) => $t->unsignedTinyInteger('bedrooms_possible')->nullable()->after('beds'),
            'living_area_source' => fn (Blueprint $t) => $t->string('living_area_source', 30)->nullable()->after('sqft'),
            'entry_level' => fn (Blueprint $t) => $t->unsignedSmallInteger('entry_level')->nullable()->after('stories'),
        ] as $column => $add) {
            if (! Schema::hasColumn('listings', $column)) {
                Schema::table('listings', fn (Blueprint $t) => $add($t));
            }
        }
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['parcel_number', 'bedrooms_possible', 'living_area_source', 'entry_level']);
        });
    }
};
