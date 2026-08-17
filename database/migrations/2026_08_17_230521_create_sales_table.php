<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('address');
            $table->string('city')->index();
            $table->string('state', 2)->default('IL');
            $table->string('zip', 10)->nullable();
            $table->unsignedInteger('sold_price');
            $table->date('sold_at')->nullable();
            // Source data only carries the year; sold_at is filled when known.
            $table->unsignedSmallInteger('sold_year')->index();
            // Single Family | Condo/Townhome | Multi-Unit | Home
            $table->string('property_type')->index();
            // listing (we represented the seller) | buyside (we represented the buyer)
            $table->string('side', 10)->index();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lng', 10, 6)->nullable();
            $table->string('mls_number')->nullable()->unique();
            $table->text('notes')->nullable();
            // hide a record from the public map/list without deleting it
            $table->boolean('is_public')->default(true)->index();
            $table->timestamps();

            $table->index(['lat', 'lng']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
