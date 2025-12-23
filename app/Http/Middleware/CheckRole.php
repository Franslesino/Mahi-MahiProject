<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware CheckRole
 * 
 * Middleware ini melakukan validasi role/peran user sebelum mengakses route tertentu.
 * Jika user belum login atau role tidak sesuai, akan menolak akses dan redirect/abort.
 */
class CheckRole
{
    /**
     * Handle incoming request - Memproses request yang masuk
     * 
     * @param Request $request - Object HTTP request
     * @param Closure $next - Closure untuk melanjutkan request ke middleware/controller berikutnya
     * @param string $role - Role yang diizinkan untuk mengakses route ini
     * @return Response - Response dari middleware berikutnya atau error response
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        // Step 1: Ambil user yang sedang login
        $user = Auth::user();

        // Step 2: Cek apakah user sudah login
        if (!$user) {
            // Jika belum login, redirect ke halaman login dengan pesan error
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Step 3: Cek apakah role user sesuai dengan role yang diizinkan
        if ($user->role !== $role) {
            // Jika role tidak sesuai, abort dengan status 403 (Forbidden)
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        // Step 4: Jika semua validasi berhasil, lanjutkan request ke middleware/controller berikutnya
        return $next($request);
    }
}