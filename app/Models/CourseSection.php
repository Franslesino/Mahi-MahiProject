<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model untuk entitas kursus bagian.
 */
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
    /**
     * Accessor untuk atribut judul.
     */
    public function getJudulAttribute()
    {
        return $this->title;
    }

    /**
     * Accessor untuk atribut deskripsi.
     */
    public function getDeskripsiAttribute()
    {
        return $this->description;
    }

    /**
     * Accessor untuk atribut urutan.
     */
    public function getUrutanAttribute()
    {
        return $this->order;
    }

    /**
     * Relasi belongsTo ke Kursus.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'course_id');
    }

    

    /**
     * Relasi hasMany ke Materi.
     */
    public function materials(): HasMany
    {
        return $this->hasMany(Materi::class, 'section_id')->orderBy('urutan');
    }
}