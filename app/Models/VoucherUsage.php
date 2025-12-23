<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas voucher usage.
 */
class VoucherUsage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'voucher_id',
        'user_id',
        'transaction_id',
        'discount_amount',
        'used_at',
    ];

    protected $casts = [
        'discount_amount' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    // Relationships
    /**
     * Relasi belongsTo ke Voucher.
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi belongsTo ke Transaction.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}