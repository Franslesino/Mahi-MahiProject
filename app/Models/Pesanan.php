<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'pesanan_id');
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'pesanan_id');
    }
}
