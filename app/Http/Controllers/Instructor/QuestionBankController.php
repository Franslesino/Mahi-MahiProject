<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\QuestionBank;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QuestionBankController extends Controller
{
    /**
     * Display a listing of question banks
     */
    public function index(Request $request)
    {
        $banksQuery = QuestionBank::where(function ($query) {
            $query->where('created_by', Auth::id())
                ->orWhere('is_public', true);
        })
            ->withCount('questions')
            ->latest();
        if (Schema::hasColumn('question_banks', 'is_internal')) {
            $banksQuery->where('is_internal', false);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $banksQuery->where(function ($query) use ($search) {
                $query->where('title', 'ilike', "%{$search}%")
                    ->orWhere('description', 'ilike', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $banksQuery->where('category', $request->category);
        }

        $banks = $banksQuery->paginate(12);

        // Get all unique categories for filter
        $categories = $this->getAvailableCategories();

        return view('instructor.question-banks.index', compact('banks', 'categories'));
    }

    /**
     * Show the form for creating a new question bank
     */
    public function create()
    {
        $categories = $this->getAvailableCategories();

        return view('instructor.question-banks.create', compact('categories'));
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
            ->route('instructor.question-banks.show', $bank)
            ->with('success', 'Bank soal berhasil dibuat!');
    }

    /**
     * Display the specified question bank
     */
    public function show(QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Check authorization
        if ($questionBank->created_by !== Auth::id() && !$questionBank->is_public) {
            abort(403, 'Anda tidak memiliki akses ke bank soal ini.');
        }

        $questionBank->load(['questions.options', 'creator']);

        return view('instructor.question-banks.show', compact('questionBank'));
    }

    /**
     * Show the form for editing the question bank
     */
    public function edit(QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Check authorization
        if ($questionBank->created_by !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit bank soal ini.');
        }

        $categories = $this->getAvailableCategories();

        return view('instructor.question-banks.edit', compact('questionBank', 'categories'));
    }

    /**
     * Update the specified question bank
     */
    public function update(Request $request, QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Check authorization
        if ($questionBank->created_by !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit bank soal ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'nullable|string|max:100',
            'is_public' => 'boolean',
        ]);

        $validated['is_public'] = $request->has('is_public');

        $questionBank->update($validated);

        return redirect()
            ->route('instructor.question-banks.show', $questionBank)
            ->with('success', 'Bank soal berhasil diperbarui!');
    }

    /**
     * Remove the specified question bank
     */
    public function destroy(QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Check authorization
        if ($questionBank->created_by !== Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus bank soal ini.');
        }

        $questionBank->delete();

        return redirect()
            ->route('instructor.question-banks.index')
            ->with('success', 'Bank soal berhasil dihapus!');
    }

    /**
     * Ambil daftar kategori unik yang bisa dipilih instruktur.
     */
    private function getAvailableCategories()
    {
        $query = QuestionBank::where(function ($query) {
            $query->where('created_by', Auth::id())
                ->orWhere('is_public', true);
        })
            ->whereNotNull('category');

        if (Schema::hasColumn('question_banks', 'is_internal')) {
            $query->where('is_internal', false);
        }

        $defaultCategories = collect([
            'Web Development',
            'Programming',
            'Data Science',
            'UI/UX Design',
            'Product Management',
            'Business & Management',
            'Marketing & Sales',
            'Finance & Accounting',
            'Language Learning',
            'Mathematics',
            'Science & Engineering',
            'Professional Skills',
            'Career Development',
        ]);

        $categories = $query->distinct()->pluck('category');

        return $defaultCategories
            ->merge($categories)
            ->filter()
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Store a new question in the bank
     */
    public function storeQuestion(Request $request, QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Izinkan pemilik atau bank publik
        if ($questionBank->created_by !== Auth::id() && !$questionBank->is_public) {
            abort(403, 'Anda tidak dapat menambah soal ke bank ini.');
        }

        // Jika bukan pilihan ganda, hapus options agar aturan min tidak men-trigger
        if ($request->input('type') !== 'multiple_choice') {
            $request->request->remove('options');
        }

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string',
            'correct_option' => 'required_if:type,multiple_choice,true_false',
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

            // Handle options based on type
            if ($validated['type'] === 'multiple_choice') {
                $options = array_values($validated['options']);
                foreach ($options as $idx => $text) {
                    $order = $idx + 1;
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct' => ((string) $validated['correct_option'] === (string) $order),
                        'order' => $order,
                    ]);
                }
            } elseif ($validated['type'] === 'true_false') {
                $correct = $validated['correct_option'] === 'true';
                $question->options()->createMany([
                    [
                        'option_text' => 'Benar',
                        'is_correct' => $correct,
                        'order' => 1,
                    ],
                    [
                        'option_text' => 'Salah',
                        'is_correct' => !$correct,
                        'order' => 2,
                    ],
                ]);
            } elseif ($validated['type'] === 'short_answer') {
                $question->update(['correct_answer' => $validated['correct_answer'] ?? '']);
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
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Izinkan pemilik atau bank publik untuk bank ini
        if (($questionBank->created_by !== Auth::id() && !$questionBank->is_public) || $question->question_bank_id !== $questionBank->id) {
            abort(403, 'Anda tidak dapat menghapus soal ini.');
        }

        // ✅ CEK APAKAH SOAL SEDANG DIGUNAKAN (BUG-006 FIX)
        // Check if question is used in any assignment
        $isUsedInAssignment = DB::table('assignment_questions')
            ->where('question_id', $question->id)
            ->exists();

        if ($isUsedInAssignment) {
            return back()->with('error', 'Soal tidak dapat dihapus karena sedang digunakan dalam assignment.');
        }

        // Check if question is used in any quiz (relasi_quiz table)
        $isUsedInQuiz = DB::table('relasi_quiz')
            ->where('question_id', $question->id)
            ->exists();

        if ($isUsedInQuiz) {
            return back()->with('error', 'Soal tidak dapat dihapus karena sedang digunakan dalam quiz.');
        }

        // Check if question has any student answers
        $hasAnswers = DB::table('jawaban_peserta')
            ->where('question_id', $question->id)
            ->exists();

        if ($hasAnswers) {
            return back()->with('error', 'Soal tidak dapat dihapus karena sudah ada jawaban dari peserta.');
        }

        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }

    /**
     * Show create question form
     */
    public function createQuestion(QuestionBank $questionBank)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Izinkan pemilik atau bank publik
        if ($questionBank->created_by !== Auth::id() && !$questionBank->is_public) {
            abort(403, 'Anda tidak dapat menambahkan soal ke bank ini.');
        }

        return view('instructor.question-banks.create-question', compact('questionBank'));
    }

    /**
     * Show edit question form
     */
    public function editQuestion(QuestionBank $questionBank, Question $question)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        if (($questionBank->created_by !== Auth::id() && !$questionBank->is_public) || $question->question_bank_id !== $questionBank->id) {
            abort(403, 'Anda tidak dapat mengedit soal ini.');
        }

        $question->load('options');

        return view('instructor.question-banks.edit-question', compact('questionBank', 'question'));
    }

    /**
     * Update an existing question
     */
    public function updateQuestion(Request $request, QuestionBank $questionBank, Question $question)
    {
        if ($questionBank->is_internal) {
            abort(404);
        }
        if (($questionBank->created_by !== Auth::id() && !$questionBank->is_public) || $question->question_bank_id !== $questionBank->id) {
            abort(403, 'Anda tidak dapat mengedit soal ini.');
        }

        if ($request->input('type') !== 'multiple_choice') {
            $request->request->remove('options');
        }

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,short_answer',
            'question_text' => 'required|string',
            'explanation' => 'nullable|string',
            'points' => 'required|integer|min:1',
            'correct_answer' => 'nullable|string',
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*' => 'required_if:type,multiple_choice|string',
            'correct_option' => 'required_if:type,multiple_choice,true_false',
        ]);

        DB::beginTransaction();
        try {
            $question->update([
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'correct_answer' => $validated['type'] === 'short_answer'
                    ? ($validated['correct_answer'] ?? '')
                    : null,
            ]);

            // Reset options
            $question->options()->delete();

            if ($validated['type'] === 'multiple_choice') {
                $options = array_values($validated['options']);
                foreach ($options as $idx => $text) {
                    $order = $idx + 1;
                    $question->options()->create([
                        'option_text' => $text,
                        'is_correct' => ((string) $validated['correct_option'] === (string) $order),
                        'order' => $order,
                    ]);
                }
            } elseif ($validated['type'] === 'true_false') {
                $correct = $validated['correct_option'] === 'true';
                $question->options()->createMany([
                    [
                        'option_text' => 'Benar',
                        'is_correct' => $correct,
                        'order' => 1,
                    ],
                    [
                        'option_text' => 'Salah',
                        'is_correct' => !$correct,
                        'order' => 2,
                    ],
                ]);
            } elseif ($validated['type'] === 'essay') {
                // no options, clear correct answer
                $question->update(['correct_answer' => null]);
            }

            DB::commit();

            return redirect()
                ->route('instructor.question-banks.show', $questionBank)
                ->with('success', 'Soal berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal memperbarui soal: ' . $e->getMessage());
        }
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

        $callback = function () use ($columns) {
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
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Izinkan pemilik atau bank publik
        if ($questionBank->created_by !== Auth::id() && !$questionBank->is_public) {
            abort(403);
        }

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
                        'points' => (int) $points,
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
                                    'is_correct' => ($i - 2) == (int) ($row[8] ?? 0),
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
                ->route('instructor.question-banks.show', $questionBank)
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
        if ($questionBank->is_internal) {
            abort(404);
        }
        // Check authorization
        if ($questionBank->created_by !== Auth::id() && !$questionBank->is_public) {
            abort(403);
        }

        $filename = 'soal_' . str_replace(' ', '_', $questionBank->title) . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($questionBank) {
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
                    $options = $question->options->sortBy('order')->values();
                    for ($i = 0; $i < 5; $i++) {
                        $row[] = isset($options[$i]) ? $options[$i]->option_text : '';
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
