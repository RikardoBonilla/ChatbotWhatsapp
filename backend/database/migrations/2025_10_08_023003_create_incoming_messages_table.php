<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('incoming_messages', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('from_phone');
            $table->text('content');
            $table->string('twilio_sid')->unique();
            $table->boolean('processed')->default(false);
            $table->uuid('response_message_id')->nullable();
            $table->timestamps();

            $table->index(['from_phone', 'created_at']);
            $table->index('processed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incoming_messages');
    }
};
