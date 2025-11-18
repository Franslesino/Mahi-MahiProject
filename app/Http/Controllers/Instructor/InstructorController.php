<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\CourseMaterial;
use App\Models\Enrollment;
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

        // Get instructor's courses
        $myCourses = Course::where('instructor_id', $instructorId)
            ->withCount(['materials', 'enrollments' => function($q) {
                $q->whereIn('status', ['paid', 'completed']);
            }])
            ->latest()
            ->get();

        // Calculate statistics
        $stats = [
            'totalCourses' => $myCourses->count(),
            'activeCourses' => $myCourses->where('status', 'active')->count(),
            'totalStudents' => Enrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->whereIn('status', ['paid', 'completed'])
                ->distinct('user_id')
                ->count('user_id'),
            'totalMaterials' => CourseMaterial::whereIn('course_id', $myCourses->pluck('id'))->count(),
            'totalRevenue' => Enrollment::whereIn('course_id', $myCourses->pluck('id'))
                ->whereIn('status', ['paid', 'completed'])
                ->sum('paid_amount') ?? 0,
            'avgRating' => $myCourses->avg('rating') ?? 0,
        ];

        // Course performance stats with revenue
        $courseStats = $myCourses->map(function($course) {
            $enrollments = Enrollment::where('course_id', $course->id)
                ->whereIn('status', ['paid', 'completed'])
                ->get();
            
            $course->students_count = $enrollments->count();
            $course->total_revenue = $enrollments->sum('paid_amount');
            
            return $course;
        })->sortByDesc('students_count');

        // Recent enrollments (last 10)
        $recentEnrollments = Enrollment::whereIn('course_id', $myCourses->pluck('id'))
            ->with(['user', 'course'])
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
        $courses = Course::where('instructor_id', Auth::id())
            ->withCount(['materials', 'enrollments' => function($q) {
                $q->whereIn('status', ['paid', 'completed']);
            }])
            ->latest()
            ->paginate(12);

        return view('instructor.courses.index', compact('courses'));
    }

    /**
     * Display course details with materials
     */
    public function showCourse(Course $course)
    {
        // Ensure instructor can only view their own courses
        if ($course->instructor_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $materials = $course->materials()->orderBy('order')->get();
        
        $stats = [
            'totalMaterials' => $materials->count(),
            'publishedMaterials' => $materials->where('status', 'published')->count(),
            'totalStudents' => $course->enrollments()->whereIn('status', ['paid', 'completed'])->count(),
            'completionRate' => $this->calculateCompletionRate($course->id),
        ];

        return view('instructor.courses.show', compact('course', 'materials', 'stats'));
    }

    /**
     * Calculate course completion rate
     */
    private function calculateCompletionRate($courseId)
    {
        $totalEnrollments = Enrollment::where('course_id', $courseId)->count();
        
        if ($totalEnrollments === 0) {
            return 0;
        }

        $completedEnrollments = Enrollment::where('course_id', $courseId)
            ->where('status', 'completed')
            ->count();

        return round(($completedEnrollments / $totalEnrollments) * 100, 1);
    }
}