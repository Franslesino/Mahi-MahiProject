<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CourseSchedule extends Model
{
    use HasFactory;

    protected $table = 'course_schedules';

    protected $fillable = [
        'kursus_id',
        'title',
        'date',
        'start_time',
        'end_time',
        'location',
        'meeting_url',
        'description',
        'order',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i',
        'order' => 'integer',
    ];

    /**
     * Relationship to course
     */
    public function kursus(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    /**
     * Alias for kursus relationship
     */
    public function course(): BelongsTo
    {
        return $this->kursus();
    }

    /**
     * Check if schedule is upcoming
     */
    public function isUpcoming(): bool
    {
        return $this->date->isFuture() || $this->date->isToday();
    }

    /**
     * Check if schedule is past
     */
    public function isPast(): bool
    {
        return $this->date->isPast() && !$this->date->isToday();
    }

    /**
     * Get formatted date
     */
    public function getFormattedDateAttribute(): string
    {
        return $this->date->translatedFormat('l, d F Y');
    }

    /**
     * Get formatted time range
     */
    public function getTimeRangeAttribute(): string
    {
        $start = Carbon::parse($this->start_time)->format('H:i');
        $end = Carbon::parse($this->end_time)->format('H:i');
        return "{$start} - {$end}";
    }

    /**
     * Get duration in minutes
     */
    public function getDurationMinutesAttribute(): int
    {
        $start = Carbon::parse($this->start_time);
        $end = Carbon::parse($this->end_time);
        return $start->diffInMinutes($end);
    }

    /**
     * Scope for upcoming schedules
     */
    public function scopeUpcoming($query)
    {
        return $query->where('date', '>=', now()->toDateString())
                     ->where('status', 'scheduled')
                     ->orderBy('date')
                     ->orderBy('start_time');
    }

    /**
     * Scope for past schedules
     */
    public function scopePast($query)
    {
        return $query->where('date', '<', now()->toDateString())
                     ->orderBy('date', 'desc');
    }
}
