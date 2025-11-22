<?php 
 
 
namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Enrollment;
use App\Models\Pesanan;
use App\Models\ItemPesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
protected $middleware = ['auth', 'role:student'];

public function __construct()
{
}

/**
* Tampilkan halaman checkout pembayaran
*/
public function showCheckout(Kursus $course)
{
    // Check if already enrolled
    $existingEnrollment = Enrollment::where('user_id', Auth::id())
        ->where('kursus_id', $course->id)
        ->first();

    if ($existingEnrollment) {
        return redirect()
            ->route('courses.show', $course)
            ->with('error', 'Anda sudah terdaftar di kursus ini!');
    }

    return view('student.payment.checkout', compact('course'));
}

/**
* Proses pembayaran dan buat pesanan
*/
public function processPayment(Request $request, Kursus $course)
{
    $request->validate([
        'metode_pembayaran' => 'required|string',
        'subtotal' => 'required|numeric',
        'diskon' => 'required|numeric',
        'pajak' => 'required|numeric',
        'total_bayar' => 'required|numeric',
    ]);

    // Check if already enrolled
    $existingEnrollment = Enrollment::where('user_id', Auth::id())
        ->where('kursus_id', $course->id)
        ->first();

    if ($existingEnrollment) {
        return redirect()
            ->route('courses.show', $course)
            ->with('error', 'Anda sudah terdaftar di kursus ini!');
    }

    try {
        // Generate nomor pesanan unik
        $nomorPesanan = 'PES-' . Auth::id() . '-' . time();

        // Buat pesanan
        $pesanan = Pesanan::create([
            'user_id' => Auth::id(),
            'nomor_pesanan' => $nomorPesanan,
            'subtotal' => $request->subtotal,
            'jumlah_diskon' => $request->diskon,
            'total_bayar' => $request->total_bayar,
        ]);

        // Buat item pesanan
        ItemPesanan::create([
            'pesanan_id' => $pesanan->id,
            'kursus_id' => $course->id,
            'jumlah' => 1,
            'harga_satuan' => $request->subtotal,
            'total_harga' => $request->subtotal,
        ]);

        // Buat enrollment otomatis
        Enrollment::create([
            'user_id' => Auth::id(),
            'kursus_id' => $course->id,
            'status_pendaftaran' => 'active',
            'tanggal_daftar' => now(),
        ]);

        return redirect()
            ->route('student.course.learn', $course)
            ->with('success', 'Pembayaran berhasil! Anda sekarang terdaftar di kursus ini.');

    } catch (\Exception $e) {
        return redirect()
            ->route('courses.show', $course)
            ->with('error', 'Terjadi kesalahan saat memproses pembayaran: ' . $e->getMessage());
    }
}

/**
* Enroll student to course
*/
public function enroll(Request $request, Kursus $course)
{
// Check if already enrolled
$existingEnrollment = Enrollment::where('user_id', Auth::id())
->where('kursus_id', $course->id)
->first();

if ($existingEnrollment) {
return redirect()
->route('courses.show', $course)
->with('error', 'Anda sudah terdaftar di kursus ini!');
}

// Create enrollment
$enrollment = Enrollment::create([
'user_id' => Auth::id(),
'kursus_id' => $course->id,
'status_pendaftaran' => 'active',
'tanggal_daftar' => now(),
]);

return redirect()
->route('student.course.learn', $course)
->with('success', 'Selamat! Anda berhasil mendaftar kursus ini.');
}

/**
* Show learning page
*/
public function learn(Kursus $course)
{
// Check if enrolled
$enrollment = Enrollment::where('user_id', Auth::id())
->where('kursus_id', $course->id)
->where('status_pendaftaran', 'active')
->firstOrFail();

$course->load([
'pembuat',
'materi' => function($query) {
$query->orderBy('urutan');
}
]);

$materials = $course->materi;
$currentMaterial = $materials->first();

return view('student.learn', compact('course', 'materials', 'currentMaterial', 'enrollment'));
}

/**
* My courses page
*/
public function myCourses()
{
$enrollments = Enrollment::where('user_id', Auth::id())
->where('status_pendaftaran', 'active')
->with(['kursus.pembuat'])
->latest()
->get();

return view('student.my-courses', compact('enrollments'));
}
}