<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->string('signature_image')->nullable()->after('badge_color');
            $table->string('signature_name')->nullable()->after('signature_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropColumn(['signature_image', 'signature_name']);
        });
    }
};
