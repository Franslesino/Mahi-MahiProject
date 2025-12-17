<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseSection;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        $query = CourseSection::query();

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        $perPage = max(1, min(50, (int) $request->get('per_page', 10)));

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

    public function show(CourseSection $section)
    {
        $section->load([
            'course:id,judul,kategori',
            'materials' => fn($q) => $q->orderBy('urutan'),
        ]);

        return response()->json([
            'success' => true,
            'data' => $section,
        ]);
    }
}
