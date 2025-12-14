<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemPesanan extends Model
{
    protected $table = 'item_pesanan';

    protected $fillable = [
        'pesanan_id',
        'kursus_id',
        'jumlah',
        'harga_satuan',
        'total_item',
    ];

    protected $casts = [
        'harga_satuan' => 'decimal:2',
        'total_item' => 'decimal:2',
        'dibuat_pada' => 'datetime',
    ];

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_id');
    }

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}
