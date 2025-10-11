<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('phone_number');
            $table->string('current_state')->default('idle');
            $table->json('context')->nullable();
            $table->timestamp('last_message_at');
            $table->timestamps();

            $table->index(['phone_number', 'current_state']);
            $table->index('last_message_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversations');
    }
};