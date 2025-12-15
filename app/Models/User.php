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
 * @property string|null $avatar
 * @property string|null $google_id
 * @property string|null $jenis_kelamin
 * @property string $role
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string|null $password
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
        'avatar',
        'google_id',
        'jenis_kelamin',
        'profesi',
        'email_verified_at',
        'first_name',
        'last_name',
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

    public function instructorCourses()
    {
        return $this->hasMany(Kursus::class, 'instructor_id');
    }

    /**
     * Relasi: courses yang dibuat (sebagai admin/creator)
     */
    public function createdCourses()
    {
        return $this->hasMany(Kursus::class, 'created_by');
    }

    /**
     * Relasi: enrollments (pendaftaran course)
     */
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'user_id');
    }

    /**
     * Relasi: transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'user_id');
    }

    /**
     * Get full name
     */
    public function getFullNameAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return $this->first_name . ' ' . $this->last_name;
        }
        return $this->name;
    }

    /**
     * Get avatar URL
     */
    public function getAvatarUrlAttribute()
    {
        if ($this->avatar_path) {
            if (str_starts_with($this->avatar_path, 'http')) {
                return $this->avatar_path;
            }
            return asset('storage/' . $this->avatar_path);
        }
        return null;
    }

    /**
     * Get initials for avatar placeholder
     */
    public function getInitialsAttribute()
    {
        if ($this->first_name && $this->last_name) {
            return strtoupper(substr($this->first_name, 0, 1) . substr($this->last_name, 0, 1));
        }
        return strtoupper(substr($this->name, 0, 2));
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



    

    // --- Relasi ke Notifications ---
    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at')->orderBy('created_at', 'desc');
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

    // --- Google OAuth helper ---
    public function isGoogleUser()
    {
        return !is_null($this->google_id);
    }
}
