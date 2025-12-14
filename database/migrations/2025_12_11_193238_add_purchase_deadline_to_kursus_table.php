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
            // Hapus kolom lama
            $table->dropColumn('course_duration_minutes');
            // Tambah kolom baru
            $table->timestamp('purchase_deadline_date')->nullable()->comment('Batas waktu pembelian kursus')->after('access_duration_days');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropColumn('purchase_deadline_date');
            $table->integer('course_duration_minutes')->nullable()->after('access_duration_days');
        });
    }
};
