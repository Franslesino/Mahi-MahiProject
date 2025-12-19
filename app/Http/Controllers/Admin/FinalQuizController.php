<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Quiz;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Models\QuestionBank;
use App\Models\RelasiQuiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinalQuizController extends Controller
{
    public function edit($kursusId)
    {
        $kursus = Kursus::with(['finalQuiz.soal.options'])->findOrFail($kursusId);
        
        // Get available quizzes for this course (not yet used as final quiz)
        $availableQuizzes = Quiz::where('kursus_id', $kursusId)
            ->where(function($query) use ($kursus) {
                $query->where('is_final_quiz', false)
                      ->orWhere('id', $kursus->final_quiz_id);
            })
            ->get();

        // Get question banks for import
        $questionBanks = QuestionBank::with('questions.options')->get();

        return view('admin.courses.final-quiz-settings', compact('kursus', 'availableQuizzes', 'questionBanks'));
    }

    public function update(Request $request, $kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);

        $validated = $request->validate([
            'require_final_quiz' => 'nullable|boolean',
            'final_quiz_id' => 'required_if:require_final_quiz,1|nullable|exists:quiz,id',
            'min_passing_score' => 'required_if:require_final_quiz,1|nullable|integer|min:0|max:100',
            'max_attempts' => 'required_if:require_final_quiz,1|nullable|integer|min:1',
        ]);

        DB::transaction(function() use ($kursus, $validated) {
            // Update old final quiz if exists
            if ($kursus->final_quiz_id && $kursus->final_quiz_id != ($validated['final_quiz_id'] ?? null)) {
                Quiz::where('id', $kursus->final_quiz_id)->update(['is_final_quiz' => false]);
            }

            // Update course
            $kursus->update([
                'require_final_quiz' => $validated['require_final_quiz'] ?? false,
                'final_quiz_id' => $validated['require_final_quiz'] ? $validated['final_quiz_id'] : null,
                'min_passing_score' => $validated['require_final_quiz'] ? $validated['min_passing_score'] : null,
                'max_quiz_attempts' => $validated['require_final_quiz'] ? $validated['max_attempts'] : null,
            ]);

            // Update new final quiz
            if ($validated['require_final_quiz'] && $validated['final_quiz_id']) {
                Quiz::where('id', $validated['final_quiz_id'])->update([
                    'is_final_quiz' => true,
                    'passing_grade' => $validated['min_passing_score'],
                    'kesempatan_mengerjakan' => $validated['max_attempts']
                ]);
            }
        });

        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', 'Pengaturan final quiz berhasil diperbarui!');
    }

    public function createQuiz($kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        // Check if course already has a final quiz
        if ($kursus->final_quiz_id) {
            return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
                ->with('error', 'Kursus ini sudah memiliki final quiz. Setiap kursus hanya boleh memiliki 1 final quiz.');
        }

        return view('admin.courses.create-final-quiz', compact('kursus'));
    }

    public function storeQuiz(Request $request, $kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);

        // Validate only 1 final quiz per course
        if ($kursus->final_quiz_id) {
            return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
                ->with('error', 'Kursus ini sudah memiliki final quiz. Setiap kursus hanya boleh memiliki 1 final quiz.');
        }

        $validated = $request->validate([
            'judul_quiz' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        DB::transaction(function() use ($kursus, $validated) {
            $quiz = Quiz::create([
                'judul_quiz' => $validated['judul_quiz'],
                'deskripsi' => $validated['deskripsi'] ?? null,
                'kursus_id' => $kursus->id,
                'is_final_quiz' => true,
                'is_active' => false,
                'passing_grade' => 60, // Default passing grade
                'kesempatan_mengerjakan' => 3, // Default attempts
                'durasi_quiz' => 60, // Default duration in minutes
            ]);

            $kursus->update([
                'final_quiz_id' => $quiz->id,
                'require_final_quiz' => true,
            ]);
        });

        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', 'Final quiz berhasil dibuat! Silakan tambahkan soal-soal.');
    }

    public function importFromBankSoal(Request $request, $kursusId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return redirect()->back()->with('error', 'Silakan buat final quiz terlebih dahulu.');
        }

        $validated = $request->validate([
            'question_ids' => 'required|array|min:1',
            'question_ids.*' => 'exists:questions,id',
        ]);

        DB::transaction(function() use ($kursus, $validated) {
            $currentMaxUrutan = RelasiQuiz::where('quiz_id', $kursus->final_quiz_id)->max('urutan') ?? 0;

            foreach ($validated['question_ids'] as $index => $questionId) {
                RelasiQuiz::create([
                    'quiz_id' => $kursus->final_quiz_id,
                    'question_id' => $questionId,
                    'urutan' => $currentMaxUrutan + $index + 1,
                ]);
            }
        });

        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', count($validated['question_ids']) . ' soal berhasil ditambahkan!');
    }

    public function storeNewQuestion(Request $request, $kursusId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return redirect()->back()->with('error', 'Silakan buat final quiz terlebih dahulu.');
        }

        $validated = $request->validate([
            'question_type' => 'required|in:multiple_choice,true_false,essay',
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            'options' => 'required_if:question_type,multiple_choice,true_false|array',
            'options.*.text' => 'required_if:question_type,multiple_choice,true_false|string',
            'options.*.is_correct' => 'nullable|boolean',
            'correct_option' => 'nullable|integer',
            'save_to_bank' => 'nullable|boolean',
            'question_bank_id' => [
                'required_if:save_to_bank,1',
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== 'new' && $value !== null && !\DB::table('question_banks')->where('id', $value)->exists()) {
                        $fail('ID bank soal tidak valid.');
                    }
                }
            ],
        ]);

        DB::transaction(function() use ($kursus, $validated) {
            // Determine question bank ID
            $questionBankId = null;
            if (!empty($validated['save_to_bank'])) {
                if ($validated['question_bank_id'] === 'new') {
                    // Create auto bank
                    $autoBank = QuestionBank::firstOrCreate(
                        ['title' => 'Soal Final Quiz (Auto)', 'created_by' => auth()->id()],
                        ['description' => 'Bank soal otomatis untuk final quiz', 'is_public' => false]
                    );
                    $questionBankId = $autoBank->id;
                } else {
                    $questionBankId = $validated['question_bank_id'];
                }
            } else {
                // Fallback: Questions must belong to a bank in this schema
                $autoBank = QuestionBank::firstOrCreate(
                    ['title' => 'Soal Final Quiz (Auto)', 'created_by' => auth()->id()],
                    ['description' => 'Bank soal otomatis untuk final quiz', 'is_public' => false]
                );
                $questionBankId = $autoBank->id;
            }

            // Create question
            $question = Question::create([
                'question_bank_id' => $questionBankId,
                'question_text' => $validated['question_text'],
                'type' => $validated['question_type'],
                'points' => $validated['points'],
            ]);

            // Create options for multiple choice and true/false
            if (in_array($validated['question_type'], ['multiple_choice', 'true_false'])) {
                foreach ($validated['options'] as $index => $option) {
                    $isCorrect = false;
                    
                    // Check logic: either from is_correct field or correct_option index
                    if (isset($option['is_correct']) && ($option['is_correct'] == '1' || $option['is_correct'] === true)) {
                        $isCorrect = true;
                    } elseif (isset($validated['correct_option']) && $validated['correct_option'] == $index) {
                        $isCorrect = true;
                    }

                    QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $option['text'],
                        'is_correct' => $isCorrect,
                        'order' => $index + 1
                    ]);
                }
            }

            // Add to quiz
            $currentMaxUrutan = RelasiQuiz::where('quiz_id', $kursus->final_quiz_id)->max('urutan') ?? 0;
            RelasiQuiz::create([
                'quiz_id' => $kursus->final_quiz_id,
                'question_id' => $question->id,
                'urutan' => $currentMaxUrutan + 1,
            ]);
        });

        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', 'Soal baru berhasil ditambahkan!');
    }

    public function removeQuestion($kursusId, $questionId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return redirect()->back()->with('error', 'Final quiz tidak ditemukan.');
        }

        // Check if quiz is active
        if ($kursus->finalQuiz->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus soal karena quiz sedang aktif. Nonaktifkan quiz terlebih dahulu.');
        }

        DB::transaction(function() use ($kursus, $questionId) {
            // Find and delete the relation
            $relasi = RelasiQuiz::where('quiz_id', $kursus->final_quiz_id)
                ->where('question_id', $questionId)
                ->first();

            if ($relasi) {
                $deletedUrutan = $relasi->urutan;
                $relasi->delete();

                // Reorder remaining questions
                RelasiQuiz::where('quiz_id', $kursus->final_quiz_id)
                    ->where('urutan', '>', $deletedUrutan)
                    ->decrement('urutan');
            }
        });

        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', 'Soal berhasil dihapus!');
    }

    public function toggleActivation($kursusId)
    {
        $kursus = Kursus::with('finalQuiz.soal')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return redirect()->back()->with('error', 'Final quiz tidak ditemukan.');
        }

        // Check if quiz has questions
        if ($kursus->finalQuiz->soal->isEmpty() && !$kursus->finalQuiz->is_active) {
            return redirect()->back()->with('error', 'Tidak dapat mengaktifkan quiz. Quiz harus memiliki minimal 1 soal.');
        }

        $kursus->finalQuiz->update([
            'is_active' => !$kursus->finalQuiz->is_active
        ]);

        $status = $kursus->finalQuiz->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.courses.final-quiz.edit', $kursusId)
            ->with('success', "Final quiz berhasil {$status}!");
    }

    public function statistics($kursusId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);

        if (!$kursus->finalQuiz) {
            return redirect()->back()->with('error', 'Final quiz tidak ditemukan.');
        }

        // Get quiz attempts statistics
        $statistics = DB::table('quiz_attempts')
            ->select(
                'user_id',
                'users.name',
                'users.email',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('MAX(score) as best_score'),
                DB::raw('AVG(score) as avg_score'),
                DB::raw('MAX(CASE WHEN is_passed = true THEN 1 ELSE 0 END) as is_passed')
            )
            ->join('users', 'quiz_attempts.user_id', '=', 'users.id')
            ->where('quiz_attempts.kursus_id', $kursusId)
            ->where('quiz_attempts.quiz_id', $kursus->final_quiz_id)
            ->groupBy('user_id', 'users.name', 'users.email')
            ->orderBy('best_score', 'desc')
            ->get();

        // Calculate summary
        $summary = [
            'total_participants' => $statistics->count(),
            'passed' => $statistics->where('is_passed', 1)->count(),
            'failed' => $statistics->where('is_passed', 0)->count(),
            'avg_score' => $statistics->avg('avg_score') ?? 0,
        ];

        return view('admin.courses.final-quiz-statistics', compact('kursus', 'statistics', 'summary'));
    }
}
