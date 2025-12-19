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

        'videos',

        // final quiz settings
        'final_quiz_id',
        'min_passing_score',
        'max_quiz_attempts',
        'require_final_quiz',

        // time settings
        'access_duration_days',
        'purchase_deadline_date',

        // course method settings
        'metode',
        'lokasi',
        'alamat_lengkap',
        'kuota_peserta',
        'jadwal_mulai',
        'jadwal_selesai',
        'hari_kelas',
        'waktu_kelas',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'status_berbayar' => 'boolean',
        'status_diterbitkan' => 'boolean',

        'videos' => 'integer',
        'access_duration_days' => 'integer',
        'purchase_deadline_date' => 'datetime',
        'min_passing_score' => 'decimal:2',
        'max_quiz_attempts' => 'integer',
        'require_final_quiz' => 'boolean',
        'kuota_peserta' => 'integer',
        'jadwal_mulai' => 'date',
        'jadwal_selesai' => 'date',
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

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }
        if (str_starts_with($this->image, 'http')) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
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

    // quiz
    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'kursus_id');
    }

    // assignments / quiz assignments
    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'kursus_id');
    }

    // final quiz
    public function finalQuiz()
    {
        return $this->belongsTo(Quiz::class, 'final_quiz_id');
    }

    // admin yang membuat (kalau dipakai)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // class sessions untuk offline/hybrid
    public function classSessions()
    {
        return $this->hasMany(ClassSession::class, 'kursus_id')->orderBy('tanggal')->orderBy('waktu_mulai');
    }

    /*
    |--------------------------------------------------------------------------
    | METHOD ACCESSORS
    |--------------------------------------------------------------------------
    */

    // Badge metode kursus
    public function getMetodeBadgeAttribute()
    {
        $badges = [
            'online' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Online</span>',
            'offline' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Offline</span>',
            'hybrid' => '<span class="px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Hybrid</span>',
        ];

        return $badges[$this->metode ?? 'online'] ?? $badges['online'];
    }

    // Label metode
    public function getMetodeLabelAttribute()
    {
        $labels = [
            'online' => 'Online',
            'offline' => 'Offline',
            'hybrid' => 'Hybrid',
        ];

        return $labels[$this->metode ?? 'online'] ?? 'Online';
    }

    // Cek apakah perlu jadwal (offline atau hybrid)
    public function getRequiresScheduleAttribute()
    {
        return in_array($this->metode, ['offline', 'hybrid']);
    }

    // Cek apakah kuota masih tersedia
    public function getHasAvailableSlotsAttribute()
    {
        if (!$this->kuota_peserta) {
            return true; // unlimited
        }

        $enrolled = $this->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed'])
            ->count();

        return $enrolled < $this->kuota_peserta;
    }

    // Sisa slot
    public function getAvailableSlotsAttribute()
    {
        if (!$this->kuota_peserta) {
            return null; // unlimited
        }

        $enrolled = $this->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed'])
            ->count();

        return max(0, $this->kuota_peserta - $enrolled);
    }
}
