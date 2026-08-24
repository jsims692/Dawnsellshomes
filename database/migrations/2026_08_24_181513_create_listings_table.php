<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('listings', function (Blueprint $table) {
            $table->id();
            // RESO identifiers
            $table->string('listing_key')->unique();      // ListingKey (MLS GRID primary key)
            $table->string('listing_id')->index();        // ListingId (the MLS number shown on displays)
            $table->string('status')->index();            // MlsStatus (Active, Active Under Contract, ...)
            $table->unsignedInteger('list_price')->index();
            // address (display honors address_public per Rule 7)
            $table->string('street_address')->nullable();
            $table->string('city')->index();
            $table->string('state', 2)->default('IL');
            $table->string('zip', 10)->nullable();
            $table->boolean('address_public')->default(true);   // InternetAddressDisplayYN
            $table->boolean('display_public')->default(true);   // InternetEntireListingDisplayYN
            $table->boolean('avm_allowed')->default(true);      // InternetAutomatedValuationDisplayYN
            $table->boolean('comments_allowed')->default(true); // InternetConsumerCommentYN
            // facts
            $table->unsignedTinyInteger('beds')->nullable();
            $table->unsignedTinyInteger('baths_full')->nullable();
            $table->unsignedTinyInteger('baths_half')->nullable();
            $table->unsignedInteger('sqft')->nullable();
            $table->string('property_type')->nullable()->index();     // PropertyType
            $table->string('property_subtype')->nullable();           // PropertySubType
            $table->unsignedSmallInteger('year_built')->nullable();
            $table->text('remarks')->nullable();                      // PublicRemarks only
            $table->string('subdivision')->nullable()->index();
            // required attribution (Rule 22)
            $table->string('list_office_name');
            $table->string('list_office_phone')->nullable();
            $table->string('list_office_email')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            // media: array of {url, order} kept within MLS GRID caching rules
            $table->json('media')->nullable();
            $table->unsignedSmallInteger('photo_count')->default(0);
            $table->timestampTz('mls_modified_at')->nullable()->index(); // ModificationTimestamp (incremental sync cursor)
            $table->json('raw')->nullable();                             // full RESO record for fields we add later
            $table->boolean('is_demo')->default(false)->index();         // pre-approval sample rows, never real data
            $table->timestamps();

            $table->index(['city', 'status', 'list_price']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('listings');
    }
};
