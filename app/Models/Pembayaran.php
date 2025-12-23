<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas pembayaran.
 */
class Pembayaran extends Model
{
    protected $table = 'pembayaran';

    protected $fillable = [
        'pesanan_id',
        'user_id',
        'jumlah_bayar',
        'tanggal_pembayaran',
        'metode_pembayaran',
        'status_pembayaran',
        'catatan',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_pembayaran' => 'datetime',
    ];

    /**
     * Relasi belongsTo ke Pesanan.
     */
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
