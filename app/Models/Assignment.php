<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'kursus_id',
        'materi_id',
        'title',
        'description',
        'type',
        'duration_minutes',
        'time_limit',
        'passing_score',
        'start_date',
        'due_date',
        'show_results_immediately',
        'allow_multiple_attempts',
        'max_attempts',
        'randomize_questions',
        'is_published',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'due_date' => 'datetime',
        'show_results_immediately' => 'boolean',
        'allow_multiple_attempts' => 'boolean',
        'randomize_questions' => 'boolean',
        'is_published' => 'boolean',
        'duration_minutes' => 'integer',
        'time_limit' => 'integer',
        'passing_score' => 'integer',
        'max_attempts' => 'integer',
    ];

    /**
     * Get the course this assignment belongs to
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class);
    }

    /**
     * Get the material this assignment belongs to
     */
    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    /**
     * Get all questions in this assignment
     */
    public function questions()
    {
        return $this->belongsToMany(Question::class, 'assignment_questions')
            ->withPivot('order', 'points')
            ->orderBy('assignment_questions.order')
            ->withTimestamps();
    }

    /**
     * Get all submissions for this assignment
     */
    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    /**
     * Get submission for a specific user
     */
    public function submissionForUser($userId)
    {
        return $this->submissions()->where('user_id', $userId)->latest()->first();
    }

    /**
     * Check if assignment is available
     */
    public function isAvailable()
    {
        if (!$this->is_published) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->due_date && $now->gt($this->due_date)) {
            return false;
        }

        return true;
    }

    /**
     * Get total points
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('assignment_questions.points') 
            ?: $this->questions()->sum('questions.points');
    }
}
