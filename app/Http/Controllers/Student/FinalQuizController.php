<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use App\Models\Kursus;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\JawabanPeserta;
use App\Models\Question;
use App\Models\QuestionOption;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controller untuk fitur kuis final.
 */
class FinalQuizController extends Controller
{
    /**
     * Menampilkan halaman final quiz untuk kursus
     */
    public function show($kursusId)
    {
        $user = Auth::user();
        $kursus = Kursus::with(['finalQuiz'])->findOrFail($kursusId);

        // Pastikan user terdaftar di kursus ini
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->first();

        if (!$enrollment) {
            abort(403, 'Anda belum terdaftar di kursus ini.');
        }

        // Cek apakah kursus memiliki final quiz
        if (!$kursus->require_final_quiz || !$kursus->final_quiz_id) {
            return redirect()->route('courses.show', $kursusId)
                ->with('info', 'Kursus ini tidak memiliki final quiz.');
        }

        $finalQuiz = $kursus->finalQuiz;

        // Ambil semua attempts user untuk final quiz ini
        $attempts = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $finalQuiz->id)
            ->where('kursus_id', $kursusId)
            ->orderBy('attempt_number', 'desc')
            ->get();

        $latestAttempt = $attempts->first();
        $attemptCount = $attempts->count();
        $hasPassed = $attempts->where('is_passed', true)->isNotEmpty();

        // Check if there's an incomplete attempt (started but not completed)
        $incompleteAttempt = $attempts->whereNull('completed_at')->first();
        $hasIncompleteAttempt = $incompleteAttempt !== null;

        // Can only retake if no incomplete attempt and within max attempts
        $canRetake = !$hasIncompleteAttempt && $attemptCount < $kursus->max_quiz_attempts;

        return view('student.courses.final-quiz', compact(
            'kursus',
            'finalQuiz',
            'attempts',
            'latestAttempt',
            'attemptCount',
            'canRetake',
            'hasPassed',
            'hasIncompleteAttempt',
            'incompleteAttempt'
        ));
    }

    /**
     * Memulai attempt baru untuk final quiz
     */
    public function start(Request $request, $kursusId)
    {
        $user = Auth::user();
        $kursus = Kursus::with(['finalQuiz'])->findOrFail($kursusId);

        // Validasi enrollment
        $enrollment = Enrollment::where('user_id', $user->id)
            ->where('kursus_id', $kursusId)
            ->first();

        if (!$enrollment) {
            return response()->json(['error' => 'Anda belum terdaftar di kursus ini.'], 403);
        }

        if (!$kursus->require_final_quiz || !$kursus->final_quiz_id) {
            return response()->json(['error' => 'Kursus ini tidak memiliki final quiz.'], 400);
        }

        $finalQuiz = $kursus->finalQuiz;

        // Cek jumlah attempt
        $attemptCount = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $finalQuiz->id)
            ->where('kursus_id', $kursusId)
            ->count();

        if ($attemptCount >= $kursus->max_quiz_attempts) {
            return response()->json([
                'error' => 'Anda telah mencapai batas maksimal percobaan (' . $kursus->max_quiz_attempts . 'x).'
            ], 403);
        }

        // Cek apakah ada attempt yang belum selesai
        $incompleteAttempt = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $finalQuiz->id)
            ->where('kursus_id', $kursusId)
            ->whereNull('completed_at')
            ->first();

        if ($incompleteAttempt) {
            // Redirect ke attempt yang belum selesai
            return response()->json([
                'success' => true,
                'message' => 'Anda memiliki percobaan yang belum selesai.',
                'redirect' => route('courses.final-quiz.take', [$kursusId, $incompleteAttempt->id])
            ]);
        }

        // Cek apakah sudah pernah lulus
        $hasPassed = QuizAttempt::where('user_id', $user->id)
            ->where('quiz_id', $finalQuiz->id)
            ->where('kursus_id', $kursusId)
            ->where('is_passed', true)
            ->exists();

        if ($hasPassed) {
            return response()->json([
                'success' => true,
                'message' => 'Anda sudah lulus final quiz ini.',
                'redirect' => route('courses.final-quiz.show', $kursusId)
            ]);
        }

        // Buat attempt baru
        $attempt = QuizAttempt::create([
            'user_id' => $user->id,
            'quiz_id' => $finalQuiz->id,
            'kursus_id' => $kursusId,
            'attempt_number' => $attemptCount + 1,
            'started_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'redirect' => route('courses.final-quiz.take', [$kursusId, $attempt->id])
        ]);
    }

    /**
     * Halaman mengerjakan final quiz
     */
    public function take($kursusId, $attemptId)
    {
        $user = Auth::user();
        $attempt = QuizAttempt::with(['quiz.soal.options', 'kursus'])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Cek apakah sudah selesai
        if ($attempt->completed_at) {
            return redirect()
                ->route('courses.final-quiz.result', [$kursusId, $attemptId])
                ->with('info', 'Anda sudah menyelesaikan quiz ini.');
        }

        $quiz = $attempt->quiz;
        $questions = $quiz->soal()->with('options')->get();

        return view('student.courses.take-final-quiz', compact('attempt', 'quiz', 'questions', 'kursusId'));
    }

    /**
     * Submit jawaban final quiz
     */
    public function submit(Request $request, $kursusId, $attemptId)
    {
        $user = Auth::user();
        $attempt = QuizAttempt::where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        if ($attempt->completed_at) {
            return response()->json(['error' => 'Quiz ini sudah diselesaikan.'], 400);
        }

        $kursus = Kursus::findOrFail($kursusId);
        $quiz = $attempt->quiz;
        $answers = $request->input('answers', []);

        DB::beginTransaction();
        try {
            $totalScore = 0;
            $totalPossiblePoints = 0;
            $correctCount = 0;

            // Ambil semua pertanyaan quiz supaya perhitungan konsisten
            $allQuestions = $quiz->soal()->with('options')->get();

            foreach ($allQuestions as $question) {
                $questionId = $question->id;
                $answerValue = $answers[$questionId] ?? null;

                $isEssay = $question->type === 'essay' || $question->options->count() === 0;
                $selectedOptionId = null;
                $answerText = null;
                $pointsEarned = 0;
                $isCorrect = false;

                if ($isEssay) {
                    // Simpan teks jawaban, tidak auto-grading
                    $answerText = is_string($answerValue) ? $answerValue : '';
                    // Essay tidak ikut totalPossiblePoints (manual grading)
                } else {
                    $selectedOptionId = $answerValue;
                    $correctOption = $question->options->firstWhere('is_correct', true);
                    $isCorrect = $correctOption && $selectedOptionId && $correctOption->id == $selectedOptionId;
                    $pointsEarned = $isCorrect ? ($question->points ?? 0) : 0;
                    $totalScore += $pointsEarned;
                    $totalPossiblePoints += ($question->points ?? 0);
                    if ($isCorrect) {
                        $correctCount++;
                    }
                }

                // Simpan jawaban
                JawabanPeserta::create([
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                    'quiz_attempt_id' => $attempt->id,
                    'attempt_number' => $attempt->attempt_number,
                    'question_id' => $questionId,
                    'selected_option_id' => $selectedOptionId,
                    'answer_text' => $answerText,
                    'points_earned' => $pointsEarned,
                    'nilai_tercapai' => $isEssay ? null : ($isCorrect ? 1 : 0),
                    'submitted_at' => now(),
                ]);
            }

            // Hitung score persentase: gunakan poin jika ada, fallback ke rasio benar
            if ($totalPossiblePoints > 0) {
                $scorePercentage = ($totalScore / $totalPossiblePoints) * 100;
            } else {
                $scorePercentage = $allQuestions->count() > 0 ? ($correctCount / $allQuestions->count()) * 100 : 0;
            }
            $isPassed = $scorePercentage >= $kursus->min_passing_score;

            // Update attempt
            $attempt->update([
                'score' => $scorePercentage,
                'is_passed' => $isPassed,
                'completed_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'redirect' => route('courses.final-quiz.result', [$kursusId, $attemptId])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Melihat hasil final quiz
     */
    public function result($kursusId, $attemptId)
    {
        $user = Auth::user();
        $attempt = QuizAttempt::with([
            'quiz.soal.options',
            'kursus',
            'jawabanPeserta.question.options'
        ])
            ->where('id', $attemptId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $kursus = $attempt->kursus;
        $quiz = $attempt->quiz;

        // Recalculate score from stored answers using the same logic as submit()
        $answers = $attempt->jawabanPeserta ?? collect();
        $questions = $quiz->soal()->with('options')->get();

        $totalPossiblePoints = 0;
        $totalScore = 0;
        $correctCount = 0;

        foreach ($questions as $question) {
            $answer = $answers->firstWhere('question_id', $question->id);
            $isEssay = $question->type === 'essay' || $question->options->count() === 0;

            if ($isEssay) {
                continue; // tidak dihitung otomatis
            }

            $points = $question->points ?? 0;
            $totalPossiblePoints += $points;

            $isCorrect = $answer && $answer->nilai_tercapai == 1;
            if ($isCorrect) {
                $correctCount++;
                $totalScore += $points;
            }
        }

        if ($totalPossiblePoints > 0) {
            $computedScore = round(($totalScore / $totalPossiblePoints) * 100, 2);
        } else {
            $totalQuestions = $questions->count();
            $computedScore = $totalQuestions > 0 ? round(($correctCount / $totalQuestions) * 100, 2) : 0;
        }

        // Sync attempt score/pass flag if different
        $passingScore = $kursus->min_passing_score ?? ($quiz->passing_score ?? 70);
        $computedPassed = $computedScore >= $passingScore;
        if (abs(($attempt->score ?? 0) - $computedScore) > 0.01 || (bool) $attempt->is_passed !== $computedPassed) {
            $attempt->score = $computedScore;
            $attempt->is_passed = $computedPassed;
            $attempt->save();
        }

        return view('student.courses.final-quiz-result', compact('attempt', 'kursus', 'quiz'));
    }

    /**
     * Check if final quiz is still active (for polling)
     */
    public function checkStatus($kursusId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return response()->json([
                'is_active' => false,
                'message' => 'Final quiz tidak ditemukan.'
            ]);
        }

        return response()->json([
            'is_active' => (bool) $kursus->finalQuiz->is_active,
            'message' => $kursus->finalQuiz->is_active ? 'Quiz aktif' : 'Final quiz sedang dalam proses maintenance. Silakan coba lagi nanti.'
        ]);
    }
}
