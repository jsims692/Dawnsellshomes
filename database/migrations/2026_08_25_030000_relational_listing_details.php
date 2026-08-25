<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The MLS-sheet detail data, modeled relationally:
     *  - listing_rooms:    one row per room (name, dimensions, level, flooring)
     *  - listing_features: one row per multi-value item (appliances, interior/
     *    exterior features, amenities, heating, sewer…), keyed by category —
     *    heating/cooling/basement/hoa_includes move here from string columns
     *  - single-valued sheet fields become real columns
     * Only consumer-displayable fields are stored; broker-only data (private
     * remarks, lockbox, showing info, agreement type, owner info) never is.
     */
    public function up(): void
    {
        Schema::create('listing_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('name', 60);
            $table->string('dimensions', 20)->nullable();
            $table->string('level', 30)->nullable();
            $table->string('flooring', 40)->nullable();
            $table->unsignedTinyInteger('sort')->default(0);
        });

        Schema::create('listing_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->string('category', 30)->index();
            $table->string('value', 150);
        });

        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['heating', 'cooling', 'basement', 'hoa_includes']);
            $table->string('exposure', 40)->nullable()->after('ownership');
            $table->string('age_range', 20)->nullable()->after('exposure');
            $table->string('parcel_number', 20)->nullable()->after('age_range');
            $table->string('township', 40)->nullable()->after('parcel_number');
            $table->string('county', 40)->nullable()->after('township');
            $table->string('elementary_school', 80)->nullable()->after('county');
            $table->string('middle_school', 80)->nullable()->after('elementary_school');
            $table->string('high_school', 80)->nullable()->after('middle_school');
            $table->string('water_body', 60)->nullable()->after('high_school');
            $table->string('virtual_tour_url', 255)->nullable()->after('water_body');
            $table->unsignedTinyInteger('fireplaces')->nullable()->after('virtual_tour_url');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listing_features');
        Schema::dropIfExists('listing_rooms');
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['exposure', 'age_range', 'parcel_number', 'township', 'county',
                'elementary_school', 'middle_school', 'high_school', 'water_body',
                'virtual_tour_url', 'fireplaces']);
            $table->string('heating', 120)->nullable();
            $table->string('cooling', 120)->nullable();
            $table->string('basement', 80)->nullable();
            $table->string('hoa_includes', 255)->nullable();
        });
    }
};
