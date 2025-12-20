<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Make passing_score nullable for regular quizzes (material review only)
     */
    public function up(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->integer('passing_score')->nullable()->default(null)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->integer('passing_score')->default(70)->change();
        });
    }
};
