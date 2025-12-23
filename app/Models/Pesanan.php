<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas pesanan.
 */
class Pesanan extends Model
{
    protected $table = 'pesanan';

    protected $fillable = [
        'user_id',
        'nomor_pesanan',
        'subtotal',
        'jumlah_diskon',
        'total_bayar',
        'status_pembayaran',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'jumlah_diskon' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi hasMany ke ItemPesanan.
     */
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }
}
