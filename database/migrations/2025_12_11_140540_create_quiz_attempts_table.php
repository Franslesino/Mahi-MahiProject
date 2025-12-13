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
        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('quiz_id')->constrained('quiz')->onDelete('cascade');
            $table->foreignId('kursus_id')->constrained('kursus')->onDelete('cascade');
            $table->integer('attempt_number')->default(1)->comment('Percobaan ke berapa');
            $table->decimal('score', 5, 2)->nullable()->comment('Nilai yang didapat');
            $table->boolean('is_passed')->default(false)->comment('Apakah lulus');
            $table->timestamp('started_at')->nullable()->comment('Waktu mulai');
            $table->timestamp('completed_at')->nullable()->comment('Waktu selesai');
            $table->timestamps();
            
            // Index untuk query yang sering digunakan
            $table->index(['user_id', 'quiz_id', 'kursus_id']);
            $table->index(['quiz_id', 'user_id', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
    }
};
