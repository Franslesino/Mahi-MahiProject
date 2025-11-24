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
        ->withQueryString();

    return view('home.index', compact('courses'));
})->name('home');

// Test Google Config (Public - untuk debugging)
Route::get('/test-google-config', function () {
    return [
        'socialite_installed' => class_exists('Laravel\Socialite\Facades\Socialite'),
        'client_id' => config('services.google.client_id'),
        'client_secret' => config('services.google.client_secret') ? 'SET (' . strlen(config('services.google.client_secret')) . ' chars)' : 'NOT SET',
        'redirect' => config('services.google.redirect'),
        'env_client_id' => env('GOOGLE_CLIENT_ID') ? 'SET' : 'NOT SET',
    ];
});

// ==========================
// Voucher Validation (AJAX)
// ==========================
Route::post('/voucher/validate', [App\Http\Controllers\VoucherController::class, 'validate'])
    ->middleware('auth')
    ->name('voucher.validate');

// ==========================
// Authentication Routes
// ==========================

// Login & Register (Manual)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

// Google OAuth Routes
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==========================
// Protected Routes (AUTH)
// ==========================
Route::middleware('auth')->group(function () {

    // ======================
    // Student Routes
    // ======================
    Route::middleware('role:student')->group(function () {

        // List & detail course
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Transactions (NEW!)
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/checkout/{course}', [\App\Http\Controllers\Student\TransactionController::class, 'checkout'])->name('checkout');
            Route::post('/process/{course}', [\App\Http\Controllers\Student\TransactionController::class, 'process'])->name('process');
            Route::get('/{transaction}', [\App\Http\Controllers\Student\TransactionController::class, 'show'])->name('show');
            Route::post('/{transaction}/confirm', [\App\Http\Controllers\Student\TransactionController::class, 'confirm'])->name('confirm');
            Route::post('/{transaction}/cancel', [\App\Http\Controllers\Student\TransactionController::class, 'cancel'])->name('cancel');
        });

        // My transactions
        Route::get('/my-transactions', [\App\Http\Controllers\Student\TransactionController::class, 'myTransactions'])->name('my-transactions');

        // Enroll (redirect to checkout)
        Route::post('/courses/{course}/enroll', function (Kursus $course) {
            return redirect()->route('transactions.checkout', $course);
        })->name('courses.enroll');

        // Home student
        Route::get('/user', function (Request $request) {
            return redirect()->route('home', $request->only('search', 'category'));
        })->name('user.home');

        // Learn
        Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->name('student.course.learn');

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        // Notifications
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');

        // My courses
        Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');
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
                
                // Total Users (semua pengguna)
                $totalUsers = \App\Models\User::count();
                
                // Total Transaksi (enrollments atau transactions)
                $totalEnrollments = \App\Models\Transaction::count();
                
                // Total Pendapatan (dari transaksi yang sudah paid)
                $totalRevenue = \App\Models\Transaction::where('status', 'paid')
                    ->sum('total_bayar');

                return view('admin.dashboard', compact(
                    'totalCourses',
                    'activeCourses',
                    'totalStudents',
                    'totalInstructors',
                    'totalUsers',
                    'totalEnrollments',
                    'totalRevenue'
                ));
            })->name('dashboard');

            // Users management
            Route::resource('users', UserController::class);

            // Courses management
            Route::resource('courses', AdminCourseController::class);

            // Transactions management
            Route::get('transactions', [\App\Http\Controllers\Admin\TransactionController::class, 'index'])
                ->name('transactions.index');
            Route::get('transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'show'])
                ->name('transactions.show');
            Route::patch('transactions/{transaction}/status', [\App\Http\Controllers\Admin\TransactionController::class, 'updateStatus'])
                ->name('transactions.updateStatus');
            Route::delete('transactions/{transaction}', [\App\Http\Controllers\Admin\TransactionController::class, 'destroy'])
                ->name('transactions.destroy');
            Route::get('transactions/export', [\App\Http\Controllers\Admin\TransactionController::class, 'export'])
                ->name('transactions.export');

            // Vouchers management
            Route::resource('vouchers', \App\Http\Controllers\Admin\VoucherController::class);
            Route::patch('vouchers/{voucher}/toggle', [\App\Http\Controllers\Admin\VoucherController::class, 'toggleStatus'])
                ->name('vouchers.toggle');
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
    