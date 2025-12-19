<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\User;
use App\Models\Notification;
use App\Models\MaterialCompletion;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Materi;
use App\Services\SupabaseStorageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Kursus::with(['pembuat', 'instructor']);

        // Search: judul, kategori, instructor name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                    ->orWhere('kategori', 'ILIKE', "%{$search}%")
                    ->orWhereHas('instructor', function ($q) use ($search) {
                        $q->where('name', 'ILIKE', "%{$search}%");
                    });
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $courses = $query->latest()->paginate(10)->withQueryString();

        return view('admin.courses.index', compact('courses'));
    }

    public function create()
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.create', compact('instructors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'access_duration_days' => 'nullable|integer|min:1',
            'purchase_deadline_date' => 'nullable|date|after:now',
        ]);

        $data = [
            'judul' => $validated['title'],
            'deskripsi' => $validated['description'],
            'kategori' => $validated['category'],
            'harga' => $validated['price'],
            'status' => $validated['status'],
            'status_berbayar' => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',
            'pembuat' => Auth::id(),

            'mode' => $validated['mode'],
            'metode' => strtolower($validated['mode']), // sync to metode field for class sessions
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning' => $validated['learning'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'badge_color' => $validated['badge_color'] ?? 'blue',
            'instructor_id' => $validated['instructor_id'] ?? null,
            'created_by' => Auth::id(),
            'rating' => 0,
            'videos' => 0,
            'access_duration_days' => $validated['access_duration_days'] ?? null,
            'purchase_deadline_date' => $validated['purchase_deadline_date'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $storage = app(SupabaseStorageService::class);
            $upload = $storage->upload($request->file('image'), 'courses');
            $data['image'] = $upload['public_url'] ?? $upload['path'];
        }

        $course = Kursus::create($data);

        // Notifikasi ke instruktur
        if (!empty($validated['instructor_id'] ?? null)) {
            Notification::create([
                'user_id' => $validated['instructor_id'],
                'title' => 'Kursus baru ditugaskan',
                'message' => 'Anda ditugaskan sebagai instruktur untuk kursus "' . $validated['title'] . '".',
                'type' => 'info',
            ]);
        }

        // Notifikasi ke admin yang membuat
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Kursus berhasil dibuat',
            'message' => 'Kursus "' . ($validated['title'] ?? 'Tanpa judul') . '" telah berhasil dibuat.',
            'type' => 'success',
        ]);

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Kursus $course)
    {
        $instructors = User::where('role', 'instructor')->get();
        // variabel yang dilempar ke view: $course dan $instructors
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'nullable|exists:users,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'access_duration_days' => 'nullable|integer|min:1',
            'purchase_deadline_date' => 'nullable|date|after:now',
        ]);

        $data = [
            'judul' => $validated['title'],
            'deskripsi' => $validated['description'],
            'kategori' => $validated['category'],
            'harga' => $validated['price'],
            'status' => $validated['status'],
            'status_berbayar' => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',

            'mode' => $validated['mode'],
            'metode' => strtolower($validated['mode']), // sync to metode field for class sessions
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning' => $validated['learning'] ?? null,
            'badge' => $validated['badge'] ?? null,
            'badge_color' => $validated['badge_color'] ?? 'blue',
            'instructor_id' => $validated['instructor_id'] ?? null,
            'access_duration_days' => $validated['access_duration_days'] ?? null,
            'purchase_deadline_date' => $validated['purchase_deadline_date'] ?? null,
        ];

        if ($request->hasFile('image')) {
            $storage = app(SupabaseStorageService::class);
            // Hapus file lama
            if ($course->image) {
                $storage->delete($course->image);
                if (!str_starts_with($course->image, 'http') && Storage::disk('public')->exists($course->image)) {
                    Storage::disk('public')->delete($course->image);
                }
            }

            $upload = $storage->upload($request->file('image'), 'courses');
            $data['image'] = $upload['public_url'] ?? $upload['path'];
        }

        // Cek apakah instructor berubah
        $oldInstructorId = $course->instructor_id;
        $newInstructorId = $validated['instructor_id'] ?? null;

        $course->update($data);

        // Notifikasi jika instructor berganti
        if ($oldInstructorId != $newInstructorId && !empty($newInstructorId)) {
            Notification::create([
                'user_id' => $newInstructorId,
                'title' => 'Kursus baru ditugaskan',
                'message' => 'Anda ditugaskan sebagai instruktur untuk kursus "' . $validated['title'] . '".',
                'type' => 'info',
            ]);
        }

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Kursus $course)
    {
        if ($course->image) {
            $storage = app(SupabaseStorageService::class);
            $storage->delete($course->image);
            if (!str_starts_with($course->image, 'http') && Storage::disk('public')->exists($course->image)) {
                Storage::disk('public')->delete($course->image);
            }
        }

        $course->delete();

        return redirect()
            ->route('admin.courses.index')
            ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Kursus $course)
    {
        $course->load([
            'pembuat',
            'materi' => function ($query) {
                $query->orderBy('urutan');
            },
            'instructor',
            'enrollments.user'
        ]);

        // Get assignments for this course
        $assignments = Assignment::where('kursus_id', $course->id)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();

        // Statistics
        $stats = [
            'total_enrollments' => $course->enrollments->count(),
            'total_materials' => $course->materi->count(),
            'total_assignments' => $assignments->count(),
            'active_students' => $course->enrollments->where('status', 'active')->count(),
        ];

        // Progress & score per participant
        $materialIds = $course->materi->pluck('id');
        $totalMaterials = $materialIds->count();
        $passingScore = $assignments->firstWhere('passing_score')?->passing_score ?? 60;

        $completionCounts = MaterialCompletion::select('user_id', DB::raw('COUNT(*) as completed_count'))
            ->whereIn('materi_id', $materialIds)
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $assignmentIds = $assignments->pluck('id');
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

        // Fallback dari MaterialCompletion (quiz) bila belum ada submissions
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

        $participantProgress = $course->enrollments->map(function ($enrollment) use ($completionCounts, $submissionStats, $completionAggregated, $totalMaterials, $passingScore) {
            $completed = $completionCounts[$enrollment->user_id]->completed_count ?? 0;
            $progress = $totalMaterials > 0 ? round(($completed / $totalMaterials) * 100) : 0;
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

        return view('admin.courses.show', compact('course', 'assignments', 'stats', 'participantProgress', 'passingScore'));
    }

    /**
     * Admin Course Detail - Full course management like instructor
     */
    public function courseDetail(Kursus $course)
    {
        // Load sections dengan materials
        $sections = $course->sections()
            ->with([
                'materials' => function ($query) {
                    $query->orderBy('urutan')->with('assignment');
                }
            ])
            ->orderBy('order')
            ->get();

        // Stats
        $totalMaterials = $course->materi()->count();
        $totalVideos = $course->materi()->where('type', 'video')->count();
        $studentsCount = $course->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->distinct('user_id')
            ->count('user_id');

        $questionBanks = \App\Models\QuestionBank::where(function ($query) {
            $query->where('created_by', Auth::id())
                ->orWhere('is_public', true);
        })
            ->withCount('questions')
            ->get();

        // Progress & score per participant
        $materialIds = $course->materi()->pluck('id');
        $totalMaterials = $materialIds->count();
        $passingScore = \App\Models\Assignment::where('kursus_id', $course->id)->firstWhere('passing_score')?->passing_score ?? 60;

        $completionCounts = MaterialCompletion::select('user_id', DB::raw('COUNT(*) as completed_count'))
            ->whereIn('materi_id', $materialIds)
            ->whereNotNull('completed_at')
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $assignmentIds = \App\Models\Assignment::where('kursus_id', $course->id)->pluck('id');
        $assignmentCount = $assignmentIds->count();
        $submissionStats = collect();
        if ($assignmentIds->isNotEmpty()) {
            $submissions = \App\Models\Submission::whereIn('assignment_id', $assignmentIds)
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

        // Fallback nilai dari MaterialCompletion (quiz)
        $quizAssignments = \App\Models\Assignment::where('kursus_id', $course->id)->where('type', 'quiz')->get(['id', 'materi_id']);
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
            ->map(function ($enrollment) use ($completionCounts, $submissionStats, $completionAggregated, $totalMaterials, $passingScore) {
                $completed = $completionCounts[$enrollment->user_id]->completed_count ?? 0;
                $progress = $totalMaterials > 0 ? round(($completed / $totalMaterials) * 100) : 0;
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

        return view('admin.courses.course-detail', compact(
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

    /**
     * Admin Course Panel - Dashboard like instructor panel
     */
    public function panel(Kursus $course)
    {
        $course->load(['pembuat', 'instructor', 'enrollments.user', 'materi']);

        // Calculate statistics
        $stats = [
            'totalMaterials' => $course->materi->count(),
            'videosCount' => $course->materi->where('type', 'video')->count(),
            'studentsCount' => $course->enrollments()->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])->count(),
        ];

        // Recent enrollments (last 10)
        $recentEnrollments = $course->enrollments()
            ->with('user')
            ->latest()
            ->take(10)
            ->get();

        // Participant progress
        $participantProgress = $course->enrollments()->with('user')->get()->map(function ($enrollment) use ($course) {
            $totalMaterials = $course->materi->count();
            $completedMaterials = \App\Models\MaterialCompletion::where('user_id', $enrollment->user_id)
                ->whereIn('materi_id', $course->materi->pluck('id'))
                ->whereNotNull('completed_at')
                ->count();

            $progress = $totalMaterials > 0 ? round(($completedMaterials / $totalMaterials) * 100) : 0;

            return [
                'user' => $enrollment->user,
                'progress' => $progress,
                'completed' => $completedMaterials,
                'total' => $totalMaterials,
                'status' => $enrollment->status_pendaftaran,
            ];
        });

        return view('admin.courses.panel', compact('course', 'stats', 'recentEnrollments', 'participantProgress'));
    }

    /**
     * Admin Modules - Display modules like instructor
     */
    public function modules(Kursus $course)
    {
        // Get all sections (modules) for this course
        $sections = \App\Models\CourseSection::where('course_id', $course->id)
            ->with([
                'materials' => function ($query) {
                    $query->orderBy('urutan');
                }
            ])
            ->orderBy('order')
            ->get();

        return view('admin.courses.modules', compact('course', 'sections'));
    }

    /**
     * Store new module
     */
    public function storeModule(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $order = $validated['order'] ?? null;

        if ($order === null) {
            $lastSection = \App\Models\CourseSection::where('course_id', $course->id)
                ->orderBy('order', 'desc')
                ->first();
            $order = $lastSection ? $lastSection->order + 1 : 1;
        }

        \App\Models\CourseSection::create([
            'course_id' => $course->id,
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $order,
        ]);

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Modul berhasil ditambahkan!');
    }

    /**
     * Update module
     */
    public function updateModule(Request $request, Kursus $course, \App\Models\CourseSection $section)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'nullable|integer|min:0',
        ]);

        $section->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'order' => $validated['order'] ?? $section->order,
        ]);

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Modul berhasil diupdate!');
    }

    /**
     * Delete module
     */
    public function destroyModule(Kursus $course, \App\Models\CourseSection $section)
    {
        $section->delete();

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Modul berhasil dihapus!');
    }

    /**
     * Display materials in a module
     */
    public function moduleMaterials(Kursus $course, \App\Models\CourseSection $section)
    {
        $materials = \App\Models\Materi::where('section_id', $section->id)
            ->orderBy('urutan')
            ->get();

        return view('admin.courses.materials', compact('course', 'section', 'materials'));
    }

    /**
     * Store new material in module
     */
    public function storeMaterial(Request $request, Kursus $course, \App\Models\CourseSection $section)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'content' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,document,quiz,reading,class_session',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mov|max:102400', // 100MB
            'section_id' => 'required|exists:course_sections,id',
            'duration' => 'nullable|integer|min:0',
            'is_preview' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
            'status_terkunci' => 'nullable|boolean',
            // Class session fields
            'session_date' => 'required_if:type,class_session|nullable|date',
            'session_start_time' => 'required_if:type,class_session|nullable',
            'session_end_time' => 'required_if:type,class_session|nullable',
            'session_location' => 'nullable|string|max:255',
            'session_meeting_link' => 'nullable|url',
            'session_type' => 'required_if:type,class_session|nullable|in:offline,online',
        ]);

        $lastMaterial = \App\Models\Materi::where('section_id', $section->id)
            ->orderBy('urutan', 'desc')
            ->first();

        $filePath = null;
        $fileUrl = null;
        if ($request->hasFile('file')) {
            $storage = app(SupabaseStorageService::class);
            $upload = $storage->upload($request->file('file'), 'materials');
            $fileUrl = $upload['public_url'] ?? $upload['path'];
            $filePath = $upload['path'];
        }

        \App\Models\Materi::create([
            'kursus_id' => $course->id,
            'section_id' => $section->id,
            'judul' => $validated['judul'],
            'description' => $validated['description'],
            'isi' => $validated['content'] ?? $validated['isi'] ?? null,
            'content' => $validated['content'] ?? null,
            'type' => $validated['type'],
            'url_konten' => $fileUrl,
            'file_url' => $filePath,
            'duration' => $validated['duration'] ?? 0,
            'urutan' => $lastMaterial ? $lastMaterial->urutan + 1 : 1,
            'is_preview' => $validated['is_preview'] ?? false,
            'status' => $validated['status'] ?? ($validated['type'] === 'class_session' ? 'published' : 'draft'),
            'status_terkunci' => $validated['status_terkunci'] ?? false,
            // Class session
            'session_date' => $validated['session_date'] ?? null,
            'session_start_time' => $validated['session_start_time'] ?? null,
            'session_end_time' => $validated['session_end_time'] ?? null,
            'session_location' => $validated['session_location'] ?? null,
            'session_meeting_link' => $validated['session_meeting_link'] ?? null,
            'session_type' => $validated['session_type'] ?? null,
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Materi berhasil ditambahkan!',
                'redirect' => route('admin.courses.detail', $course)
            ]);
        }

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Update material
     */
    public function updateMaterial(Request $request, Kursus $course, \App\Models\CourseSection $section, \App\Models\Materi $material)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'content' => 'nullable|string',
            'file_path' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mov|max:102400',
            'video_url' => 'nullable|url',
            'type' => 'required|in:video,pdf,text,document,quiz,reading,class_session',
            'is_preview' => 'nullable|boolean',
            'status_terkunci' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
            // Class session fields
            'session_date' => 'required_if:type,class_session|nullable|date',
            'session_start_time' => 'required_if:type,class_session|nullable',
            'session_end_time' => 'required_if:type,class_session|nullable',
            'session_location' => 'nullable|string|max:255',
            'session_meeting_link' => 'nullable|url',
            'session_type' => 'required_if:type,class_session|nullable|in:offline,online',
        ]);

        $data = [
            'judul' => $validated['judul'],
            'description' => $validated['description'] ?? null,
            'isi' => $validated['content'] ?? $validated['isi'] ?? null,
            'content' => $validated['content'] ?? null,
            'type' => $validated['type'],
            'is_preview' => $request->boolean('is_preview'),
            'status_terkunci' => $request->boolean('status_terkunci'),
            'status' => $validated['status'] ?? $material->status,
        ];

        // Handle file upload
        if ($request->hasFile('file_path')) {
            $storage = app(SupabaseStorageService::class);
            $upload = $storage->upload($request->file('file_path'), 'materials');

            $data['file_url'] = $upload['path'];
            $data['url_konten'] = $upload['public_url'] ?? $upload['path'];
        }

        // Handle class session fields
        if ($validated['type'] === 'class_session') {
            $data['session_date'] = $validated['session_date'] ?? null;
            $data['session_start_time'] = $validated['session_start_time'] ?? null;
            $data['session_end_time'] = $validated['session_end_time'] ?? null;
            $data['session_location'] = $validated['session_location'] ?? null;
            $data['session_meeting_link'] = $validated['session_meeting_link'] ?? null;
            $data['session_type'] = $validated['session_type'] ?? null;
            $data['status'] = 'published';
        }

        if ($request->filled('video_url')) {
            $data['url_konten'] = $validated['video_url'];
        }

        $material->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Materi berhasil diperbarui!',
                'redirect' => route('admin.courses.detail', $course)
            ]);
        }

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Delete material
     */
    public function destroyMaterial(Kursus $course, \App\Models\CourseSection $section, \App\Models\Materi $material)
    {
        $material->delete();

        return redirect()->route('admin.courses.detail', $course)
            ->with('success', 'Materi berhasil dihapus!');
    }

    /**
     * Preview material
     */
    public function previewMaterial(Kursus $course, \App\Models\Materi $material)
    {
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

        return view('admin.courses.material-preview', compact(
            'course',
            'material',
            'materials',
            'prevMaterial',
            'nextMaterial'
        ));
    }

    public function streamMaterialFile(Kursus $course, Materi $material)
    {
        if ($material->kursus_id !== $course->id) {
            abort(404);
        }

        return $this->streamMaterialAsset($material);
    }

    protected function streamMaterialAsset(Materi $material)
    {
        $source = $material->file_url ?: $material->url_konten;
        if ($material->url_konten && Str::startsWith($material->url_konten, ['http://', 'https://'])) {
            $source = $material->url_konten;
        }
        if (!$source) {
            abort(404, 'File tidak ditemukan');
        }

        $filenameBase = Str::slug($material->judul ?? $material->title ?? 'material');
        $extension = pathinfo(parse_url($source, PHP_URL_PATH) ?? '', PATHINFO_EXTENSION);
        $downloadName = $filenameBase . ($extension ? '.' . $extension : '');

        $supabase = app(SupabaseStorageService::class);
        $supabaseObject = $supabase->fetchObject($source);
        if ($supabaseObject) {
            return response($supabaseObject['body'], 200, [
                'Content-Type' => $supabaseObject['content_type'],
                'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            ]);
        }

        if (!Str::startsWith($source, ['http://', 'https://'])) {
            $path = ltrim($source, '/');
            $disk = config('filesystems.materials_disk', 'public');

            if (Str::startsWith($path, 'storage/')) {
                $path = ltrim(substr($path, strlen('storage/')), '/');
                $disk = 'public';
            }

            $storage = Storage::disk($disk);
            if ($storage->exists($path)) {
                $mime = $storage->mimeType($path) ?? 'application/octet-stream';
                $stream = $storage->readStream($path);
                if (!$stream) {
                    abort(404, 'File tidak ditemukan');
                }

                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                }, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
                ]);
            }

            if (Storage::exists($path)) {
                $mime = Storage::mimeType($path) ?? 'application/octet-stream';
                $stream = Storage::readStream($path);
                if (!$stream) {
                    abort(404, 'File tidak ditemukan');
                }

                return response()->stream(function () use ($stream) {
                    fpassthru($stream);
                }, 200, [
                    'Content-Type' => $mime,
                    'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
                ]);
            }
        }

        $fallbackUrl = $material->file_url_full;
        if ($fallbackUrl && filter_var($fallbackUrl, FILTER_VALIDATE_URL)) {
            return redirect()->away($fallbackUrl);
        }

        if (filter_var($source, FILTER_VALIDATE_URL)) {
            $fallback = $material->file_url_full ?? $source;
            return redirect()->away($fallback);
        }

        abort(404, 'File tidak ditemukan');
    }

    /**
     * Edit material
     */
    public function editMaterial(Kursus $course, \App\Models\CourseSection $section, \App\Models\Materi $material)
    {
        return view('admin.courses.edit-material', compact('course', 'section', 'material'));
    }

    /**
     * Store quiz (quick create from course detail)
     */
    public function storeQuiz(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'duration_minutes' => 'nullable|integer|min:1',
            'passing_score' => 'nullable|integer|min:0|max:100',
            'randomize_questions' => 'nullable|in:0,1,true,false,on,off',
        ]);

        // Ensure section belongs to course if provided
        if (!empty($validated['section_id']) && !$course->sections()->where('id', $validated['section_id'])->exists()) {
            return back()->withErrors(['section_id' => 'Section tidak valid untuk kursus ini.'])->withInput();
        }

        $validated['randomize_questions'] = $request->boolean('randomize_questions');

        DB::beginTransaction();
        try {
            // Create materi placeholder in selected section
            $sectionId = $validated['section_id'] ?? null;
            $urutan = $sectionId
                ? ($course->materi()->where('section_id', $sectionId)->max('urutan') ?? 0) + 1
                : ($course->materi()->max('urutan') ?? 0) + 1;

            $material = $course->materi()->create([
                'section_id' => $sectionId,
                'judul' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => 'quiz',
                'urutan' => $urutan,
                'status' => 'draft',
                'is_preview' => false,
                'status_terkunci' => true,
            ]);

            // Create assignment (quiz)
            $assignment = \App\Models\Assignment::create([
                'kursus_id' => $course->id,
                'materi_id' => $material->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'type' => 'quiz',
                'duration_minutes' => $validated['duration_minutes'] ?? null,
                'passing_score' => $validated['passing_score'] ?? 60,
                'start_date' => null,
                'due_date' => null,
                'show_results_immediately' => true,
                'allow_multiple_attempts' => false,
                'max_attempts' => null,
                'randomize_questions' => $validated['randomize_questions'],
                'is_published' => false,
            ]);

            // Optional: import all questions from selected bank
            if (!empty($validated['question_bank_id'])) {
                $bank = \App\Models\QuestionBank::where('id', $validated['question_bank_id'])
                    ->with('questions.options')
                    ->firstOrFail();

                $order = 0;
                foreach ($bank->questions as $question) {
                    $assignment->questions()->attach($question->id, [
                        'order' => ++$order,
                        'points' => $question->points,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.courses.detail', $course)
                ->with('success', 'Quiz berhasil dibuat! Anda bisa mengelola soal dari detail quiz.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Admin Quiz Creation Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Gagal membuat quiz: ' . $e->getMessage()])->withInput();
        }
    }

    // Final quiz methods removed - createFinalQuiz() and storeFinalQuiz()
}
