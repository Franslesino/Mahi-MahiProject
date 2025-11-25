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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->onDelete('cascade');
            $table->enum('type', ['multiple_choice', 'true_false', 'essay', 'short_answer'])->default('multiple_choice');
            $table->text('question_text');
            $table->text('explanation')->nullable(); // Penjelasan jawaban
            $table->integer('points')->default(1); // Bobot nilai
            $table->integer('order')->default(0); // Urutan soal
            $table->text('correct_answer')->nullable(); // Untuk essay/short answer
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
