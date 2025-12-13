<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
    ];

    protected $casts = [
        'points_earned' => 'decimal:2',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }

    public function selectedOption()
    {
        return $this->belongsTo(QuestionOption::class, 'selected_option_id');
    }

    public function quizAttempt()
    {
        return $this->belongsTo(QuizAttempt::class, 'quiz_attempt_id');
    }
}
