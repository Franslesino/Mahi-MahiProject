<?php

namespace App\Models;

use App\Models\Assignment;
use App\Models\CourseSection;
use App\Models\Enrollment;
use App\Models\QuestionBank;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Kursus extends Model
{
    use HasFactory;

    protected $table = 'kursus';

    protected $fillable = [
        'judul',
        'slug',
        'deskripsi',
        'kategori',
        'harga',
        'pembuat', // admin who created
        'image',
        'status_diterbitkan',
        'start_date',
        'end_date',
        'duration',
        'level',
        'badge', // best seller, new, popular
        'instructor_id', // specific instructor assigned
        
        // Final Quiz Configuration
        'require_final_quiz',
        'final_quiz_id',
        'min_passing_score',

        // Purchase Limitation
        'purchase_deadline',
        'purchase_deadline_active', // boolean toggle
        'display_purchase_deadline', // boolean toggle for UI

        // Discount
        'discount_price',
        'discount_start_at',
        'discount_end_at',
        
        // Mode & Capacity
        'mode', // 'Online', 'Offline', 'Hybrid'
        'max_participants', // null if unlimited (Online)
        'default_location',
        'mode_description',
        'latitude',
        'longitude',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'status_diterbitkan' => 'boolean',
        'require_final_quiz' => 'boolean',
        'purchase_deadline_active' => 'boolean',
        'display_purchase_deadline' => 'boolean',
        'purchase_deadline' => 'datetime',
        'discount_start_at' => 'datetime',
        'discount_end_at' => 'datetime',
        'max_participants' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getImageUrlAttribute()
    {
        if (!$this->image) {
            return null;
        }

        if (Str::startsWith($this->image, ['http://', 'https://'])) {
            return $this->image;
        }

        return asset('storage/' . $this->image);
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI
    |--------------------------------------------------------------------------
    */

    // pembuat kursus (admin)
    public function pembuat()
    {
        return $this->belongsTo(User::class, 'pembuat');
    }

    // New relationship for Instructor
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

     public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // materi
    public function materi()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }

    // alias supaya bisa dipakai sebagai materials
    public function materials()
    {
        return $this->hasMany(Materi::class, 'kursus_id');
    }

    // alias untuk modules/sections
    public function sections()
    {
        return $this->hasMany(CourseSection::class, 'course_id')->orderBy('order');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'kursus_id');
    }

    /**
     * Get students enrolled in this course
     * Used by MidtransNotificationController to check if user already enrolled
     */
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'kursus_id', 'user_id')
            ->withPivot('status_pendaftaran')
            ->wherePivotIn('status_pendaftaran', ['active', 'completed', 'paid']);
    }

    public function diskon()
    {
        return $this->hasMany(Diskon::class, 'kursus_id');
    }

    public function itemPesanan()
    {
        return $this->hasMany(ItemPesanan::class, 'kursus_id');
    }

    public function questionBanks()
    {
        return $this->hasMany(QuestionBank::class, 'course_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'kursus_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'kursus_id');
    }

    public function finalQuiz()
    {
        return $this->belongsTo(Quiz::class, 'final_quiz_id');
    }

    public function videos()
    {
        return $this->hasMany(Materi::class, 'kursus_id')->where('type', 'video');
    }

    /**
     * Check if course has a configured final quiz
     */
    public function hasFinalQuiz(): bool
    {
        return !empty($this->final_quiz_id);
    }

    /**
     * Get max quiz attempts with default fallback
     */
    public function getMaxQuizAttemptsValue(): int
    {
        return $this->max_quiz_attempts ?? 3;
    }

    public function schedules()
    {
        // course_schedules table uses kursus_id as FK
        return $this->hasMany(CourseSchedule::class, 'kursus_id');
    }

    public function upcomingSchedules()
    {
        return $this->schedules()
            ->where('date', '>=', now()->toDateString())
            ->where('status', '!=', 'cancelled')
            ->orderBy('date')
            ->orderBy('start_time');
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & ATTRIBUTES
    |--------------------------------------------------------------------------
    */
    
    /**
     * Get purchase deadline date object
     */
    public function getPurchaseDeadlineDateAttribute()
    {
        return $this->purchase_deadline;
    }

    /*
    |--------------------------------------------------------------------------
    | MODE HELPER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if course is Online mode
     */
    public function isOnline(): bool
    {
        return strtolower($this->mode ?? 'online') === 'online';
    }

    /**
     * Check if course is Offline mode
     */
    public function isOffline(): bool
    {
        return strtolower($this->mode ?? '') === 'offline';
    }

    /**
     * Check if course is Hybrid mode
     */
    public function isHybrid(): bool
    {
        return strtolower($this->mode ?? '') === 'hybrid';
    }

    /**
     * Check if course has capacity limit
     */
    public function hasCapacityLimit(): bool
    {
        return $this->max_participants !== null && $this->max_participants > 0;
    }
    
    /**
     * Get count of enrolled participants (active/paid)
     */
    public function getParticipantsCountAttribute(): int
    {
        return $this->enrollments()
            ->whereIn('status_pendaftaran', ['active', 'completed', 'paid'])
            ->count();
    }

    /**
     * Check if course is full (for Offline/Hybrid)
     */
    public function isFull(): bool
    {
        if (!$this->hasCapacityLimit()) {
            return false;
        }

        return $this->participants_count >= $this->max_participants;
    }

    /**
     * Get available slots
     */
    public function getAvailableSlotsAttribute(): ?int
    {
        if (!$this->hasCapacityLimit()) {
            return null; // unlimited
        }

        return max(0, $this->max_participants - $this->participants_count);
    }

    /**
     * Get mode description
     */
    public function getModeDescriptionText(): string
    {
        if ($this->mode_description) {
            return $this->mode_description;
        }

        // Default descriptions
        return match (strtolower($this->mode ?? 'online')) {
            'online' => 'Pembelajaran 100% online. Akses materi kapan saja, di mana saja.',
            'offline' => 'Pembelajaran tatap muka dengan jadwal pertemuan yang ditentukan.',
            'hybrid' => 'Kombinasi pembelajaran online dan tatap muka untuk pengalaman belajar optimal.',
            default => 'Pembelajaran digital.',
        };
    }

    /**
     * Check if course requires schedule (Offline/Hybrid)
     */
    public function requiresSchedule(): bool
    {
        return $this->isOffline() || $this->isHybrid();
    }
}
