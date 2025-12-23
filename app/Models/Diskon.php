<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas diskon.
 */
class Diskon extends Model
{
    protected $table = 'diskon';

    protected $fillable = [
        'kode_diskon',
        'jenis_diskon',
        'nilai_diskon',
        'mulai_berlaku',
        'berakhir_pada',
        'batas_penggunaan',
        'penggunaan',
        'status_diskon',
        'kursus_id',
    ];

    protected $casts = [
        'nilai_diskon' => 'decimal:2',
        'mulai_berlaku' => 'datetime',
        'berakhir_pada' => 'datetime',
        'status_diskon' => 'boolean',
        'dibuat_pada' => 'datetime',
    ];

    /**
     * Relasi belongsTo ke Kursus.
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }
}
