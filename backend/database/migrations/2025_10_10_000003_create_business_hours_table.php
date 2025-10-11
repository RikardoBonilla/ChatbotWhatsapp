<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_hours', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('day_of_week');
            $table->time('open_time')->nullable();
            $table->time('close_time')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->string('timezone')->default('America/Bogota');
            $table->timestamps();

            $table->index(['day_of_week', 'is_closed']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_hours');
    }
};