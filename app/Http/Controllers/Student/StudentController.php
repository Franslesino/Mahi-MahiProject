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
        // Middleware already applied in routes
    }

    /**
     * Enroll student to course (DEPRECATED - use TransactionController)
     */
    public function enroll(Request $request, Kursus $course)
    {
        // Redirect to checkout instead
        return redirect()->route('transactions.checkout', $course);
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
            ->with([
                'kursus' => function($query) {
                    $query->withCount('materi');
                },
                'kursus.pembuat'
            ])
            ->latest('tanggal_daftar')
            ->get();

        return view('my-courses', compact('enrollments'));
    }
}