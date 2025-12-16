<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Make class_session_id nullable so attendance can be created via materi_id
     */
    public function up(): void
    {
        // For PostgreSQL, we need to use raw SQL to alter column nullable
        DB::statement('ALTER TABLE attendances ALTER COLUMN class_session_id DROP NOT NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Note: This might fail if there are null values
        DB::statement('ALTER TABLE attendances ALTER COLUMN class_session_id SET NOT NULL');
    }
};
