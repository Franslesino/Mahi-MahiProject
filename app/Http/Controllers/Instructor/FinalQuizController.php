<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Quiz;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FinalQuizController extends Controller
{
    /**
     * Halaman setting final quiz untuk kursus
     */
    public function edit($kursusId)
    {
        $kursus = Kursus::with(['finalQuiz', 'quizzes'])->findOrFail($kursusId);
        
        // Pastikan user adalah instruktur atau admin
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        // Ambil semua quiz yang bisa dijadikan final quiz
        $availableQuizzes = Quiz::where('kursus_id', $kursusId)
            ->where(function($query) {
                $query->where('is_final_quiz', true)
                      ->orWhereNull('is_final_quiz');
            })
            ->get();

        // Question banks (publik + milik instruktur) untuk import/simpan soal
        $questionBanks = \App\Models\QuestionBank::with('questions.options')
            ->where(function($q) {
                $q->where('created_by', Auth::id())
                  ->orWhere('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('instructor.courses.final-quiz-settings', compact('kursus', 'availableQuizzes', 'questionBanks'));
    }

    /**
     * Update setting final quiz
     */
    public function update(Request $request, $kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        $validated = $request->validate([
            'require_final_quiz' => 'required|boolean',
            'final_quiz_id' => 'nullable|exists:quiz,id',
            'min_passing_score' => 'required|numeric|min:0|max:100',
            'max_quiz_attempts' => 'required|integer|min:1|max:10',
            'durasi_quiz' => 'nullable|integer|min:1',
        ]);

        // Jika require_final_quiz true, maka final_quiz_id harus ada
        if ($validated['require_final_quiz'] && empty($validated['final_quiz_id'])) {
            return back()->withErrors(['final_quiz_id' => 'Silakan pilih quiz untuk dijadikan final quiz.']);
        }

        // Jika tidak require final quiz, set final_quiz_id ke null
        if (!$validated['require_final_quiz']) {
            $validated['final_quiz_id'] = null;
        }

        DB::beginTransaction();
        try {
            // Update kursus
            $kursus->update($validated);

            // Update quiz yang dipilih sebagai final quiz
            if ($validated['final_quiz_id']) {
                Quiz::where('id', $validated['final_quiz_id'])->update([
                    'is_final_quiz' => true,
                    'passing_grade' => $validated['min_passing_score'],
                    'kesempatan_mengerjakan' => $validated['max_quiz_attempts'],
                    'durasi_quiz' => $validated['durasi_quiz']
                ]);
                
                // Set quiz lain yang bukan final quiz
                Quiz::where('kursus_id', $kursusId)
                    ->where('id', '!=', $validated['final_quiz_id'])
                    ->update(['is_final_quiz' => false]);
            } else {
                // Jika tidak ada final quiz, set semua quiz di kursus ini is_final_quiz = false
                Quiz::where('kursus_id', $kursusId)->update(['is_final_quiz' => false]);
            }

            DB::commit();

            return redirect()
                ->route('instructor.courses.final-quiz.edit', $kursusId)
                ->with('success', 'Pengaturan final quiz berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Melihat statistik final quiz
     */
    public function statistics($kursusId)
    {
        $kursus = Kursus::with(['finalQuiz'])->findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        if (!$kursus->final_quiz_id) {
            return redirect()
                ->route('instructor.courses.final-quiz.edit', $kursusId)
                ->with('info', 'Kursus ini belum memiliki final quiz.');
        }

        // Ambil statistik attempts
        $attempts = DB::table('quiz_attempts')
            ->where('kursus_id', $kursusId)
            ->where('quiz_id', $kursus->final_quiz_id)
            ->select(
                'user_id',
                DB::raw('COUNT(*) as total_attempts'),
                DB::raw('MAX(score) as best_score'),
                DB::raw('AVG(score) as avg_score'),
                DB::raw('MAX(CASE WHEN is_passed = true THEN 1 ELSE 0 END) as has_passed')
            )
            ->groupBy('user_id')
            ->get();

        // Ambil data user
        $userIds = $attempts->pluck('user_id');
        $users = DB::table('users')
            ->whereIn('id', $userIds)
            ->select('id', 'name', 'email')
            ->get()
            ->keyBy('id');

        // Gabungkan data
        $statistics = $attempts->map(function($attempt) use ($users) {
            $attempt->user = $users->get($attempt->user_id);
            return $attempt;
        });

        return view('instructor.courses.final-quiz-statistics', compact('kursus', 'statistics'));
    }

    /**
     * Helper untuk cek apakah user bisa manage course
     */
    private function canManageCourse($kursus)
    {
        $user = Auth::user();
        
        // Admin bisa manage semua course
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instruktur hanya bisa manage course yang dia ajar
        if ($user->role === 'pengajar' || $user->role === 'instructor') {
            return $kursus->instructor_id == $user->id || $kursus->pembuat == $user->id;
        }
        
        return false;
    }

    /**
     * Halaman untuk membuat quiz baru sebagai final quiz
     */
    public function createQuiz($kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        // Prevent creating multiple final quizzes - only 1 allowed per course
        if ($kursus->final_quiz_id) {
            return redirect()->route('instructor.courses.final-quiz.edit', $kursusId)
                ->with('error', 'Kursus ini sudah memiliki final quiz. Setiap kursus hanya dapat memiliki 1 final quiz.');
        }

        // Ambil Question Banks (sistem terpadu)
        $questionBanks = \App\Models\QuestionBank::with(['questions.options'])
            ->where(function($query) {
                $query->where('created_by', Auth::id())
                      ->orWhere('is_public', true);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('instructor.courses.create-final-quiz', compact('kursus', 'questionBanks'));
    }

    /**
     * Simpan quiz baru sebagai final quiz
     */
    public function storeQuiz(Request $request, $kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        // Check if final quiz already exists - only 1 quiz allowed per course
        if ($kursus->final_quiz_id) {
            return back()->withErrors(['error' => 'Kursus ini sudah memiliki final quiz. Setiap kursus hanya dapat memiliki 1 final quiz. Gunakan fitur tambah soal untuk menambah pertanyaan.']);
        }

        $validated = $request->validate([
            'judul_quiz' => 'required|string|max:255',
            'durasi_quiz' => 'nullable|integer|min:1',
            'kesempatan_mengerjakan' => 'required|integer|min:1|max:10',
            'passing_grade' => 'required|numeric|min:0|max:100',
            'question_bank_questions' => 'nullable|array',
            'question_bank_questions.*' => 'exists:questions,id',
        ]);

        DB::beginTransaction();
        try {
            // Buat quiz baru
            $quiz = Quiz::create([
                'judul_quiz' => $validated['judul_quiz'],
                'durasi_quiz' => $validated['durasi_quiz'] ?? null,
                'kesempatan_mengerjakan' => $validated['kesempatan_mengerjakan'],
                'passing_grade' => $validated['passing_grade'],
                'kursus_id' => $kursusId,
                'is_final_quiz' => true,
                'is_active' => true,
            ]);

            $urutan = 1;
            
            // Attach soal dari Question Banks
            if (!empty($validated['question_bank_questions'])) {
                foreach ($validated['question_bank_questions'] as $questionId) {
                    DB::table('relasi_quiz')->insert([
                        'quiz_id' => $quiz->id,
                        'question_id' => $questionId,
                        'urutan' => $urutan++,
                        'is_active' => true,
                    ]);
                }
            }

            // Set sebagai final quiz untuk kursus
            $kursus->update([
                'final_quiz_id' => $quiz->id,
                'require_final_quiz' => true,
                'min_passing_score' => $validated['passing_grade'],
                'max_quiz_attempts' => $validated['kesempatan_mengerjakan'],
            ]);

            // Set quiz lain di kursus ini menjadi bukan final quiz
            Quiz::where('kursus_id', $kursusId)
                ->where('id', '!=', $quiz->id)
                ->update(['is_final_quiz' => false]);

            DB::commit();

            return redirect()
                ->route('instructor.courses.final-quiz.edit', $kursusId)
                ->with('success', 'Final quiz berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Import soal dari bank soal ke quiz yang sudah ada
     */
    public function importFromBankSoal(Request $request, $kursusId)
    {
        $kursus = Kursus::with('finalQuiz')->findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        if (!$kursus->final_quiz_id) {
            return back()->withErrors(['error' => 'Kursus ini belum memiliki final quiz.']);
        }

        // Check if quiz is active
        if ($kursus->finalQuiz->is_active) {
            return back()->withErrors(['error' => 'Final quiz sedang aktif. Nonaktifkan terlebih dahulu untuk mengubah soal.']);
        }

        $validated = $request->validate([
            'questions' => 'required|array',
            // Validasi ke tabel questions (soal dari question bank)
            'questions.*' => 'exists:questions,id',
        ]);

        DB::beginTransaction();
        try {
            $quiz = $kursus->finalQuiz;
            
            // Dapatkan urutan terakhir
            $lastUrutan = DB::table('relasi_quiz')
                ->where('quiz_id', $quiz->id)
                ->max('urutan') ?? 0;

            // Tambahkan soal baru
            foreach ($validated['questions'] as $index => $soalId) {
                // Cek apakah soal sudah ada
                $exists = DB::table('relasi_quiz')
                    ->where('quiz_id', $quiz->id)
                    ->where('question_id', $soalId)
                    ->exists();

                if (!$exists) {
                    DB::table('relasi_quiz')->insert([
                        'quiz_id' => $quiz->id,
                        'question_id' => $soalId,
                        'urutan' => $lastUrutan + $index + 1,
                        'is_active' => true,
                    ]);
                }
            }

            DB::commit();

            return back()->with('success', 'Soal berhasil diimpor ke final quiz!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove question from final quiz
     */
    public function removeQuestion($kursusId, $questionId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        if (!$kursus->final_quiz_id) {
            return back()->withErrors(['error' => 'Kursus ini tidak memiliki final quiz.']);
        }

        // Check if quiz is active
        if ($kursus->finalQuiz->is_active) {
            return back()->withErrors(['error' => 'Final quiz sedang aktif. Nonaktifkan terlebih dahulu untuk mengubah soal.']);
        }

        try {
            // Delete from relasi_quiz
            DB::table('relasi_quiz')
                ->where('quiz_id', $kursus->final_quiz_id)
                ->where('question_id', $questionId)
                ->delete();

            // Reorder remaining questions
            $remainingQuestions = DB::table('relasi_quiz')
                ->where('quiz_id', $kursus->final_quiz_id)
                ->orderBy('urutan')
                ->get();

            foreach ($remainingQuestions as $index => $question) {
                DB::table('relasi_quiz')
                    ->where('id', $question->id)
                    ->update(['urutan' => $index + 1]);
            }

            return back()->with('success', 'Soal berhasil dihapus dari final quiz!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Toggle activation status of final quiz
     */
    public function toggleActivation($kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        if (!$kursus->final_quiz_id) {
            return back()->withErrors(['error' => 'Kursus ini tidak memiliki final quiz.']);
        }

        $quiz = $kursus->finalQuiz;
        
        // Check if quiz has questions before activating
        if (!$quiz->is_active && $quiz->soal()->count() == 0) {
            return back()->withErrors(['error' => 'Tidak bisa mengaktifkan final quiz tanpa soal. Tambahkan soal terlebih dahulu.']);
        }

        // Item #23: Check if there are students currently taking the quiz before deactivating
        $activeAttempts = [];
        if ($quiz->is_active) {
            // Quiz is being deactivated, check for in-progress attempts
            $inProgressAttempts = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                ->where('kursus_id', $kursusId)
                ->whereNull('completed_at')
                ->with('user:id,name,email')
                ->get();
            
            if ($inProgressAttempts->count() > 0) {
                $studentNames = $inProgressAttempts->map(fn($a) => $a->user->name ?? $a->user->email ?? 'Unknown')->implode(', ');
                $activeAttempts = $inProgressAttempts;
                
                // Still deactivate but add warning
                $quiz->is_active = false;
                $quiz->save();
                
                return back()->with('warning', 
                    'Final quiz berhasil dinonaktifkan. PERHATIAN: ' . $inProgressAttempts->count() . 
                    ' peserta sedang mengerjakan quiz (' . $studentNames . '). ' .
                    'Jawaban mereka yang belum diselesaikan tidak akan bisa dilanjutkan.'
                );
            }
        }

        $quiz->is_active = !$quiz->is_active;
        $quiz->save();

        $status = $quiz->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', 'Final quiz berhasil ' . $status . '.');
    }

    /**
     * Store new question directly to final quiz
     */
    public function storeNewQuestion(Request $request, $kursusId)
    {
        $kursus = Kursus::findOrFail($kursusId);
        
        if (!$this->canManageCourse($kursus)) {
            abort(403, 'Anda tidak memiliki akses untuk mengelola kursus ini.');
        }

        if (!$kursus->final_quiz_id) {
            return back()->withErrors(['error' => 'Kursus ini tidak memiliki final quiz.']);
        }

        // Check if quiz is active
        if ($kursus->finalQuiz->is_active) {
            return back()->withErrors(['error' => 'Final quiz sedang aktif. Nonaktifkan terlebih dahulu untuk menambah soal.']);
        }

        $validated = $request->validate([
            'type' => 'required|in:multiple_choice,true_false,essay',
            'question_text' => 'required|string',
            'points' => 'required|numeric|min:1',
            'options' => 'required_unless:type,essay|array|min:2',
            'options.*' => 'required_unless:type,essay|string',
            'correct_option' => 'required_unless:type,essay|numeric',
            'save_to_bank' => 'nullable|boolean',
            'question_bank_id' => 'nullable',
        ]);

        DB::beginTransaction();
        try {
            $questionBankId = null;

            // If save to bank is checked, use the selected bank
            if ($request->save_to_bank) {
                if (($validated['question_bank_id'] ?? null) === 'new') {
                    $autoBank = \App\Models\QuestionBank::firstOrCreate(
                        [
                            'created_by' => Auth::id(),
                            'title' => 'Soal Final Quiz (Auto)',
                        ],
                        [
                            'description' => 'Bank soal otomatis untuk pertanyaan final quiz',
                            'is_public' => false,
                        ]
                    );
                    $questionBankId = $autoBank->id;
                } else {
                    $questionBankId = $validated['question_bank_id'];
                }
            } else {
                // Create/get a default "Final Quiz Questions" bank for this instructor
                $defaultBank = \App\Models\QuestionBank::firstOrCreate([
                    'created_by' => Auth::id(),
                    'title' => 'Soal Final Quiz (Auto)',
                ], [
                    'description' => 'Bank soal otomatis untuk pertanyaan final quiz',
                    'is_public' => false,
                ]);
                $questionBankId = $defaultBank->id;
            }

            // Create question
            $question = \App\Models\Question::create([
                'question_bank_id' => $questionBankId,
                'type' => $validated['type'],
                'question_text' => $validated['question_text'],
                'points' => $validated['points'] ?? 1,
            ]);

            // Create options if not essay
            if ($validated['type'] !== 'essay' && !empty($validated['options'])) {
                foreach ($validated['options'] as $index => $optionText) {
                    \App\Models\QuestionOption::create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => ($index == $validated['correct_option']),
                    ]);
                }
            }

            // Get current max urutan
            $maxUrutan = DB::table('relasi_quiz')
                ->where('quiz_id', $kursus->final_quiz_id)
                ->max('urutan') ?? 0;

            // Add to final quiz
            DB::table('relasi_quiz')->insert([
                'quiz_id' => $kursus->final_quiz_id,
                'question_id' => $question->id,
                'urutan' => $maxUrutan + 1,
                'is_active' => true,
            ]);

            DB::commit();

            $message = 'Soal berhasil ditambahkan ke final quiz!';
            if ($request->save_to_bank) {
                $message .= ' Soal juga telah disimpan ke bank soal yang dipilih.';
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }
}
