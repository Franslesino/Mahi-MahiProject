<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find materials with type PDF
$pdfs = App\Models\Materi::where('type', 'pdf')->get(['id', 'judul', 'file_url', 'url_konten', 'kursus_id']);

echo "=== PDF Materials in Database ===\n";
foreach ($pdfs as $pdf) {
    echo "ID: {$pdf->id}\n";
    echo "Title: {$pdf->judul}\n";
    echo "file_url: " . ($pdf->file_url ?: "(empty)") . "\n";
    echo "url_konten: " . ($pdf->url_konten ?: "(empty)") . "\n";
    echo "file_url_full: " . ($pdf->file_url_full ?: "(empty)") . "\n";
    echo "---\n";
}
