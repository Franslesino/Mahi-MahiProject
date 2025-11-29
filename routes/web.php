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
use App\Http\Controllers\MidtransNotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VoucherController;
use App\Models\Kursus;
use App\Models\Enrollment;
use Illuminate\Support\Facades\Auth;

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
        ->withCount([
            'materi',
            'materi as videos_count' => function ($q) {
                $q->where('type', 'video');
            },
            'enrollments as students_count' => function ($q) {
                $q->whereIn('status_pendaftaran', ['active', 'completed', 'paid']);
            },
        ])
        ->latest()
        ->paginate(12)
        ->withQueryString();

    // Category counts for pills
    $categoryCounts = Kursus::where('status_diterbitkan', true)
        ->selectRaw('kategori, COUNT(*) as total')
        ->groupBy('kategori')
        ->pluck('total', 'kategori');

    return view('home.index', compact('courses', 'categoryCounts'));
})->name('home');

// Terms & Conditions (public)
Route::get('/terms', function () {
    return view('student.courses.terms');
})->name('terms');

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

// Midtrans Webhook Notification (Public - no authentication)
Route::post('/api/midtrans/notification', [App\Http\Controllers\MidtransNotificationController::class, 'handleNotification'])->name('midtrans.notification');

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
        Route::get('/courses/{course}/materials/{material}', [StudentController::class, 'viewMaterial'])->name('courses.materials.view');
        Route::post('/courses/{course}/materials/{material}/complete', [StudentController::class, 'markMaterialComplete'])->name('courses.materials.complete');
        Route::get('/courses/{course}/materials/{material}/quiz', [StudentController::class, 'quiz'])->name('courses.materials.quiz');
        Route::post('/courses/{course}/materials/{material}/quiz/submit', [StudentController::class, 'quizSubmit'])->name('courses.materials.quiz.submit');

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/checkout/{course}', [StudentTransactionController::class, 'checkout'])->name('checkout');
            Route::post('/process/{course}', [StudentTransactionController::class, 'process'])->name('process');
            Route::post('/complete-payment', [StudentTransactionController::class, 'completePayment'])->name('complete-payment');
            Route::get('/check-status', [StudentTransactionController::class, 'checkStatus'])->name('check-status');
            Route::get('/{transaction}', [StudentTransactionController::class, 'show'])->name('show');
            Route::post('/{transaction}/confirm', [StudentTransactionController::class, 'confirm'])->name('confirm');
            Route::post('/{transaction}/cancel', [StudentTransactionController::class, 'cancel'])->name('cancel');
            // Midtrans callbacks
            Route::get('/finish', [StudentTransactionController::class, 'finish'])->name('finish');
            Route::get('/unfinish', [StudentTransactionController::class, 'unfinish'])->name('unfinish');
            Route::get('/error', [StudentTransactionController::class, 'error'])->name('error');
        });

        // My transactions
        Route::get('/my-transactions', [StudentTransactionController::class, 'myTransactions'])->name('my-transactions');

        // Enroll (redirect to checkout)
        Route::post('/courses/{course}/enroll', function (Kursus $course) {
            return redirect()->route('transactions.checkout', $course);
        })->name('courses.enroll');

        // Home student
        Route::get('/user', function (Request $request) {
            return redirect()->route('home', $request->only('search', 'category'));
        })->name('user.home');

        // Learn Course
        Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->name('student.course.learn');

        // My Courses
        Route::get('/my-courses', [StudentController::class, 'myCourses'])->name('my-courses');

        // ========================================
        // Certificate Routes (NEW!)
        // ========================================
        Route::prefix('student')->name('student.')->group(function () {
            // Download Certificate
            Route::get('/enrollment/{enrollment}/certificate/download', [StudentController::class, 'downloadCertificate'])
                ->name('certificate.download');
            
            // Preview Certificate (AJAX)
            Route::get('/enrollment/{enrollment}/certificate-preview', function (Enrollment $enrollment) {
                // Verify ownership
                if ($enrollment->user_id !== Auth::id()) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
                
                $certificate = $enrollment->sertifikat;
                
                if (!$certificate) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Sertifikat belum tersedia'
                    ], 404);
                }
                
                $issuedAt = $certificate->tanggal_terbit ?? $certificate->tanggal_diterbitkan ?? $certificate->created_at;
                $certificateNumber = $certificate->kode_sertifikat ?? $certificate->nomor_sertifikat ?? 'N/A';
                
                return response()->json([
                    'success' => true,
                    'url' => asset($certificate->url_unduhan),
                    'number' => $certificateNumber,
                    'issued_date' => $issuedAt ? $issuedAt->format('d F Y') : null
                ]);
            })->name('certificate.preview');
        });

        // Profile
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
        Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

        // Notifications
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
        Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
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

            // Bantuan (static)
            Route::get('/help', function () {
                return view('instructor.help');
            })->name('help');

            // Notifikasi (polling JSON)
            Route::get('/notifications/poll', function () {
                $user = \Illuminate\Support\Facades\Auth::user();
                $items = $user->notifications()->latest()->take(10)->get()->map(function ($n) {
                    return [
                        'id' => $n->id,
                        'title' => $n->title,
                        'message' => $n->message,
                        'type' => $n->type,
                        'unread' => $n->isUnread(),
                        'time' => $n->created_at?->diffForHumans(),
                    ];
                });
                return [
                    'unread' => $user->unreadNotifications()->count(),
                    'items' => $items,
                ];
            })->name('notifications.poll');

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
