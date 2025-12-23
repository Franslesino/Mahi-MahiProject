<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas relasi kuis.
 */
class RelasiQuiz extends Model
{
    protected $table = 'relasi_quiz';

    protected $fillable = [
        'quiz_id',
        'question_id',
        'bank_soal_id',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi belongsTo ke Quiz.
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Relasi belongsTo ke BankSoal.
     */
    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    /**
     * Relasi belongsTo ke Question.
     */
    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
