<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\BankSoal;
use App\Models\OpsiJawaban;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

/**
 * Controller untuk fitur bank soal.
 */
class BankSoalController extends Controller
{
    /**
     * Display a listing of bank soal
     */
    public function index()
    {
        // Redirect to Question Banks (new system)
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Bank Soal lama telah digabung ke Question Banks. Silakan gunakan Question Banks untuk mengelola soal.');
    }

    /**
     * API: Get all bank soal for import selection
     */
    public function api()
    {
        // Only admin and instructor can access
        if (!in_array(Auth::user()->role, ['admin', 'instructor'])) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $result = [];

        // Get from question_banks (new system only)
        $questionBanks = \App\Models\QuestionBank::with(['questions.options'])
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('is_public', true);
            })
            ->get();

        foreach ($questionBanks as $bank) {
            foreach ($bank->questions as $question) {
                $result[] = [
                    'id' => 'qb_' . $question->id,
                    'source' => 'question_bank',
                    'source_id' => $question->id,
                    'bank_name' => $bank->title,
                    'pertanyaan' => $question->question_text,
                    'tipe_soal' => $question->type,
                    'kategori' => $bank->description ?? 'Umum',
                    'options' => $question->options->map(function($opt) {
                        return [
                            'teks_opsi' => $opt->option_text,
                            'is_benar' => $opt->is_correct,
                        ];
                    })->toArray(),
                    'created_at' => $question->created_at,
                ];
            }
        }

        // Sort by created_at descending
        usort($result, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json($result);
    }

    /**
     * Show the form for creating a new question
     */
    public function create()
    {
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Silakan gunakan Question Banks untuk membuat soal baru.');
    }

    /**
     * Store a newly created question
     */
    public function store(Request $request)
    {
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Silakan gunakan Question Banks untuk membuat soal baru.');
    }

    /**
     * Show the form for editing the specified question
     */
    public function edit($id)
    {
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Silakan gunakan Question Banks untuk mengedit soal.');
    }

    /**
     * Update the specified question
     */
    public function update(Request $request, $id)
    {
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Silakan gunakan Question Banks untuk mengedit soal.');
    }

    /**
     * Remove the specified question
     */
    public function destroy($id)
    {
        return redirect()->route('instructor.question-banks.index')
            ->with('info', 'Silakan gunakan Question Banks untuk menghapus soal.');
    }
}
