<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $course_id
 * @property string $title
 * @property string|null $description
 * @property string|null $type
 * @property string|null $file_url
 * @property string|null $content
 * @property int|null $duration
 * @property int|null $order
 * @property bool|null $is_preview
 * @property string|null $status
 * @property int|null $uploaded_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Course $course
 * @property-read \App\Models\User|null $uploader
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereFileUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereIsPreview($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CourseMaterial whereUploadedBy($value)
 * @mixin \Eloquent
 */
class CourseMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'type',
        'file_url',
        'content',
        'duration',
        'order',
        'is_preview',
        'status',
        'uploaded_by',
    ];

    protected $casts = [
        'is_preview' => 'boolean',
    ];

    // Relationship
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}