<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Enrollment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CertificateController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:student']);
    }

    /**
     * Temukan enrollment berdasarkan id enrollment atau (fallback) course_id milik user.
     */
    protected function findEnrollmentOrAbort($id): Enrollment
    {
        $query = Enrollment::with(['kursus', 'user'])
            ->where('user_id', Auth::id())
            ->whereIn('status_pendaftaran', ['paid', 'completed', 'active']);

        // coba by enrollment id, jika tidak ada fallback by kursus_id / course_id
        $enrollment = (clone $query)->where('id', $id)->first();
        if (!$enrollment) {
            $enrollment = (clone $query)->where(function ($q) use ($id) {
                $q->where('kursus_id', $id)->orWhere('course_id', $id);
            })->first();
        }

        abort_unless($enrollment, 404);

        return $enrollment;
    }

    public function show($enrollmentId)
    {
        $enrollment = $this->findEnrollmentOrAbort($enrollmentId);
        return view('student.certificates.show', [
            'enrollment' => $enrollment,
            'user' => $enrollment->user,
            'course' => $enrollment->course,
            'issuedAt' => now(),
        ]);
    }

    public function download($enrollmentId)
    {
        $enrollment = $this->findEnrollmentOrAbort($enrollmentId);

        // Jika dompdf tersedia, gunakan untuk generate PDF, jika tidak fallback ke HTML download
        if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.certificates.show', [
                'enrollment' => $enrollment,
                'user' => $enrollment->user,
                'course' => $enrollment->course,
                'issuedAt' => now(),
                'downloadMode' => true,
            ])->setPaper('a4', 'landscape');

            $filename = 'certificate-' . $enrollment->id . '.pdf';
            return $pdf->download($filename);
        }

        // Fallback: kirim HTML sebagai file .html (agar tetap bisa diunduh)
        $html = view('student.certificates.show', [
            'enrollment' => $enrollment,
            'user' => $enrollment->user,
            'course' => $enrollment->course,
            'issuedAt' => now(),
            'downloadMode' => true,
        ])->render();

        return response($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'attachment; filename="certificate-' . $enrollment->id . '.html"',
        ]);
    }
}
