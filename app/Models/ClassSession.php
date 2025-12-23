<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas kelas sesi.
 */
class ClassSession extends Model
{
    use HasFactory;

    protected $table = 'class_sessions';

    protected $fillable = [
        'kursus_id',
        'judul',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi',
        'tipe',
        'meeting_link',
        'catatan',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_mulai' => 'datetime:H:i',
        'waktu_selesai' => 'datetime:H:i',
    ];

    // Relasi ke Kursus
    /**
     * Relasi belongsTo ke Kursus.
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    // Alias
    /**
     * Menangani logika model.
     */
    public function course()
    {
        return $this->kursus();
    }

    // Relasi ke Attendances
    /**
     * Relasi hasMany ke Attendance.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'class_session_id');
    }

    // Accessor untuk format tanggal
    /**
     * Accessor untuk atribut formatted date.
     */
    public function getFormattedDateAttribute()
    {
        return $this->tanggal->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    // Accessor untuk format waktu
    /**
     * Accessor untuk atribut formatted time.
     */
    public function getFormattedTimeAttribute()
    {
        return $this->waktu_mulai->format('H:i') . ' - ' . $this->waktu_selesai->format('H:i');
    }

    // Scope untuk sesi yang akan datang
    /**
     * Scope query untuk upcoming.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('tanggal', '>=', now()->toDateString())
            ->where('status', 'scheduled')
            ->orderBy('tanggal')
            ->orderBy('waktu_mulai');
    }

    // Scope untuk sesi yang sudah lewat
    /**
     * Scope query untuk past.
     */
    public function scopePast($query)
    {
        return $query->where('tanggal', '<', now()->toDateString())
            ->orWhere('status', 'completed')
            ->orderBy('tanggal', 'desc');
    }

    // Hitung jumlah hadir
    /**
     * Accessor untuk atribut hadir count.
     */
    public function getHadirCountAttribute()
    {
        return $this->attendances()->where('status', 'hadir')->count();
    }

    // Hitung total enrolled students untuk kursus ini
    /**
     * Accessor untuk atribut total students.
     */
    public function getTotalStudentsAttribute()
    {
        return $this->kursus->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed'])
            ->count();
    }
}
