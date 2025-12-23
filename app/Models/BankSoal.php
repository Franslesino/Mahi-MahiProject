<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas bank soal.
 */
class BankSoal extends Model
{
    protected $table = 'bank_soal';

    protected $fillable = [
        'pertanyaan',
        'image_url',
        'tipe_soal',
        'kategori',
    ];

    /**
     * Relasi hasMany ke OpsiJawaban.
     */
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

    /**
     * Relasi hasMany ke JawabanPeserta.
     */
    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'bank_soal_id');
    }

    /**
     * Menangani logika model.
     */
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
