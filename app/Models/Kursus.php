<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

        // field tambahan admin
        'image',
        'mode',
        'discount_price',
        'learning',
        'badge',
        'badge_color',
        'instructor_id',
        'created_by',
        'rating',
        'videos',
    ];

    protected $casts = [
        'harga'             => 'decimal:2',
        'discount_price'    => 'decimal:2',
        'status_berbayar'   => 'boolean',
        'status_diterbitkan'=> 'boolean',
        'rating'            => 'decimal:1',
        'videos'            => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSOR – bikin alias biar bisa pakai $course->title, price, dll
    |--------------------------------------------------------------------------
    */

    public function getTitleAttribute()
    {
        return $this->judul;
    }

    public function getDescriptionAttribute()
    {
        return $this->deskripsi;
    }

    public function getCategoryAttribute()
    {
        return $this->kategori;
    }

    public function getPriceAttribute()
    {
        return $this->harga;
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // pembuat kursus (admin)
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat');
    }

     public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // materi
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }

    // alias supaya bisa dipakai sebagai materials
    public function materials()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }

    public function pengajarKursus()
    {
        return $this->hasMany(PengajarKursus::class, 'kursus_id');
    }

    public function quiz()
    {
        return $this->hasMany(Quiz::class, 'kursus_id');
    }

    public function diskon()
    {
        return $this->hasMany(Diskon::class, 'kursus_id');
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'kursus_id');
    }

    // instruktur utama kursus
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function sections(): HasMany
{
    return $this->hasMany(CourseSection::class, 'course_id')->orderBy('order');
}

    // admin yang membuat (kalau dipakai)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}