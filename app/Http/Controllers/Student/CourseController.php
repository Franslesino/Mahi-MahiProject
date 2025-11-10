<?php

// ============================================
// app/Http/Controllers/Public/CourseController.php
// ============================================

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
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
        $query = Course::where('status', 'active')
            ->with('instructor')
            ->withCount('materials');

        // Filter by category
        if ($request->has('category') && $request->category) {
            $query->where('category', $request->category);
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $courses = $query->latest()->paginate(12);

        // Get categories for filter
        $categories = Course::where('status', 'active')
            ->select('category')
            ->distinct()
            ->pluck('category');

        return view('courses.index', compact('courses', 'categories'));
    }

    /**
     * Display course detail
     */
    public function show(Course $course)
    {
        // Only show active courses
        if ($course->status !== 'active') {
            abort(404);
        }

        // Load relationships
        $course->load([
            'instructor',
            'materials' => function($query) {
                $query->where('status', 'published')->orderBy('order');
            }
        ]);

        $course->loadCount('materials');

        // Check if user is enrolled
        $isEnrolled = false;
        if (Auth::check()) {
            $isEnrolled = Enrollment::where('user_id', Auth::user()->id)
                ->where('course_id', $course->id)
                ->whereIn('status', ['paid', 'completed'])
                ->exists();
        }

        // Get materials
        // Show all if enrolled, only preview if not enrolled or guest
        $materials = $course->materials()
    ->where('status', 'published')
    ->orderBy('order')
    ->get();


        // Related courses (same category)
        $relatedCourses = Course::where('category', $course->category)
            ->where('id', '!=', $course->id)
            ->where('status', 'active')
            ->inRandomOrder()
            ->take(5)
            ->get();

        // Instructor stats
        $instructorCourses = Course::where('instructor_id', $course->instructor_id)
            ->where('status', 'active')
            ->count();
        
        $instructorStudents = Enrollment::whereIn('course_id', 
            Course::where('instructor_id', $course->instructor_id)->pluck('id')
        )
        ->whereIn('status', ['paid', 'completed'])
        ->distinct('user_id')
        ->count('user_id');

        return view('courses.show', compact(
            'course',
            'materials',
            'isEnrolled',
            'relatedCourses',
            'instructorCourses',
            'instructorStudents'
        ));
    }
}