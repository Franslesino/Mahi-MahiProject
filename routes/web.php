<?php 

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Instructor\MaterialController;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;

// ==========================
// Public Routes
// ==========================
Route::get('/', function () {
    $courses = Course::where('status', 'active')
                    ->with('instructor')
                    ->latest()
                    ->paginate(10); // ✅ ini yang kamu rubah!
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
// Protected Routes
// ==========================
Route::middleware('auth')->group(function () {
    
    // ======================
    // Student Routes
    // ======================
    Route::middleware('role:student')->group(function () {
        Route::get('/courses', 'App\Http\Controllers\Student\CourseController@index')->name('courses.index');
        Route::get('/courses/{course}', 'App\Http\Controllers\Student\CourseController@show')->name('courses.show');
        Route::post('/courses/{course}/enroll', [\App\Http\Controllers\Student\StudentController::class, 'enroll'])->name('courses.enroll');      
        // ✅ route utama untuk student setelah login
       Route::get('/user', function () {
    $courses = \App\Models\Course::where('status', 'active')
                ->with('instructor')
                ->latest()
                ->paginate(10); // ✅ WAJIB paginate
    return view('home.index', compact('courses'));
})->name('user.home');

Route::middleware(['auth', 'role:student'])->group(function () {

    Route::get('/courses/{course}/learn', 
        [\App\Http\Controllers\Student\StudentController::class, 'learn']
    )->name('student.course.learn');

});



        Route::get('/my-courses', function () {
            return view('student.courses.index');
        })->name('my-courses');
        
        Route::get('/profile', function () {
            return view('profile');
        })->name('profile');
    });

    // ======================
    // Admin Routes
    // ======================
    Route::middleware('role:admin')
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

        Route::get('/dashboard', function () {
            $totalCourses = Course::count();
            $activeCourses = Course::where('status', 'active')->count();
            $totalStudents = \App\Models\User::where('role', 'student')->count();
            $totalInstructors = \App\Models\User::where('role', 'instructor')->count();

            return view('admin.dashboard', compact(
                'totalCourses',
                'activeCourses',
                'totalStudents',
                'totalInstructors'
            ));
        })->name('dashboard');

        // ✅ Tambahkan route users biar error hilang
        Route::resource('users', UserController::class);

        // ✅ Route lama tetap dipertahankan
        Route::resource('courses', AdminCourseController::class);
    });

    // ======================
    // Instructor Routes
    // ======================
    Route::middleware('role:instructor')
        ->prefix('instructor')
        ->name('instructor.')
        ->group(function () {

            

        Route::get('/dashboard', function () {
            $myCourses = Course::where('instructor_id', Auth::id())->withCount('materials')->get();
            $totalMaterials = \App\Models\CourseMaterial::whereIn('course_id', $myCourses->pluck('id'))->count();
            
            return view('instructor.dashboard', compact('myCourses', 'totalMaterials'));
        })->name('dashboard');
        
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