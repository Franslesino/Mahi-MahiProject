<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas pengajar kursus.
 */
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

    /**
     * Relasi belongsTo ke Kursus.
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    /**
     * Relasi belongsTo ke User.
     */
    public function pengajar()
    {
        return $this->belongsTo(User::class, 'pengajar_id');
    }
}
