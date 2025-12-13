<?php
// Test API Bank Soal
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Simulate authenticated request
$request = Illuminate\Http\Request::create('/instructor/bank-soal/api', 'GET');

// Mock authentication (you need to set actual user)
// For testing, just output the result

use App\Models\BankSoal;

echo "Total Bank Soal: " . BankSoal::count() . "\n\n";

$data = BankSoal::with('opsiJawaban')->get();

echo "JSON Output:\n";
echo json_encode($data, JSON_PRETTY_PRINT);
