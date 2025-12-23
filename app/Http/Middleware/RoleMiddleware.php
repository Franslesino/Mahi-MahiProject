<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware RoleMiddleware
 * 
 * Middleware ini memvalidasi bahwa user sudah login dan memiliki role yang sesuai.
 * Jika user tidak login atau role tidak match, akses akan ditolak dengan status 403.
 */
class RoleMiddleware
{
    /**
     * Handle incoming request - Memproses request yang masuk
     * 
     * @param $request - Object HTTP request
     * @param Closure $next - Closure untuk melanjutkan request
     * @param $role - Role yang diizinkan untuk mengakses route
     * @return Response - Response dari middleware berikutnya atau abort 403
     */
    public function handle($request, Closure $next, $role)
    {
        // Step 1: Cek apakah user login DAN role user sesuai dengan role yang diizinkan
        // Menggunakan operator OR (||) untuk menolak jika salah satu kondisi tidak terpenuhi
        if (!Auth::check() || Auth::user()->role !== $role) {
            // Jika tidak login atau role tidak sesuai, abort dengan status 403 (Forbidden)
            abort(403, 'Unauthorized');
        }

        // Step 2: Jika validasi berhasil, lanjutkan request ke middleware/controller berikutnya
        return $next($request);
    }
}