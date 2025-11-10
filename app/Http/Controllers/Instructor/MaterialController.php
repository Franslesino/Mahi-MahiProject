<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\CourseMaterial;
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
    
    $courses = Course::where('instructor_id', $instructorId)
                    ->withCount('materials')
                    ->latest()
                    ->get();
    
    return view('instructor.courses', compact('courses'));
}

    /**
     * Show the course detail with its materials.
     */
    public function show(Course $course)
    {
        // Pastikan instructor hanya bisa akses kursus miliknya
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $materials = $course->materials()->orderBy('order')->latest()->get();

        return view('instructor.course-detail', compact('course', 'materials'));
    }

    /**
     * Show the form for creating a new material.
     */
    public function create(Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.materials.create', compact('course'));
    }

    /**
     * Store a newly created material.
     */
    public function store(Request $request, Course $course)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => 'nullable|file|mimes:pdf,mp4,avi,mov,jpg,jpeg,png|max:100000',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
        ]);

        $fileUrl = null;
        if ($request->hasFile('file')) {
            $fileUrl = $request->file('file')->store('materials', 'public');
        }

        $course->materials()->create([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'file_url' => $fileUrl,
            'content' => $request->content,
            'duration' => $request->duration,
            'order' => $course->materials()->count() + 1,
            'status' => 'published',
            'uploaded_by' => Auth::id(),
        ]);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified material.
     */
    public function edit(Course $course, CourseMaterial $material)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('instructor.materials.edit', compact('course', 'material'));
    }

    /**
     * Update the material.
     */
    public function update(Request $request, Course $course, CourseMaterial $material)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:video,pdf,text,quiz',
            'file' => 'nullable|file|mimes:pdf,mp4,avi,mov,jpg,jpeg,png|max:100000',
            'content' => 'nullable|string',
            'duration' => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('file')) {
            // Hapus file lama jika ada
            if ($material->file_url) {
                Storage::disk('public')->delete($material->file_url);
            }
            $material->file_url = $request->file('file')->store('materials', 'public');
        }

        $material->update([
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'content' => $request->content,
            'duration' => $request->duration,
        ]);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil diperbarui.');
    }

    /**
     * Remove the material.
     */
    public function destroy(Course $course, CourseMaterial $material)
    {
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($material->file_url) {
            Storage::disk('public')->delete($material->file_url);
        }

        $material->delete();

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Materi berhasil dihapus.');
    }
}