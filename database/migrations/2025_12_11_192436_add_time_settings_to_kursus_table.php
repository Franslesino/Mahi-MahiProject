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
            $table->integer('access_duration_days')->nullable()->comment('Lama akses kursus setelah pembelian (hari)')->after('require_final_quiz');
            $table->integer('course_duration_minutes')->nullable()->comment('Estimasi durasi pengerjaan kursus (menit)')->after('access_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropColumn(['access_duration_days', 'course_duration_minutes']);
        });
    }
};
