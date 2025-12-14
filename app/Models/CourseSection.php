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

    // Accessor untuk kompatibilitas dengan view berbahasa Indonesia
    public function getJudulAttribute()
    {
        return $this->title;
    }

    public function getDeskripsiAttribute()
    {
        return $this->description;
    }

    public function getUrutanAttribute()
    {
        return $this->order;
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    

    public function materials(): HasMany
    {
        return $this->hasMany(Materi::class, 'section_id')->orderBy('urutan');
    }
}