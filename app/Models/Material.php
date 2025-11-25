<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


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
    'section_id',  // TAMBAHKAN INI
    'judul',
    'description',  // TAMBAHKAN INI
    'isi',
    'content',  // TAMBAHKAN INI
    'type',
    'url_konten',
    'file_url',  // TAMBAHKAN INI
    'duration',  // TAMBAHKAN INI
    'urutan',
    'status_terkunci',
    'is_preview',  // TAMBAHKAN INI
    'status',  // TAMBAHKAN INI
];

    protected $casts = [
        'status_terkunci' => 'boolean',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function section(): BelongsTo
{
    return $this->belongsTo(CourseSection::class, 'section_id');
}

    public function progress()
    {
        return $this->hasMany(Progress::class, 'materi_id');
    }
}