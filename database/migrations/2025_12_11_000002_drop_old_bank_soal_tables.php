<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * PERINGATAN: Migration ini akan menghapus tabel bank_soal lama
     * Jalankan hanya setelah migration sebelumnya sukses dan data sudah diverifikasi
     */
    public function up(): void
    {
        // Hapus foreign key constraints dari tabel yang mereferensi bank_soal
        Schema::table('relasi_quiz', function (Blueprint $table) {
            // Make bank_soal_id nullable dulu
            $table->dropForeign(['bank_soal_id']);
            $table->unsignedBigInteger('bank_soal_id')->nullable()->change();
        });

        Schema::table('jawaban_peserta', function (Blueprint $table) {
            $table->dropForeign(['bank_soal_id']);
            $table->dropForeign(['opsi_jawaban_id']);
            $table->unsignedBigInteger('bank_soal_id')->nullable()->change();
            $table->unsignedBigInteger('opsi_jawaban_id')->nullable()->change();
        });

        // Drop tabel lama
        Schema::dropIfExists('opsi_jawaban');
        Schema::dropIfExists('bank_soal');

        // Hapus kolom yang sudah tidak dipakai
        Schema::table('relasi_quiz', function (Blueprint $table) {
            $table->dropColumn('bank_soal_id');
        });

        Schema::table('jawaban_peserta', function (Blueprint $table) {
            $table->dropColumn(['bank_soal_id', 'opsi_jawaban_id']);
        });

        // Drop migration mapping table (sudah tidak perlu)
        Schema::dropIfExists('migration_mapping_bank_soal');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate bank_soal tables
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->string('image_url')->nullable();
            $table->enum('tipe_soal', ['multiple_choice', 'essay', 'true_false'])->default('multiple_choice');
            $table->string('kategori')->nullable();
            $table->timestamps();
        });

        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->text('teks_opsi');
            $table->boolean('is_benar')->default(false);
            $table->timestamps();
        });

        // Restore columns
        Schema::table('relasi_quiz', function (Blueprint $table) {
            $table->foreignId('bank_soal_id')->nullable()->constrained('bank_soal')->onDelete('cascade');
        });

        Schema::table('jawaban_peserta', function (Blueprint $table) {
            $table->foreignId('bank_soal_id')->nullable()->constrained('bank_soal')->onDelete('cascade');
            $table->foreignId('opsi_jawaban_id')->nullable()->constrained('opsi_jawaban')->onDelete('set null');
        });
    }
};
