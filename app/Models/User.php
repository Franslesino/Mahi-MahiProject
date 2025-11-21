<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $phone
 * @property string|null $no_telepon
 * @property string|null $profile_url
 * @property string|null $jenis_kelamin
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'no_telepon',
        'profile_url',
        'jenis_kelamin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // --- Relasi ke Kursus ---
    public function kursusAsInstructor()
    {
        return $this->hasMany(Kursus::class, 'pembuat');
    }

    public function pengajarKursus()
    {
        return $this->hasMany(PengajarKursus::class, 'pengajar_id');
    }

    // --- Relasi ke Enrollments ---
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    // --- Relasi ke Materi ---
    public function materi()
    {
        return $this->hasMany(Materi::class);
    }

    // --- Relasi ke Pesanan ---
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class);
    }

    // --- Relasi ke Jawaban Peserta ---
    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class);
    }

    // --- Role helper ---
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }
}