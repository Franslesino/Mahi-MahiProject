<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== HASIL MIGRASI KONSOLIDASI DATABASE ===\n\n";

// Check Question Banks
$totalBanks = \App\Models\QuestionBank::count();
echo "📁 Total Question Banks: {$totalBanks}\n";

if ($totalBanks > 0) {
    $migratedBank = \App\Models\QuestionBank::where('title', 'LIKE', '%Migrasi%')->first();
    if ($migratedBank) {
        echo "   ✅ Bank 'Bank Soal Lama (Migrasi)' ditemukan (ID: {$migratedBank->id})\n";
        echo "   📝 Total soal di bank migrasi: " . $migratedBank->questions()->count() . "\n";
    }
}

echo "\n";

// Check Questions
$totalQuestions = \App\Models\Question::count();
echo "❓ Total Questions: {$totalQuestions}\n";

if ($totalQuestions > 0) {
    $sampleQuestion = \App\Models\Question::with('options')->first();
    echo "   Sample question:\n";
    echo "   - ID: {$sampleQuestion->id}\n";
    echo "   - Text: " . substr($sampleQuestion->question_text, 0, 50) . "...\n";
    echo "   - Type: {$sampleQuestion->type}\n";
    echo "   - Options: " . $sampleQuestion->options->count() . "\n";
}

echo "\n";

// Check Question Options
$totalOptions = \App\Models\QuestionOption::count();
echo "✔️  Total Question Options: {$totalOptions}\n\n";

// Check relasi_quiz
$relasiWithQuestionId = \Illuminate\Support\Facades\DB::table('relasi_quiz')
    ->whereNotNull('question_id')
    ->count();
echo "🔗 Relasi Quiz dengan question_id: {$relasiWithQuestionId}\n";

// Check if old tables still exist
try {
    \Illuminate\Support\Facades\DB::table('bank_soal')->count();
    echo "⚠️  WARNING: Tabel 'bank_soal' masih ada!\n";
} catch (\Exception $e) {
    echo "✅ Tabel 'bank_soal' sudah dihapus\n";
}

try {
    \Illuminate\Support\Facades\DB::table('opsi_jawaban')->count();
    echo "⚠️  WARNING: Tabel 'opsi_jawaban' masih ada!\n";
} catch (\Exception $e) {
    echo "✅ Tabel 'opsi_jawaban' sudah dihapus\n";
}

echo "\n=== MIGRASI SELESAI ===\n";
