<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->unsignedInteger('previous_price')->nullable()->after('original_list_price');
            $table->timestamp('price_dropped_at')->nullable()->after('previous_price')->index();
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropColumn(['previous_price', 'price_dropped_at']);
        });
    }
};
