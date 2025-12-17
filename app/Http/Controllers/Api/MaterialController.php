<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Materi::query();

        if ($request->filled('course_id')) {
            $query->where('kursus_id', $request->course_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_preview')) {
            $query->where('is_preview', filter_var($request->is_preview, FILTER_VALIDATE_BOOLEAN));
        }

        $perPage = max(1, min(50, (int) $request->get('per_page', 10)));

        $materials = $query
            ->with(['kursus:id,judul', 'section:id,title,course_id'])
            ->orderBy('urutan')
            ->paginate($perPage)
            ->appends($request->query());

        return response()->json([
            'success' => true,
            'data' => $materials->items(),
            'meta' => [
                'current_page' => $materials->currentPage(),
                'last_page' => $materials->lastPage(),
                'per_page' => $materials->perPage(),
                'total' => $materials->total(),
            ],
        ]);
    }

    public function show(Materi $material)
    {
        $material->load(['kursus:id,judul', 'section:id,title,course_id']);

        return response()->json([
            'success' => true,
            'data' => $material,
        ]);
    }
}
