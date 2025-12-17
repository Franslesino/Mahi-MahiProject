<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Create course_schedules table for Offline/Hybrid mode courses
     * Stores meeting schedules (date, time, location)
     */
    public function up(): void
    {
        Schema::create('course_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->string('title');                        // Nama sesi (e.g., "Pertemuan 1: Pengenalan")
            $table->date('date');                           // Tanggal pertemuan
            $table->time('start_time');                     // Waktu mulai
            $table->time('end_time');                       // Waktu selesai
            $table->string('location')->nullable();          // Lokasi fisik (untuk Offline/Hybrid)
            $table->string('meeting_url')->nullable();       // Link meeting online (untuk Hybrid)
            $table->text('description')->nullable();         // Deskripsi/catatan sesi
            $table->integer('order')->default(0);            // Urutan sesi
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            // Index untuk query yang sering digunakan
            $table->index(['kursus_id', 'date']);
            $table->index(['kursus_id', 'order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('course_schedules');
    }
};
