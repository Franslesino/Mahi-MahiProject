<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware NoCacheHeaders
 * 
 * Middleware ini menambahkan HTTP headers untuk mencegah browser menyimpan cache
 * dari halaman yang memerlukan autentikasi (seperti halaman login, dashboard, dll).
 * Ini memastikan halaman sensitif selalu fresh dan tidak disimpan di browser cache.
 */
class NoCacheHeaders
{
    /**
     * Handle incoming request - Memproses response sebelum dikirim ke client
     * 
     * @param Request $request - Object HTTP request
     * @param Closure $next - Closure untuk melanjutkan request ke middleware/controller berikutnya
     * @return Response - Response dengan tambahan no-cache headers
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Step 1: Lanjutkan request ke middleware/controller berikutnya
        $response = $next($request);

        // Step 2: Tambahkan HTTP headers untuk disable cache di browser
        return $response->withHeaders([
            // Instruksi Cache-Control untuk mencegah cache dan selalu validasi
            'Cache-Control' => 'no-cache, no-store, max-age=0, must-revalidate',
            // Header Pragma legacy untuk backward compatibility dengan HTTP/1.0
            'Pragma' => 'no-cache',
            // Set tanggal expired ke masa lalu untuk memastikan halaman dianggap sudah expired
            'Expires' => 'Sat, 01 Jan 2000 00:00:00 GMT',
        ]);
    }
}
