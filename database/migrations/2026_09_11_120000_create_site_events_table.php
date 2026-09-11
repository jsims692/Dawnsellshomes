<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_events', function (Blueprint $table) {
            $table->id();
            $table->string('event', 24);
            $table->string('path', 191)->nullable();
            $table->json('meta')->nullable();
            $table->string('vhash', 64);          // sha256(ip+key+day) — never the IP
            $table->string('city', 60)->nullable(); // GeoIp guess, for "where are visitors"
            $table->boolean('mobile')->default(false);
            $table->timestamp('created_at');
            $table->index(['event', 'created_at']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_events');
    }
};
