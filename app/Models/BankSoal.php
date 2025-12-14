<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'pertanyaan',
        'image_url',
        'tipe_soal',
        'kategori',
    ];

    public function opsiJawaban()
    {
        return $this->hasMany(OpsiJawaban::class, 'bank_soal_id');
    }

    /**
     * Alias untuk opsiJawaban untuk kompatibilitas dengan view
     */
    public function options()
    {
        return $this->opsiJawaban();
    }

    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'bank_soal_id');
    }

    public function relasiQuiz()
    {
        return $this->belongsToMany(
            Quiz::class,
            'relasi_quiz',
            'bank_soal_id',
            'quiz_id'
        )->withPivot('is_active', 'urutan');
    }
}
