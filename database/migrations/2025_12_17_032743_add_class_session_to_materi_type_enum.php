<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Add 'class_session' to the type enum in materi table
     */
    public function up(): void
    {
        // For PostgreSQL, we need to add the new enum value
        DB::statement("ALTER TABLE materi DROP CONSTRAINT IF EXISTS materi_type_check");
        DB::statement("ALTER TABLE materi ADD CONSTRAINT materi_type_check CHECK (type::text = ANY (ARRAY['video'::character varying, 'pdf'::character varying, 'text'::character varying, 'quiz'::character varying, 'class_session'::character varying]::text[]))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove class_session from enum (revert to original)
        DB::statement("ALTER TABLE materi DROP CONSTRAINT IF EXISTS materi_type_check");
        DB::statement("ALTER TABLE materi ADD CONSTRAINT materi_type_check CHECK (type::text = ANY (ARRAY['video'::character varying, 'pdf'::character varying, 'text'::character varying, 'quiz'::character varying]::text[]))");
    }
};
