<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\MaterialCompletion;
use App\Models\Materi;
use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected $middleware = ['auth', 'role:student'];

    public function __construct()
    {
        // Middleware already applied in routes
    }

    /**
     * Show learning page
     */
    public function learn(Request $request, Kursus $course)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.');
        }

        $course->load([
            'pembuat',
            'sections.materials' => function ($query) {
                $query->orderBy('urutan', 'asc');
            },
        ]);

        $materials = $course->sections
            ->flatMap(function ($section) {
                return $section->materials;
            })
            ->values();

        $materialIds = $materials->pluck('id')->toArray();
        $completedIds = MaterialCompletion::where('user_id', Auth::id())
            ->whereIn('materi_id', $materialIds)
            ->pluck('materi_id')
            ->toArray();
        $totalMaterials = $materials->count();
        $progress = $totalMaterials > 0 ? min(100, round((count($completedIds) / $totalMaterials) * 100)) : 0;

        $materialId = $request->query('material');
        $currentMaterial = null;

        if ($materialId) {
            $currentMaterial = $materials->firstWhere('id', $materialId);
        }
        if (!$currentMaterial) {
            $currentMaterial = $materials->first();
        }

        return view('student.learn', [
            'course' => $course,
            'sections' => $course->sections,
            'materials' => $materials,
            'currentMaterial' => $currentMaterial,
            'enrollment' => $enrollment,
            'completedIds' => $completedIds,
            'progress' => $progress,
        ]);
    }

    public function viewMaterial(Request $request, Kursus $course, $materialId)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.');
        }

        $material = $course->materi()->where('id', $materialId)->firstOrFail();

        $completed = MaterialCompletion::where('user_id', Auth::id())
            ->where('materi_id', $material->id)
            ->first();

        return view('student.material-view', [
            'course' => $course,
            'material' => $material,
            'completed' => $completed,
        ]);
    }

    public function markMaterialComplete(Request $request, Kursus $course, $materialId)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.');
        }

        $material = $course->materi()->where('id', $materialId)->firstOrFail();

        MaterialCompletion::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'materi_id' => $material->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        return back()->with('success', 'Materi ditandai selesai.');
    }

    public function quiz(Request $request, Kursus $course, Materi $material)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.');
        }

        $assignment = Assignment::where('materi_id', $material->id)
            ->with(['questions.options'])
            ->first();

        if (!$assignment || $assignment->questions->isEmpty()) {
            return redirect()->route('student.course.learn', $course)
                ->with('error', 'Quiz belum memiliki soal.');
        }

        // Jika sudah pernah dikerjakan dan tersimpan, langsung tampilkan hasil
        $completion = MaterialCompletion::where('user_id', Auth::id())
            ->where('materi_id', $material->id)
            ->whereNotNull('answers_json')
            ->first();

        if ($completion) {
            $answers = $completion->answers_json ?? [];
            [$results, $score] = $this->computeQuizResults($assignment, $answers);
            return view('student.quiz-result', [
                'course' => $course,
                'material' => $material,
                'assignment' => $assignment,
                'results' => $results,
                'score' => $completion->score ?? $score,
            ]);
        }

        return view('student.quiz', [
            'course' => $course,
            'material' => $material,
            'assignment' => $assignment,
        ]);
    }

    public function quizSubmit(Request $request, Kursus $course, Materi $material)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            return redirect()->route('courses.show', $course)
                ->with('error', 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.');
        }

        $request->validate([
            'answers_json' => 'required|string'
        ]);

        $assignment = Assignment::where('materi_id', $material->id)
            ->with(['questions.options'])
            ->firstOrFail();

        $answers = json_decode($request->input('answers_json'), true) ?? [];

        [$results, $score] = $this->computeQuizResults($assignment, $answers);

        $completion = MaterialCompletion::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'materi_id' => $material->id,
            ],
            [
                'completed_at' => now(),
                'answers_json' => $answers,
                'score' => $score,
            ]
        );

        return view('student.quiz-result', [
            'course' => $course,
            'material' => $material,
            'assignment' => $assignment,
            'results' => $results,
            'score' => $score,
        ]);
    }

    private function computeQuizResults($assignment, $answers)
    {
        $results = [];
        $totalCount = 0;
        $correctCount = 0;

        foreach ($assignment->questions as $question) {
            $userAnswer = $answers[$question->id] ?? null;
            $totalCount++;
            $isCorrect = null;
            $correctText = null;
            $userText = null;

            if ($question->options->count() > 0) {
                // Objective
                $correctOption = $question->options->firstWhere('is_correct', true);
                $correctText = $correctOption?->option_text;
                $selectedOption = $question->options->firstWhere('id', $userAnswer);
                $userText = $selectedOption?->option_text;
                $isCorrect = $selectedOption && $selectedOption->is_correct;
                if ($isCorrect) {
                    $correctCount++;
                }
            } else {
                // Essay/short answer
                $userText = $userAnswer;
                $correctText = $question->correct_answer;
                if ($correctText !== null && $userText !== null) {
                    $isCorrect = trim(mb_strtolower($userText)) === trim(mb_strtolower($correctText));
                    if ($isCorrect) {
                        $correctCount++;
                    }
                } else {
                    $isCorrect = null; // manual grading/pending
                }
            }

            $results[] = [
                'id' => $question->id,
                'text' => $question->question_text,
                'user_answer' => $userText,
                'correct_answer' => $correctText,
                'is_correct' => $isCorrect,
            ];
        }

        $score = $totalCount > 0 ? round(($correctCount / $totalCount) * 100) : null;

        return [$results, $score];
    }

    /**
     * My courses page
     */
    public function myCourses()
    {
        $enrollments = Enrollment::where('user_id', Auth::id())
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->with([
                'kursus' => function ($query) {
                    $query->withCount('materi');
                },
                'kursus.pembuat',
            ])
            ->latest('tanggal_daftar')
            ->get();

        return view('my-courses', compact('enrollments'));
    }
}
