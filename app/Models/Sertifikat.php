<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    // Use correct table name matching migration
    protected $table = 'sertifikats';

    protected $fillable = [
        'enrollment_id',
        'nomor_sertifikat',
        'kode_sertifikat',
        'tanggal_terbit',
        'tanggal_diterbitkan',
        'url_unduhan',
    ];

    protected $casts = [
        'tanggal_terbit' => 'datetime',
        'tanggal_diterbitkan' => 'datetime',
    ];

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }
}

