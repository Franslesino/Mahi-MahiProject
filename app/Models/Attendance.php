<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas absensi.
 */
class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendances';

    protected $fillable = [
        'class_session_id',
        'materi_id',
        'enrollment_id',
        'user_id',
        'status',
        'check_in_time',
        'catatan',
        'updated_by',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
    ];

    // Status options
    const STATUS_HADIR = 'hadir';
    const STATUS_TIDAK_HADIR = 'tidak_hadir';
    const STATUS_IZIN = 'izin';
    const STATUS_SAKIT = 'sakit';
    const STATUS_TERLAMBAT = 'terlambat';

    /**
     * Mengambil status options.
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_HADIR => 'Hadir',
            self::STATUS_TIDAK_HADIR => 'Tidak Hadir',
            self::STATUS_IZIN => 'Izin',
            self::STATUS_SAKIT => 'Sakit',
            self::STATUS_TERLAMBAT => 'Terlambat',
        ];
    }

    // Relasi ke ClassSession
    /**
     * Relasi belongsTo ke ClassSession.
     */
    public function classSession()
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id');
    }

    // Relasi ke Materi (for material-based class sessions)
    /**
     * Relasi belongsTo ke Materi.
     */
    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    // Relasi ke Enrollment
    /**
     * Relasi belongsTo ke Enrollment.
     */
    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class, 'enrollment_id');
    }

    // Relasi ke User (student)
    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Relasi ke User yang update (instructor)
    /**
     * Relasi belongsTo ke User.
     */
    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Accessor untuk label status
    /**
     * Accessor untuk atribut status label.
     */
    public function getStatusLabelAttribute()
    {
        return self::getStatusOptions()[$this->status] ?? $this->status;
    }

    // Accessor untuk badge warna
    /**
     * Accessor untuk atribut status badge.
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            self::STATUS_HADIR => 'bg-green-100 text-green-800',
            self::STATUS_TIDAK_HADIR => 'bg-red-100 text-red-800',
            self::STATUS_IZIN => 'bg-yellow-100 text-yellow-800',
            self::STATUS_SAKIT => 'bg-orange-100 text-orange-800',
            self::STATUS_TERLAMBAT => 'bg-blue-100 text-blue-800',
        ];

        $color = $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
        $label = $this->status_label;

        return "<span class=\"px-2 py-1 rounded-full text-xs font-medium {$color}\">{$label}</span>";
    }
}
