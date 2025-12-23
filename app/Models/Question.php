<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas soal.
 */
class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_bank_id',
        'type',
        'question_text',
        'explanation',
        'points',
        'order',
        'correct_answer',
    ];

    protected $casts = [
        'points' => 'integer',
        'order' => 'integer',
    ];

    /**
     * Get the question bank this question belongs to
     */
    public function questionBank()
    {
        return $this->belongsTo(QuestionBank::class);
    }

    /**
     * Get all options for this question
     */
    public function options()
    {
        return $this->hasMany(QuestionOption::class)->orderBy('order');
    }

    /**
     * Get correct options
     */
    public function correctOptions()
    {
        return $this->hasMany(QuestionOption::class)->where('is_correct', true);
    }

    /**
     * Get assignments using this question
     */
    public function assignments()
    {
        return $this->belongsToMany(Assignment::class, 'assignment_questions')
            ->withPivot('order', 'points')
            ->withTimestamps();
    }

    /**
     * Check if question is multiple choice
     */
    public function isMultipleChoice()
    {
        return $this->type === 'multiple_choice';
    }

    /**
     * Check if question is essay
     */
    public function isEssay()
    {
        return $this->type === 'essay';
    }
}
