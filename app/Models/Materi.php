<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'kursus_id',
        'judul',
        'isi',
        'url_konten',
        'urutan',
        'status_terkunci',
    ];

    protected $casts = [
        'status_terkunci' => 'boolean',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function progress()
    {
        return $this->hasMany(Progress::class, 'materi_id');
    }
}
