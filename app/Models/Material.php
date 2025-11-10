<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property-read \App\Models\Course|null $course
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Material query()
 * @mixin \Eloquent
 */
class Material extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'file_path',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}