<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\User;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalCourses = Course::count();
        $totalUsers = User::count();
        $totalEnrollments = Enrollment::count();

        return view('admin.dashboard', compact('totalCourses', 'totalUsers', 'totalEnrollments'));
    }

    public function courses(Request $request)
    {
        $query = Course::with('instructor');

        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    public function users()
    {
        $users = User::paginate(15);
        return view('admin.users.index', compact('users'));
    }

    public function enrollments()
    {
        $enrollments = Enrollment::with(['user', 'course'])->latest()->paginate(15);
        return view('admin.enrollments.index', compact('enrollments'));
    }

    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);

        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()->back()->with('success', 'Course berhasil dihapus.');
    }
}