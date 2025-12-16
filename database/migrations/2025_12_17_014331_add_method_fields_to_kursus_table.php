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
        Schema::table('kursus', function (Blueprint $table) {
            // Metode kursus: online, offline, hybrid
            $table->enum('metode', ['online', 'offline', 'hybrid'])->default('online')->after('kategori');
            
            // Lokasi untuk offline/hybrid
            $table->string('lokasi')->nullable()->after('metode');
            $table->text('alamat_lengkap')->nullable()->after('lokasi');
            
            // Kuota peserta (null = unlimited)
            $table->integer('kuota_peserta')->nullable()->after('alamat_lengkap');
            
            // Jadwal kelas
            $table->date('jadwal_mulai')->nullable()->after('kuota_peserta');
            $table->date('jadwal_selesai')->nullable()->after('jadwal_mulai');
            $table->string('hari_kelas')->nullable()->after('jadwal_selesai'); // e.g. "Senin, Rabu, Jumat"
            $table->string('waktu_kelas')->nullable()->after('hari_kelas'); // e.g. "09:00 - 12:00"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropColumn([
                'metode',
                'lokasi',
                'alamat_lengkap',
                'kuota_peserta',
                'jadwal_mulai',
                'jadwal_selesai',
                'hari_kelas',
                'waktu_kelas',
            ]);
        });
    }
};
