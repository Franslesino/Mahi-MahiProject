<?php

// ============================================
// app/Http/Controllers/Public/CourseController.php
// ============================================

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    /**
     * Display all active courses
     */
    public function index(Request $request)
    {
        $query = Kursus::where('status_diterbitkan', true)
            ->with('pembuat')
            ->withCount('materi');

        // Filter by category
        if ($request->has('kategori') && $request->kategori) {
            $query->where('kategori', $request->kategori);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->latest()->paginate(12);

        // Get categories for filter
        $categories = Kursus::where('status_diterbitkan', true)
            ->select('kategori')
            ->distinct()
            ->pluck('kategori');

        return view('student.courses.index', compact('courses', 'categories'));
    }

    /**
     * Display course detail
     */
    public function show(Kursus $course)
    {
        // Only show published courses
        if (!$course->status_diterbitkan) {
            abort(404);
        }

        // Load relationships
        $course->load([
            'pembuat',
            'materi' => function($query) {
                $query->orderBy('urutan');
            },
            'upcomingSchedules',
        ]);

        $course->loadCount(['materi', 'assignments']);

        // Check if user is enrolled
        $isEnrolled = false;
        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::user()->id)
                ->where('kursus_id', $course->id)
                ->whereIn('status_pendaftaran', ['active', 'completed'])
                ->exists();
        }

        // Get materials
        $materials = $course->materi()
            ->orderBy('urutan')
            ->get();

        // Related courses (same category)
        $relatedCourses = Kursus::where('kategori', $course->kategori)
            ->where('id', '!=', $course->id)
            ->where('status_diterbitkan', true)
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Instructor stats
        $instructorCourses = Kursus::where('pembuat', $course->pembuat)
            ->where('status_diterbitkan', true)
            ->count();
        
        $instructorStudents = Enrollment::whereIn('kursus_id', 
            Kursus::where('pembuat', $course->pembuat)->pluck('id')
        )
        ->whereIn('status_pendaftaran', ['active', 'completed'])
        ->distinct('user_id')
        ->count('user_id');

        return view('student.courses.show', compact(
            'course',
            'materials',
            'isEnrolled',
            'relatedCourses',
            'instructorCourses',
            'instructorStudents'
        ));
    }
}
