<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materi;
use Illuminate\Http\Request;
use App\Services\SupabaseStorageService;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    public function index(Kursus $course)
    {
        $materials = $course->materi()->orderBy('urutan')->get();
        return view('admin.courses.materials.index', compact('course', 'materials'));
    }

    public function create(Kursus $course)
    {
        return view('admin.courses.materials.create', compact('course'));
    }

    public function store(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'url_konten' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mov|max:51200', // 50MB
            'urutan' => 'nullable|integer|min:0',
            'status_terkunci' => 'nullable|boolean',
        ]);

        $storage = app(SupabaseStorageService::class);

        // Get next order number if not provided
        if (!isset($validated['urutan'])) {
            $validated['urutan'] = $course->materi()->max('urutan') + 1;
        }

        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $upload = $storage->upload($file, 'materials');
            $validated['url_konten'] = $upload['public_url'] ?? $upload['path'];
            $validated['file_url'] = $upload['path'];
        }

        $validated['kursus_id'] = $course->id;
        $validated['status_terkunci'] = $request->has('status_terkunci');

        Materi::create($validated);

        return redirect()
            ->route('admin.courses.materials.index', $course)
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    public function edit(Kursus $course, Materi $material)
    {
        // Verify material belongs to course
        if ($material->kursus_id !== $course->id) {
            abort(404);
        }

        return view('admin.courses.materials.edit', compact('course', 'material'));
    }

    public function update(Request $request, Kursus $course, Materi $material)
    {
        // Verify material belongs to course
        if ($material->kursus_id !== $course->id) {
            abort(404);
        }

        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'url_konten' => 'nullable|string',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,mp4,avi,mov|max:51200',
            'urutan' => 'nullable|integer|min:0',
            'status_terkunci' => 'nullable|boolean',
        ]);

        $storage = app(SupabaseStorageService::class);

        // Handle file upload
        if ($request->hasFile('file')) {
            // Delete old file if exists
            $storage->delete($material->file_url ?: $material->url_konten);
            if ($material->url_konten && !str_starts_with($material->url_konten, 'http') && Storage::disk('public')->exists($material->url_konten)) {
                Storage::disk('public')->delete($material->url_konten);
            }

            $upload = $storage->upload($request->file('file'), 'materials');
            $validated['url_konten'] = $upload['public_url'] ?? $upload['path'];
            $validated['file_url'] = $upload['path'];
        }

        $validated['status_terkunci'] = $request->has('status_terkunci');

        $material->update($validated);

        return redirect()
            ->route('admin.courses.materials.index', $course)
            ->with('success', 'Materi berhasil diperbarui!');
    }

    public function destroy(Kursus $course, Materi $material)
    {
        // Verify material belongs to course
        if ($material->kursus_id !== $course->id) {
            abort(404);
        }

        // Delete file if exists
        $storage = app(SupabaseStorageService::class);
        $storage->delete($material->file_url ?: $material->url_konten);
        if ($material->url_konten && !str_starts_with($material->url_konten, 'http') && Storage::disk('public')->exists($material->url_konten)) {
            Storage::disk('public')->delete($material->url_konten);
        }

        $material->delete();

        return redirect()
            ->route('admin.courses.materials.index', $course)
            ->with('success', 'Materi berhasil dihapus!');
    }

    public function reorder(Request $request, Kursus $course)
    {
        $validated = $request->validate([
            'materials' => 'required|array',
            'materials.*.id' => 'required|exists:materi,id',
            'materials.*.urutan' => 'required|integer|min:0',
        ]);

        foreach ($validated['materials'] as $materialData) {
            Materi::where('id', $materialData['id'])
                ->where('kursus_id', $course->id)
                ->update(['urutan' => $materialData['urutan']]);
        }

        return response()->json(['success' => true]);
    }
}
