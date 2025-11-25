<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MaterialCompletion extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'materi_id',
        'completed_at',
        'answers_json',
        'score',
    ];

    protected $dates = [
        'completed_at',
    ];

    protected $casts = [
        'answers_json' => 'array',
    ];
}
