<?php 
 
 
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
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
public function enroll(Request $request, Kursus $course)
{
// Check if already enrolled
$existingEnrollment = Enrollment::where('user_id', Auth::id())
->where('kursus_id', $course->id)
->first();

if ($existingEnrollment) {
return redirect()
->route('courses.show', $course)
->with('error', 'Anda sudah terdaftar di kursus ini!');
}

// Create enrollment
$enrollment = Enrollment::create([
'user_id' => Auth::id(),
'kursus_id' => $course->id,
'status_pendaftaran' => 'active',
'tanggal_daftar' => now(),
]);

return redirect()
->route('student.course.learn', $course)
->with('success', 'Selamat! Anda berhasil mendaftar kursus ini.');
}

/**
* Show learning page
*/
public function learn(Kursus $course)
{
// Check if enrolled
$enrollment = Enrollment::where('user_id', Auth::id())
->where('kursus_id', $course->id)
->where('status_pendaftaran', 'active')
->firstOrFail();

$course->load([
'pembuat',
'materi' => function($query) {
$query->orderBy('urutan');
}
]);

$materials = $course->materi;
$currentMaterial = $materials->first();

return view('student.learn', compact('course', 'materials', 'currentMaterial', 'enrollment'));
}

/**
* My courses page
*/
public function myCourses()
{
$enrollments = Enrollment::where('user_id', Auth::id())
->where('status_pendaftaran', 'active')
->with(['kursus.pembuat'])
->latest()
->get();

return view('student.my-courses', compact('enrollments'));
}
}