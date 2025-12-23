<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas jawaban peserta.
 */
class JawabanPeserta extends Model
{
    protected $table = 'jawaban_peserta';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'quiz_attempt_id',
        'attempt_number',
        'question_id',
        'selected_option_id',
        'points_earned',
        'submitted_at',
        'answer_text', // Legacy field
        'nilai_tercapai',
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'submitted_at' => 'datetime',
        'nilai_tercapai' => 'decimal:2',
    ];

    /**
     * Relasi belongsTo ke User.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi belongsTo ke Quiz.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Relasi belongsTo ke Question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    /**
     * Relasi belongsTo ke QuestionOption.
     */
    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    /**
     * Relasi belongsTo ke QuizAttempt.
     */
    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }
}
