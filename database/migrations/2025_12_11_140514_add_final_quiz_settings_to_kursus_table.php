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
        Schema::table('kursus', function (Blueprint $table) {
            $table->unsignedBigInteger('final_quiz_id')->nullable()->after('instructor_id');
            $table->decimal('min_passing_score', 5, 2)->default(70.00)->after('final_quiz_id')->comment('Nilai minimum untuk lulus final quiz');
            $table->integer('max_quiz_attempts')->default(3)->after('min_passing_score')->comment('Jumlah maksimal percobaan final quiz');
            $table->boolean('require_final_quiz')->default(false)->after('max_quiz_attempts')->comment('Apakah final quiz wajib untuk mendapatkan sertifikat');
            
            $table->foreign('final_quiz_id')->references('id')->on('quiz')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropForeign(['final_quiz_id']);
            $table->dropColumn(['final_quiz_id', 'min_passing_score', 'max_quiz_attempts', 'require_final_quiz']);
        });
    }
};
