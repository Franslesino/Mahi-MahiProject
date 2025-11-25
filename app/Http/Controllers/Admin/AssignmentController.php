<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Kursus;
use App\Models\QuestionBank;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignmentController extends Controller
{
    /**
     * Display a listing of assignments
     */
    public function index()
    {
        $assignments = Assignment::with(['kursus', 'materi'])
            ->withCount(['questions', 'submissions'])
            ->latest()
            ->paginate(15);

        return view('admin.assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new assignment
     */
    public function create(Request $request)
    {
        $courses = Kursus::all();
        
        $selectedCourse = null;
        $materials = collect();
        
        if ($request->has('course_id')) {
            $selectedCourse = Kursus::find($request->course_id);
            if ($selectedCourse) {
                $materials = $selectedCourse->materi;
            }
        }

        return view('admin.assignments.create', compact('courses', 'selectedCourse', 'materials'));
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
            'passing_score' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'show_results_immediately' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'randomize_questions' => 'boolean',
        ]);

        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['randomize_questions'] = $request->has('randomize_questions');
        $validated['is_published'] = false;

        $assignment = Assignment::create($validated);

        return redirect()
            ->route('admin.assignments.edit-questions', $assignment)
            ->with('success', 'Assignment berhasil dibuat! Sekarang tambahkan soal-soal.');
    }

    /**
     * Display the specified assignment
     */
    public function show(Assignment $assignment)
    {
        $assignment->load(['kursus', 'materi', 'questions.options', 'submissions.user']);

        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the assignment
     */
    public function edit(Assignment $assignment)
    {
        $courses = Kursus::all();
        $materials = $assignment->kursus->materi;

        return view('admin.assignments.edit', compact('assignment', 'courses', 'materials'));
    }

    /**
     * Update the specified assignment
     */
    public function update(Request $request, Assignment $assignment)
    {
        $validated = $request->validate([
            'kursus_id' => 'required|exists:kursus,id',
            'materi_id' => 'nullable|exists:materi,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:quiz,assignment,exam',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'start_date' => 'nullable|date',
            'due_date' => 'nullable|date|after_or_equal:start_date',
            'show_results_immediately' => 'boolean',
            'allow_multiple_attempts' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'randomize_questions' => 'boolean',
        ]);

        $validated['show_results_immediately'] = $request->has('show_results_immediately');
        $validated['allow_multiple_attempts'] = $request->has('allow_multiple_attempts');
        $validated['randomize_questions'] = $request->has('randomize_questions');

        $assignment->update($validated);

        return redirect()
            ->route('admin.assignments.show', $assignment)
            ->with('success', 'Assignment berhasil diperbarui!');
    }

    /**
     * Remove the specified assignment
     */
    public function destroy(Assignment $assignment)
    {
        $assignment->delete();

        return redirect()
            ->route('admin.assignments.index')
            ->with('success', 'Assignment berhasil dihapus!');
    }

    /**
     * Show page to add/manage questions for assignment
     */
    public function editQuestions(Assignment $assignment)
    {
        $assignment->load(['questions.options']);
        
        // Get available question banks (all banks for admin)
        $questionBanks = QuestionBank::withCount('questions')->get();

        return view('admin.assignments.edit-questions', compact('assignment', 'questionBanks'));
    }

    /**
     * Add questions from question bank to assignment
     */
    public function addQuestions(Request $request, Assignment $assignment)
    {
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
        $assignment->questions()->detach($question->id);

        return back()->with('success', 'Soal berhasil dihapus dari assignment!');
    }

    /**
     * Publish assignment
     */
    public function publish(Assignment $assignment)
    {
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
        $assignment->update(['is_published' => false]);

        return back()->with('success', 'Assignment berhasil di-unpublish!');
    }
}
