<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk Modul/Section Kursus
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_collapsed')->default(false);
            $table->timestamps();

            // Foreign key
            $table->foreign('course_id')
                  ->references('id')
                  ->on('kursus')
                  ->onDelete('cascade');

            // Index untuk performa
            $table->index('course_id');
            $table->index(['course_id', 'order']);
        });

        // Update tabel materi
        Schema::table('materi', function (Blueprint $table) {
            if (!Schema::hasColumn('materi', 'section_id')) {
                $table->unsignedBigInteger('section_id')->nullable()->after('kursus_id');
                $table->foreign('section_id')->references('id')->on('course_sections')->onDelete('cascade');
            }
            if (!Schema::hasColumn('materi', 'description')) {
                $table->text('description')->nullable()->after('judul');
            }
            if (!Schema::hasColumn('materi', 'duration')) {
                $table->integer('duration')->nullable()->after('type');
            }
            if (!Schema::hasColumn('materi', 'file_url')) {
                $table->string('file_url', 500)->nullable()->after('url_konten');
            }
            if (!Schema::hasColumn('materi', 'content')) {
                $table->longText('content')->nullable()->after('isi');
            }
            if (!Schema::hasColumn('materi', 'is_preview')) {
                $table->boolean('is_preview')->default(false)->after('status_terkunci');
            }
            if (!Schema::hasColumn('materi', 'status')) {
                $table->enum('status', ['published', 'draft'])->default('draft')->after('is_preview');
            }
        });
    }

    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            if (Schema::hasColumn('materi', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn([
                    'section_id', 'description', 'duration', 
                    'file_url', 'content', 'is_preview', 'status'
                ]);
            }
        });
        Schema::dropIfExists('course_sections');
    }
};