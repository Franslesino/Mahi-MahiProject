<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== MENGHAPUS BANK SOAL LAMA (MIGRASI) ===\n\n";

// Find the migrated bank
$migratedBank = \App\Models\QuestionBank::where('title', 'LIKE', '%Migrasi%')->first();

if (!$migratedBank) {
    echo "❌ Bank Soal Lama (Migrasi) tidak ditemukan.\n";
    exit;
}

echo "📁 Ditemukan: {$migratedBank->title} (ID: {$migratedBank->id})\n";
echo "📝 Total soal: " . $migratedBank->questions()->count() . "\n\n";

// Delete the bank (cascade akan menghapus questions dan options)
try {
    $questionCount = $migratedBank->questions()->count();
    $migratedBank->delete();
    
    echo "✅ Bank Soal Lama (Migrasi) berhasil dihapus!\n";
    echo "✅ {$questionCount} soal ikut terhapus (cascade delete)\n";
    echo "\n=== SELESAI ===\n";
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
