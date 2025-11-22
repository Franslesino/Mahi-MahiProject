<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Instructor\MaterialController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\ProfileController;
use App\Models\Kursus;

// ==========================
// Public Routes
// ==========================
Route::get('/', function (Request $request) {
    $query = Kursus::where('status_diterbitkan', true);

    // 🔍 Search (by judul & deskripsi)
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'ilike', "%{$search}%")
              ->orWhere('deskripsi', 'ilike', "%{$search}%");
        });
    }

    // 🏷 Filter kategori
    if ($request->filled('category') && $request->category !== 'Semua') {
        $query->where('kategori', $request->category);
    }

    $courses = $query
        ->with(['pembuat', 'instructor'])
        ->withCount('materi')
        ->latest()
        ->paginate(12)
        ->withQueryString(); // biar query tetap ada di pagination

    return view('home.index', compact('courses'));
})->name('home');

// ==========================
// Authentication
// ==========================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================
// Protected Routes (AUTH)
// ==========================
Route::middleware('auth')->group(function () {

    // ======================
    // Student Routes
    // ======================
    Route::middleware('role:student')->group(function () {

        // List & detail course (pakai controller student)
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Payment routes
        Route::get('/courses/{course}/checkout', [StudentController::class, 'showCheckout'])->name('payment.checkout');
        Route::post('/courses/{course}/payment/process', [StudentController::class, 'processPayment'])->name('payment.process');

        // Enroll
        Route::post('/courses/{course}/enroll', [StudentController::class, 'enroll'])->name('courses.enroll');

        // Home student -> pakai view yang sama dengan home, tapi lewat route home
        Route::get('/user', function (Request $request) {
            return redirect()->route('home', $request->only('search', 'category'));
        })->name('user.home');

        // Learn
        Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->name('student.course.learn');

        // Profile (controller beneran)
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // My courses (sementara view statis)
        Route::get('/my-courses', function () {
            return view('student.courses.index');
        })->name('my-courses');
    });

    // ======================
    // Admin Routes
    // ======================
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', function () {
                $totalCourses     = Kursus::count();
                $activeCourses    = Kursus::where('status_diterbitkan', true)->count();
                $totalStudents    = \App\Models\User::where('role', 'student')->count();
                $totalInstructors = \App\Models\User::where('role', 'instructor')->count();

                return view('admin.dashboard', compact(
                    'totalCourses',
                    'activeCourses',
                    'totalStudents',
                    'totalInstructors'
                ));
            })->name('dashboard');

            // Users management
            Route::resource('users', UserController::class);

            // Courses management
            Route::resource('courses', AdminCourseController::class);
        });

    // ======================
    // Instructor Routes
    // ======================
    Route::middleware('role:instructor')
        ->prefix('instructor')
        ->name('instructor.')
        ->group(function () {

            Route::get('/dashboard', [InstructorController::class, 'dashboard'])->name('dashboard');

            // Course Management
            Route::get('/courses', [MaterialController::class, 'index'])->name('courses');
            Route::get('/courses/{course}', [MaterialController::class, 'show'])->name('courses.show');

            // Material Management
            Route::get('/courses/{course}/materials/create', [MaterialController::class, 'create'])->name('materials.create');
            Route::post('/courses/{course}/materials', [MaterialController::class, 'store'])->name('materials.store');
            Route::get('/courses/{course}/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
            Route::put('/courses/{course}/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
            Route::delete('/courses/{course}/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');
        });
});
