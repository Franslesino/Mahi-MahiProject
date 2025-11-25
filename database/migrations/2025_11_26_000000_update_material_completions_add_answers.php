<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('material_completions', function (Blueprint $table) {
            $table->json('answers_json')->nullable()->after('completed_at');
            $table->integer('score')->nullable()->after('answers_json');
        });
    }

    public function down(): void
    {
        Schema::table('material_completions', function (Blueprint $table) {
            $table->dropColumn(['answers_json', 'score']);
        });
    }
};

