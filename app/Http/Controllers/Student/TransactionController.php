<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Kursus;
use App\Models\Transaction;
use App\Models\Enrollment;
use App\Models\Voucher;
use App\Models\VoucherUsage;
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
     * Process transaction creation
     */
    public function process(Request $request, Kursus $course)
    {
        $request->validate([
            'payment_method' => 'required|in:bank_transfer,e_wallet,virtual_account',
            'payment_channel' => 'required|string',
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
                'payment_deadline' => now()->addHours(24), // 24 jam untuk bayar
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

                // Increment voucher usage count
                $voucher->incrementUsage();
            }

            DB::commit();

            return redirect()
                ->route('transactions.show', $transaction)
                ->with('success', 'Transaksi berhasil dibuat. Silakan lakukan pembayaran.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage())
                ->withInput();
        }
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

            // If voucher was used, restore usage count (optional)
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
}