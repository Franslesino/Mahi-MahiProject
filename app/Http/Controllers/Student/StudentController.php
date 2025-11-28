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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Schema;

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

        // Auto-generate certificate if course is completed
        if ($progress >= 100) {
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

        // Check if all materials are completed
        $course->load('sections.materials');
        $materials = $course->sections
            ->flatMap(function ($section) {
                return $section->materials;
            })
            ->values();

        $materialIds = $materials->pluck('id')->toArray();
        $completedCount = MaterialCompletion::where('user_id', Auth::id())
            ->whereIn('materi_id', $materialIds)
            ->count();

        // If all materials completed, generate certificate
        if ($completedCount >= count($materialIds) && count($materialIds) > 0) {
            $this->generateCertificateIfNeeded($enrollment, $course);
        }

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

        return view('my-courses', compact('enrollments'));
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

        $user = Auth::user();
        $certificateNumber = 'CERT-' . strtoupper(uniqid());
        
        // Get instructor name
        $instructorName = $course->pembuat->name ?? $course->instructor->name ?? 'Bagus Fransislino';

        // Generate certificate image
        $certificatePath = $this->generateCertificateImage(
            $user->name,
            $course->judul ?? $course->title,
            $certificateNumber,
            now()->format('F d, Y'),
            $instructorName
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
    private function generateCertificateImage($studentName, $courseName, $certificateNumber, $date, $instructorName)
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
        $html = $this->getCertificateHtml($studentName, $courseName, $certificateNumber, $date, $instructorName);
        
        // Save HTML file (in production, convert this to PDF/PNG)
        file_put_contents(storage_path('app/public/' . $filename), $html);

        return $filename;
    }

    /**
     * Get certificate HTML template
     */
    private function getCertificateHtml($studentName, $courseName, $certificateNumber, $date, $instructorName)
    {
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
            border: 8px solid #1e40af;
            position: relative;
            padding: 40px 60px;
            box-shadow: 0 0 30px rgba(0,0,0,0.1);
        }
        .inner-border {
            border: 2px solid #3b82f6;
            height: 100%;
            padding: 30px;
            position: relative;
        }
        .circle-left {
            position: absolute;
            width: 150px;
            height: 150px;
            background: #f59e0b;
            border-radius: 50%;
            top: 120px;
            left: 40px;
            opacity: 0.8;
        }
        .circle-right {
            position: absolute;
            width: 150px;
            height: 150px;
            background: #f59e0b;
            border-radius: 50%;
            bottom: 80px;
            right: 40px;
            opacity: 0.8;
        }
        .ellipse-top {
            position: absolute;
            width: 220px;
            height: 120px;
            background: #5b7fc7;
            border-radius: 50%;
            top: 50px;
            right: 80px;
            opacity: 0.8;
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
            font-size: 48px;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 25px;
            text-transform: uppercase;
            letter-spacing: 4px;
        }
        .subtitle {
            font-size: 18px;
            color: #374151;
            margin-bottom: 15px;
        }
        .name {
            font-size: 42px;
            color: #5b7fc7;
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
            font-size: 26px;
            color: #1e3a8a;
            font-weight: bold;
            margin: 20px 0 10px;
        }
        .date {
            font-size: 13px;
            color: #6b7280;
            margin: 8px 0 25px;
        }
        .certificate-number {
            font-size: 11px;
            color: #9ca3af;
            letter-spacing: 1px;
            margin-bottom: 15px;
        }
        .signature-section {
            margin-top: 25px;
        }
        .signature {
            font-size: 28px;
            color: #5b7fc7;
            font-style: italic;
            margin-bottom: 3px;
            font-weight: 600;
        }
        .signature-name {
            font-size: 15px;
            color: #1e3a8a;
            font-weight: bold;
            margin-bottom: 3px;
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
                    Has Successfully Completed the Webace Training<br>
                    Program, Entitled
                </div>
                <div class="course-name">{$courseName}</div>
                <div class="date">on {$date}</div>
                <div class="certificate-number">{$certificateNumber}</div>
                <div class="signature-section">
                    <div class="signature">{$instructorName}</div>
                    <div class="signature-name">Mr. {$instructorName}</div>
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

        // Ambil HTML sertifikat
        $htmlContent = null;
        if (Storage::disk('public')->exists($filePath)) {
            $htmlContent = Storage::disk('public')->get($filePath);
        } elseif (filter_var($certificate->url_unduhan, FILTER_VALIDATE_URL)) {
            $htmlContent = @file_get_contents($certificate->url_unduhan);
        }

        if (!$htmlContent) {
            return redirect()->back()->with('error', 'File sertifikat tidak ditemukan.');
        }

        // Render ke PDF dan unduh
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadHTML($htmlContent)->setPaper('a4', 'landscape');
        return $pdf->download($certificateNumber . '.pdf');
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
