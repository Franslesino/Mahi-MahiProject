<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Sync the 'mode' field based on 'metode' field for existing courses
     * that may have inconsistent values.
     */
    public function up(): void
    {
        // Sync mode from metode (if metode is set but mode might be wrong)
        // 'metode' uses lowercase: online, offline, hybrid
        // 'mode' uses capitalized: Online, Offline, Hybrid

        DB::statement("
            UPDATE kursus 
            SET mode = CASE 
                WHEN metode = 'online' THEN 'Online'
                WHEN metode = 'offline' THEN 'Offline'
                WHEN metode = 'hybrid' THEN 'Hybrid'
                ELSE mode
            END
            WHERE metode IS NOT NULL
        ");

        // For courses where metode is null but mode is null, set default to 'Online'
        DB::statement("
            UPDATE kursus 
            SET mode = 'Online' 
            WHERE mode IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to reverse this data sync
    }
};
