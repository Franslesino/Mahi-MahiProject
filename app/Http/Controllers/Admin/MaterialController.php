<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\CourseSection;
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
        // Load sections untuk dropdown
        $sections = $course->sections()->orderBy('order')->get();
        return view('admin.courses.materials.create', compact('course', 'sections'));
    }

    public function store(Request $request, Kursus $course)
    {
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

        // Load sections untuk dropdown
        $sections = $course->sections()->orderBy('order')->get();

        return view('admin.courses.materials.edit', compact('course', 'material', 'sections'));
    }

    public function update(Request $request, Kursus $course, Materi $material)
    {
        // Verify material belongs to course
        if ($material->kursus_id !== $course->id) {
            abort(404);
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
