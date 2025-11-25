<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CourseSection extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_collapsed'
    ];

    protected $casts = [
        'is_collapsed' => 'boolean',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    

    public function materials(): HasMany
    {
        return $this->hasMany(Materi::class, 'section_id')->orderBy('urutan');
    }
}