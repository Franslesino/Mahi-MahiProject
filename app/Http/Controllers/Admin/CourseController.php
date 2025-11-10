<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\User;

class CourseController extends Controller
{
    // Tampilkan semua kursus
    public function index()
    {
        $courses = Course::with('instructor')->latest()->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    // Form tambah kursus
    public function create()
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.create', compact('instructors'));
    }

    // Simpan kursus baru
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'instructor_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        Course::create($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil ditambahkan.');
    }

    // Tampilkan detail kursus
    public function show(Course $course)
    {
        return view('admin.courses.show', compact('course'));
    }

    // Form edit kursus
    public function edit(Course $course)
    {
        $instructors = User::where('role', 'instructor')->get();
        return view('admin.courses.edit', compact('course', 'instructors'));
    }

    // Update kursus
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'instructor_id' => 'required|exists:users,id',
            'status' => 'required|in:active,inactive',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil diperbarui.');
    }

    // Hapus kursus
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('admin.courses.index')->with('success', 'Kursus berhasil dihapus.');
    }
}