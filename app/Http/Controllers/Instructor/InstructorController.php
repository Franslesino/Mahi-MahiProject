<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materi;
use App\Models\Enrollment;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InstructorController extends Controller
{
    /**
     * Display instructor dashboard with analytics
     */
    public function dashboard()
    {
        $instructorId = Auth::id();

       // ✅ PERBAIKAN: Get courses dari kedua kolom
    $myCourses = Kursus::where(function($query) use ($instructorId) {
            $query->where('pembuat', $instructorId)
                  ->orWhere('instructor_id', $instructorId);
        })
        ->withCount(['materi', 'enrollments' => function($q) {
            $q->whereIn('status_pendaftaran', ['active', 'completed']);
        }])
        ->latest()
        ->get();

        $courseIds = $myCourses->pluck('id');

        // Calculate statistics
        $stats = [
            'totalCourses' => $myCourses->count(),
            'activeCourses' => $myCourses->where('status_diterbitkan', true)->count(),
            'totalStudents' => Enrollment::whereIn('kursus_id', $courseIds)
                ->whereIn('status_pendaftaran', ['active', 'completed'])
                ->distinct('user_id')
                ->count('user_id'),
            'totalMaterials' => Materi::whereIn('kursus_id', $courseIds)->count(),
        ];

        // Course performance stats
        $courseStats = $myCourses->map(function($course) {
            $enrollments = Enrollment::where('kursus_id', $course->id)
                ->whereIn('status_pendaftaran', ['active', 'completed'])
                ->get();
            
            $course->students_count = $enrollments->count();
            
            return $course;
        })->sortByDesc('students_count');

        // Recent enrollments (last 10)
        $recentEnrollments = Enrollment::whereIn('kursus_id', $myCourses->pluck('id'))
        ->with(['user', 'kursus']) // Load relasi kursus
        ->latest()
        ->take(10)
        ->get();

        return view('instructor.dashboard', compact(
            'stats',
            'myCourses',
            'courseStats',
            'recentEnrollments'
        ));
    }

    /**
     * Display list of instructor's courses
     */
    public function courses()
    {
        $courses = Kursus::where(function($query) {
            $query->where('pembuat', Auth::id())
                  ->orWhere('instructor_id', Auth::id());
        })
        ->withCount(['materi', 'enrollments' => function($q) {
            $q->whereIn('status_pendaftaran', ['active', 'completed']);
        }])
        ->latest()
        ->paginate(12);

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * Display course details with materials
     */
    public function showCourse(Kursus $course)
    {
        // Ensure instructor can only view their own courses
        if ($course->pembuat !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $materials = $course->materi()->orderBy('urutan')->get();
        
        $stats = [
            'totalMaterials' => $materials->count(),
            'totalStudents' => $course->enrollments()->whereIn('status_pendaftaran', ['active', 'completed'])->count(),
            'completionRate' => $this->calculateCompletionRate($course->id),
        ];

        return view('instructor.courses.show', compact('course', 'materials', 'stats'));
    }

    /**
     * Calculate course completion rate
     */
    private function calculateCompletionRate($courseId)
    {
        $totalEnrollments = Enrollment::where('kursus_id', $courseId)->count();
        
        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = Enrollment::where('kursus_id', $courseId)
            ->where('status_pendaftaran', 'completed')
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 1);
    }
}
