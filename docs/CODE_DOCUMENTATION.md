# DOKUMENTASI KODE PROGRAM
## UpGreenius E-Learning Platform

---

## Metadata
- **Prepared by:** Tim Pengembang UpGreenius
- **Framework:** Laravel 10.x
- **Database:** PostgreSQL
- **Tanggal:** 17 Desember 2024

---

## Daftar Isi
1. [Arsitektur Sistem](#1-arsitektur-sistem)
2. [Struktur Direktori](#2-struktur-direktori)
3. [Models](#3-models)
4. [Controllers](#4-controllers)
5. [Routes](#5-routes)
6. [Views](#6-views)
7. [Middleware](#7-middleware)
8. [Database](#8-database)
9. [Integrasi External](#9-integrasi-external)

---

## 1. Arsitektur Sistem

### 1.1 Diagram Arsitektur

```
┌─────────────────────────────────────────────────────────────────┐
│                         CLIENT (Browser)                        │
└──────────────────────────────┬──────────────────────────────────┘
                               │
                               ▼
┌─────────────────────────────────────────────────────────────────┐
│                      LARAVEL APPLICATION                        │
│  ┌─────────────┐  ┌─────────────┐  ┌─────────────────────────┐ │
│  │   Routes    │→ │ Controllers │→ │ Views (Blade)           │ │
│  └─────────────┘  └──────┬──────┘  └─────────────────────────┘ │
│                          │                                      │
│                          ▼                                      │
│  ┌─────────────────────────────────────────────────────────────┐│
│  │                    Models (Eloquent ORM)                    ││
│  └─────────────────────────────────────────────────────────────┘│
└──────────────────────────────┬──────────────────────────────────┘
                               │
          ┌────────────────────┼────────────────────┐
          ▼                    ▼                    ▼
┌─────────────────┐  ┌─────────────────┐  ┌─────────────────┐
│   PostgreSQL    │  │    Midtrans     │  │ Supabase Storage│
│    Database     │  │ Payment Gateway │  │  (File Storage) │
└─────────────────┘  └─────────────────┘  └─────────────────┘
```

### 1.2 Technology Stack

| Layer | Technology |
|-------|------------|
| Frontend | Blade Templates, TailwindCSS, Alpine.js |
| Backend | Laravel 10.x, PHP 8.1+ |
| Database | PostgreSQL 14+ |
| Authentication | Laravel Breeze, Google OAuth (Socialite) |
| Payment | Midtrans Payment Gateway |
| Storage | Supabase Storage |
| Cache | File-based (Laravel Cache) |

---

## 2. Struktur Direktori

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/               # Admin controllers
│   │   ├── Instructor/          # Instructor controllers
│   │   ├── Student/             # Student controllers
│   │   ├── Auth/                # Authentication controllers
│   │   ├── AuthController.php   # Main auth controller
│   │   └── ProfileController.php
│   ├── Middleware/
│   │   └── RoleMiddleware.php   # Role-based access
│   └── Requests/                # Form requests
├── Models/                      # Eloquent models (32 total)
├── Notifications/               # Email notifications
└── Providers/                   # Service providers

config/                          # Configuration files
database/
├── migrations/                  # Database migrations
└── seeders/                     # Database seeders

resources/
├── views/
│   ├── admin/                   # Admin views
│   ├── instructor/              # Instructor views
│   ├── student/                 # Student views
│   ├── auth/                    # Authentication views
│   ├── layouts/                 # Layout templates
│   └── components/              # Blade components
└── css/                         # Stylesheets

routes/
└── web.php                      # Web routes (251 routes)

tests/
├── Feature/                     # Feature tests
└── Unit/                        # Unit tests
```

---

## 3. Models

### 3.1 Daftar Model (32 Total)

| No | Model | Table | Description |
|----|-------|-------|-------------|
| 1 | User | users | Data pengguna |
| 2 | Kursus | kursus | Data kursus |
| 3 | Materi | materi | Materi pembelajaran |
| 4 | Section | sections | Section/modul kursus |
| 5 | Enrollment | enrollments | Pendaftaran kursus |
| 6 | Transaction | transactions | Transaksi pembayaran |
| 7 | Question | questions | Soal quiz |
| 8 | QuestionBank | question_banks | Bank soal |
| 9 | QuestionOption | question_options | Opsi jawaban |
| 10 | Assignment | assignments | Tugas/Quiz |
| 11 | JawabanPeserta | jawaban_peserta | Jawaban peserta |
| 12 | ProgressMateri | progress_materi | Progress materi |
| 13 | Sertifikat | sertifikats | Sertifikat |
| 14 | Notification | notifications | Notifikasi user |
| 15 | Voucher | vouchers | Voucher diskon |
| 16 | PromoBanner | promo_banners | Banner promosi |

### 3.2 Model Relationships

#### User Model

```php
// app/Models/User.php

class User extends Authenticatable
{
    // User memiliki banyak enrollment
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    // User sebagai instructor memiliki banyak kursus
    public function instructorCourses()
    {
        return $this->hasMany(Kursus::class, 'instructor_id');
    }
    
    // User memiliki banyak transaksi
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    
    // User memiliki banyak notifikasi
    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
```

#### Kursus Model

```php
// app/Models/Kursus.php

class Kursus extends Model
{
    // Kursus milik instructor
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    // Kursus memiliki banyak section
    public function sections()
    {
        return $this->hasMany(Section::class, 'kursus_id');
    }
    
    // Kursus memiliki banyak materi
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }
    
    // Kursus memiliki banyak enrollment
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }
}
```

---

## 4. Controllers

### 4.1 Daftar Controllers

| Controller | Location | Methods | Description |
|------------|----------|---------|-------------|
| **AuthController** | `app/Http/Controllers/` | 8 | Login, Register, OAuth |
| **ProfileController** | `app/Http/Controllers/` | 4 | Profile management |

#### Admin Controllers

| Controller | Methods | Description |
|------------|---------|-------------|
| DashboardController | 1 | Admin dashboard |
| UserController | 8 | User CRUD + bulk delete + export |
| CourseController | 15 | Course management |
| TransactionController | 5 | Transaction management |
| VoucherController | 6 | Voucher management |
| QuestionBankController | 12 | Question bank management |

#### Instructor Controllers

| Controller | Methods | Description |
|------------|---------|-------------|
| InstructorController | 1 | Instructor dashboard |
| MaterialController | 14 | Material CRUD |
| QuestionBankController | 14 | Question bank management |
| AssignmentController | 12 | Assignment/Quiz management |
| FinalQuizController | 8 | Final quiz management |

#### Student Controllers

| Controller | Methods | Description |
|------------|---------|-------------|
| CourseController | 2 | Browse courses |
| StudentController | 8 | Learning, progress, certificate |
| TransactionController | 14 | Payment, checkout |
| FinalQuizController | 6 | Take final quiz |

### 4.2 Controller Detail

#### AuthController

```php
// app/Http/Controllers/AuthController.php

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    
    // Proses login manual
    public function login(Request $request)
    
    // Menampilkan halaman register
    public function showRegister()
    
    // Proses registrasi
    public function register(Request $request)
    
    // Redirect ke Google OAuth
    public function redirectToGoogle()
    
    // Handle callback dari Google
    public function handleGoogleCallback()
    
    // Proses logout
    public function logout()
    
    // Redirect berdasarkan role user
    protected function redirectBasedOnRole()
}
```

#### TransactionController (Student)

```php
// app/Http/Controllers/Student/TransactionController.php

class TransactionController extends Controller
{
    // Halaman checkout
    public function checkout(Kursus $course)
    
    // Proses pembayaran
    public function process(Request $request, Kursus $course)
    
    // Detail transaksi
    public function show(Transaction $transaction)
    
    // Complete payment callback
    public function completePayment(Request $request)
    
    // Midtrans finish callback
    public function finish(Request $request)
    
    // Regenerate expired snap token
    public function regenerateSnapToken(Transaction $transaction)
    
    // Daftar transaksi user
    public function myTransactions()
}
```

#### QuestionBankController (Instructor)

```php
// app/Http/Controllers/Instructor/QuestionBankController.php

class QuestionBankController extends Controller
{
    // CRUD Bank Soal
    public function index()           // Daftar bank soal
    public function create()          // Form create
    public function store(Request)    // Simpan bank soal
    public function show(QuestionBank)// Detail bank soal
    public function edit(QuestionBank)// Form edit
    public function update(Request)   // Update bank soal
    public function destroy(QuestionBank) // Hapus bank soal
    
    // CRUD Soal dalam Bank
    public function createQuestion(QuestionBank)
    public function storeQuestion(Request, QuestionBank)
    public function editQuestion(QuestionBank, Question)
    public function updateQuestion(Request, QuestionBank, Question)
    public function destroyQuestion(QuestionBank, Question)
    
    // Import/Export
    public function exportTemplate()              // Download template CSV
    public function importQuestions(Request)      // Import dari CSV/Excel
    public function exportQuestions(QuestionBank) // Export ke CSV
}
```

---

## 5. Routes

### 5.1 Route Summary

| Prefix | Middleware | Total Routes | Description |
|--------|------------|--------------|-------------|
| `/` | guest | 15 | Public routes |
| `/auth` | guest | 4 | Authentication |
| `/courses` | auth, role:student | 25 | Student courses |
| `/transactions` | auth, role:student | 15 | Payments |
| `/instructor` | auth, role:instructor | 45 | Instructor panel |
| `/admin` | auth, role:admin | 60 | Admin panel |
| **Total** | - | **251** | - |

### 5.2 Route Examples

#### Authentication Routes

```php
// routes/web.php

// Login & Register
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Google OAuth
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback']);

// Password Reset
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->name('password.update');
```

#### Student Routes

```php
Route::middleware(['auth', 'role:student'])->group(function () {
    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
    
    // Learning
    Route::get('/courses/{course}/learn', [StudentController::class, 'learn'])->name('student.course.learn');
    Route::get('/courses/{course}/materials/{material}', [StudentController::class, 'viewMaterial']);
    Route::post('/courses/{course}/materials/{material}/complete', [StudentController::class, 'markMaterialComplete']);
    
    // Transactions
    Route::get('/transactions/checkout/{course}', [TransactionController::class, 'checkout']);
    Route::post('/transactions/process/{course}', [TransactionController::class, 'process']);
});
```

#### Admin Routes

```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Users CRUD
    Route::resource('users', UserController::class);
    Route::post('users/bulk-destroy', [UserController::class, 'bulkDestroy'])->name('users.bulk-destroy');
    Route::get('users-export', [UserController::class, 'export'])->name('users.export');
    
    // Courses
    Route::resource('courses', CourseController::class);
    
    // Transactions
    Route::get('transactions', [TransactionController::class, 'index'])->name('transactions.index');
});
```

---

## 6. Views

### 6.1 Layout Structure

```
resources/views/
├── layouts/
│   ├── app.blade.php        # Main app layout
│   ├── admin.blade.php      # Admin layout
│   ├── instructor.blade.php # Instructor layout
│   └── guest.blade.php      # Guest layout (login/register)
```

### 6.2 View Components

| Component | Location | Usage |
|-----------|----------|-------|
| Navigation | `layouts/` | Header navigation |
| Sidebar | `layouts/` | Admin/Instructor sidebar |
| Footer | `layouts/` | Footer component |
| Card | `components/` | Card UI component |
| Modal | `components/` | Modal dialogs |
| Alert | `components/` | Flash messages |

### 6.3 Blade Directives Used

```blade
{{-- Authentication check --}}
@auth / @guest

{{-- Role check --}}
@role('admin') / @endrole

{{-- Loop --}}
@foreach / @forelse / @empty

{{-- Conditionals --}}
@if / @elseif / @else

{{-- Include components --}}
@include('components.card')

{{-- Extend layouts --}}
@extends('layouts.app')
@section('content') / @endsection

{{-- Push scripts/styles --}}
@push('scripts') / @endpush
```

---

## 7. Middleware

### 7.1 Custom Middleware

#### RoleMiddleware

```php
// app/Http/Middleware/RoleMiddleware.php

class RoleMiddleware
{
    public function handle($request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect('login');
        }
        
        if (Auth::user()->role !== $role) {
            abort(403, 'Unauthorized access.');
        }
        
        return $next($request);
    }
}
```

**Penggunaan:**
```php
Route::middleware('role:admin')->group(function () {
    // Admin only routes
});

Route::middleware('role:instructor')->group(function () {
    // Instructor only routes
});

Route::middleware('role:student')->group(function () {
    // Student only routes
});
```

### 7.2 Built-in Middleware

| Middleware | Purpose |
|------------|---------|
| `auth` | Require authentication |
| `guest` | Only for non-authenticated users |
| `verified` | Require email verification |
| `throttle` | Rate limiting |

---

## 8. Database

### 8.1 Entity Relationship Diagram

```
┌─────────────┐       ┌─────────────┐       ┌─────────────┐
│    users    │───┐   │   kursus    │───┐   │   materi    │
├─────────────┤   │   ├─────────────┤   │   ├─────────────┤
│ id          │   └──→│ instructor_id│   │   │ kursus_id   │
│ name        │       │ id          │←──┘   │ section_id  │
│ email       │       │ judul       │       │ judul       │
│ role        │       │ harga       │       │ type        │
│ password    │       │ kategori    │       │ content     │
└─────────────┘       └─────────────┘       └─────────────┘
      │                     │
      │                     │
      ▼                     ▼
┌─────────────┐       ┌─────────────┐
│ enrollments │       │ transactions│
├─────────────┤       ├─────────────┤
│ user_id     │       │ user_id     │
│ kursus_id   │       │ kursus_id   │
│ status      │       │ total_bayar │
└─────────────┘       │ status      │
                      └─────────────┘
```

### 8.2 Key Tables

| Table | Primary Key | Foreign Keys |
|-------|-------------|--------------|
| users | id | - |
| kursus | id | instructor_id → users.id |
| materi | id | kursus_id, section_id |
| enrollments | id | user_id, kursus_id |
| transactions | id | user_id, kursus_id |
| questions | id | question_bank_id |
| question_options | id | question_id |

---

## 9. Integrasi External

### 9.1 Midtrans Payment Gateway

```php
// config/midtrans.php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => true,
    'is_3ds' => true,
];
```

**Usage in Controller:**
```php
// Create Snap Token
\Midtrans\Config::$serverKey = config('midtrans.server_key');
\Midtrans\Config::$isProduction = config('midtrans.is_production');

$params = [
    'transaction_details' => [
        'order_id' => $orderId,
        'gross_amount' => $amount,
    ],
    'customer_details' => [
        'first_name' => $user->name,
        'email' => $user->email,
    ],
];

$snapToken = \Midtrans\Snap::getSnapToken($params);
```

### 9.2 Google OAuth

```php
// config/services.php

'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],
```

**Usage in Controller:**
```php
// Redirect to Google
return Socialite::driver('google')->redirect();

// Handle callback
$googleUser = Socialite::driver('google')->user();
$user = User::where('google_id', $googleUser->getId())->first();
```

### 9.3 Supabase Storage

```php
// .env

SUPABASE_URL=https://xxx.supabase.co
SUPABASE_KEY=your-api-key
SUPABASE_BUCKET=uploads
```

---

## Changelog

| Version | Date | Changes |
|---------|------|---------|
| 1.0 | 17 Des 2024 | Initial documentation |

---

*Dokumentasi dibuat oleh: Tim Pengembang UpGreenius*
*Politeknik Negeri Jakarta - TI 4A*
