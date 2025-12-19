<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id');
    }
}
