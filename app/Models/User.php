<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        
        'email',
       
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            
        ];
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    // Check if user is instructor
    public function isInstructor()
    {
        return $this->role === 'instructor';
    }

    // Check if user is student
    public function isStudent()
    {
        return $this->role === 'student';
    }

    // // Relationships
    // public function enrollments()
    // {
    //     return $this->hasMany(Enrollment::class);
    // }

    // public function createdCourses()
    // {
    //     return $this->hasMany(Course::class, 'created_by');
    // }
}