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
        Schema::table('jawaban_peserta', function (Blueprint $table) {
            // Rename question_option_id to selected_option_id for consistency
            $table->renameColumn('question_option_id', 'selected_option_id');
            
            // Rename old columns to new naming convention
            $table->renameColumn('nilai_tercapai', 'points_earned');
            $table->renameColumn('tanggal_submit', 'submitted_at');
            $table->renameColumn('opsi_dipilih', 'answer_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_peserta', function (Blueprint $table) {
            // Reverse the changes
            $table->renameColumn('selected_option_id', 'question_option_id');
            $table->renameColumn('points_earned', 'nilai_tercapai');
            $table->renameColumn('submitted_at', 'tanggal_submit');
            $table->renameColumn('answer_text', 'opsi_dipilih');
        });
    }
};
