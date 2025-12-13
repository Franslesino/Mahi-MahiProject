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
        Schema::create('quiz', function (Blueprint $table) {
            $table->id();
            $table->string('judul_quiz');
            $table->decimal('passing_grade', 5, 2)->default(70.00);
            $table->integer('kesempatan_mengerjakan')->default(1);
            $table->integer('durasi_quiz')->nullable()->comment('Durasi dalam menit');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->boolean('is_final_quiz')->default(false);
            $table->integer('minimum_score')->nullable()->comment('Minimum score to pass final quiz (0-100)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz');
    }
};
