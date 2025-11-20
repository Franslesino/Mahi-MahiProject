<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $judul
 * @property string|null $deskripsi
 * @property float $harga
 * @property string|null $status
 * @property bool $status_berbayar
 * @property bool $status_diterbitkan
 * @property int|null $pembuat
 * @property string|null $kategori
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Kursus extends Model
{
    use HasFactory;

    protected $table = 'kursus';

    protected $fillable = [
        'judul',
        'deskripsi',
        'harga',
        'status',
        'status_berbayar',
        'status_diterbitkan',
        'pembuat',
        'kategori',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'status_berbayar' => 'boolean',
        'status_diterbitkan' => 'boolean',
    ];

    // Relasi ke User (pembuat)
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat');
    }

    // Relasi ke Materi
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }

    // Relasi ke Enrollment
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }

    // Relasi ke PengajarKursus
    public function pengajarKursus()
    {
        return $this->hasMany(PengajarKursus::class, 'kursus_id');
    }

    // Relasi ke Quiz
    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'kursus_id');
    }

    // Relasi ke Diskon
    public function diskon()
    {
        return $this->hasMany(Diskon::class, 'kursus_id');
    }

    // Relasi ke ItemPesanan
    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'kursus_id');
    }
}
