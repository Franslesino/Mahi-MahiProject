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
        Schema::table('quiz', function (Blueprint $table) {
            $table->boolean('is_final_quiz')->default(false)->after('kursus_id');
            $table->integer('minimum_score')->nullable()->after('is_final_quiz')->comment('Minimum score to pass final quiz (0-100)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz', function (Blueprint $table) {
            $table->dropColumn(['is_final_quiz', 'minimum_score']);
        });
    }
};
