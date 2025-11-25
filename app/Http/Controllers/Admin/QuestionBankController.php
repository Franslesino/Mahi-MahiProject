<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of question banks
     */
    public function index()
    {
        $banks = QuestionBank::withCount('questions')
            ->with('creator')
            ->latest()
            ->paginate(12);

        return view('admin.question-banks.index', compact('banks'));
    }

    /**
     * Show the form for creating a new question bank
     */
    public function create()
    {
        return view('admin.question-banks.create');
    }

    /**
     * Store a newly created question bank
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_public' => 'boolean',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['is_public'] = $request->has('is_public');

        $bank = QuestionBank::create($validated);

        return redirect()
            ->route('admin.question-banks.show', $bank)
            ->with('success', 'Bank soal berhasil dibuat!');
    }

    /**
     * Display the specified question bank
     */
    public function show(QuestionBank $questionBank)
    {
        $questionBank->load('questions.options', 'creator');

        return view('admin.question-banks.show', compact('questionBank'));
    }

    /**
     * Show the form for editing the question bank
     */
    public function edit(QuestionBank $questionBank)
    {
        return view('admin.question-banks.edit', compact('questionBank'));
    }

    /**
     * Update the specified question bank
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_public' => 'boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $questionBank->update($validated);

        return redirect()
            ->route('admin.question-banks.show', $questionBank)
            ->with('success', 'Bank soal berhasil diperbarui!');
    }

    /**
     * Remove the specified question bank
     */
    public function destroy(QuestionBank $questionBank)
    {
        $questionBank->delete();

        return redirect()
            ->route('admin.question-banks.index')
            ->with('success', 'Bank soal berhasil dihapus!');
    }

    /**
     * Store a new question in the bank
     */
    public function storeQuestion(Request $request, QuestionBank $questionBank)
    {
        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,essay,short_answer',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'nullable|array',
            'options.*.text' => 'required_with:options|string',
            'options.*.is_correct' => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Get max order
            $maxOrder = $questionBank->questions()->max('order') ?? 0;

            // Create question
            $question = $questionBank->questions()->create([
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'order' => $maxOrder + 1,
                'correct_answer' => $validated['correct_answer'] ?? null,
            ]);

            // Create options if multiple choice or true/false
            if (in_array($validated['type'], ['multiple_choice', 'true_false']) && isset($validated['options'])) {
                foreach ($validated['options'] as $index => $optionData) {
                    $question->options()->create([
                        'option_text' => $optionData['text'],
                        'is_correct' => $optionData['is_correct'] ?? false,
                        'order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Soal berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menambahkan soal: ' . $e->getMessage());
        }
    }

    /**
     * Delete a question
     */
    public function destroyQuestion(QuestionBank $questionBank, Question $question)
    {
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Show create question form
     */
    public function createQuestion(QuestionBank $questionBank)
    {
        return view('admin.question-banks.create-question', compact('questionBank'));
    }

    /**
     * Export template for bulk import
     */
    public function exportTemplate()
    {
        $filename = 'template_soal_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $columns = [
            'type',
            'question_text',
            'points',
            'option_1',
            'option_2',
            'option_3',
            'option_4',
            'option_5',
            'correct_option',
            'explanation'
        ];

        $callback = function() use ($columns) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, $columns);
            
            // Example rows
            fputcsv($file, [
                'multiple_choice',
                'Apa ibu kota Indonesia?',
                '1',
                'Jakarta',
                'Bandung',
                'Surabaya',
                'Medan',
                '',
                '1',
                'Jakarta adalah ibu kota Indonesia'
            ]);
            
            fputcsv($file, [
                'true_false',
                'Bumi itu bulat',
                '1',
                'Benar',
                'Salah',
                '',
                '',
                '',
                'true',
                'Bumi berbentuk bulat (spheroid)'
            ]);
            
            fputcsv($file, [
                'short_answer',
                'Siapa presiden pertama Indonesia?',
                '2',
                '',
                '',
                '',
                '',
                '',
                'Soekarno',
                'Ir. Soekarno adalah presiden pertama RI'
            ]);
            
            fputcsv($file, [
                'essay',
                'Jelaskan dampak revolusi industri terhadap masyarakat',
                '5',
                '',
                '',
                '',
                '',
                '',
                '',
                'Jawaban akan dinilai manual oleh instruktur'
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Import questions from file
     */
    public function importQuestions(Request $request, QuestionBank $questionBank)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,xlsx,xls|max:2048'
        ]);

        try {
            DB::beginTransaction();

            $file = $request->file('file');
            $path = $file->getRealPath();
            $data = array_map('str_getcsv', file($path));
            
            // Remove header
            $header = array_shift($data);
            
            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                try {
                    // Skip empty rows
                    if (empty(array_filter($row))) {
                        continue;
                    }

                    $type = $row[0] ?? '';
                    $questionText = $row[1] ?? '';
                    $points = $row[2] ?? 1;
                    
                    if (empty($type) || empty($questionText)) {
                        $errors[] = "Baris " . ($index + 2) . ": Tipe atau pertanyaan kosong";
                        continue;
                    }

                    // Create question
                    $question = Question::create([
                        'question_bank_id' => $questionBank->id,
                        'type' => $type,
                        'question_text' => $questionText,
                        'points' => (int)$points,
                        'explanation' => $row[9] ?? null,
                        'order' => $questionBank->questions()->max('order') + 1,
                    ]);

                    // Handle options based on type
                    if ($type === 'multiple_choice') {
                        $options = [];
                        for ($i = 3; $i <= 7; $i++) {
                            if (!empty($row[$i])) {
                                $options[] = [
                                    'question_id' => $question->id,
                                    'option_text' => $row[$i],
                                    'is_correct' => ($i - 2) == (int)($row[8] ?? 0),
                                    'order' => $i - 2,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ];
                            }
                        }
                        QuestionOption::insert($options);
                    } elseif ($type === 'true_false') {
                        $correctAnswer = strtolower($row[8] ?? '');
                        QuestionOption::insert([
                            [
                                'question_id' => $question->id,
                                'option_text' => 'Benar',
                                'is_correct' => $correctAnswer === 'true' || $correctAnswer === 'benar',
                                'order' => 1,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ],
                            [
                                'question_id' => $question->id,
                                'option_text' => 'Salah',
                                'is_correct' => $correctAnswer === 'false' || $correctAnswer === 'salah',
                                'order' => 2,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        ]);
                    } elseif ($type === 'short_answer') {
                        $question->update([
                            'correct_answer' => $row[8] ?? ''
                        ]);
                    }

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = "Baris " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "$imported soal berhasil diimport!";
            if (!empty($errors)) {
                $message .= " Dengan " . count($errors) . " error.";
            }

            return redirect()
                ->route('admin.question-banks.show', $questionBank)
                ->with('success', $message)
                ->with('import_errors', $errors);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal import soal: ' . $e->getMessage());
        }
    }

    /**
     * Export questions to CSV
     */
    public function exportQuestions(QuestionBank $questionBank)
    {
        $filename = 'soal_' . str_replace(' ', '_', $questionBank->title) . '_' . date('Y-m-d') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($questionBank) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, [
                'type',
                'question_text',
                'points',
                'option_1',
                'option_2',
                'option_3',
                'option_4',
                'option_5',
                'correct_option',
                'explanation'
            ]);
            
            // Questions
            $questions = $questionBank->questions()->with('options')->get();
            
            foreach ($questions as $question) {
                $row = [
                    $question->type,
                    $question->question_text,
                    $question->points,
                ];

                // Add options
                if ($question->type === 'multiple_choice' || $question->type === 'true_false') {
                    $options = $question->options->sortBy('order');
                    for ($i = 0; $i < 5; $i++) {
                        $row[] = $options[$i]->option_text ?? '';
                    }
                    
                    // Correct option
                    $correctOption = $options->where('is_correct', true)->first();
                    $row[] = $correctOption ? $correctOption->order : '';
                } else {
                    // Empty options for non-multiple choice
                    $row = array_merge($row, ['', '', '', '', '']);
                    
                    if ($question->type === 'short_answer') {
                        $row[] = $question->correct_answer ?? '';
                    } else {
                        $row[] = '';
                    }
                }

                $row[] = $question->explanation ?? '';

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
