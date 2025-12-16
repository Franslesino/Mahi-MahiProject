<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\MaterialCompletion;
use App\Models\Materi;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Sertifikat;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\JawabanPeserta;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
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
                $query->where('status', 'published')->orderBy('urutan', 'asc');
            },
        ]);

        $materialStats = $this->getMaterialStats($course, Auth::id());
        $materials = $materialStats['materials'];
        $materialIds = $materialStats['materialIds'];
        $completedIds = $materialStats['completedIds'];
        $progress = $materialStats['progress'];

        // Auto-generate certificate if all materials are completed
        if ($materialStats['isComplete']) {
            $this->generateCertificateIfNeeded($enrollment, $course);
        }

        $materialId = $request->query('material');
        $currentMaterial = null;

        if ($materialId) {
            $currentMaterial = $materials->firstWhere('id', $materialId);
        }
        if (!$currentMaterial) {
            $currentMaterial = $materials->first();
        }

        // Get final exam if course requires it
        $finalExam = null;
        $finalExamStatus = [];

        if ($course->final_quiz_id) {
            $finalExam = $course->finalQuiz;

            if ($finalExam) {
                // Get the latest quiz attempt for this student
                $latestAttempt = \App\Models\QuizAttempt::where('user_id', Auth::id())
                    ->where('quiz_id', $finalExam->id)
                    ->where('kursus_id', $course->id)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($latestAttempt) {
                    $finalExamStatus = [
                        'passed' => $latestAttempt->is_passed,
                        'score' => $latestAttempt->score,
                        'totalAttempts' => \App\Models\QuizAttempt::where('user_id', Auth::id())
                            ->where('quiz_id', $finalExam->id)
                            ->where('kursus_id', $course->id)
                            ->count(),
                        'maxAttempts' => $course->max_quiz_attempts ?? 3,
                        'canRetake' => !$latestAttempt->is_passed && (
                            (\App\Models\QuizAttempt::where('user_id', Auth::id())
                                ->where('quiz_id', $finalExam->id)
                                ->where('kursus_id', $course->id)
                                ->count() < ($course->max_quiz_attempts ?? 3))
                        ),
                    ];
                } else {
                    // No attempts yet
                    $finalExamStatus = [
                        'passed' => false,
                        'score' => null,
                        'totalAttempts' => 0,
                        'maxAttempts' => $course->max_quiz_attempts ?? 3,
                        'canRetake' => true,
                    ];
                }
            }
        }

        return view('student.learn', [
            'course' => $course,
            'sections' => $course->sections,
            'materials' => $materials,
            'currentMaterial' => $currentMaterial,
            'enrollment' => $enrollment,
            'completedIds' => $completedIds,
            'progress' => $progress,
            'finalExam' => $finalExam,
            'finalExamStatus' => $finalExamStatus,
            'materialsComplete' => $materialStats['isComplete'],
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
            $msg = 'Anda belum membeli kursus ini. Silakan beli terlebih dahulu.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 403)
                : redirect()->route('courses.show', $course)->with('error', $msg);
        }

        // Cari materi dari sections->materials, fallback ke relasi materi()
        $material = $course->sections()
            ->with(['materials' => fn($q) => $q->where('status', 'published')])
            ->get()
            ->flatMap->materials
            ->firstWhere('id', $materialId);

        if (!$material) {
            $material = $course->materi()->where('id', $materialId)->where('status', 'published')->first();
        }

        if (!$material) {
            $msg = 'Materi tidak ditemukan.';
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $msg], 404)
                : back()->with('error', $msg);
        }

        MaterialCompletion::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'materi_id' => $material->id,
            ],
            [
                'completed_at' => now(),
            ]
        );

        // Check if all materials are completed
        $materialStats = $this->getMaterialStats($course, Auth::id());

        // If all materials completed, generate certificate
        if ($materialStats['isComplete']) {
            $this->generateCertificateIfNeeded($enrollment, $course);
        }

        $msg = 'Materi ditandai selesai.';
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $msg,
                'progress' => $materialStats['progress'],
            ]);
        }

        return back()->with('success', $msg);
    }

    /**
     * Download material file with correct Content-Disposition
     */
    public function downloadMaterial(Request $request, Kursus $course, $materialId)
    {
        $enrollment = Enrollment::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->first();

        if (!$enrollment) {
            abort(403, 'Unauthorized');
        }

        $material = $course->sections()
            ->with('materials')
            ->get()
            ->flatMap->materials
            ->firstWhere('id', $materialId);

        if (!$material) {
            $material = $course->materi()->where('id', $materialId)->first();
        }

        if (!$material) {
            abort(404, 'Material not found');
        }

        $source = $material->file_url_full ?? $material->url_konten;
        if (!$source) {
            abort(404, 'File tidak ditemukan');
        }

        $filenameBase = Str::slug($material->judul ?? $material->title ?? 'material');

        // Jika sumber adalah URL eksternal, arahkan langsung
        if (Str::startsWith($source, ['http://', 'https://'])) {
            $ext = pathinfo(parse_url($source, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
            $downloadName = $filenameBase . ($ext ? '.' . $ext : '');
            return redirect()->away($source . (Str::contains($source, '?') ? '&' : '?') . 'download=' . $downloadName);
        }

        // If stored in public storage
        if (Str::startsWith($source, ['/storage/', 'storage/'])) {
            $path = ltrim(str_replace('/storage/', '', $source), '/');
            if (Storage::disk('public')->exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $downloadName = $filenameBase . ($ext ? '.' . $ext : '');
                return Storage::disk('public')->download($path, $downloadName);
            }
        }

        // If it's a local relative path
        if (!Str::startsWith($source, ['http://', 'https://'])) {
            $path = ltrim($source, '/');
            if (Storage::exists($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $downloadName = $filenameBase . ($ext ? '.' . $ext : '');
                return Storage::download($path, $downloadName);
            }
        }

        // If it's a remote URL, cannot force download reliably; just redirect
        if (filter_var($source, FILTER_VALIDATE_URL)) {
            return redirect()->away($source);
        }

        abort(404, 'File tidak ditemukan');
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

        $completion = MaterialCompletion::where('user_id', Auth::id())
            ->where('materi_id', $material->id)
            ->whereNotNull('answers_json')
            ->first();

        $passingScore = $assignment->passing_score ?? 60;

        if ($completion) {
            // Jika tidak minta retake, tampilkan hasil sebelumnya
            if (!$request->boolean('retake')) {
                $answers = $completion->answers_json ?? [];
                [$results, $score] = $this->computeQuizResults($assignment, $answers);
                $canRetake = $passingScore ? (($completion->score ?? 0) < $passingScore) : false;

                return view('student.quiz-result', [
                    'course' => $course,
                    'material' => $material,
                    'assignment' => $assignment,
                    'results' => $results,
                    'score' => $completion->score ?? $score,
                    'passingScore' => $passingScore,
                    'canRetake' => $canRetake,
                ]);
            }
        }

        return view('student.quiz', [
            'course' => $course,
            'material' => $material,
            'assignment' => $assignment,
            'passingScore' => $passingScore,
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

        $passingScore = $assignment->passing_score ?? 60;
        $canRetake = $passingScore ? ($score < $passingScore) : false;

        // Catat submission agar terlihat di admin/instruktur
        $attemptNumber = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', Auth::id())
            ->count() + 1;

        Submission::create([
            'assignment_id' => $assignment->id,
            'user_id' => Auth::id(),
            'attempt_number' => $attemptNumber,
            'answers' => $answers,
            'score' => $score,
            'percentage' => $score,
            'status' => 'graded',
            'started_at' => now(),
            'submitted_at' => now(),
            'graded_at' => now(),
        ]);

        return view('student.quiz-result', [
            'course' => $course,
            'material' => $material,
            'assignment' => $assignment,
            'results' => $results,
            'score' => $score,
            'passingScore' => $passingScore,
            'canRetake' => $canRetake,
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
                $correctOption = $question->options->firstWhere('is_correct', true);
                $correctText = $correctOption?->option_text;
                $selectedOption = $question->options->firstWhere('id', $userAnswer);
                $userText = $selectedOption?->option_text;
                $isCorrect = $selectedOption && $selectedOption->is_correct;
                if ($isCorrect) {
                    $correctCount++;
                }
            } else {
                $userText = $userAnswer;
                $correctText = $question->correct_answer;
                if ($correctText !== null && $userText !== null) {
                    $isCorrect = trim(mb_strtolower($userText)) === trim(mb_strtolower($correctText));
                    if ($isCorrect) {
                        $correctCount++;
                    }
                } else {
                    $isCorrect = null;
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
     * Ambil statistik materi untuk kursus tertentu
     */
    private function getMaterialStats(Kursus $course, int $userId): array
    {
        $course->loadMissing([
            'sections.materials' => function ($query) {
                $query->where('status', 'published')->orderBy('urutan', 'asc');
            },
        ]);

        $materials = $course->sections
            ->flatMap(function ($section) {
                return $section->materials;
            })
            ->values();

        $materialIds = $materials->pluck('id')->toArray();
        $completedIds = [];
        if (!empty($materialIds)) {
            $completedIds = MaterialCompletion::where('user_id', $userId)
                ->whereIn('materi_id', $materialIds)
                ->pluck('materi_id')
                ->toArray();
        }

        $totalMaterials = $materials->count();
        $progress = $totalMaterials > 0 ? min(100, round((count($completedIds) / $totalMaterials) * 100)) : 0;

        return [
            'materials' => $materials,
            'materialIds' => $materialIds,
            'completedIds' => $completedIds,
            'totalMaterials' => $totalMaterials,
            'progress' => $progress,
            'isComplete' => $totalMaterials > 0 && count($completedIds) >= $totalMaterials,
        ];
    }

    /**
     * My courses page
     */
    public function myCourses()
    {
        $certificateFk = Enrollment::getCertificateForeignKey();

        $enrollments = Enrollment::where('user_id', Auth::id())
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->with([
                'kursus' => function ($query) {
                    $query->withCount('materi');
                },
                'kursus.pembuat',
                'kursus.materi', // Fallback untuk kursus tanpa sections
                // Load sections.materials seperti di learn page untuk konsistensi penghitungan progress
                'kursus.sections.materials' => function ($query) {
                    $query->where('status', 'published')->orderBy('urutan', 'asc');
                },
                // Load certificate only if FK is known to avoid invalid column errors
                ...($certificateFk ? ['sertifikat'] : []),
            ])
            ->latest('tanggal_daftar')
            ->get();

        // Pastikan kursus yang sudah selesai memiliki sertifikat (perbaikan data lama)
        if ($certificateFk) {
            foreach ($enrollments as $enrollment) {
                if (in_array($enrollment->status_pendaftaran, ['completed']) && !$enrollment->sertifikat) {
                    // Generate sertifikat jika belum ada
                    try {
                        $this->generateCertificateIfNeeded($enrollment, $enrollment->kursus);
                        $enrollment->load('sertifikat');
                    } catch (\Throwable $e) {
                        // Jangan hentikan halaman; sertifikat akan tetap dianggap belum tersedia
                    }
                }
            }
        }

        // Get pending transactions (not expired and not cancelled)
        $pendingTransactions = \App\Models\Transaction::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->where('payment_deadline', '>', now())
            ->with([
                'kursus' => function ($query) {
                    $query->withCount('materi');
                },
                'kursus.pembuat'
            ])
            ->latest()
            ->get();

        return view('my-courses', compact('enrollments', 'pendingTransactions'));
    }

    /**
     * Generate certificate if needed
     */
    private function generateCertificateIfNeeded($enrollment, $course)
    {
        // Check if certificate already exists
        if ($enrollment->sertifikat) {
            return;
        }

        // Pastikan semua materi selesai
        $materialStats = $this->getMaterialStats($course, $enrollment->user_id ?? Auth::id());
        if (!$materialStats['isComplete']) {
            return;
        }

        // Jika kursus memiliki final quiz yang wajib, pastikan sudah lulus
        if ($course->require_final_quiz && $course->final_quiz_id) {
            $hasPassed = \App\Models\QuizAttempt::where('user_id', $enrollment->user_id ?? Auth::id())
                ->where('quiz_id', $course->final_quiz_id)
                ->where('kursus_id', $course->id)
                ->where('is_passed', true)
                ->exists();

            if (!$hasPassed) {
                return; // Belum lulus final quiz, tidak bisa generate sertifikat
            }
        }

        $user = Auth::user();
        $certificateNumber = 'CERT-' . strtoupper(uniqid());

        // Get instructor/signer name from course signature or fallback to instructor
        $signerName = $course->signature_name ?? $course->instructor->name ?? $course->pembuat->name ?? 'Instructor';

        // Get signature image URL
        $signatureImageUrl = $course->signature_image_url;

        // Generate certificate image
        $certificatePath = $this->generateCertificateImage(
            $user->name,
            $course->judul ?? $course->title,
            $certificateNumber,
            now()->format('F d, Y'),
            $signerName,
            $signatureImageUrl
        );

        // Create certificate record with flexible column detection
        $this->storeCertificateRecord($enrollment->id, $certificateNumber, $certificatePath);

        // Update enrollment status to completed
        $enrollment->update(['status_pendaftaran' => 'completed']);
    }

    /**
     * Generate certificate HTML (for now, just save path reference)
     * In production, you would use a service like Puppeteer or wkhtmltopdf
     */
    private function generateCertificateImage($studentName, $courseName, $certificateNumber, $date, $signerName, $signatureImageUrl = null)
    {
        // For now, we'll just create a path reference
        // In a real application, you'd generate an actual image/PDF here
        // using tools like: Puppeteer, wkhtmltopdf, or Intervention Image

        $filename = 'certificates/' . $certificateNumber . '.html';

        // Create certificates directory if it doesn't exist
        $certificatesPath = storage_path('app/public/certificates');
        if (!file_exists($certificatesPath)) {
            mkdir($certificatesPath, 0755, true);
        }

        // Generate certificate HTML
        $html = $this->getCertificateHtml($studentName, $courseName, $certificateNumber, $date, $signerName, $signatureImageUrl);

        // Save HTML file (in production, convert this to PDF/PNG)
        file_put_contents(storage_path('app/public/' . $filename), $html);

        return $filename;
    }

    /**
     * Get certificate HTML template
     */
    private function getCertificateHtml($studentName, $courseName, $certificateNumber, $date, $signerName, $signatureImageUrl = null)
    {
        // Build signature HTML - use image if available, otherwise use text
        $signatureHtml = $signatureImageUrl
            ? "<img src=\"{$signatureImageUrl}\" alt=\"Signature\" class=\"signature-image\">"
            : "<div class=\"signature\">{$signerName}</div>";

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Certificate - {$certificateNumber}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Georgia', serif;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        .certificate {
            width: 842px;
            height: 595px;
            background: white;
            border: 8px solid #005F56;
            position: relative;
            padding: 40px 60px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        .inner-border {
            border: 2px solid #00897B;
            height: 100%;
            padding: 30px;
            position: relative;
        }
        .circle-left {
            position: absolute;
            width: 150px;
            height: 150px;
            background: #4CAF50;
            border-radius: 50%;
            top: 120px;
            left: 40px;
            opacity: 0.3;
        }
        .circle-right {
            position: absolute;
            width: 150px;
            height: 150px;
            background: #4CAF50;
            border-radius: 50%;
            bottom: 80px;
            right: 40px;
            opacity: 0.3;
        }
        .ellipse-top {
            position: absolute;
            width: 220px;
            height: 120px;
            background: #00897B;
            border-radius: 50%;
            top: 50px;
            right: 80px;
            opacity: 0.3;
        }
        .content {
            position: relative;
            z-index: 10;
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .title {
            font-size: 42px;
            color: #005F56;
            font-weight: bold;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .subtitle {
            font-size: 18px;
            color: #374151;
            margin-bottom: 15px;
        }
        .name {
            font-size: 38px;
            color: #00897B;
            font-weight: bold;
            margin: 15px 0 20px;
            font-style: italic;
        }
        .description {
            font-size: 15px;
            color: #374151;
            margin: 10px 0;
            line-height: 1.5;
        }
        .course-name {
            font-size: 24px;
            color: #005F56;
            font-weight: bold;
            margin: 15px 0 10px;
        }
        .date {
            font-size: 13px;
            color: #6b7280;
            margin: 8px 0 20px;
        }
        .certificate-number {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .signature-section {
            margin-top: 20px;
        }
        .signature-image {
            max-height: 60px;
            max-width: 180px;
            object-fit: contain;
            margin-bottom: 5px;
        }
        .signature {
            font-size: 26px;
            color: #00897B;
            font-style: italic;
            margin-bottom: 3px;
            font-weight: 600;
        }
        .signature-name {
            font-size: 14px;
            color: #005F56;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .signature-line {
            width: 180px;
            height: 1px;
            background: #374151;
            margin: 0 auto 5px;
        }
        .signature-date {
            font-size: 12px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <div class="circle-left"></div>
        <div class="circle-right"></div>
        <div class="ellipse-top"></div>
        <div class="inner-border">
            <div class="content">
                <div class="title">CERTIFICATE OF<br>COMPLETION</div>
                <div class="subtitle">This Certifies that</div>
                <div class="name">{$studentName}</div>
                <div class="description">
                    Has Successfully Completed the UpGreenius Training<br>
                    Program, Entitled
                </div>
                <div class="course-name">{$courseName}</div>
                <div class="date">on {$date}</div>
                <div class="certificate-number">{$certificateNumber}</div>
                <div class="signature-section">
                    {$signatureHtml}
                    <div class="signature-line"></div>
                    <div class="signature-name">{$signerName}</div>
                    <div class="signature-date">on {$date}</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
    }

    /**
     * Download certificate
     */
    public function downloadCertificate(Enrollment $enrollment)
    {
        if ($enrollment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $certificate = $enrollment->sertifikat;

        if (!$certificate) {
            return redirect()->back()->with('error', 'Sertifikat belum tersedia.');
        }

        $certificateNumber = $certificate->kode_sertifikat ?? $certificate->nomor_sertifikat ?? 'certificate';

        // Jika sudah PDF tersimpan, langsung download
        $filePath = str_replace('/storage/', '', $certificate->url_unduhan);
        if (str_ends_with(strtolower($certificate->url_unduhan), '.pdf') && Storage::disk('public')->exists($filePath)) {
            return Storage::disk('public')->download($filePath, $certificateNumber . '.pdf');
        }

        // Ambil HTML sertifikat (atau buat ulang jika hilang)
        $htmlContent = null;
        if (Storage::disk('public')->exists($filePath)) {
            $htmlContent = Storage::disk('public')->get($filePath);
        } elseif (filter_var($certificate->url_unduhan, FILTER_VALIDATE_URL)) {
            $htmlContent = @file_get_contents($certificate->url_unduhan);
        }

        // Jika file hilang, regenerasi HTML lalu perbarui url_unduhan
        if (!$htmlContent) {
            $course = $enrollment->kursus ?? $enrollment->course ?? null;
            $user = $enrollment->user ?? Auth::user();

            if ($course && $user) {
                $instructorName = $course->pembuat->name ?? $course->instructor->name ?? 'Instructor';
                $issuedDate = $certificate->tanggal_terbit ?? $certificate->tanggal_diterbitkan ?? $certificate->created_at ?? now();
                $issuedDateString = $issuedDate instanceof \Illuminate\Support\Carbon
                    ? $issuedDate->format('F d, Y')
                    : now()->format('F d, Y');

                $newPath = $this->generateCertificateImage(
                    $user->name ?? 'Student',
                    $course->judul ?? $course->title ?? 'Course',
                    $certificateNumber,
                    $issuedDateString,
                    $instructorName
                );

                $certificate->url_unduhan = Storage::url($newPath);
                $certificate->save();

                if (Storage::disk('public')->exists($newPath)) {
                    $htmlContent = Storage::disk('public')->get($newPath);
                }
            }
        }

        if (!$htmlContent) {
            return redirect()->back()->with('error', 'File sertifikat tidak ditemukan.');
        }

        // Render ke PDF dan unduh
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($htmlContent)->setPaper('a4', 'landscape');
        return $pdf->download($certificateNumber . '.pdf');
    }

    /**
     * Stream certificate inline (hindari 403 akses langsung ke storage)
     */
    public function streamCertificate(Enrollment $enrollment)
    {
        if ($enrollment->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $certificate = $enrollment->sertifikat;
        if (!$certificate || !$certificate->url_unduhan) {
            abort(404, 'Certificate not found');
        }

        $url = $certificate->url_unduhan;
        $publicPath = str_replace('/storage/', '', $url);

        if (Storage::disk('public')->exists($publicPath)) {
            $mime = Storage::disk('public')->mimeType($publicPath) ?? 'application/octet-stream';
            $stream = Storage::disk('public')->readStream($publicPath);
            if (!$stream) {
                abort(404, 'Certificate file not readable');
            }

            return response()->stream(function () use ($stream) {
                fpassthru($stream);
            }, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline; filename="' . basename($publicPath) . '"',
            ]);
        }

        // If stored remotely, redirect
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return redirect()->away($url);
        }

        abort(404, 'Certificate file not found');
    }

    /**
     * Store certificate record adjusting to available columns
     */
    private function storeCertificateRecord(int $enrollmentId, string $certificateNumber, string $certificatePath): void
    {
        $table = (new Sertifikat())->getTable();
        $data = [
            'url_unduhan' => Storage::url($certificatePath),
        ];

        // Foreign key
        $fkCandidates = [
            'enrollment_id',
            'enrollments_id',
            'enrollmentid',
            'enrollmentsid',
            'enroll_id',
            'enrollment',
        ];

        foreach ($fkCandidates as $col) {
            if (Schema::hasColumn($table, $col)) {
                $data[$col] = $enrollmentId;
                break;
            }
        }

        // Certificate number column fallback
        if (Schema::hasColumn($table, 'nomor_sertifikat')) {
            $data['nomor_sertifikat'] = $certificateNumber;
        }
        if (Schema::hasColumn($table, 'kode_sertifikat')) {
            $data['kode_sertifikat'] = $certificateNumber;
        }

        // Issued date column fallback
        $issuedAt = now();
        if (Schema::hasColumn($table, 'tanggal_terbit')) {
            $data['tanggal_terbit'] = $issuedAt;
        }
        if (Schema::hasColumn($table, 'tanggal_diterbitkan')) {
            $data['tanggal_diterbitkan'] = $issuedAt;
        }

        Sertifikat::create($data);
    }
}
