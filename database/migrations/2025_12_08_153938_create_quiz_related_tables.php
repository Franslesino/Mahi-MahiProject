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
        // Tabel bank_soal
        Schema::create('bank_soal', function (Blueprint $table) {
            $table->id();
            $table->text('pertanyaan');
            $table->string('image_url')->nullable();
            $table->enum('tipe_soal', ['multiple_choice', 'essay', 'true_false'])->default('multiple_choice');
            $table->string('kategori')->nullable();
            $table->timestamps();
        });

        // Tabel opsi_jawaban
        Schema::create('opsi_jawaban', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->text('teks_opsi');
            $table->boolean('is_benar')->default(false);
            $table->timestamps();
        });

        // Tabel relasi_quiz
        Schema::create('relasi_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->integer('urutan')->default(0);
            $table->timestamps();
        });

        // Tabel jawaban_peserta
        Schema::create('jawaban_peserta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->foreignId('bank_soal_id')->constrained('bank_soal')->onDelete('cascade');
            $table->foreignId('opsi_jawaban_id')->nullable()->constrained('opsi_jawaban')->onDelete('set null');
            $table->text('opsi_dipilih')->nullable();
            $table->decimal('nilai_tercapai', 5, 2)->nullable();
            $table->timestamp('tanggal_submit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jawaban_peserta');
        Schema::dropIfExists('relasi_quiz');
        Schema::dropIfExists('opsi_jawaban');
        Schema::dropIfExists('bank_soal');
    }
};
