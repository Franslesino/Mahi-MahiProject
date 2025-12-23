<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model untuk entitas opsi jawaban.
 */
class OpsiJawaban extends Model
{
    protected $table = 'opsi_jawaban';

    protected $fillable = [
        'bank_soal_id',
        'image_url',
        'teks_opsi',
        'nilai',
        'is_correct',
    ];

    protected $casts = [
        'nilai' => 'decimal:2',
        'is_correct' => 'boolean',
    ];

    /**
     * Relasi belongsTo ke BankSoal.
     */
    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    /**
     * Relasi hasMany ke JawabanPeserta.
     */
    public function jawabanPeserta()
    {
        return $this->hasMany(JawabanPeserta::class, 'opsi_jawaban_id');
    }
}
