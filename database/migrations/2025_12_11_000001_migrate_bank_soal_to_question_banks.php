<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Buat Question Bank untuk menampung soal dari bank_soal lama
        $defaultBankId = DB::table('question_banks')->insertGetId([
            'title' => 'Bank Soal Lama (Migrasi)',
            'description' => 'Soal-soal yang dimigrasikan dari sistem bank soal lama',
            'category' => 'Migrasi',
            'created_by' => 1, // Ganti dengan admin user ID
            'is_public' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Step 2: Migrasi data dari bank_soal ke questions
        $bankSoal = DB::table('bank_soal')->get();
        
        $mappingBankSoalToQuestion = [];

        foreach ($bankSoal as $soal) {
            // Insert ke questions
            $questionId = DB::table('questions')->insertGetId([
                'question_bank_id' => $defaultBankId,
                'type' => $soal->tipe_soal,
                'question_text' => $soal->pertanyaan,
                'explanation' => null,
                'points' => 1,
                'order' => 0,
                'correct_answer' => null,
                'created_at' => $soal->created_at,
                'updated_at' => $soal->updated_at,
            ]);

            // Simpan mapping untuk update relasi
            $mappingBankSoalToQuestion[$soal->id] = $questionId;

            // Migrasi opsi jawaban
            $opsiJawaban = DB::table('opsi_jawaban')
                ->where('bank_soal_id', $soal->id)
                ->get();

            $order = 0;
            foreach ($opsiJawaban as $opsi) {
                DB::table('question_options')->insert([
                    'question_id' => $questionId,
                    'option_text' => $opsi->teks_opsi,
                    'is_correct' => $opsi->is_benar,
                    'order' => $order++,
                    'created_at' => $opsi->created_at,
                    'updated_at' => $opsi->updated_at,
                ]);
            }
        }

        // Step 3: Update relasi_quiz untuk menggunakan question_id
        // Tambah kolom baru untuk question_id di relasi_quiz
        Schema::table('relasi_quiz', function (Blueprint $table) {
            $table->foreignId('question_id')->nullable()->after('bank_soal_id')->constrained('questions')->onDelete('cascade');
        });

        // Update relasi_quiz dengan mapping
        foreach ($mappingBankSoalToQuestion as $bankSoalId => $questionId) {
            DB::table('relasi_quiz')
                ->where('bank_soal_id', $bankSoalId)
                ->update(['question_id' => $questionId]);
        }

        // Step 4: Update jawaban_peserta
        Schema::table('jawaban_peserta', function (Blueprint $table) {
            $table->foreignId('question_id')->nullable()->after('bank_soal_id')->constrained('questions')->onDelete('cascade');
            $table->foreignId('question_option_id')->nullable()->after('opsi_jawaban_id')->constrained('question_options')->onDelete('set null');
        });

        // Mapping opsi jawaban ke question options
        $opsiJawaban = DB::table('opsi_jawaban')->get();
        foreach ($opsiJawaban as $opsi) {
            if (isset($mappingBankSoalToQuestion[$opsi->bank_soal_id])) {
                $questionId = $mappingBankSoalToQuestion[$opsi->bank_soal_id];
                
                // Cari question_option_id yang sesuai
                $questionOption = DB::table('question_options')
                    ->where('question_id', $questionId)
                    ->where('option_text', $opsi->teks_opsi)
                    ->first();

                if ($questionOption) {
                    // Update jawaban_peserta
                    DB::table('jawaban_peserta')
                        ->where('opsi_jawaban_id', $opsi->id)
                        ->update([
                            'question_id' => $questionId,
                            'question_option_id' => $questionOption->id,
                        ]);
                }
            }
        }

        // Update jawaban yang tidak punya opsi (essay, dll)
        foreach ($mappingBankSoalToQuestion as $bankSoalId => $questionId) {
            DB::table('jawaban_peserta')
                ->where('bank_soal_id', $bankSoalId)
                ->whereNull('question_id')
                ->update(['question_id' => $questionId]);
        }

        // Step 5: Simpan mapping di tabel temporary untuk rollback
        Schema::create('migration_mapping_bank_soal', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('bank_soal_id');
            $table->unsignedBigInteger('question_id');
            $table->timestamps();
        });

        foreach ($mappingBankSoalToQuestion as $bankSoalId => $questionId) {
            DB::table('migration_mapping_bank_soal')->insert([
                'bank_soal_id' => $bankSoalId,
                'question_id' => $questionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore mapping
        $mappings = DB::table('migration_mapping_bank_soal')->get();
        
        foreach ($mappings as $mapping) {
            // Restore relasi_quiz
            DB::table('relasi_quiz')
                ->where('question_id', $mapping->question_id)
                ->update(['question_id' => null]);

            // Restore jawaban_peserta
            DB::table('jawaban_peserta')
                ->where('question_id', $mapping->question_id)
                ->update(['question_id' => null, 'question_option_id' => null]);
        }

        // Drop kolom baru
        Schema::table('jawaban_peserta', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropForeign(['question_option_id']);
            $table->dropColumn(['question_id', 'question_option_id']);
        });

        Schema::table('relasi_quiz', function (Blueprint $table) {
            $table->dropForeign(['question_id']);
            $table->dropColumn('question_id');
        });

        // Hapus questions dan question_options yang dimigrasikan
        $questionIds = $mappings->pluck('question_id');
        DB::table('question_options')->whereIn('question_id', $questionIds)->delete();
        DB::table('questions')->whereIn('id', $questionIds)->delete();

        // Hapus Question Bank default
        DB::table('question_banks')->where('title', 'Bank Soal Lama (Migrasi)')->delete();

        // Drop mapping table
        Schema::dropIfExists('migration_mapping_bank_soal');
    }
};
