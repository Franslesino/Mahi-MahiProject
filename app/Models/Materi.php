<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;
use App\Models\Attendance;

class Materi extends Model
{
    protected $table = 'materi';

    protected $fillable = [
        'kursus_id',
        'section_id',
        'judul',
        'description',
        'isi',
        'content',
        'type',
        'url_konten',
        'file_url',
        'duration',
        'urutan',
        'status_terkunci',
        'is_preview',
        'status',
        // Class session fields
        'session_date',
        'session_start_time',
        'session_end_time',
        'session_location',
        'session_meeting_link',
        'session_type',
    ];

    protected $casts = [
        'status_terkunci' => 'boolean',
        'is_preview' => 'boolean',
        'duration' => 'integer',
        'urutan' => 'integer',
        'session_date' => 'date',
        'session_start_time' => 'datetime:H:i',
        'session_end_time' => 'datetime:H:i',
    ];

    // Accessor untuk title
    public function getTitleAttribute()
    {
        return $this->judul;
    }

    // Accessor content
    public function getContentAttribute()
    {
        return $this->attributes['content'] ?? $this->attributes['isi'] ?? null;
    }

    // Accessor description
    public function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->attributes['isi'] ?? null;
    }

    // Accessor video_url
    public function getVideoUrlAttribute()
    {
        if ($this->type === 'video') {
            if (!empty($this->file_url)) {
                if (str_starts_with($this->file_url, 'http')) {
                    return $this->file_url;
                }
                $generated = $this->generateUrl($this->file_url);
                if ($generated) {
                    return $generated;
                }
            }
            if (!empty($this->url_konten)) {
                if (str_starts_with($this->url_konten, 'http')) {
                    return $this->url_konten;
                }
                return $this->normalizeLocalUrl($this->url_konten);
            }
        }
        return null;
    }

    // Accessor file_path untuk PDF
    public function getFilePathAttribute()
    {
        if ($this->type === 'pdf') {
            if (!empty($this->file_url)) {
                if (str_starts_with($this->file_url, 'http')) {
                    return $this->file_url;
                }
                return $this->file_url;
            }
            if (!empty($this->url_konten)) {
                if (str_starts_with($this->url_konten, 'http')) {
                    return $this->url_konten;
                }
                return str_replace('/storage/', '', $this->url_konten);
            }
        }
        return null;
    }

    public function getFileUrlFullAttribute()
    {
        if ($this->file_url) {
            if (str_starts_with($this->file_url, 'http')) {
                return $this->file_url;
            }
            $generated = $this->generateUrl($this->file_url);
            if ($generated) {
                return $generated;
            }
        }
        if (!empty($this->url_konten)) {
            if (str_starts_with($this->url_konten, 'http')) {
                return $this->url_konten;
            }
            return $this->normalizeLocalUrl($this->url_konten);
        }
        return null;
    }

    /**
     * Jika URL tersimpan masih mengarah ke localhost/port default, ganti host+port
     * dengan APP_URL agar iframe/video tidak gagal koneksi.
     */
    protected function normalizeLocalUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $parsed = parse_url($url);
        if (empty($parsed['host']) || !in_array($parsed['host'], ['localhost', '127.0.0.1'])) {
            return $url;
        }

        $appUrl = config('app.url');
        $appParsed = $appUrl ? parse_url($appUrl) : null;
        if (!$appParsed || empty($appParsed['host'])) {
            return $url;
        }

        $scheme = $appParsed['scheme'] ?? $parsed['scheme'] ?? 'http';
        $host = $appParsed['host'];
        $port = $appParsed['port'] ?? null;
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $scheme . '://' . $host . ($port ? ':' . $port : '') . $path . $query . $fragment;
    }

    // Relationships
    public function kursus(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function assignment()
    {
        return $this->hasOne(Assignment::class, 'materi_id');
    }

    // Relasi ke Attendances (for class_session type)
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'materi_id');
    }

    protected function materialsDisk(): string
    {
        return config('filesystems.materials_disk', 'public');
    }

    protected function generateUrl(string $path): ?string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        $disk = $this->materialsDisk();
        try {
            $storage = Storage::disk($disk);
            if (method_exists($storage, 'temporaryUrl')) {
                $ttl = (int) config('filesystems.temporary_url_ttl', 60);
                return $storage->temporaryUrl($path, now()->addMinutes($ttl));
            }
            return $storage->url($path);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Helper methods
    public function getTypeIcon(): string
    {
        return match ($this->type) {
            'video' => 'fas fa-video',
            'pdf' => 'fas fa-file-pdf',
            'quiz' => 'fas fa-question-circle',
            'text' => 'fas fa-align-left',
            'class_session' => 'fas fa-users',
            default => 'fas fa-file',
        };
    }

    public function getTypeColor(): string
    {
        return match ($this->type) {
            'video' => 'blue',
            'pdf' => 'red',
            'quiz' => 'yellow',
            'text' => 'green',
            'class_session' => 'orange',
            default => 'gray',
        };
    }

    // Check if this is a class session
    public function isClassSession(): bool
    {
        return $this->type === 'class_session';
    }

    // Get formatted session datetime
    public function getFormattedSessionDateAttribute(): ?string
    {
        if (!$this->session_date)
            return null;
        return $this->session_date->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    // Get formatted session time
    public function getFormattedSessionTimeAttribute(): ?string
    {
        if (!$this->session_start_time || !$this->session_end_time)
            return null;
        return $this->session_start_time->format('H:i') . ' - ' . $this->session_end_time->format('H:i');
    }

    // Check if session is upcoming
    public function isUpcoming(): bool
    {
        if (!$this->session_date)
            return false;
        return $this->session_date->gte(now()->startOfDay());
    }

    // Check if session is today
    public function isToday(): bool
    {
        if (!$this->session_date)
            return false;
        return $this->session_date->isToday();
    }
}
