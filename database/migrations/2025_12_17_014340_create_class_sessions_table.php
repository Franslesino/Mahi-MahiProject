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
        Schema::create('class_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');

            // Informasi sesi
            $table->string('judul')->nullable(); // Judul sesi, e.g. "Pertemuan 1: Pengenalan"
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');

            // Lokasi (bisa override dari kursus)
            $table->string('lokasi')->nullable();

            // Tipe sesi untuk hybrid
            $table->enum('tipe', ['online', 'offline'])->default('offline');

            // Link meeting untuk online
            $table->string('meeting_link')->nullable();

            // Catatan
            $table->text('catatan')->nullable();

            // Status
            $table->enum('status', ['scheduled', 'ongoing', 'completed', 'cancelled'])->default('scheduled');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('class_sessions');
    }
};
