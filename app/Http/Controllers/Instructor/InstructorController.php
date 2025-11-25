<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Materi;
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

        // Get instructor's courses (check both pembuat and instructor_id for backward compatibility)
        $myCourses = Kursus::where(function($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId)
                      ->orWhere('pembuat', $instructorId);
            })
            ->withCount(['materi', 'enrollments' => function($q) {
                $q->whereIn('status_pendaftaran', ['active', 'completed']);
            }])
            ->latest()
            ->get();

        // Calculate statistics
        $stats = [
            'totalCourses' => $myCourses->count(),
            'activeCourses' => $myCourses->where('status_diterbitkan', true)->count(),
            'totalStudents' => Enrollment::whereIn('kursus_id', $myCourses->pluck('id'))
                ->whereIn('status_pendaftaran', ['active', 'completed'])
                ->distinct('user_id')
                ->count('user_id'),
            'totalMaterials' => Materi::whereIn('kursus_id', $myCourses->pluck('id'))->count(),
            'totalRevenue' => 0, // Tergantung sistem pembayaran
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
            ->with(['user', 'kursus'])
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
        $instructorId = Auth::id();
        
        $courses = Kursus::where(function($query) use ($instructorId) {
                $query->where('instructor_id', $instructorId)
                      ->orWhere('pembuat', $instructorId);
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
        $instructorId = Auth::id();
        if ($course->instructor_id !== $instructorId && $course->pembuat !== $instructorId) {
            abort(403, 'Unauthorized action.');
        }

        $materials = $course->materi()->orderBy('urutan')->get();
        
        // Get assignments for this course
        $assignments = \App\Models\Assignment::where('kursus_id', $course->id)
            ->withCount('questions')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $stats = [
            'totalMaterials' => $materials->count(),
            'totalAssignments' => $assignments->count(),
            'totalStudents' => $course->enrollments()->whereIn('status_pendaftaran', ['active', 'completed'])->count(),
            'completionRate' => $this->calculateCompletionRate($course->id),
        ];

        return view('instructor.courses.show', compact('course', 'materials', 'assignments', 'stats'));
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