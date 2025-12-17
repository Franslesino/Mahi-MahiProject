<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fix #1: Add unique constraint to prevent duplicate enrollments at database level
     */
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            // Add unique constraint for user_id + kursus_id combination
            // This prevents race condition where multiple requests could create duplicate enrollments
            $table->unique(['user_id', 'kursus_id'], 'enrollments_user_kursus_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropUnique('enrollments_user_kursus_unique');
        });
    }
};
