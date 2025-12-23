<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas progres.
 */
class Progress extends Model
{
    protected $table = 'progress';

    protected $fillable = [
        'enrollment_id',
        'materi_id',
        'status_progres',
        'presentase_progres',
        'tanggal_terakhir_akses',
    ];

    protected $casts = [
        'presentase_progres' => 'decimal:2',
        'tanggal_terakhir_akses' => 'datetime',
    ];

    /**
     * Relasi belongsTo ke Enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    /**
     * Relasi belongsTo ke Materi.
     */
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}
