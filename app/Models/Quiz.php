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
        'kursus_id',
    ];

    protected $casts = [
        'passing_grade' => 'decimal:2',
    ];

    public function kursus()
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function soal()
    {
        return $this->belongsToMany(
            BankSoal::class,
            'relasi_quiz',
            'quiz_id',
            'bank_soal_id'
        )->withPivot('is_active', 'urutan');
    }

    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'quiz_id');
    }

    public function relasiQuiz()
    {
        return $this->hasMany(RelasiQuiz::class, 'quiz_id');
    }
}
