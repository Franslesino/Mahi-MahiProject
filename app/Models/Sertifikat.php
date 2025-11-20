<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    protected $table = 'sertifikat';

    protected $fillable = [
        'enrollment_id',
        'kode_sertifikat',
        'tanggal_diterbitkan',
        'url_unduhan',
    ];

    protected $casts = [
        'tanggal_diterbitkan' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }
}
