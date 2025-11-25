<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\CourseSection; // ← TAMBAHKAN INI
use App\Models\QuestionBank;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    public function index()
    {
        $instructorId = Auth::id();

        $courses = Kursus::where(function($query) use ($instructorId) {
                $query->where('pembuat', $instructorId)
                      ->orWhere('instructor_id', $instructorId);
            })
            ->withCount('materi')
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

        $totalMaterials = $course->materi()->count();
        $questionBanks = QuestionBank::where('created_by', Auth::id())
            ->orWhere('is_public', true)
            ->withCount('questions')
            ->get();

        return view('instructor.course-detail', compact('course', 'sections', 'totalMaterials', 'questionBanks'));
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

        $disk = config('filesystems.materials_disk', 'public');

        $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => 'nullable|file|max:102400',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
            'urutan' => 'nullable|integer|min:1',
            'is_preview' => 'nullable|boolean',
            'status' => 'nullable|in:published,draft',
        ]);

        $fileUrl = null;

        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials', $disk);
            $fileUrl = $path;
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
            'url_konten' => $fileUrl ? Storage::disk($disk)->url($fileUrl) : null,
            'content' => $request->content,
            'duration' => $request->duration,
            'urutan' => $urutan,
            'is_preview' => $request->is_preview ?? false,
            'status' => $request->status ?? 'draft',
            'status_terkunci' => !($request->is_preview ?? false),
        ]);

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

        $disk = config('filesystems.materials_disk', 'public');

        $request->validate([
            'section_id' => 'nullable|exists:course_sections,id',
            'judul' => 'required|string|max:255',
            'description' => 'nullable|string',
            'isi' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => 'nullable|file|max:102400',
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
                Storage::disk($disk)->delete($material->file_url);
            }
            // Hapus file lama dari url_konten juga (untuk backward compatibility)
            if ($material->url_konten) {
                $oldPath = str_replace('/storage/', '', $material->url_konten);
                if (!str_starts_with($material->url_konten, 'http')) {
                    Storage::disk($disk)->delete($oldPath);
                }
            }

            $path = $request->file('file')->store('materials', $disk);
            $updateData['file_url'] = $path;
            $updateData['url_konten'] = Storage::disk($disk)->url($path);
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

        $disk = config('filesystems.materials_disk', 'public');

        // Hapus file jika ada
        if ($material->file_url) {
            Storage::disk($disk)->delete($material->file_url);
        }
        
        // Backward compatibility - hapus dari url_konten juga
        if ($material->url_konten) {
            $oldPath = str_replace('/storage/', '', $material->url_konten);
            if (!str_starts_with($material->url_konten, 'http')) {
                Storage::disk($disk)->delete($oldPath);
            }
        }

        $material->delete();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}
