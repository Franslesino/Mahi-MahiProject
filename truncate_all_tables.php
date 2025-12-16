<?php

/**
 * Script untuk menghapus semua data dari tabel database Supabase
 * TANPA menghapus struktur tabel
 * 
 * Jalankan dengan: php truncate_all_tables.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "===========================================\n";
echo "TRUNCATE ALL TABLES - UpGreenius Database\n";
echo "===========================================\n\n";

// Disable foreign key checks untuk menghindari error constraint
DB::statement('SET session_replication_role = replica;');

// Daftar tabel yang akan di-truncate (urutan penting karena foreign key)
$tables = [
    // Tabel yang dependent dulu (child tables)
    'jawaban_peserta',
    'quiz_attempts',
    'quiz_questions',
    'quiz',
    'material_completions',
    'sertifikats',
    'submissions',
    'assignment_questions',
    'assignments',
    'question_options',
    'questions',
    'question_banks',
    'notifications',
    'transactions',
    'vouchers',
    'enrollments',
    'materi',
    'kursus',
    'promo_banners',

    // System tables (optional - uncomment jika ingin reset)
    'sessions',
    'cache',
    'cache_locks',
    'jobs',
    'job_batches',
    'failed_jobs',
    'password_reset_tokens',

    // Users table terakhir
    'users',
];

$truncatedCount = 0;
$skippedCount = 0;
$errorCount = 0;

foreach ($tables as $table) {
    try {
        if (Schema::hasTable($table)) {
            $rowCount = DB::table($table)->count();
            DB::table($table)->truncate();
            echo "✓ Truncated: {$table} ({$rowCount} rows deleted)\n";
            $truncatedCount++;
        } else {
            echo "⊘ Skipped (not exists): {$table}\n";
            $skippedCount++;
        }
    } catch (Exception $e) {
        echo "✗ Error on {$table}: " . $e->getMessage() . "\n";
        $errorCount++;
    }
}

// Re-enable foreign key checks
DB::statement('SET session_replication_role = DEFAULT;');

echo "\n===========================================\n";
echo "SUMMARY\n";
echo "===========================================\n";
echo "Truncated: {$truncatedCount} tables\n";
echo "Skipped: {$skippedCount} tables\n";
echo "Errors: {$errorCount} tables\n";
echo "\nBismillah! Database siap untuk testing ulang.\n";
