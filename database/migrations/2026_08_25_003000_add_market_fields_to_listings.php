<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Market-stats fields: the sync now also replicates Active Under Contract
     * and recently-Closed listings so city pages can show live inventory and
     * sold stats (median sale price, days on market, sale/list ratio).
     */
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->unsignedInteger('close_price')->nullable()->after('list_price');
            $table->date('close_date')->nullable()->after('close_price');
            $table->unsignedInteger('original_list_price')->nullable()->after('close_date');
            $table->unsignedSmallInteger('days_on_market')->nullable()->after('original_list_price');
            $table->index(['city', 'status']);
            $table->index(['city', 'close_date']);
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropIndex(['city', 'status']);
            $table->dropIndex(['city', 'close_date']);
            $table->dropColumn(['close_price', 'close_date', 'original_list_price', 'days_on_market']);
        });
    }
};
