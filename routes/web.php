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
use App\Models\PromoBanner;
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

    // Get active promo banners
    $promoBanners = PromoBanner::active()->get();

    return view('home.index', compact('courses', 'categoryCounts', 'promoBanners'));
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
        
        // Final Quiz Routes
        Route::get('/courses/{kursus}/final-quiz', [\App\Http\Controllers\Student\FinalQuizController::class, 'show'])->name('courses.final-quiz.show');
        Route::post('/courses/{kursus}/final-quiz/start', [\App\Http\Controllers\Student\FinalQuizController::class, 'start'])->name('courses.final-quiz.start');
        Route::get('/courses/{kursus}/final-quiz/{attempt}', [\App\Http\Controllers\Student\FinalQuizController::class, 'take'])->name('courses.final-quiz.take');
        Route::post('/courses/{kursus}/final-quiz/{attempt}/submit', [\App\Http\Controllers\Student\FinalQuizController::class, 'submit'])->name('courses.final-quiz.submit');
        Route::get('/courses/{kursus}/final-quiz/{attempt}/result', [\App\Http\Controllers\Student\FinalQuizController::class, 'result'])->name('courses.final-quiz.result');

        // Transactions
        Route::prefix('transactions')->name('transactions.')->group(function () {
            Route::get('/checkout/{course}', [StudentTransactionController::class, 'checkout'])->name('checkout');
            Route::post('/process/{course}', [StudentTransactionController::class, 'process'])->name('process');
            Route::post('/complete-payment', [StudentTransactionController::class, 'completePayment'])->name('complete-payment');
            Route::post('/get-payment-details', [StudentTransactionController::class, 'getPaymentDetails'])->name('get-payment-details');
            Route::get('/check-status', [StudentTransactionController::class, 'checkStatus'])->name('check-status');
            Route::get('/debug/{transactionCode}/midtrans-response', [StudentTransactionController::class, 'debugMidtransResponse'])->name('debug-midtrans');
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
                // Pastikan enrollment milik user
                $enrollment = Enrollment::where('id', $enrollment->id)
                    ->where('user_id', Auth::id())
                    ->first();

                if (!$enrollment) {
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
        Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
        Route::delete('/notifications', [\App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');
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
            
            // Admin Course Panel (like instructor panel)
            Route::prefix('courses/{course}')->name('courses.')->group(function () {
                Route::get('detail', [AdminCourseController::class, 'courseDetail'])->name('detail');
                Route::get('panel', [AdminCourseController::class, 'panel'])->name('panel');
                Route::get('modules', [AdminCourseController::class, 'modules'])->name('modules');
                Route::post('modules', [AdminCourseController::class, 'storeModule'])->name('modules.store');
                Route::put('modules/{section}', [AdminCourseController::class, 'updateModule'])->name('modules.update');
                Route::delete('modules/{section}', [AdminCourseController::class, 'destroyModule'])->name('modules.destroy');
                Route::get('modules/{section}/materials', [AdminCourseController::class, 'moduleMaterials'])->name('modules.materials');
                Route::post('modules/{section}/materials', [AdminCourseController::class, 'storeMaterial'])->name('modules.materials.store');
                Route::get('modules/{section}/materials/{material}/edit', [AdminCourseController::class, 'editMaterial'])->name('modules.materials.edit');
                Route::put('modules/{section}/materials/{material}', [AdminCourseController::class, 'updateMaterial'])->name('modules.materials.update');
                Route::delete('modules/{section}/materials/{material}', [AdminCourseController::class, 'destroyMaterial'])->name('modules.materials.destroy');
                
                // Preview material
                Route::get('materials/{material}/preview', [AdminCourseController::class, 'previewMaterial'])->name('materials.preview');
                
                // Quiz routes
                Route::post('quizzes', [AdminCourseController::class, 'storeQuiz'])->name('quizzes.store');
            });

            // Assignment/Quiz management routes for Admin (using instructor controller)
            Route::prefix('assignments')->name('assignments.')->group(function () {
                Route::get('/{assignment}/questions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'editQuestions'])->name('edit-questions');
                Route::post('/{assignment}/questions', [\App\Http\Controllers\Instructor\AssignmentController::class, 'addQuestions'])->name('add-questions');
                Route::post('/{assignment}/questions/create', [\App\Http\Controllers\Instructor\AssignmentController::class, 'storeQuestion'])->name('store-question');
                Route::delete('/{assignment}/questions/{question}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'removeQuestion'])->name('remove-question');
                Route::get('/{assignment}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'show'])->name('show');
                Route::put('/{assignment}', [\App\Http\Controllers\Instructor\AssignmentController::class, 'update'])->name('update');
                Route::post('/{assignment}/publish', [\App\Http\Controllers\Instructor\AssignmentController::class, 'publish'])->name('publish');
                Route::post('/{assignment}/unpublish', [\App\Http\Controllers\Instructor\AssignmentController::class, 'unpublish'])->name('unpublish');
            });

            // Final Quiz Routes for Admin
            Route::prefix('courses/{kursus}')->name('courses.')->group(function () {
                Route::get('/final-quiz', [\App\Http\Controllers\Admin\FinalQuizController::class, 'edit'])->name('final-quiz.edit');
                Route::put('/final-quiz', [\App\Http\Controllers\Admin\FinalQuizController::class, 'update'])->name('final-quiz.update');
                Route::get('/final-quiz/statistics', [\App\Http\Controllers\Admin\FinalQuizController::class, 'statistics'])->name('final-quiz.statistics');
                Route::get('/final-quiz/create-quiz', [\App\Http\Controllers\Admin\FinalQuizController::class, 'createQuiz'])->name('final-quiz.create-quiz');
                Route::post('/final-quiz/store-quiz', [\App\Http\Controllers\Admin\FinalQuizController::class, 'storeQuiz'])->name('final-quiz.store-quiz');
                Route::post('/final-quiz/import-from-bank', [\App\Http\Controllers\Admin\FinalQuizController::class, 'importFromBankSoal'])->name('final-quiz.import-from-bank');
                Route::post('/final-quiz/store-new-question', [\App\Http\Controllers\Admin\FinalQuizController::class, 'storeNewQuestion'])->name('final-quiz.store-new-question');
                Route::delete('/final-quiz/remove-question/{question}', [\App\Http\Controllers\Admin\FinalQuizController::class, 'removeQuestion'])->name('final-quiz.remove-question');
                Route::post('/final-quiz/toggle-activation', [\App\Http\Controllers\Admin\FinalQuizController::class, 'toggleActivation'])->name('final-quiz.toggle-activation');
            });

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

            // Promo Banners management
            Route::resource('promo-banners', \App\Http\Controllers\Admin\PromoBannerController::class);
            Route::patch('promo-banners/{promoBanner}/toggle', [\App\Http\Controllers\Admin\PromoBannerController::class, 'toggleStatus'])
                ->name('promo-banners.toggle');

            // Course Materials Management
            Route::prefix('courses/{course}')->name('courses.')->group(function () {
                Route::get('materials', [\App\Http\Controllers\Admin\MaterialController::class, 'index'])->name('materials.index');
                Route::get('materials/create', [\App\Http\Controllers\Admin\MaterialController::class, 'create'])->name('materials.create');
                Route::post('materials', [\App\Http\Controllers\Admin\MaterialController::class, 'store'])->name('materials.store');
                Route::get('materials/{material}/edit', [\App\Http\Controllers\Admin\MaterialController::class, 'edit'])->name('materials.edit');
                Route::put('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'update'])->name('materials.update');
                Route::delete('materials/{material}', [\App\Http\Controllers\Admin\MaterialController::class, 'destroy'])->name('materials.destroy');
            });

            // Bank Soal Management (New Unified System)
            Route::get('bank-soal/api', [\App\Http\Controllers\Instructor\BankSoalController::class, 'api'])->name('bank-soal.api');
            Route::resource('bank-soal', \App\Http\Controllers\Instructor\BankSoalController::class)->names([
                'index' => 'bank-soal.index',
                'create' => 'bank-soal.create',
                'store' => 'bank-soal.store',
                'edit' => 'bank-soal.edit',
                'update' => 'bank-soal.update',
                'destroy' => 'bank-soal.destroy',
            ]);
            
            // Question Bank Management
            Route::resource('question-banks', \App\Http\Controllers\Admin\QuestionBankController::class);
            Route::get('/question-banks/{questionBank}/create-question', [\App\Http\Controllers\Admin\QuestionBankController::class, 'createQuestion'])->name('question-banks.create-question');
            Route::post('/question-banks/{questionBank}/questions', [\App\Http\Controllers\Admin\QuestionBankController::class, 'storeQuestion'])->name('question-banks.questions.store');
            Route::delete('/question-banks/{questionBank}/questions/{question}', [\App\Http\Controllers\Admin\QuestionBankController::class, 'destroyQuestion'])->name('question-banks.questions.destroy');
            
            // Question Import/Export (Old System)
            Route::get('/question-banks/export/template', [\App\Http\Controllers\Admin\QuestionBankController::class, 'exportTemplate'])->name('question-banks.export-template');
            Route::post('/question-banks/{questionBank}/import', [\App\Http\Controllers\Admin\QuestionBankController::class, 'importQuestions'])->name('question-banks.import-questions');
            Route::get('/question-banks/{questionBank}/export', [\App\Http\Controllers\Admin\QuestionBankController::class, 'exportQuestions'])->name('question-banks.export-questions');
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

            // Notification Actions
            Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
            Route::post('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
            Route::delete('/notifications/{notification}', [\App\Http\Controllers\NotificationController::class, 'destroy'])->name('notifications.destroy');
            Route::delete('/notifications', [\App\Http\Controllers\NotificationController::class, 'destroyAll'])->name('notifications.destroy-all');

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

            // Final Quiz Management
            Route::get('/courses/{kursus}/final-quiz', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'edit'])->name('courses.final-quiz.edit');
            Route::put('/courses/{kursus}/final-quiz', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'update'])->name('courses.final-quiz.update');
            Route::get('/courses/{kursus}/final-quiz/statistics', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'statistics'])->name('courses.final-quiz.statistics');
            Route::get('/courses/{kursus}/final-quiz/create-quiz', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'createQuiz'])->name('courses.final-quiz.create-quiz');
            Route::post('/courses/{kursus}/final-quiz/store-quiz', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'storeQuiz'])->name('courses.final-quiz.store-quiz');
            Route::post('/courses/{kursus}/final-quiz/import-from-bank', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'importFromBankSoal'])->name('courses.final-quiz.import-from-bank');
            Route::post('/courses/{kursus}/final-quiz/store-new-question', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'storeNewQuestion'])->name('courses.final-quiz.store-new-question');
            Route::delete('/courses/{kursus}/final-quiz/remove-question/{question}', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'removeQuestion'])->name('courses.final-quiz.remove-question');
            Route::post('/courses/{kursus}/final-quiz/toggle-activation', [\App\Http\Controllers\Instructor\FinalQuizController::class, 'toggleActivation'])->name('courses.final-quiz.toggle-activation');
            
            Route::resource('bank-soal', \App\Http\Controllers\Instructor\BankSoalController::class)->names([
                'index' => 'bank-soal.index',
                'create' => 'bank-soal.create',
                'store' => 'bank-soal.store',
                'edit' => 'bank-soal.edit',
                'update' => 'bank-soal.update',
                'destroy' => 'bank-soal.destroy',
            ]);

            // Section Management (INSTRUKTUR)
            Route::resource('courses.sections', \App\Http\Controllers\Instructor\SectionController::class)->shallow();
        });
});
