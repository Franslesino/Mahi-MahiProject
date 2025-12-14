<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }
}
