<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\CourseController as AdminCourseController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Instructor\InstructorController;
use App\Http\Controllers\Instructor\MaterialController;
use App\Http\Controllers\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Student\StudentController;
use App\Http\Controllers\Student\TransactionController as StudentTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
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

    // ==========================
    // Voucher Validation (AJAX)
    // ==========================
    Route::post('/voucher/validate', [VoucherController::class, 'validate'])->name('voucher.validate');

    // ======================
    // Student Routes
    // ======================
        Route::middleware('role:student')->group(function () {



            // List & detail course
            Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
            Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');
            Route::get('/courses/{course}/materials/{material}', [\App\Http\Controllers\Student\StudentController::class, 'viewMaterial'])->name('courses.materials.view');
            Route::post('/courses/{course}/materials/{material}/complete', [\App\Http\Controllers\Student\StudentController::class, 'markMaterialComplete'])->name('courses.materials.complete');
            Route::get('/courses/{course}/materials/{material}/quiz', [\App\Http\Controllers\Student\StudentController::class, 'quiz'])->name('courses.materials.quiz');
            Route::post('/courses/{course}/materials/{material}/quiz/submit', [\App\Http\Controllers\Student\StudentController::class, 'quizSubmit'])->name('courses.materials.quiz.submit');

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

        // Course List & Detail
        Route::get('/courses', [StudentCourseController::class, 'index'])->name('courses.index');
        Route::get('/courses/{course}', [StudentCourseController::class, 'show'])->name('courses.show');

        // Enroll (redirect to checkout)
        Route::post('/courses/{course}/enroll', function(Kursus $course) {
            return redirect()->route('transactions.checkout', $course);
        })->name('courses.enroll');

        // Learn Course
        Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->name('student.course.learn');


        // My Courses
        Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/checkout/{course}', [StudentTransactionController::class, 'checkout'])->name('checkout');
            Route::post('/process/{course}', [StudentTransactionController::class, 'process'])->name('process');
            Route::get('/{transaction}', [StudentTransactionController::class, 'show'])->name('show');
            Route::post('/{transaction}/confirm', [StudentTransactionController::class, 'confirm'])->name('confirm');
            Route::post('/{transaction}/cancel', [StudentTransactionController::class, 'cancel'])->name('cancel');
        });

        // My Transactions
        Route::get('/my-transactions', [StudentTransactionController::class, 'myTransactions'])->name('my-transactions');


        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');

        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');


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

            // Dashboard (using controller)
           Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            // Users management
            Route::resource('users', UserController::class);

            // Courses management
            Route::resource('courses', AdminCourseController::class);

            // Transactions management

            Route::get('transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');
            Route::get('transactions/{transaction}', [AdminTransactionController::class, 'show'])->name('transactions.show');
            Route::patch('transactions/{transaction}/status', [AdminTransactionController::class, 'updateStatus'])->name('transactions.updateStatus');
            Route::delete('transactions/{transaction}', [AdminTransactionController::class, 'destroy'])->name('transactions.destroy');
            Route::get('transactions-export', [AdminTransactionController::class, 'export'])->name('transactions.export');

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

            // Course Materials Management
            Route::prefix('courses/{course}')->name('courses.')->group(function () {
                Route::get('materials', [\App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('materials.index');
                Route::get('materials/create', [\App\Http\Controllers\Admin\MaterialController::class, 'create'])->name('materials.create');
                Route::post('materials', [\App\Http\Controllers\Admin\MaterialController::class, 'store'])->name('materials.store');
                Route::get('materials/{material}/edit', [\App\Http\Controllers\Admin\MaterialController::class, 'edit'])->name('materials.edit');
                Route::put('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'update'])->name('materials.update');
                Route::delete('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('materials.destroy');
            });

            // Question Bank Management
            Route::resource('question-banks', \App\Http\Controllers\Admin\QuestionBankController::class);
            Route::get('/question-banks/{questionBank}/create-question', [\App\Http\Controllers\Admin\QuestionBankController::class, 'createQuestion'])->name('question-banks.create-question');
            Route::post('/question-banks/{questionBank}/questions', [\App\Http\Controllers\Admin\QuestionBankController::class, 'storeQuestion'])->name('question-banks.questions.store');
            Route::delete('/question-banks/{questionBank}/questions/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroyQuestion'])->name('question-banks.questions.destroy');
            
            // Question Import/Export
            Route::get('/question-banks/export/template', [\App\Http\Controllers\Admin\QuestionBankController::class, 'exportTemplate'])->name('question-banks.export-template');
            Route::post('/question-banks/{questionBank}/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'importQuestions'])->name('question-banks.import-questions');
            Route::get('/question-banks/{questionBank}/export', [\App\Http\Controllers\Admin\QuestionBankController::class, 'exportQuestions'])->name('question-banks.export-questions');

            // Assignment Management
            Route::get('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'index'])->name('assignments.index');
            Route::get('/assignments/create', [\App\Http\Controllers\Admin\AssignmentController::class, 'create'])->name('assignments.create');
            Route::post('/assignments', [\App\Http\Controllers\Admin\AssignmentController::class, 'store'])->name('assignments.store');
            Route::get('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'show'])->name('assignments.show');
            Route::get('/assignments/{assignment}/edit', [\App\Http\Controllers\Admin\AssignmentController::class, 'edit'])->name('assignments.edit');
            Route::put('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'update'])->name('assignments.update');
            Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Admin\AssignmentController::class, 'destroy'])->name('assignments.destroy');
            Route::get('/assignments/{assignment}/questions', [\App\Http\Controllers\Admin\AssignmentController::class, 'editQuestions'])->name('assignments.edit-questions');
            Route::post('/assignments/{assignment}/questions', [\App\Http\Controllers\Admin\AssignmentController::class, 'addQuestions'])->name('assignments.add-questions');
            Route::delete('/assignments/{assignment}/questions/{question}', [\App\Http\Controllers\Admin\AssignmentController::class, 'removeQuestion'])->name('assignments.remove-question');
            Route::post('/assignments/{assignment}/publish', [\App\Http\Controllers\Admin\AssignmentController::class, 'publish'])->name('assignments.publish');
            Route::post('/assignments/{assignment}/unpublish', [\App\Http\Controllers\Admin\AssignmentController::class, 'unpublish'])->name('assignments.unpublish');

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
             // Route preview materi (fix error)
            Route::post('/', [MaterialController::class, 'store'])->name('store');
            Route::get('/courses/{course}/materials/{material}/preview', [MaterialController::class, 'preview'])->name('materials.preview');

            Route::get('/courses/{course}/materials/{material}/edit', [MaterialController::class, 'edit'])->name('materials.edit');
            Route::put('/courses/{course}/materials/{material}', [MaterialController::class, 'update'])->name('materials.update');
            Route::delete('/courses/{course}/materials/{material}', [MaterialController::class, 'destroy'])->name('materials.destroy');

            // Question Bank Management
            Route::resource('question-banks', \App\Http\Controllers\Instructor\QuestionBankController::class);
            Route::get('/question-banks/{questionBank}/create-question', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'createQuestion'])->name('question-banks.create-question');
            Route::post('/question-banks/{questionBank}/questions', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'storeQuestion'])->name('question-banks.questions.store');
            Route::delete('/question-banks/{questionBank}/questions/{question}', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'destroyQuestion'])->name('question-banks.questions.destroy');
            
            // Question Import/Export
            Route::get('/question-banks/export/template', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'exportTemplate'])->name('question-banks.export-template');
            Route::post('/question-banks/{questionBank}/import', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'importQuestions'])->name('question-banks.import-questions');
            Route::get('/question-banks/{questionBank}/export', [\App\Http\Controllers\Instructor\QuestionBankController::class, 'exportQuestions'])->name('question-banks.export-questions');

            // Assignment Management
            Route::get('/assignments', [\App\Http\Controllers\Instructor\AssignmentController::class, 'index'])->name('assignments.index');
            Route::get('/assignments/create', [\App\Http\Controllers\Instructor\AssignmentController::class, 'create'])->name('assignments.create');
            Route::post('/assignments', [\App\Http\Controllers\Instructor\AssignmentController::class, 'store'])->name('assignments.store');
            Route::get('/assignments/{assignment}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'show'])->name('assignments.show');
            Route::get('/assignments/{assignment}/edit', [\App\Http\Controllers\Instructor\AssignmentController::class, 'edit'])->name('assignments.edit');
            Route::put('/assignments/{assignment}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'update'])->name('assignments.update');
            Route::delete('/assignments/{assignment}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'destroy'])->name('assignments.destroy');
            Route::get('/assignments/{assignment}/questions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'editQuestions'])->name('assignments.edit-questions');
            Route::post('/assignments/{assignment}/questions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'addQuestions'])->name('assignments.add-questions');
            Route::post('/assignments/{assignment}/questions/create', [\App\Http\Controllers\Instructor\AssignmentController::class, 'storeQuestion'])->name('assignments.store-question');
            Route::delete('/assignments/{assignment}/questions/{question}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'removeQuestion'])->name('assignments.remove-question');
            Route::post('/assignments/{assignment}/publish', [\App\Http\Controllers\Instructor\AssignmentController::class, 'publish'])->name('assignments.publish');
            Route::post('/assignments/{assignment}/unpublish', [\App\Http\Controllers\Instructor\AssignmentController::class, 'unpublish'])->name('assignments.unpublish');

            // Quick quiz creation from course detail
            Route::post('/courses/{course}/quizzes', [\App\Http\Controllers\Instructor\AssignmentController::class, 'quickCreateFromCourse'])->name('courses.quizzes.store');

            // Section Management (INSTRUKTUR)
            Route::resource('courses.sections', \App\Http\Controllers\Instructor\SectionController::class)->shallow();
        });
});
