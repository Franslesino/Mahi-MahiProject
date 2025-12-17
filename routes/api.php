<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CourseController;
use App\Http\Controllers\Api\SectionController;
use App\Http\Controllers\Api\MaterialController;
use App\Http\Controllers\VoucherController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Auth (session cookie)
Route::middleware('web')
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

// Authenticated API
Route::middleware(['web', 'auth'])
    ->withoutMiddleware([VerifyCsrfToken::class])
    ->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/voucher/validate', [VoucherController::class, 'validateVoucher']);
    });

// Public course endpoints
Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/{course}', [CourseController::class, 'show']);

// Sections & Materials (read-only)
Route::get('/sections', [SectionController::class, 'index']);
Route::get('/sections/{section}', [SectionController::class, 'show']);
Route::get('/materials', [MaterialController::class, 'index']);
Route::get('/materials/{material}', [MaterialController::class, 'show']);
