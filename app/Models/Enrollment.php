<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

/**
 * Model untuk entitas pendaftaran.
 *
 * @property int $id
 * @property int $user_id
 * @property int $kursus_id
 * @property \Illuminate\Support\Carbon $tanggal_daftar
 * @property string $status_pendaftaran
 * @property \Illuminate\Support\Carbon|null $tanggal_selesai
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'kursus_id',
        'user_id',
        'status_pendaftaran',
        'tanggal_daftar',
        'tanggal_selesai',
    ];

    protected $casts = [
        'tanggal_daftar' => 'datetime',
        'tanggal_selesai' => 'datetime',
    ];

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi belongsTo ke Kursus.
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }

    /**
     * Relasi belongsTo ke Kursus.
     */
    public function course()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    /**
     * Relasi hasOne ke Sertifikat.
     */
    public function sertifikat()
    {
        $certificateFk = static::getCertificateForeignKey();

        if (!$certificateFk) {
            // Avoid error if schema is unknown; use neutral keys and always-false condition
            return $this->hasOne(Sertifikat::class, 'id', 'id')->whereRaw('1=0');
        }

        return $this->hasOne(Sertifikat::class, $certificateFk);
    }

    /**
     * Mengambil kunci asing sertifikat.
     */
    public static function getCertificateForeignKey(): ?string
    {
        static $cached = null;
        if ($cached !== null) {
            return $cached;
        }

        $cached = self::resolveCertificateForeignKey();
        return $cached;
    }

    /**
     * Menentukan kunci asing sertifikat.
     */
    private static function resolveCertificateForeignKey(): ?string
    {
        try {
            $table = (new Sertifikat)->getTable();
            $columns = Schema::getColumnListing($table);
        } catch (\Throwable $e) {
            return null;
        }

        $candidates = [
            'enrollment_id',
            'enrollments_id',
            'enrollmentid',
            'enrollmentsid',
            'enrollment',
            'enroll_id',
        ];

        foreach ($candidates as $col) {
            if (in_array($col, $columns, true)) {
                return $col;
            }
        }

        return null;
    }
}
