<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan kolom baru ke tabel kursus yang sudah ada
        Schema::table('kursus', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada, jika belum baru ditambahkan
            if (!Schema::hasColumn('kursus', 'instructor_id')) {
                $table->foreignId('instructor_id')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('kursus', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            }
            if (!Schema::hasColumn('kursus', 'image')) {
                $table->string('image', 500)->nullable();
            }
            if (!Schema::hasColumn('kursus', 'mode')) {
                $table->enum('mode', ['Online', 'Offline', 'Hybrid'])->default('Online');
            }
            if (!Schema::hasColumn('kursus', 'discount_price')) {
                $table->decimal('discount_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('kursus', 'rating')) {
                $table->decimal('rating', 2, 1)->default(0);
            }
            if (!Schema::hasColumn('kursus', 'learning')) {
                $table->text('learning')->nullable();
            }
            if (!Schema::hasColumn('kursus', 'badge')) {
                $table->string('badge', 50)->nullable();
            }
            if (!Schema::hasColumn('kursus', 'badge_color')) {
                $table->string('badge_color', 50)->default('blue');
            }
            if (!Schema::hasColumn('kursus', 'videos')) {
                $table->integer('videos')->default(0);
            }
        });
    }

    public function down(): void
    {
        Schema::table('kursus', function (Blueprint $table) {
            $table->dropForeign(['instructor_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn([
                'instructor_id',
                'created_by',
                'image',
                'mode',
                'discount_price',
                'rating',
                'learning',
                'badge',
                'badge_color',
                'videos'
            ]);
        });
    }
};