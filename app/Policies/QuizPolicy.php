<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use App\Models\Kursus;
use Illuminate\Auth\Access\Response;

class QuizPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->role === 'admin' || $user->role === 'instructor';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // Admin bisa lihat semua
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructor bisa lihat quiz dari kursus mereka
        if ($user->role === 'instructor') {
            $course = $quiz->kursus;
            return $course && ($course->instructor_id == $user->id || 
                   $course->pembuat == $user->id || 
                   $course->created_by == $user->id);
        }
        
        // Student bisa lihat quiz yang mereka ikuti
        if ($user->role === 'student') {
            return $quiz->kursus->enrollments()
                ->where('user_id', $user->id)
                ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
                ->exists();
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Kursus $course): bool
    {
        // Admin dapat mengakses semua
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructor hanya bisa mengakses kursus yang dia ajar
        if ($user->role === 'instructor') {
            return $course->instructor_id == $user->id || 
                   $course->pembuat == $user->id || 
                   $course->created_by == $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        $course = $quiz->kursus;
        if (!$course) {
            return false;
        }
        
        // Admin dapat mengakses semua
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructor hanya bisa mengakses kursus yang dia ajar
        if ($user->role === 'instructor') {
            return $course->instructor_id == $user->id || 
                   $course->pembuat == $user->id || 
                   $course->created_by == $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quiz $quiz): bool
    {
        $course = $quiz->kursus;
        if (!$course) {
            return false;
        }
        
        // Admin dapat mengakses semua
        if ($user->role === 'admin') {
            return true;
        }
        
        // Instructor hanya bisa mengakses kursus yang dia ajar
        if ($user->role === 'instructor') {
            return $course->instructor_id == $user->id || 
                   $course->pembuat == $user->id || 
                   $course->created_by == $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return $user->role === 'admin';
    }
}
