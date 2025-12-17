<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_code',
        'user_id',
        'kursus_id',
        'harga_asli',
        'harga_diskon',
        'total_bayar',
        'diskon_persen',
        'payment_method',
        'payment_channel',
        'payment_details',
        'status',
        'payment_deadline',
        'paid_at',
        'expired_at',
        'notes',
        'invoice_url',
        'snap_token',
        'voucher_discount_amount',
    ];

    protected $casts = [
        'harga_asli' => 'decimal:2',
        'harga_diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'paid_at' => 'datetime',
        'expired_at' => 'datetime',
        'payment_details' => 'array',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired');
    }

    // Helper Methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function isExpired()
    {
        // If already marked as expired
        if ($this->status === 'expired') {
            return true;
        }

        // If pending and past deadline, auto-mark as expired for consistency
        if ($this->status === 'pending' && $this->payment_deadline && Carbon::now()->gt($this->payment_deadline)) {
            $this->markAsExpired();
            return true;
        }

        return false;
    }

    public function markAsPaid()
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);
    }

    public function markAsExpired()
    {
        $this->update([
            'status' => 'expired',
            'expired_at' => now(),
        ]);
    }

    /**
     * Generate Midtrans Snap Token
     */
    public function generateSnapToken()
    {
        try {
            if ($this->snap_token && $this->status === 'pending') {
                return $this->snap_token;
            }

            $midtransService = new \App\Services\MidtransService();
            $snapToken = $midtransService->generateSnapToken($this);

            // Save token to database
            $this->update(['snap_token' => $snapToken]);

            return $snapToken;
        } catch (\Exception $e) {
            \Log::error('Failed to generate snap token: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update transaction status berdasarkan notifikasi Midtrans
     */
    public function handleMidtransNotification($notification)
    {
        $midtransService = new \App\Services\MidtransService();
        return $midtransService->handleNotification($notification);
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => '<span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs rounded-full font-semibold">Menunggu Pembayaran</span>',
            'paid' => '<span class="px-3 py-1 bg-green-100 text-green-700 text-xs rounded-full font-semibold">Pembayaran Sukses</span>',
            'expired' => '<span class="px-3 py-1 bg-red-100 text-red-700 text-xs rounded-full font-semibold">Kadaluarsa</span>',
            'cancelled' => '<span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-semibold">Dibatalkan</span>',
            'refunded' => '<span class="px-3 py-1 bg-purple-100 text-purple-700 text-xs rounded-full font-semibold">Dikembalikan</span>',
            default => '<span class="px-3 py-1 bg-gray-100 text-gray-700 text-xs rounded-full font-semibold">Unknown</span>',
        };
    }

    public function getFormattedDeadlineAttribute()
    {
        if (!$this->payment_deadline) return null;
        return $this->payment_deadline->locale('id')->isoFormat('D MMMM YYYY, HH:mm');
    }

    // Generate unique transaction code
    public static function generateTransactionCode()
    {
        do {
            $code = 'TRX-' . strtoupper(uniqid());
        } while (self::where('transaction_code', $code)->exists());
        
        return $code;
    }
}