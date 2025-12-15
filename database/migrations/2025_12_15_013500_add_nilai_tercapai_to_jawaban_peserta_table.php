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
            if (!Schema::hasColumn('jawaban_peserta', 'nilai_tercapai')) {
                // Simpan status benar/salah: 1 = benar, 0 = salah, null = tidak dinilai/essay
                $table->decimal('nilai_tercapai', 5, 2)->nullable()->after('points_earned');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jawaban_peserta', function (Blueprint $table) {
            if (Schema::hasColumn('jawaban_peserta', 'nilai_tercapai')) {
                $table->dropColumn('nilai_tercapai');
            }
        });
    }
};
