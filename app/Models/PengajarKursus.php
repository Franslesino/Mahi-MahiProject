<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajarKursus extends Model
{
    protected $table = 'pengajar_kursus';

    protected $fillable = [
        'kursus_id',
        'pengajar_id',
        'tanggal_ditugaskan',
        'keterangan_peran',
    ];

    protected $casts = [
        'tanggal_ditugaskan' => 'datetime',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function pengajar()
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }
}
