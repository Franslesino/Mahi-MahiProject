<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Model untuk entitas kuis attempt.
 */
class QuizAttempt extends Model
{
    use HasFactory;

    protected $table = 'quiz_attempts';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'kursus_id',
        'attempt_number',
        'score',
        'is_passed',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'is_passed' => 'boolean',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke Quiz
     */
    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    /**
     * Relasi ke Kursus
     */
    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    /**
     * Relasi ke JawabanPeserta
     */
    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'quiz_attempt_id');
    }

    /**
     * Scope untuk mendapatkan attempt terakhir user untuk quiz tertentu
     */
    public function scopeLatestAttempt($query, $userId, $quizId)
    {
        return $query->where('user_id', $userId)
                    ->where('quiz_id', $quizId)
                    ->orderBy('attempt_number', 'desc')
                    ->first();
    }

    /**
     * Scope untuk mendapatkan semua attempts user untuk quiz tertentu
     */
    public function scopeUserAttempts($query, $userId, $quizId)
    {
        return $query->where('user_id', $userId)
                    ->where('quiz_id', $quizId)
                    ->orderBy('attempt_number', 'desc');
    }
}
