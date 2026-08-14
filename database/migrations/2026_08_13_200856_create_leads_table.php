<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            // buy | sell | both | value | invest | rent | other (the form's select options)
            $table->string('interest')->nullable();
            $table->text('message')->nullable();
            // honeypot hit or other bot signal — stored but excluded from forwarding
            $table->boolean('is_spam')->default(false)->index();
            $table->string('source_page')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            // when the lead was successfully forwarded to the CRM webhook
            $table->timestamp('forwarded_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
