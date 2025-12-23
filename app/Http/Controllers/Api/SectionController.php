<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use Illuminate\Http\Request;

/**
 * Controller untuk fitur bagian.
 */
class SectionController extends Controller
{
    /**
     * Daftar section, opsional filter by course_id.
     */
    public function index(Request $request)
    {
        $query = CourseSection::query();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->get('course_id'));
        }

        $perPage = (int) $request->get('per_page', 10);
        $perPage = max(1, min(50, $perPage));

        $sections = $query
            ->with(['course:id,judul,kategori'])
            ->withCount('materials')
            ->orderBy('order')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $sections->items(),
            'meta' => [
                'current_page' => $sections->currentPage(),
                'last_page' => $sections->lastPage(),
                'per_page' => $sections->perPage(),
                'total' => $sections->total(),
            ],
        ]);
    }

    /**
     * Detail section + materials.
     */
    public function show(CourseSection $section)
    {
        $section->load([
            'course:id,judul,kategori',
            'materials' => function ($q) {
                $q->orderBy('urutan');
            },
        ]);

        return response()->json([
            'success' => true,
            'data' => $section,
        ]);
    }
}