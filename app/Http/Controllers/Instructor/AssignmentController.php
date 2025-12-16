<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Kursus;
use App\Models\QuestionBank;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments
     */
    public function index()
    {
        $instructorId = Auth::id();
        $assignments = Assignment::whereHas('kursus', function($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId)
                      ->orWhere('pembuat', $instructorId);
            })
            ->with(['kursus', 'materi'])
            ->withCount(['questions', 'submissions'])
            ->latest()
            ->paginate(15);

        return view('instructor.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create(Request $request)
    {
        $instructorId = Auth::id();
        $courses = Kursus::where(function($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId)
                      ->orWhere('pembuat', $instructorId);
            })->get();
        
        $selectedCourse = null;
        $materials = collect();
        
        if ($request->has('course_id')) {
            $selectedCourse = Kursus::find($request->course_id);
            if ($selectedCourse && ($selectedCourse->instructor_id == $instructorId || $selectedCourse->pembuat == $instructorId)) {
                $materials = $selectedCourse->materi;
            }
        }

        return view('instructor.assignments.create', compact('courses', 'selectedCourse', 'materials'));
    }

    /**
     * Store a newly created assignment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'materi_id' => 'nullable|exists:materi,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,assignment,exam',
            'duration_minutes' => 'nullable|integer|min:1',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'show_results_immediately' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'randomize_questions' => 'boolean',
        ]);

        // Verify course ownership
        $course = Kursus::findOrFail($validated['kursus_id']);
        if ($course->instructor_id !== Auth::id() && $course->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }

        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['is_published'] = false;

        $assignment = Assignment::create($validated);

        return redirect()
            ->route('instructor.assignments.edit-questions', $assignment)
            ->with('success', 'Assignment berhasil dibuat! Sekarang tambahkan soal-soal.');
    }

    /**
     * Display the specified assignment
     */
    public function show(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke assignment ini.');
        }

        $assignment->load(['kursus', 'materi', 'questions.options', 'submissions.user']);

        return view('instructor.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the assignment
     */
    public function edit(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit assignment ini.');
        }

        $courses = Kursus::where(function($query) { $instructorId = Auth::id(); $query->where('instructor_id', $instructorId)->orWhere('pembuat', $instructorId); })->get();
        $materials = $assignment->kursus->materi;

        return view('instructor.assignments.edit', compact('assignment', 'courses', 'materials'));
    }

    /**
     * Update the specified assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit assignment ini.');
        }

        $validated = $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'materi_id' => 'nullable|exists:materi,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,assignment,exam',
            'duration_minutes' => 'nullable|integer|min:1',
            'time_limit' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'show_results_immediately' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'randomize_questions' => 'boolean',
        ]);

        // Verify course ownership
        $course = Kursus::findOrFail($validated['kursus_id']);
        if ($course->instructor_id !== Auth::id() && $course->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke kursus ini.');
        }

        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['randomize_questions'] = $request->has('randomize_questions');

        $assignment->update($validated);

        return redirect()
            ->route('instructor.assignments.show', $assignment)
            ->with('success', 'Assignment berhasil diperbarui!');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak dapat menghapus assignment ini.');
        }

        $assignment->delete();

        return redirect()
            ->route('instructor.assignments.index')
            ->with('success', 'Assignment berhasil dihapus!');
    }

    /**
     * Show page to add/manage questions for assignment
     */
    public function editQuestions(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403, 'Anda tidak dapat mengedit assignment ini.');
        }

        $assignment->load(['questions.options']);
        
        // Get available question banks
        $questionBanks = QuestionBank::where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('is_public', true);
            })
            ->with(['questions.options'])
            ->withCount('questions')
            ->get();
        if (Schema::hasColumn('question_banks', 'is_internal')) {
            $questionBanks = $questionBanks->where('is_internal', false);
        }

        $ownedBanks = $questionBanks->where('created_by', Auth::id());

        return view('instructor.assignments.edit-questions', compact('assignment', 'questionBanks', 'ownedBanks'));
    }

    /**
     * Add questions from question bank to assignment
     */
    public function addQuestions(Request $request, Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        DB::beginTransaction();
        try {
            $maxOrder = $assignment->questions()->max('assignment_questions.order') ?? 0;

            foreach ($validated['question_ids'] as $index => $questionId) {
                // Check if question already added
                if (!$assignment->questions()->where('questions.id', $questionId)->exists()) {
                    $question = Question::find($questionId);
                    $assignment->questions()->attach($questionId, [
                        'order' => $maxOrder + $index + 1,
                        'points' => $question->points,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', count($validated['question_ids']) . ' soal berhasil ditambahkan!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan soal: ' . $e->getMessage());
        }
    }

    /**
     * Remove question from assignment
     */
    public function removeQuestion(Assignment $assignment, Question $question)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403);
        }

        $assignment->questions()->detach($question->id);

        return back()->with('success', 'Soal berhasil dihapus dari assignment!');
    }

    /**
     * Quick create quiz (assignment) from course page with optional bank import
     */
    public function quickCreateFromCourse(Request $request, Kursus $course)
    {
        if ($course->instructor_id !== Auth::id() && $course->pembuat !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'randomize_questions' => 'nullable|in:0,1,true,false,on,off',
        ]);

        // Ensure section belongs to course if provided
        if (!empty($validated['section_id']) && !$course->sections()->where('id', $validated['section_id'])->exists()) {
            abort(422, 'Section tidak valid untuk kursus ini.');
        }

        $validated['randomize_questions'] = $request->boolean('randomize_questions');

        DB::beginTransaction();
        try {
            // Create materi placeholder in selected section
            $sectionId = $validated['section_id'] ?? null;
            $urutan = $sectionId
                ? ($course->materi()->where('section_id', $sectionId)->max('urutan') ?? 0) + 1
                : ($course->materi()->max('urutan') ?? 0) + 1;

            $material = $course->materi()->create([
                'section_id' => $sectionId,
                'judul' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => 'quiz',
                'urutan' => $urutan,
                'status' => 'draft',
                'is_preview' => false,
                'status_terkunci' => true,
            ]);

            // Create assignment (quiz)
            $assignment = Assignment::create([
                'kursus_id' => $course->id,
                'materi_id' => $material->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => 'quiz',
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'passing_score' => $validated['passing_score'] ?? 60,
                'start_date' => null,
                'due_date' => null,
                'show_results_immediately' => true,
                'allow_multiple_attempts' => false,
                'max_attempts' => null,
                'randomize_questions' => $validated['randomize_questions'],
                'is_published' => false,
            ]);

            // Optional: import all questions from selected bank
            if (!empty($validated['question_bank_id'])) {
                $bankQuery = QuestionBank::where('id', $validated['question_bank_id'])
                    ->where(function($q) {
                        $q->where('created_by', Auth::id())
                          ->orWhere('is_public', true);
                    })->with('questions.options');
                if (Schema::hasColumn('question_banks', 'is_internal')) {
                    $bankQuery->where('is_internal', false);
                }
                $bank = $bankQuery->firstOrFail();

                $order = 0;
                foreach ($bank->questions as $question) {
                    $assignment->questions()->attach($question->id, [
                        'order' => ++$order,
                        'points' => $question->points,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('instructor.courses.show', $course)
                ->with('success', 'Quiz berhasil dibuat! Tambahkan/atur soal di halaman ini atau lanjutkan di menu Assignment & Quiz.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat quiz: ' . $e->getMessage());
        }
    }

    /**
     * Store a newly created question directly from assignment page and attach it
     */
    public function storeQuestion(Request $request, Assignment $assignment)
    {
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403);
        }

        $isMulti = $request->has('questions');
            if ($isMulti) {
                $validated = $request->validate([
                    'question_bank_id' => 'nullable|exists:question_banks,id',
                    'save_to_bank' => 'nullable|boolean',
                    'questions' => 'required|array|min:1',
                    'questions.*.type' => 'required|in:multiple_choice,true_false,essay,short_answer',
                    'questions.*.question_text' => 'required|string',
                    'questions.*.explanation' => 'nullable|string',
                    'questions.*.points' => 'required|integer|min:1',
                    'questions.*.correct_answer' => 'nullable|string',
                    // opsi hanya wajib untuk multiple_choice / true_false
                    'questions.*.options' => 'nullable|array',
                    'questions.*.options.*.text' => 'required_if:questions.*.type,multiple_choice,true_false|string',
                    'questions.*.options.*.is_correct' => 'nullable|boolean',
                ]);
            } else {
                $validated = $request->validate([
                    'question_bank_id' => 'nullable|exists:question_banks,id',
                    'type' => 'required|in:multiple_choice,true_false,essay,short_answer',
                    'question_text' => 'required|string',
                    'explanation' => 'nullable|string',
                    'points' => 'required|integer|min:1',
                    'correct_answer' => 'nullable|string',
                    // opsi hanya wajib untuk multiple_choice / true_false
                    'options' => 'nullable|array',
                    'options.*.text' => 'required_if:type,multiple_choice,true_false|string',
                    'options.*.is_correct' => 'nullable|boolean',
                    'save_to_bank' => 'nullable|boolean',
                ]);
            }

        $saveToBank = $request->boolean('save_to_bank', true);

        // Tentukan target bank: publik/owned jika ingin simpan ke bank, atau bank internal kalau tidak.
        if ($saveToBank) {
            if (!empty($validated['question_bank_id'])) {
                $bankQuery = QuestionBank::where('id', $validated['question_bank_id'])
                    ->where(function($q) {
                        $q->where('created_by', Auth::id())->orWhere('is_public', true);
                    });
                if (Schema::hasColumn('question_banks', 'is_internal')) {
                    $bankQuery->where('is_internal', false);
                }
                $questionBank = $bankQuery->firstOrFail();
            } else {
                $questionBank = QuestionBank::firstOrCreate(
                    [
                        'created_by' => Auth::id(),
                        'title' => 'Bank Kursus: ' . $assignment->kursus->judul,
                        'is_internal' => false,
                    ],
                    [
                        'description' => 'Bank soal otomatis untuk kursus ' . $assignment->kursus->judul,
                        'category' => $assignment->kursus->kategori ?? null,
                        'is_public' => false,
                    ]
                );
            }
        } else {
            $questionBank = QuestionBank::firstOrCreate(
                [
                    'created_by' => Auth::id(),
                    'title' => 'Internal Assignment: ' . $assignment->id,
                    'is_internal' => true,
                ],
                [
                    'description' => 'Bank internal (tidak tampil) untuk soal khusus assignment ' . $assignment->title,
                    'category' => $assignment->kursus->kategori ?? null,
                    'is_public' => false,
                ]
            );
        }

        DB::beginTransaction();
        try {
            $maxOrder = $questionBank->questions()->max('order') ?? 0;
            $nextAssignmentOrder = ($assignment->questions()->max('assignment_questions.order') ?? 0);

            $items = $isMulti ? $validated['questions'] : [[
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'explanation' => $validated['explanation'] ?? null,
                'points' => $validated['points'],
                'correct_answer' => $validated['correct_answer'] ?? null,
                'options' => $validated['options'] ?? [],
            ]];

            foreach ($items as $item) {
                $question = $questionBank->questions()->create([
                    'type' => $item['type'],
                    'question_text' => $item['question_text'],
                    'explanation' => $item['explanation'] ?? null,
                    'points' => $item['points'],
                    'order' => ++$maxOrder,
                    'correct_answer' => in_array($item['type'], ['short_answer']) ? ($item['correct_answer'] ?? null) : null,
                ]);

                if ($item['type'] === 'multiple_choice') {
                    $options = collect($item['options'] ?? [])
                        ->filter(fn ($opt) => isset($opt['text']) && trim($opt['text']) !== '')
                        ->values();

                    if ($options->count() < 2) {
                        throw new \Exception('Minimal dua opsi untuk pilihan ganda.');
                    }

                    $hasCorrect = $options->contains(fn ($opt) => !empty($opt['is_correct']));
                    if (!$hasCorrect) {
                        throw new \Exception('Pilih minimal satu jawaban benar.');
                    }

                    foreach ($options as $index => $optionData) {
                        $question->options()->create([
                            'option_text' => $optionData['text'],
                            'is_correct' => !empty($optionData['is_correct']),
                            'order' => $index + 1,
                        ]);
                    }
                } elseif ($item['type'] === 'true_false') {
                    $correct = strtolower($item['correct_answer'] ?? 'true');
                    $question->options()->createMany([
                        [
                            'option_text' => 'Benar',
                            'is_correct' => in_array($correct, ['true', 'benar', '1']),
                            'order' => 1,
                        ],
                        [
                            'option_text' => 'Salah',
                            'is_correct' => in_array($correct, ['false', 'salah', '0']),
                            'order' => 2,
                        ],
                    ]);
                }

                $assignment->questions()->attach($question->id, [
                    'order' => ++$nextAssignmentOrder,
                    'points' => $item['points'],
                ]);
            }

            DB::commit();

            return back()->with('success', ($isMulti ? count($items) : 1) . ' soal baru berhasil dibuat dan ditambahkan ke quiz!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal membuat soal: ' . $e->getMessage());
        }
    }

    /**
     * Publish assignment
     */
    public function publish(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403);
        }

        if ($assignment->questions()->count() == 0) {
            return back()->with('error', 'Tidak dapat mempublikasi assignment tanpa soal!');
        }

        $assignment->update(['is_published' => true]);

        return back()->with('success', 'Assignment berhasil dipublikasi!');
    }

    /**
     * Unpublish assignment
     */
    public function unpublish(Assignment $assignment)
    {
        // Check authorization
        if ($assignment->kursus->instructor_id !== Auth::id() && $assignment->kursus->pembuat !== Auth::id()) {
            abort(403);
        }

        $assignment->update(['is_published' => false]);

        return back()->with('success', 'Assignment berhasil di-unpublish!');
    }
}
