<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kursus;

class CourseController extends Controller
{
    /**
     * Halaman semua kursus (search + kategori)
     */
    public function index(Request $request)
    {
        $query = Kursus::where('status_diterbitkan', true)
            ->with(['instructor'])
            ->withCount('materi');

        // 🔍 SEARCH
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('judul', 'ILIKE', "%{$search}%")
                  ->orWhere('deskripsi', 'ILIKE', "%{$search}%");
            });
        }

        // 🏷 FILTER KATEGORI
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('kategori', $request->category);
        }

        $courses = $query
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('courses.index', compact('courses'));
    }

    /**
     * Detail Kursus
     */
    public function show(Kursus $course)
    {
        // Hitung total materi
        $course->load(['instructor'])
               ->loadCount('materi');

        // Untuk detail halaman
        $materials = $course->materi;

        // Enroll status (sementara false jika tidak ada fitur enroll)
        $isEnrolled = false;

        // Related Courses
        $relatedCourses = Kursus::where('kategori', $course->kategori)
            ->where('id', '!=', $course->id)
            ->latest()
            ->take(4)
            ->get();

        // Instructor stats
        $instructorCourses = Kursus::where('instructor_id', $course->instructor_id)->count();
        $instructorStudents = 0;

        return view('courses.show', compact(
            'course',
            'materials',
            'relatedCourses',
            'isEnrolled',
            'instructorCourses',
            'instructorStudents'
        ));
    }
}
