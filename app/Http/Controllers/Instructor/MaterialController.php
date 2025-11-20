<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kursus;
use App\Models\Materi;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class MaterialController extends Controller
{
    /**
     * Display a listing of instructor's courses.
     */
   public function index()
{
    $instructorId = Auth::id();
    
    $courses = Kursus::where('pembuat', $instructorId)
                    ->withCount('materi')
                    ->latest()
                    ->get();
    
    return view('instructor.courses', compact('courses'));
}

    /**
     * Show the course detail with its materials.
     */
    public function show(Kursus $course)
    {
        // Pastikan instructor hanya bisa akses kursus miliknya
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $materials = $course->materi()->orderBy('urutan')->latest()->get();

        return view('instructor.course-detail', compact('course', 'materials'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create(Kursus $course)
    {
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.materials.create', compact('course'));
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request, Kursus $course)
    {
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'url_konten' => 'nullable|url',
            'urutan' => 'nullable|integer|min:0',
            'status_terkunci' => 'nullable|boolean',
        ]);

        $course->materi()->create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'url_konten' => $request->url_konten,
            'urutan' => $request->urutan ?? $course->materi()->count() + 1,
            'status_terkunci' => $request->status_terkunci ?? false,
        ]);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.materials.edit', compact('course', 'material'));
    }

    /**
     * Update the material.
     */
    public function update(Request $request, Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'nullable|string',
            'url_konten' => 'nullable|url',
            'urutan' => 'nullable|integer|min:0',
            'status_terkunci' => 'nullable|boolean',
        ]);

        $material->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'url_konten' => $request->url_konten,
            'urutan' => $request->urutan,
            'status_terkunci' => $request->status_terkunci,
        ]);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the material.
     */
    public function destroy(Kursus $course, Materi $material)
    {
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $material->delete();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}