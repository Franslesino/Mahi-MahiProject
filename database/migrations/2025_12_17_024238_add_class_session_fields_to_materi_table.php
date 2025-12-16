<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * Adds fields to materi table to support class_session type (offline/hybrid sessions)
     */
    public function up(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            // Class session specific fields
            $table->date('session_date')->nullable()->after('status');
            $table->time('session_start_time')->nullable()->after('session_date');
            $table->time('session_end_time')->nullable()->after('session_start_time');
            $table->string('session_location')->nullable()->after('session_end_time');
            $table->string('session_meeting_link')->nullable()->after('session_location');
            $table->enum('session_type', ['offline', 'online'])->nullable()->after('session_meeting_link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('materi', function (Blueprint $table) {
            $table->dropColumn([
                'session_date',
                'session_start_time',
                'session_end_time',
                'session_location',
                'session_meeting_link',
                'session_type',
            ]);
        });
    }
};
