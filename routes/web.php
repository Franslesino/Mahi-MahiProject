<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman home dengan data dummy
Route::get('/', function () {
    $courses = collect([
        (object)[
            'id' => 1,
            'title' => 'Python for Everybody Specialization',
            'category' => 'Programming',
            'image' => 'https://images.unsplash.com/photo-1461749280684-dccba630e2f6?w=400',
            'videos' => 20,
            'mode' => 'Hybrid',
            'price' => 450000,
            'rating' => 4.8,
            'badge' => null,
        ],
        (object)[
            'id' => 2,
            'title' => 'UI/UX Design Fundamentals',
            'category' => 'Design',
            'image' => 'https://images.unsplash.com/photo-1561070791-2526d30994b5?w=400',
            'videos' => 15,
            'mode' => 'Online',
            'price' => 300000,
            'rating' => 4.6,
            'badge' => null,
        ],
        (object)[
            'id' => 3,
            'title' => 'Flutter Mobile Development',
            'category' => 'Mobile',
            'image' => 'https://images.unsplash.com/photo-1504639725590-34d0984388bd?w=400',
            'videos' => 35,
            'mode' => 'Online',
            'price' => 550000,
            'rating' => 5.0,
            'badge' => 'NEW',
        ],
        (object)[
            'id' => 4,
            'title' => 'Data Science Essentials',
            'category' => 'Data Science',
            'image' => 'https://images.unsplash.com/photo-1551288049-bebda4e38f71?w=400',
            'videos' => 30,
            'mode' => 'Hybrid',
            'price' => 400000,
            'rating' => 4.7,
            'badge' => null,
        ],
        (object)[
            'id' => 5,
            'title' => 'Full Stack Web Development',
            'category' => 'Web Development',
            'image' => 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?w=400',
            'videos' => 40,
            'mode' => 'Online',
            'price' => 500000,
            'rating' => 4.9,
            'badge' => 'HOT',
        ],
        (object)[
            'id' => 6,
            'title' => 'Blender 3D Modeling',
            'category' => '3D Design',
            'image' => 'https://images.unsplash.com/photo-1626785774573-4b799315345d?w=400',
            'videos' => 25,
            'mode' => 'Online',
            'price' => 250000,
            'rating' => 4.8,
            'badge' => null,
        ],
        (object)[
            'id' => 7,
            'title' => 'Digital Marketing Mastery',
            'category' => 'Marketing',
            'image' => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=400',
            'videos' => 18,
            'mode' => 'Online',
            'price' => 350000,
            'rating' => 4.5,
            'badge' => null,
        ],
        (object)[
            'id' => 8,
            'title' => 'Machine Learning Basics',
            'category' => 'AI/ML',
            'image' => 'https://images.unsplash.com/photo-1555949963-aa79dcee981c?w=400',
            'videos' => 32,
            'mode' => 'Hybrid',
            'price' => 600000,
            'rating' => 4.9,
            'badge' => 'NEW',
        ],
    ]);

    return view('home.index', compact('courses'));
})->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (butuh login)
Route::middleware('auth')->group(function () {
    Route::get('/my-courses', function () {
        return view('my-courses');
    })->name('my-courses');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

Route::get('/admin/dashboard', function () {
    return view('admin.dashboard');
})->middleware('auth');

Route::get('/user/dashboard', function () {
    $courses = []; // sementara kosong
    return view('home.index', compact('courses'));
})->middleware('auth');