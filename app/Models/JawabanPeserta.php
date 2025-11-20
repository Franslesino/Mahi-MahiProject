<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanPeserta extends Model
{
    protected $table = 'jawaban_peserta';

    protected $fillable = [
        'user_id',
        'quiz_id',
        'bank_soal_id',
        'opsi_jawaban_id',
        'opsi_dipilih',
        'nilai_tercapai',
        'tanggal_submit',
    ];

    protected $casts = [
        'nilai_tercapai' => 'decimal:2',
        'tanggal_submit' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'quiz_id');
    }

    public function bankSoal()
    {
        return $this->belongsTo(BankSoal::class, 'bank_soal_id');
    }

    public function opsiJawaban()
    {
        return $this->belongsTo(OpsiJawaban::class, 'opsi_jawaban_id');
    }
}
