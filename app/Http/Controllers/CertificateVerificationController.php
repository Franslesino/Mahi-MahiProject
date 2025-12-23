<?php

namespace App\Http\Controllers;

use App\Models\Sertifikat;
use Illuminate\Http\Request;

/**
 * Controller untuk fitur verifikasi sertifikat.
 */
class CertificateVerificationController extends Controller
{
    /**
     * Show the verification page.
     */
    public function index()
    {
        return view('certificate.verify');
    }

    /**
     * Check the certificate code.
     */
    public function check(Request $request)
    {
        $request->validate([
            'kode' => 'required|string|max:255',
        ], [
            'kode.required' => 'Silakan masukkan kode sertifikat.',
        ]);

        $kode = trim($request->kode);

        // Search in kode_sertifikat
        $certificate = Sertifikat::with(['enrollment.user', 'enrollment.kursus'])
            ->where('kode_sertifikat', $kode)
            ->first();

        if (!$certificate) {
            return view('certificate.verify')->with('error', 'Sertifikat tidak ditemukan. Pastikan kode yang Anda masukkan benar.');
        }

        return view('certificate.verify', compact('certificate'));
    }
}
