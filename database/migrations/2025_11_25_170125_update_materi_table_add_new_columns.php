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
        // Update tabel materi untuk menambahkan kolom baru
        Schema::table('materi', function (Blueprint $table) {
            // Tambahkan kolom section_id jika belum ada
            if (!Schema::hasColumn('materi', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('kursus_id');
                $table->foreign('section_id')
                      ->references('id')
                      ->on('course_sections')
                      ->onDelete('cascade');
            }

            // Tambahkan description jika belum ada
            if (!Schema::hasColumn('materi', 'description')) {
                $table->text('description')->nullable()->after('judul');
            }

            // Tambahkan content jika belum ada
            if (!Schema::hasColumn('materi', 'content')) {
                $table->longText('content')->nullable()->after('isi');
            }

            // Rename tipe_materi ke type jika ada tipe_materi
            if (Schema::hasColumn('materi', 'tipe_materi') && !Schema::hasColumn('materi', 'type')) {
                $table->renameColumn('tipe_materi', 'type');
            }
            
            // Atau tambahkan type baru jika tipe_materi tidak ada
            if (!Schema::hasColumn('materi', 'type') && !Schema::hasColumn('materi', 'tipe_materi')) {
                $table->enum('type', ['video', 'pdf', 'text', 'quiz'])->default('video')->after('isi');
            }

            // Tambahkan file_url jika belum ada
            if (!Schema::hasColumn('materi', 'file_url')) {
                $table->string('file_url', 500)->nullable()->after('url_konten');
            }

            // Tambahkan duration jika belum ada
            if (!Schema::hasColumn('materi', 'duration')) {
                $table->integer('duration')->nullable()->after('type')->comment('Duration in minutes');
            }

            // Tambahkan is_preview jika belum ada
            if (!Schema::hasColumn('materi', 'is_preview')) {
                $table->boolean('is_preview')->default(false)->after('status_terkunci');
            }

            // Tambahkan status jika belum ada
            if (!Schema::hasColumn('materi', 'status')) {
                $table->enum('status', ['published', 'draft'])->default('draft')->after('is_preview');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            // Drop foreign key dulu sebelum drop column
            if (Schema::hasColumn('materi', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            }

            // Drop kolom-kolom yang ditambahkan
            $columns = ['description', 'content', 'file_url', 'duration', 'is_preview', 'status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('materi', $column)) {
                    $table->dropColumn($column);
                }
            }

            // Rename type kembali ke tipe_materi jika perlu
            if (Schema::hasColumn('materi', 'type')) {
                $table->renameColumn('type', 'tipe_materi');
            }
        });
    }
};