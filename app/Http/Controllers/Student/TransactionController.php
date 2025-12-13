<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use App\Models\Notification;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    /**
     * Show checkout page with transaction details
     */
    public function checkout(Kursus $course)
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

        // Check for pending transaction
        $pendingTransaction = Transaction::where('user_id', Auth::id())
            ->where('kursus_id', $course->id)
            ->where('status', 'pending')
            ->where('payment_deadline', '>', now())
            ->first();

        if ($pendingTransaction) {
            return redirect()
                ->route('transactions.show', $pendingTransaction)
                ->with('info', 'Anda memiliki transaksi yang belum diselesaikan.');
        }

        $course->load('pembuat');

        // Calculate pricing
        $hargaAsli = $course->harga ?? $course->price ?? 0;
        $hargaDiskon = ($course->discount_price && $course->discount_price > 0) 
            ? $course->discount_price 
            : null;
        
        $totalBayar = $hargaDiskon ?? $hargaAsli;
        $diskonPersen = ($hargaDiskon && $hargaAsli > 0)
            ? round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100)
            : 0;

        return view('student.transactions.checkout', compact(
            'course',
            'hargaAsli',
            'hargaDiskon',
            'totalBayar',
            'diskonPersen'
        ));
    }

    /**
     * Process transaction creation with Midtrans Snap Token
     */
    public function process(Request $request, Kursus $course)
    {
        $request->validate([
            'payment_method' => 'nullable|in:bank_transfer,e_wallet,virtual_account',
            'payment_channel' => 'nullable|string',
            'notes' => 'nullable|string|max:500',
            'voucher_id' => 'nullable|exists:vouchers,id',
            'voucher_discount' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Calculate pricing
            $hargaAsli = $course->harga ?? $course->price ?? 0;
            $hargaDiskon = ($course->discount_price && $course->discount_price > 0) 
                ? $course->discount_price 
                : null;
            
            $totalBayar = $hargaDiskon ?? $hargaAsli;
            $diskonPersen = ($hargaDiskon && $hargaAsli > 0)
                ? round((($hargaAsli - $hargaDiskon) / $hargaAsli) * 100)
                : 0;

            // Apply voucher if provided
            $voucherDiscount = 0;
            $voucherId = null;

            if ($request->filled('voucher_id')) {
                $voucher = Voucher::find($request->voucher_id);

                if ($voucher && $voucher->canBeUsedBy(Auth::id(), $course->id)) {
                    $voucherDiscount = $voucher->calculateDiscount($totalBayar);
                    $totalBayar -= $voucherDiscount;
                    $voucherId = $voucher->id;
                }
            }

            // Ensure total is not negative
            $totalBayar = max(0, $totalBayar);

            // Create transaction
            $transaction = Transaction::create([
                'transaction_code' => Transaction::generateTransactionCode(),
                'user_id' => Auth::id(),
                'kursus_id' => $course->id,
                'harga_asli' => $hargaAsli,
                'harga_diskon' => $hargaDiskon,
                'total_bayar' => $totalBayar,
                'diskon_persen' => $diskonPersen,
                'payment_method' => $request->payment_method,
                'payment_channel' => $request->payment_channel,
                'payment_deadline' => now()->addHours(24),
                'notes' => $request->notes,
                'status' => 'pending',
            ]);

            // Record voucher usage if applied
            if ($voucherId && $voucherDiscount > 0) {
                VoucherUsage::create([
                    'voucher_id' => $voucherId,
                    'user_id' => Auth::id(),
                    'transaction_id' => $transaction->id,
                    'discount_amount' => $voucherDiscount,
                    'used_at' => now(),
                ]);

                $voucher->incrementUsage();
            }

            // Generate Snap Token
            $midtransService = new MidtransService();
            $snapToken = $midtransService->generateSnapToken($transaction);
            $transaction->update(['snap_token' => $snapToken]);

            DB::commit();

            // Return JSON response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat',
                'transaction_id' => $transaction->id,
                'transaction_code' => $transaction->transaction_code,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Transaction Process Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Complete payment and create enrollment (Called after Midtrans payment success)
     */
    public function completePayment(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|integer|exists:transactions,id',
            'transaction_code' => 'required|string',
        ]);

        try {
            $transaction = Transaction::findOrFail($request->transaction_id);

            // Authorization check
            if ($transaction->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 403);
            }

            DB::beginTransaction();

            // Mark as paid
            $transaction->markAsPaid();

            // Create enrollment if not exists
            $existingEnrollment = Enrollment::where('user_id', $transaction->user_id)
                ->where('kursus_id', $transaction->kursus_id)
                ->first();

            if (!$existingEnrollment) {
                Enrollment::create([
                    'user_id' => $transaction->user_id,
                    'kursus_id' => $transaction->kursus_id,
                    'status_pendaftaran' => 'active',
                    'tanggal_daftar' => now(),
                ]);

                // Notify instructor yang mengampuh kursus ini
                $course = $transaction->kursus;
                if ($course) {
                    // Prioritas: instructor_id, jika tidak ada baru pembuat (admin)
                    $instructorId = $course->instructor_id ?: $course->pembuat;
                    
                    // Hanya kirim notifikasi ke instructor, bukan admin
                    if ($instructorId && $instructorId != $course->pembuat) {
                        Notification::create([
                            'user_id' => $instructorId,
                            'title' => 'Pendaftar baru',
                            'message' => 'Pengguna ' . Auth::user()->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                            'type' => 'info',
                        ]);
                    } elseif ($instructorId == $course->pembuat && $course->pembuat) {
                        // Jika tidak ada instructor_id, kirim ke pembuat (admin/creator)
                        Notification::create([
                            'user_id' => $course->pembuat,
                            'title' => 'Pendaftar baru',
                            'message' => 'Pengguna ' . Auth::user()->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                            'type' => 'info',
                        ]);
                    }
                }
            }

            // Create notification for user
            Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Berhasil',
                'message' => 'Pembayaran untuk kursus "' . $transaction->kursus->judul . '" telah berhasil dikonfirmasi. Selamat belajar!',
                'type' => 'success',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dan Anda telah terdaftar di kursus',
                'redirect_url' => route('student.course.learn', $transaction->kursus),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Complete Payment Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Check payment status (for polling from frontend)
     */
    public function checkStatus(Request $request)
    {
        $code = $request->query('code');
        $transaction = Transaction::where('transaction_code', $code)->first();

        if (!$transaction || $transaction->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'status' => 'not_found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'paid_at' => $transaction->paid_at,
        ]);
    }

    /**
     * Show transaction detail
     */
    public function show(Transaction $transaction)
    {
        // Authorization check
        if ($transaction->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $transaction->load(['kursus.pembuat', 'user']);

        // Check if expired
        if ($transaction->isPending() && $transaction->isExpired()) {
            $transaction->markAsExpired();
        }

        return view('student.transactions.show', compact('transaction'));
    }

    /**
     * Simulate payment confirmation (for development)
     */
    public function confirm(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$transaction->isPending()) {
            return redirect()
                ->route('transactions.show', $transaction)
                ->with('error', 'Transaksi sudah diproses.');
        }

        try {
            DB::beginTransaction();

            // Mark as paid
            $transaction->markAsPaid();

            // Create enrollment
            Enrollment::create([
                'user_id' => $transaction->user_id,
                'kursus_id' => $transaction->kursus_id,
                'status_pendaftaran' => 'active',
                'tanggal_daftar' => now(),
            ]);

            // Notify instructor yang mengampuh kursus ini
            $course = $transaction->kursus;
            if ($course) {
                // Prioritas: instructor_id, jika tidak ada baru pembuat (admin)
                $instructorId = $course->instructor_id ?: $course->pembuat;
                
                // Hanya kirim notifikasi ke instructor, bukan admin
                if ($instructorId && $instructorId != $course->pembuat) {
                    Notification::create([
                        'user_id' => $instructorId,
                        'title' => 'Pendaftar baru',
                        'message' => 'Pengguna ' . Auth::user()->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                        'type' => 'info',
                    ]);
                } elseif ($instructorId == $course->pembuat && $course->pembuat) {
                    // Jika tidak ada instructor_id, kirim ke pembuat (admin/creator)
                    Notification::create([
                        'user_id' => $course->pembuat,
                        'title' => 'Pendaftar baru',
                        'message' => 'Pengguna ' . Auth::user()->name . ' mendaftar kursus "' . ($course->judul ?? $course->title) . '".',
                        'type' => 'info',
                    ]);
                }
            }

            // Create notification for successful payment
            Notification::create([
                'user_id' => $transaction->user_id,
                'title' => 'Pembayaran Berhasil',
                'message' => 'Pembayaran untuk kursus "' . $transaction->kursus->judul . '" telah berhasil dikonfirmasi. Selamat belajar!',
                'type' => 'success',
            ]);

            DB::commit();

            return redirect()
                ->route('student.course.learn', $transaction->kursus)
                ->with('success', 'Pembayaran berhasil! Selamat belajar!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Cancel transaction
     */
    public function cancel(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        if (!$transaction->isPending()) {
            return back()->with('error', 'Hanya transaksi pending yang bisa dibatalkan.');
        }

        try {
            DB::beginTransaction();

            $transaction->update(['status' => 'cancelled']);

            // If voucher was used, restore usage count
            $voucherUsage = VoucherUsage::where('transaction_id', $transaction->id)->first();
            if ($voucherUsage) {
                $voucherUsage->voucher->decrement('used_count');
                $voucherUsage->delete();
            }

            DB::commit();

            return redirect()
                ->route('courses.show', $transaction->kursus)
                ->with('info', 'Transaksi dibatalkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * My transactions list
     */
    public function myTransactions()
    {
        $transactions = Transaction::where('user_id', Auth::id())
            ->with('kursus')
            ->latest()
            ->paginate(10);

        return view('student.transactions.index', compact('transactions'));
    }

    /**
     * Get payment details from Midtrans (payment method and channel)
     * Called after Midtrans payment to retrieve actual payment method used
     */
    public function getPaymentDetails(Request $request)
    {
        $request->validate([
            'transaction_code' => 'required|string',
        ]);

        try {
            $transaction = Transaction::where('transaction_code', $request->transaction_code)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            // Get transaction details from Midtrans
            $midtransService = new MidtransService();
            $paymentDetails = $midtransService->getTransactionDetails($request->transaction_code);

            \Log::info('Payment Details Retrieved:', [
                'payment_type' => $paymentDetails['payment_type'],
                'payment_channel' => $paymentDetails['payment_channel'],
                'full_response' => $paymentDetails['full_details'],
            ]);

            // Update transaction with actual payment details from Midtrans
            $transaction->update([
                'payment_method' => $paymentDetails['payment_type'],
                'payment_channel' => $paymentDetails['payment_channel'],
            ]);

            return response()->json([
                'success' => true,
                'payment_method' => $paymentDetails['payment_type'],
                'payment_channel' => $paymentDetails['payment_channel'],
                'transaction_status' => $paymentDetails['transaction_status'],
            ]);

        } catch (\Exception $e) {
            \Log::error('Get Payment Details Error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pembayaran: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Debug endpoint - Get full Midtrans response for a transaction
     * Used for debugging payment channel extraction
     * Access: GET /debug/transactions/{transaction_code}/midtrans-response
     */
    public function debugMidtransResponse($transactionCode)
    {
        try {
            $transaction = Transaction::where('transaction_code', $transactionCode)
                ->where('user_id', Auth::id())
                ->first();

            if (!$transaction) {
                return response()->json([
                    'success' => false,
                    'message' => 'Transaksi tidak ditemukan',
                ], 404);
            }

            // Get raw Midtrans response
            $midtransService = new MidtransService();
            $midtransStatus = $midtransService->getTransactionStatus($transactionCode);

            // Convert to array for better viewing
            $response = json_decode(json_encode($midtransStatus), true);

            return response()->json([
                'success' => true,
                'transaction_code' => $transactionCode,
                'payment_method' => $response['payment_type'] ?? null,
                'midtrans_full_response' => $response,
                'all_keys' => array_keys($response),
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }
}
