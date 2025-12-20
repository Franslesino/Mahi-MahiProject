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

        $courses = Kursus::where(function ($query) use ($instructorId) {
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
            ->with([
                'materials' => function ($query) {
                    $query->orderBy('urutan')->with('assignment');
                }
            ])
            ->orderBy('order')
            ->get();

        // Stats - only count materials that belong to sections (ignore orphan materials)
        $sectionIds = $sections->pluck('id');
        $materialIds = Materi::whereIn('section_id', $sectionIds)->pluck('id');
        $totalMaterials = $materialIds->count(); // only count materials in sections
        $totalMaterialsForProgress = max(1, $totalMaterials); // divisor progress supaya tidak 0
        $totalVideos = Materi::whereIn('id', $materialIds)->where('type', 'video')->count();
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
                return (object) [
                    'best_score' => $best?->score,
                    'best_percentage' => $best?->percentage,
                    'last_submitted_at' => $best?->submitted_at,
                    'attempt_number' => $best?->attempt_number,
                ];
            });
        }

        // Fallback nilai dari MaterialCompletion (quiz) bila belum ada submissions
        $quizAssignments = Assignment::where('kursus_id', $course->id)->where('type', 'quiz')->get(['id', 'materi_id']);
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
            return (object) [
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

        // Base validation rules
        $rules = [
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz,class_session',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'urutan' => 'nullable|integer|min:1',
            'is_preview' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
        ];

        // Add file validation for non-class_session types
        if ($request->type !== 'class_session') {
            $rules['file'] = [
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
            ];
        }

        // Add class session validation rules
        if ($request->type === 'class_session') {
            $rules['session_date'] = 'required|date';
            $rules['session_start_time'] = 'required';
            $rules['session_end_time'] = 'required';
            $rules['session_location'] = 'nullable|string|max:255';
            $rules['session_meeting_link'] = 'nullable|url';
            $rules['session_type'] = 'required|in:offline,online';
        }

        $request->validate($rules);

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

        $materialData = [
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
        ];

        // Add class session specific fields
        if ($request->type === 'class_session') {
            $materialData['session_date'] = $request->session_date;
            $materialData['session_start_time'] = $request->session_start_time;
            $materialData['session_end_time'] = $request->session_end_time;
            $materialData['session_location'] = $request->session_location;
            $materialData['session_meeting_link'] = $request->session_meeting_link;
            $materialData['session_type'] = $request->session_type;
            $materialData['status'] = 'published'; // Auto publish class sessions
        }

        $course->materi()->create($materialData);

        $successMessage = $request->type === 'class_session'
            ? 'Sesi tatap muka berhasil ditambahkan.'
            : 'Materi berhasil ditambahkan.';

        if ($request->ajax() || $request->wantsJson() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMessage,
                'redirect' => route('instructor.courses.show', $course->id),
            ], 200);
        }

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', $successMessage);
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

        // Base validation rules
        $rules = [
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz,class_session',
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
        ];

        // Add class session validation rules only if type is class_session
        if ($request->type === 'class_session') {
            $rules['session_date'] = 'required|date';
            $rules['session_start_time'] = 'required';
            $rules['session_end_time'] = 'required';
            $rules['session_location'] = 'nullable|string|max:255';
            $rules['session_meeting_link'] = 'nullable|url';
            $rules['session_type'] = ['required', Rule::in(['offline', 'online'])];
        }

        $request->validate($rules);

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

        if ($request->type === 'class_session') {
            $updateData = array_merge($updateData, [
                'session_date' => $request->session_date,
                'session_start_time' => $request->session_start_time,
                'session_end_time' => $request->session_end_time,
                'session_location' => $request->session_location,
                'session_meeting_link' => $request->session_meeting_link,
                'session_type' => $request->session_type,
                'status' => 'published',
            ]);
        } else {
            // Bersihkan field sesi jika tipe diubah
            $updateData = array_merge($updateData, [
                'session_date' => null,
                'session_start_time' => null,
                'session_end_time' => null,
                'session_location' => null,
                'session_meeting_link' => null,
                'session_type' => null,
            ]);
        }

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

    /**
     * Display attendance management for a class_session material
     */
    public function attendance(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        if ($material->type !== 'class_session') {
            abort(404, 'Materi ini bukan sesi tatap muka.');
        }

        // Get attendances for this material
        $attendances = $material->attendances()
            ->with(['user', 'enrollment'])
            ->orderBy('created_at')
            ->get();

        $statusOptions = \App\Models\Attendance::getStatusOptions();

        return view('instructor.materials.attendance', compact('course', 'material', 'attendances', 'statusOptions'));
    }

    /**
     * Update a single attendance record
     */
    public function updateAttendance(Request $request, Kursus $course, Materi $material, \App\Models\Attendance $attendance)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => 'required|in:hadir,tidak_hadir,izin,sakit,terlambat',
            'catatan' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'],
            'catatan' => $validated['catatan'] ?? null,
            'check_in_time' => $validated['status'] === 'hadir' ? now() : null,
            'updated_by' => Auth::id(),
        ]);

        // Auto-complete material when status is 'hadir'
        if ($validated['status'] === 'hadir') {
            \App\Models\MaterialCompletion::firstOrCreate(
                [
                    'user_id' => $attendance->user_id,
                    'materi_id' => $material->id,
                ],
                [
                    'completed_at' => now(),
                ]
            );
        } else {
            // Remove completion if status changed from hadir to something else
            \App\Models\MaterialCompletion::where('user_id', $attendance->user_id)
                ->where('materi_id', $material->id)
                ->delete();
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status kehadiran berhasil diperbarui.',
            ]);
        }

        return back()->with('success', 'Status kehadiran berhasil diperbarui.');
    }

    /**
     * Generate attendance records for all enrolled students
     */
    public function generateAttendance(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        // Get existing attendance user IDs
        $existingUserIds = $material->attendances()->pluck('user_id')->toArray();

        // Get enrolled students - use status_pendaftaran with all possible status values
        $enrollments = $course->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid', 'approved', 'enrolled', 'pending'])
            ->whereNotIn('user_id', $existingUserIds)
            ->get();

        $created = 0;
        foreach ($enrollments as $enrollment) {
            \App\Models\Attendance::create([
                'materi_id' => $material->id,
                'enrollment_id' => $enrollment->id,
                'user_id' => $enrollment->user_id,
                'status' => 'tidak_hadir',
            ]);
            $created++;
        }

        return back()->with('success', "Berhasil menambahkan $created peserta ke daftar kehadiran.");
    }

    /**
     * Mark all students as present
     */
    public function markAllPresent(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id() && $course->instructor_id !== Auth::id()) {
            abort(403);
        }

        $material->attendances()->update([
            'status' => 'hadir',
            'check_in_time' => now(),
            'updated_by' => Auth::id(),
        ]);

        // Auto-complete material for all attendees
        $attendances = $material->attendances()->get();
        foreach ($attendances as $attendance) {
            \App\Models\MaterialCompletion::firstOrCreate(
                [
                    'user_id' => $attendance->user_id,
                    'materi_id' => $material->id,
                ],
                [
                    'completed_at' => now(),
                ]
            );
        }

        return back()->with('success', 'Semua peserta ditandai hadir dan materi dihitung selesai.');
    }
}
