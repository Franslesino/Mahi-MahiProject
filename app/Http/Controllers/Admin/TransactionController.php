<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $query = Transaction::with(['user', 'kursus']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment method
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Search by transaction code or user name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'ilike', "%{$search}%")
                                ->orWhere('email', 'ilike', "%{$search}%");
                  });
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->latest()->paginate(15)->withQueryString();

        // Calculate statistics
        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'paid' => Transaction::where('status', 'paid')->count(),
            'total_revenue' => Transaction::where('status', 'paid')->sum('total_bayar'),
            'today_revenue' => Transaction::where('status', 'paid')
                                        ->whereDate('paid_at', today())
                                        ->sum('total_bayar'),
            'this_month_revenue' => Transaction::where('status', 'paid')
                                              ->whereMonth('paid_at', now()->month)
                                              ->whereYear('paid_at', now()->year)
                                              ->sum('total_bayar'),
        ];

        return view('admin.transactions.index', compact('transactions', 'stats'));
    }

    public function show(Transaction $transaction)
    {
        $transaction->load(['user', 'kursus.pembuat']);
        
        return view('admin.transactions.show', compact('transaction'));
    }

    public function updateStatus(Request $request, Transaction $transaction)
    {
        $request->validate([
            'status' => 'required|in:pending,paid,expired,cancelled,refunded'
        ]);

        $oldStatus = $transaction->status;
        $transaction->status = $request->status;

        // Update timestamps based on status
        if ($request->status === 'paid' && $oldStatus !== 'paid') {
            $transaction->paid_at = now();
            
            // Enroll user to course if not already enrolled
            $enrollment = $transaction->user->enrollments()
                ->where('kursus_id', $transaction->kursus_id)
                ->first();
                
            if (!$enrollment) {
                $transaction->user->enrollments()->create([
                    'kursus_id' => $transaction->kursus_id,
                    'tanggal_enroll' => now(),
                ]);
            }
        }

        $transaction->save();

        return redirect()->back()->with('success', 'Status transaksi berhasil diperbarui');
    }

    public function destroy(Transaction $transaction)
    {
        // Only allow deletion of cancelled/expired transactions
        if (!in_array($transaction->status, ['cancelled', 'expired'])) {
            return redirect()->back()->with('error', 'Hanya transaksi yang dibatalkan atau kadaluarsa yang dapat dihapus');
        }

        $transaction->delete();

        return redirect()->route('admin.transactions.index')
            ->with('success', 'Transaksi berhasil dihapus');
    }

    public function export(Request $request)
    {
        // TODO: Implement export to Excel/PDF
        // You can use packages like maatwebsite/excel or barryvdh/laravel-dompdf
        
        return back()->with('info', 'Fitur export akan segera tersedia');
    }
}