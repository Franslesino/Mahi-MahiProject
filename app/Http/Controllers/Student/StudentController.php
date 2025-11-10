<?php 
 
 
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
protected $middleware = ['auth', 'role:student'];

public function __construct()
{
}

/**
* Enroll student to course
*/
public function enroll(Request $request, Course $course)
{
// Check if already enrolled
$existingEnrollment = Enrollment::where('user_id', Auth::id())
->where('course_id', $course->id)
->first();

if ($existingEnrollment) {
return redirect()
->route('courses.show', $course)
->with('error', 'Anda sudah terdaftar di kursus ini!');
}

// Create enrollment
$enrollment = Enrollment::create([
'user_id' => Auth::id(),
'course_id' => $course->id,
'status' => 'paid', // Langsung paid (nanti bisa integrasikan payment gateway)
'paid_amount' => $course->discount_price ?? $course->price,
'enrolled_at' => now(),
]);

return redirect()
->route('student.course.learn', $course)
->with('success', 'Selamat! Anda berhasil mendaftar kursus ini.');
}

/**
* Show learning page
*/
public function learn(Course $course)
{
// Check if enrolled
$enrollment = Enrollment::where('user_id', Auth::id())
->where('course_id', $course->id)
->where('status', 'paid')
->firstOrFail();

$course->load([
'instructor',
'materials' => function($query) {
$query->where('status', 'published')->orderBy('order');
}
]);

$materials = $course->materials;
$currentMaterial = $materials->first();

return view('student.learn', compact('course', 'materials', 'currentMaterial', 'enrollment'));
}

/**
* My courses page
*/
public function myCourses()
{
$enrollments = Enrollment::where('user_id', Auth::id())
->where('status', 'paid')
->with(['course.instructor'])
->latest()
->get();

return view('student.my-courses', compact('enrollments'));
}
}