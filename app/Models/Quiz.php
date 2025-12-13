<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $table = 'quiz';

    protected $fillable = [
        'judul_quiz',
        'passing_grade',
        'kesempatan_mengerjakan',
        'durasi_quiz',
        'time_limit',
        'kursus_id',
        'is_final_quiz',
        'minimum_score',
        'is_active',
    ];

    protected $casts = [
        'passing_grade' => 'decimal:2',
        'is_final_quiz' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function soal()
    {
        return $this->belongsToMany(
            Question::class,
            'relasi_quiz',
            'quiz_id',
            'question_id'
        )->withPivot('is_active', 'urutan');
    }

    /**
     * Alias untuk soal() untuk kompatibilitas dengan view
     */
    public function questions()
    {
        return $this->soal()->orderBy('relasi_quiz.urutan');
    }

    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'quiz_id');
    }

    public function relasiQuiz()
    {
        return $this->hasMany(RelasiQuiz::class, 'quiz_id');
    }

    /**
     * Relasi ke QuizAttempt
     */
    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class, 'quiz_id');
    }

    /**
     * Relasi ke kursus yang menggunakan quiz ini sebagai final quiz
     */
    public function kursusAsFinalQuiz()
    {
        return $this->hasMany(Kursus::class, 'final_quiz_id');
    }

    /**
     * Check if quiz is available for students
     */
    public function isAvailable()
    {
        return $this->is_active ?? true;
    }

    /**
     * Check if this is a final quiz
     */
    public function isFinalQuiz()
    {
        return $this->is_final_quiz ?? false;
    }
}
