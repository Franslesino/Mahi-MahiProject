<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas submission.
 */
class Submission extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'user_id',
        'attempt_number',
        'answers',
        'score',
        'percentage',
        'status',
        'started_at',
        'submitted_at',
        'graded_at',
        'graded_by',
        'feedback',
    ];

    protected $casts = [
        'answers' => 'array',
        'score' => 'decimal:2',
        'percentage' => 'decimal:2',
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'attempt_number' => 'integer',
    ];

    /**
     * Get the assignment this submission belongs to
     */
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    /**
     * Get the user who submitted
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the grader
     */
    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }

    /**
     * Check if submission is graded
     */
    public function isGraded()
    {
        return $this->status === 'graded';
    }

    /**
     * Check if passed
     */
    public function isPassed()
    {
        if (!$this->isGraded()) {
            return false;
        }

        return $this->percentage >= $this->assignment->passing_score;
    }

    /**
     * Get answer for a specific question
     */
    public function getAnswerForQuestion($questionId)
    {
        return $this->answers[$questionId] ?? null;
    }
}
