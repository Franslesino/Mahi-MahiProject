<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $kursus_id
 * @property string $judul
 * @property string|null $isi
 * @property string|null $url_konten
 * @property int $urutan
 * @property bool $status_terkunci
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Material extends Model
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
