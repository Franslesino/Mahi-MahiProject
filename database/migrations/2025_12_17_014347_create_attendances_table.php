<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_session_id')->constrained('class_sessions')->onDelete('cascade');
            $table->foreignId('enrollment_id')->constrained('enrollments')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Status kehadiran
            $table->enum('status', ['hadir', 'tidak_hadir', 'izin', 'sakit', 'terlambat'])->default('tidak_hadir');

            // Waktu check-in (diisi instruktur)
            $table->timestamp('check_in_time')->nullable();

            // Catatan dari instruktur
            $table->text('catatan')->nullable();

            // Siapa yang mengupdate (instruktur)
            $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');

            $table->timestamps();

            // Unique constraint: satu user hanya bisa punya satu attendance per session
            $table->unique(['class_session_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
