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
        Schema::table('question_banks', function (Blueprint $table) {
            if (!Schema::hasColumn('question_banks', 'is_internal')) {
                $table->boolean('is_internal')->default(false)->after('is_public');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_banks', function (Blueprint $table) {
            if (Schema::hasColumn('question_banks', 'is_internal')) {
                $table->dropColumn('is_internal');
            }
        });
    }
};
