<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->json('keywords')->after('keyword');
            $table->boolean('fuzzy_match')->default(false)->after('keywords');
            $table->string('trigger_type')->default('contains')->after('fuzzy_match');
            $table->json('variables')->nullable()->after('trigger_type');
        });

        DB::statement("UPDATE keyword_rules SET keywords = JSON_ARRAY(keyword)");

        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->dropColumn('keyword');
        });
    }

    public function down(): void
    {
        Schema::table('keyword_rules', function (Blueprint $table) {
            $table->string('keyword')->after('id');
            $table->dropColumn(['keywords', 'fuzzy_match', 'trigger_type', 'variables']);
        });
    }
};