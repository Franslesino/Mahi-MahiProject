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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Kursus::with(['pembuat', 'instructor'])
            ->withCount('materi')
            ->latest()
            ->paginate(10);

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
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode'           => 'required|in:Online,Offline,Hybrid',
            'learning'       => 'nullable|string',
            'badge'          => 'nullable|string|max:50',
            'badge_color'    => 'nullable|string|max:50',
            'status'         => 'required|in:active,inactive,draft',
            'instructor_id'  => 'required|exists:users,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'judul'              => $validated['title'],
            'deskripsi'          => $validated['description'],
            'kategori'           => $validated['category'],
            'harga'              => $validated['price'],
            'status'             => $validated['status'],
            'status_berbayar'    => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',
            'pembuat'            => Auth::id(),

            'mode'           => $validated['mode'],
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning'       => $validated['learning'] ?? null,
            'badge'          => $validated['badge'] ?? null,
            'badge_color'    => $validated['badge_color'] ?? 'blue',
            'instructor_id'  => $validated['instructor_id'],
            'created_by'     => Auth::id(),
            'rating'         => 0,
            'videos'         => 0,
        ];

        if ($request->hasFile('image')) {
            $storage = app(SupabaseStorageService::class);
            $upload = $storage->upload($request->file('image'), 'courses');
            $data['image'] = $upload['public_url'] ?? $upload['path'];
        }

        $course = Kursus::create($data);

        // Notifikasi ke instruktur
        if (!empty($validated['instructor_id'])) {
            Notification::create([
                'user_id' => $validated['instructor_id'],
                'title'   => 'Kursus baru ditugaskan',
                'message' => 'Anda ditugaskan sebagai instruktur untuk kursus "' . $validated['title'] . '".',
                'type'    => 'info',
            ]);
        }

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
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'required|string|max:100',
            'price'          => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'mode'           => 'required|in:Online,Offline,Hybrid',
            'learning'       => 'nullable|string',
            'badge'          => 'nullable|string|max:50',
            'badge_color'    => 'nullable|string|max:50',
            'status'         => 'required|in:active,inactive,draft',
            'instructor_id'  => 'required|exists:users,id',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $data = [
            'judul'              => $validated['title'],
            'deskripsi'          => $validated['description'],
            'kategori'           => $validated['category'],
            'harga'              => $validated['price'],
            'status'             => $validated['status'],
            'status_berbayar'    => $validated['price'] > 0,
            'status_diterbitkan' => $validated['status'] === 'active',

            'mode'           => $validated['mode'],
            'discount_price' => $validated['discount_price'] ?? 0,
            'learning'       => $validated['learning'] ?? null,
            'badge'          => $validated['badge'] ?? null,
            'badge_color'    => $validated['badge_color'] ?? 'blue',
            'instructor_id'  => $validated['instructor_id'],
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

        $course->update($data);

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
            'materi' => function($query) {
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
        $totalMaterials = max(1, $materialIds->count());
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
                return (object)[
                    'best_score' => $best?->score,
                    'best_percentage' => $best?->percentage,
                    'last_submitted_at' => $best?->submitted_at,
                    'attempt_number' => $best?->attempt_number,
                ];
            });
        }

        // Fallback dari MaterialCompletion (quiz) bila belum ada submissions
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
}
