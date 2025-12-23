<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk fitur tugas.
 */
class AssignmentController extends Controller
{
    /**
     * Menampilkan daftar tugas.
     */
    public function index()
    {
        // Get enrolled courses
        $enrolledCourseIds = Enrollment::where('user_id', auth()->id())
            ->pluck('kursus_id');

        // Get assignments from enrolled courses
        $assignments = Assignment::whereIn('kursus_id', $enrolledCourseIds)
            ->where('is_published', true)
            ->with(['kursus', 'submissions' => function($query) {
                $query->where('user_id', auth()->id())
                    ->latest();
            }])
            ->orderBy('due_date')
            ->get();

        return view('student.assignments.index', compact('assignments'));
    }

    /**
     * Menampilkan detail tugas.
     */
    public function show(Assignment $assignment)
    {
        // Check if user is enrolled
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('kursus_id', $assignment->kursus_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        if (!$assignment->is_published) {
            abort(403, 'Assignment ini belum dipublikasikan.');
        }

        // Get user's submissions
        $submissions = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->orderBy('attempt_number', 'desc')
            ->get();

        $canStartNew = true;

        // Check if can start new attempt
        if (!$assignment->allow_multiple_attempts && $submissions->isNotEmpty()) {
            $canStartNew = false;
        } elseif ($assignment->allow_multiple_attempts && $assignment->max_attempts) {
            if ($submissions->count() >= $assignment->max_attempts) {
                $canStartNew = false;
            }
        }

        // Check for in-progress submission
        $inProgressSubmission = $submissions->where('status', 'in_progress')->first();

        return view('student.assignments.show', compact('assignment', 'submissions', 'canStartNew', 'inProgressSubmission'));
    }

    /**
     * Memulai tugas.
     */
    public function start(Assignment $assignment)
    {
        // Check if user is enrolled
        $enrollment = Enrollment::where('user_id', auth()->id())
            ->where('kursus_id', $assignment->kursus_id)
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        if (!$assignment->is_published) {
            abort(403, 'Assignment ini belum dipublikasikan.');
        }

        // Check if already has in-progress submission
        $inProgress = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->where('status', 'in_progress')
            ->first();

        if ($inProgress) {
            return redirect()->route('student.assignments.take', [$assignment, $inProgress]);
        }

        // Check attempt limit
        $submissionsCount = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->count();

        if (!$assignment->allow_multiple_attempts && $submissionsCount > 0) {
            return back()->with('error', 'Anda sudah mengerjakan assignment ini.');
        }

        if ($assignment->allow_multiple_attempts && $assignment->max_attempts) {
            if ($submissionsCount >= $assignment->max_attempts) {
                return back()->with('error', 'Anda sudah mencapai batas maksimal percobaan.');
            }
        }

        // Create new submission
        $submission = Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => auth()->id(),
            'attempt_number' => $submissionsCount + 1,
            'status' => 'in_progress',
            'started_at' => now(),
            'answers' => []
        ]);

        return redirect()->route('student.assignments.take', [$assignment, $submission]);
    }

    /**
     * Menangani proses take.
     */
    public function take(Assignment $assignment, Submission $submission)
    {
        // Verify ownership
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        if ($submission->assignment_id !== $assignment->id) {
            abort(403);
        }

        if ($submission->status !== 'in_progress') {
            return redirect()->route('student.assignments.result', [$assignment, $submission]);
        }

        // Check if time expired (if duration is set)
        if ($assignment->duration_minutes && $submission->started_at) {
            $deadline = $submission->started_at->addMinutes($assignment->duration_minutes);
            if (now()->greaterThan($deadline)) {
                // Auto submit
                return $this->submit($assignment, $submission, new Request(['auto_submit' => true]));
            }
        }

        // Get questions (randomize if needed)
        $questions = $assignment->questions()->with('options')->get();
        
        if ($assignment->randomize_questions) {
            $questions = $questions->shuffle();
        }

        return view('student.assignments.take', compact('assignment', 'submission', 'questions'));
    }

    /**
     * Mengirim tugas.
     */
    public function submit(Assignment $assignment, Submission $submission, Request $request)
    {
        // Verify ownership
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        if ($submission->status !== 'in_progress') {
            return back()->with('error', 'Submission ini sudah diserahkan.');
        }

        // Get answers from request
        $answers = $request->input('answers', []);

        // Calculate score
        $totalScore = 0;
        $totalPoints = 0;

        foreach ($assignment->questions as $question) {
            $points = $question->pivot->points ?? $question->points;
            $totalPoints += $points;

            $userAnswer = $answers[$question->id] ?? null;

            if ($question->type === 'multiple_choice') {
                // Check if answer is correct
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $totalScore += $points;
                }
            } elseif ($question->type === 'true_false') {
                // Check if answer is correct
                $correctOption = $question->options->where('is_correct', true)->first();
                if ($correctOption && $userAnswer == $correctOption->id) {
                    $totalScore += $points;
                }
            } elseif ($question->type === 'short_answer') {
                // Check if answer matches correct_answer (case insensitive)
                if ($userAnswer && $question->correct_answer) {
                    if (strtolower(trim($userAnswer)) === strtolower(trim($question->correct_answer))) {
                        $totalScore += $points;
                    }
                }
            }
            // Essay questions need manual grading
        }

        // Calculate percentage
        $percentage = $totalPoints > 0 ? ($totalScore / $totalPoints) * 100 : 0;

        // Determine status
        $hasEssay = $assignment->questions->contains(function($q) {
            return $q->type === 'essay';
        });

        $status = $hasEssay ? 'submitted' : 'graded';

        // Update submission
        $submission->update([
            'answers' => $answers,
            'score' => $totalScore,
            'percentage' => $percentage,
            'status' => $status,
            'submitted_at' => now(),
            'graded_at' => $hasEssay ? null : now()
        ]);

        return redirect()->route('student.assignments.result', [$assignment, $submission])
            ->with('success', 'Assignment berhasil diserahkan!');
    }

    /**
     * Menampilkan hasil tugas.
     */
    public function result(Assignment $assignment, Submission $submission)
    {
        // Verify ownership
        if ($submission->user_id !== auth()->id()) {
            abort(403);
        }

        if ($submission->status === 'in_progress') {
            return redirect()->route('student.assignments.take', [$assignment, $submission]);
        }

        // Get questions with user answers
        $questions = $assignment->questions()->with('options')->get();

        return view('student.assignments.result', compact('assignment', 'submission', 'questions'));
    }
}
