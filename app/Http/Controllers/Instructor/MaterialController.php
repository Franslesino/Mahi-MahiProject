<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\CourseSection; // ← TAMBAHKAN INI
use App\Models\QuestionBank;
use App\Models\MaterialCompletion;
use App\Models\Assignment;
use App\Models\Submission;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class MaterialController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();

        $courses = Kursus::where(function($query) use ($instructorId) {
                $query->where('pembuat', $instructorId)
                      ->orWhere('instructor_id', $instructorId);
            })
            ->withCount([
                'materi',
                'enrollments as enrollments_count',
            ])
            ->latest()
            ->get();

        return view('instructor.courses', compact('courses'));
    }

    public function show(Kursus $course)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        // Load sections dengan materials
        $sections = $course->sections()
            ->with(['materials' => function($query) {
                $query->orderBy('urutan')->with('assignment');
            }])
            ->orderBy('order')
            ->get();

        // Stats
        $materialIds = $course->materi()->pluck('id');
        $totalMaterials = $materialIds->count(); // angka display apa adanya
        $totalMaterialsForProgress = max(1, $totalMaterials); // divisor progress supaya tidak 0
        $totalVideos = $course->materi()->where('type', 'video')->count();
        $studentsCount = $course->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->distinct('user_id')
            ->count('user_id');

        $questionBanks = QuestionBank::where('created_by', Auth::id())
            ->orWhere('is_public', true)
            ->withCount('questions')
            ->get();

        // Progress & score per participant
        $passingScore = Assignment::where('kursus_id', $course->id)->firstWhere('passing_score')?->passing_score ?? 60;

        $completionCounts = MaterialCompletion::select('user_id', DB::raw('COUNT(*) as completed_count'))
            ->whereIn('materi_id', $materialIds)
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $assignmentIds = Assignment::where('kursus_id', $course->id)->pluck('id');
        $assignmentCount = $assignmentIds->count();
        $submissionStats = collect();
        if ($assignmentIds->isNotEmpty()) {
            $submissions = Submission::whereIn('assignment_id', $assignmentIds)
                ->orderByDesc('percentage')
                ->orderByDesc('submitted_at')
                ->get();
            $submissionStats = $submissions->groupBy('user_id')->map(function ($items) {
                $best = $items->first();
                return (object)[
                    'best_score' => $best?->score,
                    'best_percentage' => $best?->percentage,
                    'last_submitted_at' => $best?->submitted_at,
                    'attempt_number' => $best?->attempt_number,
                ];
            });
        }

        // Fallback nilai dari MaterialCompletion (quiz) bila belum ada submissions
        $quizAssignments = Assignment::where('kursus_id', $course->id)->where('type', 'quiz')->get(['id','materi_id']);
        $quizMaterialIds = $quizAssignments->pluck('materi_id');
        $assignmentByMaterial = $quizAssignments->pluck('id', 'materi_id');

        $completionScores = $quizMaterialIds->isNotEmpty()
            ? MaterialCompletion::select('user_id', 'materi_id', 'score', 'completed_at')
                ->whereIn('materi_id', $quizMaterialIds)
                ->whereNotNull('score')
                ->get()
                ->groupBy('user_id')
            : collect();

        $completionAggregated = $completionScores->map(function ($items) use ($assignmentByMaterial) {
            $best = $items->sortByDesc('score')->first();
            return (object)[
                'best_score' => $best?->score,
                'best_percentage' => $best?->score,
                'last_submitted_at' => $best?->completed_at,
                'assignment_id' => $best ? ($assignmentByMaterial[$best->materi_id] ?? null) : null,
            ];
        });

        $participantProgress = $course->enrollments()
            ->with('user')
            ->get()
            ->map(function ($enrollment) use ($completionCounts, $submissionStats, $completionAggregated, $totalMaterials, $totalMaterialsForProgress, $passingScore) {
                $completed = $completionCounts[$enrollment->user_id]->completed_count ?? 0;
                $progress = $totalMaterialsForProgress > 0 ? round(($completed / $totalMaterialsForProgress) * 100) : 0;
                $submission = $submissionStats[$enrollment->user_id] ?? null;
                $fallback = $completionAggregated[$enrollment->user_id] ?? null;
                $bestScore = $submission->best_score ?? $fallback->best_score ?? null;
                $bestPercentage = $submission->best_percentage ?? $fallback->best_percentage ?? null;
                $lastSubmit = $submission->last_submitted_at ?? $fallback->last_submitted_at ?? null;
                $attempt = $submission->attempt_number ?? null;
                $isPassed = $bestPercentage !== null ? $bestPercentage >= $passingScore : null;
                
                return [
                    'user' => $enrollment->user,
                    'progress' => $progress,
                    'completed' => $completed,
                    'total' => $totalMaterials,
                    'best_score' => $bestScore,
                    'best_percentage' => $bestPercentage,
                    'last_submitted_at' => $lastSubmit,
                    'best_attempt' => $attempt,
                    'is_passed' => $isPassed,
                ];
            });

        return view('instructor.course-detail', compact(
            'course',
            'sections',
            'totalMaterials',
            'totalVideos',
            'studentsCount',
            'questionBanks',
            'participantProgress',
            'assignmentCount'
        ));
    }

    public function preview(Kursus $course, Materi $material)
{
    if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
        abort(403, 'Unauthorized action.');
    }

    // Ambil semua materi berdasarkan urutan
    $materials = $course->materi()->with('assignment.questions.options')->orderBy('urutan')->get();
    $material->loadMissing('assignment.questions.options');

    // Cari index materi saat ini
    $currentIndex = $materials->search(function ($m) use ($material) {
        return $m->id === $material->id;
    });

    // Tentukan prev & next
    $prevMaterial = $currentIndex > 0 ? $materials[$currentIndex - 1] : null;
    $nextMaterial = $currentIndex < $materials->count() - 1 ? $materials[$currentIndex + 1] : null;

    return view('instructor.material-preview', compact(
        'course',
        'material',
        'materials', 
        'prevMaterial',
        'nextMaterial'
    ));
}


    public function create(Kursus $course)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        // Load sections untuk dropdown
        $sections = $course->sections()->orderBy('order')->get();

        return view('instructor.materials.create', compact('course', 'sections'));
    }

    public function store(Request $request, Kursus $course)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $storage = app(SupabaseStorageService::class);

        $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => [
                'nullable',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$value) {
                        return;
                    }
                    $ext = strtolower($value->getClientOriginalExtension());
                    $mime = $value->getMimeType();
                    if ($request->type === 'pdf' && $ext !== 'pdf') {
                        return $fail('Format file tidak valid. Harus PDF.');
                    }
                    if ($request->type === 'video' && !in_array($ext, ['mp4', 'mov'], true) && !in_array($mime, ['video/mp4', 'video/quicktime'], true)) {
                        return $fail('Format file tidak didukung. Gunakan MP4 atau MOV.');
                    }
                },
            ],
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'urutan' => 'nullable|integer|min:1',
            'is_preview' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
        ]);

        $fileUrl = null;
        $filePublicUrl = null;

        if ($request->hasFile('file')) {
            $upload = $storage->upload($request->file('file'), 'materials');
            $fileUrl = $upload['path'];
            $filePublicUrl = $upload['public_url'] ?? $upload['path'];
        }

        // Tentukan urutan
        $urutan = $request->urutan;
        if (!$urutan) {
            if ($request->section_id) {
                // ✅ PERBAIKAN: Ganti SectionController::find menjadi CourseSection::find
                $section = CourseSection::find($request->section_id);
                $urutan = $section->materials()->count() + 1;
            } else {
                $urutan = $course->materi()->count() + 1;
            }
        }

        $course->materi()->create([
            'section_id' => $request->section_id,
            'judul' => $request->judul,
            'description' => $request->description,
            'isi' => $request->content ?? $request->isi,
            'type' => $request->type,
            'file_url' => $fileUrl,
            'url_konten' => $filePublicUrl ?? null,
            'content' => $request->content,
            'duration' => $request->duration,
            'urutan' => $urutan,
            'is_preview' => $request->is_preview ?? false,
            'status' => $request->status ?? 'draft',
            'status_terkunci' => !($request->is_preview ?? false),
        ]);

         if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
    return response()->json([
        'success' => true,
        'message' => 'Materi berhasil ditambahkan',
        'redirect' => route('instructor.courses.show', $course->id),
    ], 200);
}

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    public function edit(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        // Load sections untuk dropdown
        $sections = $course->sections()->orderBy('order')->get();

        return view('instructor.materials.edit', compact('course', 'material', 'sections'));
    }

    public function update(Request $request, Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $storage = app(SupabaseStorageService::class);

        $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => [
                'nullable',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) use ($request) {
                    if (!$value) {
                        return;
                    }
                    $ext = strtolower($value->getClientOriginalExtension());
                    $mime = $value->getMimeType();
                    if ($request->type === 'pdf' && $ext !== 'pdf') {
                        return $fail('Format file tidak valid. Harus PDF.');
                    }
                    if ($request->type === 'video' && !in_array($ext, ['mp4', 'mov'], true) && !in_array($mime, ['video/mp4', 'video/quicktime'], true)) {
                        return $fail('Format file tidak didukung. Gunakan MP4 atau MOV.');
                    }
                },
            ],
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'urutan' => 'nullable|integer|min:1',
            'is_preview' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
            'status_terkunci' => 'nullable|boolean',
        ]);

        $updateData = [
            'section_id' => $request->section_id,
            'judul' => $request->judul,
            'description' => $request->description,
            'isi' => $request->content ?? $request->isi,
            'type' => $request->type,
            'content' => $request->content,
            'duration' => $request->duration,
            'urutan' => $request->urutan ?? $material->urutan,
            'is_preview' => $request->is_preview ?? false,
            'status' => $request->status ?? 'draft',
            'status_terkunci' => $request->status_terkunci ?? false,
        ];

        if ($request->hasFile('file')) {
            // Hapus file lama
            if ($material->file_url) {
                $storage->delete($material->file_url);
            }
            // Hapus file lama dari url_konten juga (untuk backward compatibility)
            if ($material->url_konten) {
                $oldPath = str_replace('/storage/', '', $material->url_konten);
                if (!str_starts_with($material->url_konten, 'http')) {
                    Storage::delete($oldPath);
                }
            }

            $upload = $storage->upload($request->file('file'), 'materials');
            $updateData['file_url'] = $upload['path'];
            $updateData['url_konten'] = $upload['public_url'] ?? $upload['path'];
        }

        $material->update($updateData);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $storage = app(SupabaseStorageService::class);

        // Hapus file jika ada
        if ($material->file_url) {
            $storage->delete($material->file_url);
        }
        
        // Backward compatibility - hapus dari url_konten juga
        if ($material->url_konten) {
            $oldPath = str_replace('/storage/', '', $material->url_konten);
            if (!str_starts_with($material->url_konten, 'http')) {
                Storage::delete($oldPath);
            }
        }

        $material->delete();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}
