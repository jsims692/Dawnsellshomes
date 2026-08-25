<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Auction listings (SpecialListingConditions: Auction) — real inventory,
     * but excluded from city-page showcase cards: their "photo" is an auction
     * badge graphic, not the home.
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->boolean('is_auction')->default(false)->after('dwelling');
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn('is_auction');
        });
    }
};
