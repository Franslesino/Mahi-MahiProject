<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Models\Kursus;
use App\Models\CourseSection;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/*
|--------------------------------------------------------------------------
| API Routes
| Prefix: /api (ditetapkan oleh bootstrap/app.php)
|--------------------------------------------------------------------------
*/

// Auth: register
Route::post('/register', function (Request $request) {
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:6|confirmed',
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role' => 'student',
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Registrasi berhasil',
        'user' => $user,
    ], 201);
});

// Auth: login
Route::post('/login', function (Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (!Auth::attempt($credentials)) {
        return response()->json([
            'success' => false,
            'message' => 'Email atau password salah',
        ], 401);
    }

    $request->session()->regenerate();

    return response()->json([
        'success' => true,
        'message' => 'Login berhasil',
        'user' => Auth::user(),
    ]);
});

// Auth: logout
Route::middleware('auth')->post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return response()->json([
        'success' => true,
        'message' => 'Logout berhasil',
    ]);
});

Route::get('/courses', function (Request $request) {
    $perPage = (int) ($request->query('per_page', 10));
    $query = Kursus::query()
        ->where('status_diterbitkan', true)
        ->latest();

    if ($search = $request->query('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('judul', 'ILIKE', '%' . $search . '%')
              ->orWhere('deskripsi', 'ILIKE', '%' . $search . '%');
        });
    }

    if ($category = $request->query('category')) {
        $query->where('kategori', $category);
    }

    $courses = $query->paginate($perPage);

    return response()->json([
        'success' => true,
        'data' => $courses->items(),
        'meta' => [
            'total' => $courses->total(),
            'per_page' => $courses->perPage(),
            'current_page' => $courses->currentPage(),
            'last_page' => $courses->lastPage(),
        ],
    ]);
});

Route::get('/sections', function (Request $request) {
    $query = CourseSection::with('materials')->orderBy('order');

    if ($courseId = $request->query('course_id')) {
        $query->where('course_id', $courseId);
    }

    $sections = $query->get();

    return response()->json([
        'success' => true,
        'data' => $sections,
    ]);
});

Route::get('/sections/{section}', function (CourseSection $section) {
    $section->load(['course', 'materials']);

    return response()->json([
        'success' => true,
        'data' => $section,
    ]);
});

Route::get('/materials', function (Request $request) {
    $query = Materi::with(['kursus', 'section'])->orderBy('created_at', 'desc');

    if ($type = $request->query('type')) {
        $query->where('type', $type);
    }

    if ($courseId = $request->query('course_id')) {
        $query->where('kursus_id', $courseId);
    }

    $materials = $query->paginate((int) $request->query('per_page', 10));

    return response()->json([
        'success' => true,
        'data' => $materials->items(),
        'meta' => [
            'total' => $materials->total(),
            'per_page' => $materials->perPage(),
            'current_page' => $materials->currentPage(),
            'last_page' => $materials->lastPage(),
        ],
    ]);
});

Route::get('/materials/{material}', function (Materi $material) {
    $material->load(['kursus', 'section']);

    return response()->json([
        'success' => true,
        'data' => $material,
    ]);
});
