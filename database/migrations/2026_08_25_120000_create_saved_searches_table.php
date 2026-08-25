<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saved_searches', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->string('name', 80)->nullable();
            $table->json('criteria');
            $table->string('token', 40)->unique();
            $table->boolean('active')->default(true);
            $table->timestamp('last_notified_at')->nullable();
            $table->timestamps();
            $table->index(['active', 'last_notified_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_searches');
    }
};
