<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $category
 * @property string|null $image
 * @property int|null $videos
 * @property string|null $mode
 * @property numeric $price
 * @property numeric|null $discount_price
 * @property string|null $learning
 * @property string|null $badge
 * @property string|null $badge_color
 * @property string|null $status
 * @property int|null $instructor_id
 * @property int|null $created_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $creator
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Enrollment> $enrollments
 * @property-read int|null $enrollments_count
 * @property-read \App\Models\User|null $instructor
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\CourseMaterial> $materials
 * @property-read int|null $materials_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereBadge($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereBadgeColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCategory($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereDiscountPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereInstructorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereLearning($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereMode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Course whereVideos($value)
 * @mixin \Eloquent
 */
class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'category',
        'image',
        'videos',
        'mode',
        'price',
        'discount_price',
        'learning',
        'badge',
        'badge_color',
        'status',
        'instructor_id',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
        'rating' => 'decimal:1',
        'videos' => 'integer',
    ];

    // Relasi ke User (instruktur)
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    // Relasi ke User (pembuat)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relasi ke CourseMaterial
    public function materials()
    {
        return $this->hasMany(CourseMaterial::class);
    }

    // Relasi ke Enrollment
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
}