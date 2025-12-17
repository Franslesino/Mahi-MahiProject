<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add capacity and location fields to kursus table for Offline/Hybrid mode
     */
    public function up(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            // Kapasitas peserta (untuk Offline/Hybrid)
            $table->integer('max_participants')->nullable()->after('mode');
            
            // Lokasi default untuk pertemuan (untuk Offline/Hybrid)
            $table->string('default_location')->nullable()->after('max_participants');
            
            // Deskripsi tambahan per mode
            $table->text('mode_description')->nullable()->after('default_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropColumn(['max_participants', 'default_location', 'mode_description']);
        });
    }
};
