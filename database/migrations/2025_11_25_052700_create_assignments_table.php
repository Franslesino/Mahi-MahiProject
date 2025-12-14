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
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->foreignId('materi_id')->nullable()->constrained('materi')->onDelete('set null');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['quiz', 'assignment', 'exam'])->default('quiz');
            $table->integer('duration_minutes')->nullable(); // Durasi pengerjaan
            $table->integer('passing_score')->default(70); // Nilai minimal lulus
            $table->timestamp('start_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->boolean('show_results_immediately')->default(true);
            $table->boolean('allow_multiple_attempts')->default(false);
            $table->integer('max_attempts')->nullable();
            $table->boolean('randomize_questions')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignments');
    }
};
