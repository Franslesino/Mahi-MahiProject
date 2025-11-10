<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // ✅ WAJIB ADA

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['instructor', 'creator'])
                        ->withCount('materials')
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'nullable|url',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'required|exists:users,id',
        ]);

        // ✅ Auth sudah dikenali
        $validated['created_by'] = Auth::id();
        $validated['rating'] = 0;
        $validated['videos'] = 0;

        Course::create($validated);

        return redirect()->route('admin.courses.index')
                         ->with('success', 'Kursus berhasil ditambahkan!');
    }

    public function edit(Course $course)
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    public function update(Request $request, Course $course)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string|max:100',
            'image' => 'nullable|url',
            'mode' => 'required|in:Online,Offline,Hybrid',
            'price' => 'required|numeric|min:0',
            'discount_price' => 'nullable|numeric|min:0|lt:price',
            'learning' => 'nullable|string',
            'badge' => 'nullable|string|max:50',
            'badge_color' => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive,draft',
            'instructor_id' => 'required|exists:users,id',
        ]);

        $course->update($validated);

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil diupdate!');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.courses.index')
                        ->with('success', 'Kursus berhasil dihapus!');
    }

    public function show(Course $course)
    {
        $course->load(['instructor', 'materials.uploader']);
        return view('admin.courses.show', compact('course'));
    }
}