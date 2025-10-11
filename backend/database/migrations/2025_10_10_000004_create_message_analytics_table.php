<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_analytics', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->uuid('keyword_rule_id')->nullable();
            $table->string('phone_number')->nullable();
            $table->integer('incoming_messages')->default(0);
            $table->integer('outgoing_messages')->default(0);
            $table->integer('successful_matches')->default(0);
            $table->integer('failed_matches')->default(0);
            $table->decimal('avg_response_time_ms', 8, 2)->default(0);
            $table->json('peak_hours')->nullable();
            $table->timestamps();

            $table->index(['date', 'keyword_rule_id']);
            $table->index(['date', 'phone_number']);
            $table->index('date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('message_analytics');
    }
};