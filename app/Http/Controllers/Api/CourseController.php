<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use Illuminate\Http\Request;

/**
 * Controller untuk fitur kursus.
 */
class CourseController extends Controller
{
    /**
     * Daftar kursus (published).
     */
    public function index(Request $request)
    {
        $query = Kursus::query()->where('status_diterbitkan', true);

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ilike', "%{$search}%")
                    ->orWhere('deskripsi', 'ilike', "%{$search}%");
            });
        }

        if ($request->filled('category') && strtolower($request->get('category')) !== 'semua') {
            $query->where('kategori', $request->get('category'));
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(50, $perPage));

        $courses = $query
            ->with([
                'pembuat:id,name,email',
                'instructor:id,name,email',
            ])
            ->withCount([
                'materi as materials_count',
                'enrollments as students_count' => function ($q) {
                    $q->whereIn('status_pendaftaran', ['active', 'completed', 'paid']);
                },
            ])
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $courses->items(),
            'meta' => [
                'current_page' => $courses->currentPage(),
                'last_page' => $courses->lastPage(),
                'per_p  age' => $courses->perPage(),
                'total' => $courses->total(),
            ],
        ]);
    }

    /**
     * Detail kursus + sections/materials.
     */
    public function show(Kursus $course)
    {
        if (!$course->status_diterbitkan) {
            return response()->json([
                'success' => false,
                'message' => 'Course tidak ditemukan',
            ], 404);
        }

        $course->load([
            'pembuat:id,name,email',
            'instructor:id,name,email',
            'sections.materials',
        ]);

        return response()->json([
            'success' => true,
            'data' => $course,
        ]);
    }
}