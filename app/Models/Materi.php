<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;
use App\Models\Attendance;

/**
 * Model untuk entitas materi.
 */
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
    /**
     * Accessor untuk atribut title.
     */
    public function getTitleAttribute()
    {
        return $this->judul;
    }

    // Accessor content
    /**
     * Accessor untuk atribut content.
     */
    public function getContentAttribute()
    {
        return $this->attributes['content'] ?? $this->attributes['isi'] ?? null;
    }

    // Accessor description
    /**
     * Accessor untuk atribut description.
     */
    public function getDescriptionAttribute()
    {
        return $this->attributes['description'] ?? $this->attributes['isi'] ?? null;
    }

    // Accessor video_url
    /**
     * Accessor untuk atribut video url.
     */
    public function getVideoUrlAttribute()
    {
        if ($this->type === 'video') {
            return $this->getFileUrlFullAttribute();
        }
        return null;
    }

    // Accessor file_path untuk PDF
    /**
     * Accessor untuk atribut file path.
     */
    public function getFilePathAttribute()
    {
        if ($this->type === 'pdf') {
            return $this->getFileUrlFullAttribute();
        }
        return null;
    }

    /**
     * Accessor untuk atribut file url full.
     */
    public function getFileUrlFullAttribute()
    {
        // 1. Prioritaskan url_konten jika sudah merupakan Full URL (biasanya dari Supabase)
        if (!empty($this->url_konten) && str_starts_with($this->url_konten, 'http')) {
            $signedUrl = $this->maybeSignedSupabaseUrl($this->url_konten);
            if ($signedUrl) {
                return $this->normalizeLocalUrl($signedUrl);
            }

            return $this->normalizeLocalUrl($this->url_konten);
        }

        // 2. Cek file_url jika merupakan Full URL
        if ($this->file_url && str_starts_with($this->file_url, 'http')) {
            $signedUrl = $this->maybeSignedSupabaseUrl($this->file_url);
            if ($signedUrl) {
                return $this->normalizeLocalUrl($signedUrl);
            }

            return $this->normalizeLocalUrl($this->file_url);
        }

        // 3. Jika file_url adalah path, generate URL-nya
        if ($this->file_url) {
            $generated = $this->generateUrl($this->file_url);
            if ($generated) {
                return $generated;
            }
        }

        // 4. Fallback ke url_konten (bisa path lokal atau link manual)
        if (!empty($this->url_konten)) {
            if (str_starts_with($this->url_konten, '/storage/')) {
                return $this->normalizeLocalUrl($this->url_konten);
            }

            if (str_starts_with($this->url_konten, 'storage/')) {
                return $this->normalizeLocalUrl('/' . $this->url_konten);
            }

            // Jika tampaknya hanya nama file, coba tebak foldernya
            if (!str_contains($this->url_konten, '/') && !str_contains($this->url_konten, '\\')) {
                $guessedPath = 'materials/' . $this->url_konten;
                $generated = $this->generateUrl($guessedPath);
                if ($generated) return $generated;
            }

            $disk = $this->materialsDisk();
            try {
                $storage = Storage::disk($disk);
                $path = ltrim($this->url_konten, '/');
                if ($storage->exists($path)) {
                    return $this->normalizeLocalUrl($storage->url($path));
                }
            } catch (\Exception $e) { }

            $generated = $this->generateUrl($this->url_konten);
            if ($generated) return $generated;

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
        $host = $parsed['host'] ?? null;

        // Jika host sudah bukan localhost/127.0.0.1, kembalikan apa adanya
        if (empty($host) || !in_array($host, ['localhost', '127.0.0.1'])) {
            return $url;
        }

        $appUrl = config('app.url');
        $appParsed = $appUrl ? parse_url($appUrl) : null;

        // Gunakan host dari APP_URL jika bukan localhost, jika tidak pakai host dari request saat ini
        $targetHost = $appParsed && !in_array(($appParsed['host'] ?? ''), ['localhost', '127.0.0.1'])
            ? ($appParsed['host'] ?? null)
            : (request()->getHost() ?: null);

        if (!$targetHost) {
            return $url; // fallback: biarkan apa adanya
        }

        $scheme = $appParsed['scheme'] ?? $parsed['scheme'] ?? request()->getScheme() ?? 'http';
        $port = $appParsed['port'] ?? request()->getPort() ?? null;
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? '?' . $parsed['query'] : '';
        $fragment = isset($parsed['fragment']) ? '#' . $parsed['fragment'] : '';

        return $scheme . '://' . $targetHost . ($port ? ':' . $port : '') . $path . $query . $fragment;
    }

    /**
     * Menangani logika model.
     */
    protected function maybeSignedSupabaseUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }

        $serviceKey = config('services.supabase.service_key');
        if (!$serviceKey) {
            return null;
        }

        if (str_starts_with($url, 'http')) {
            if (!str_contains($url, '/storage/v1/object/')) {
                return null;
            }
            // Jika sudah ada /public/ di URL, tidak perlu di-sign lagi
            if (str_contains($url, '/storage/v1/object/public/')) {
                return $url;
            }
        }

        try {
            $supabase = app(\App\Services\SupabaseStorageService::class);
            $ttlMinutes = (int) config('filesystems.temporary_url_ttl', 60);
            return $supabase->signedUrl($url, max(60, $ttlMinutes) * 60);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Relationships
    /**
     * Relasi belongsTo ke Kursus.
     */
    public function kursus(): BelongsTo
    {
        return $this->belongsTo(Kursus::class, 'kursus_id');
    }

    /**
     * Relasi belongsTo ke CourseSection.
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    /**
     * Relasi hasOne ke Assignment.
     */
    public function assignment()
    {
        return $this->hasOne(Assignment::class, 'materi_id');
    }

    // Relasi ke Attendances (for class_session type)
    /**
     * Relasi hasMany ke Attendance.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'materi_id');
    }

    /**
     * Menangani logika model.
     */
    protected function materialsDisk(): string
    {
        return config('filesystems.materials_disk', 'public');
    }

    /**
     * Menangani logika model.
     */
    protected function generateUrl(string $path): ?string
    {
        if (str_starts_with($path, 'http')) {
            return $path;
        }

        // Jika menggunakan Supabase, coba generate URL dari sana (signed lalu public)
        if (config('services.supabase.url') && config('services.supabase.service_key')) {
            try {
                $supabase = app(\App\Services\SupabaseStorageService::class);
                $ttlMinutes = (int) config('filesystems.temporary_url_ttl', 60);
                $url = $supabase->signedUrl($path, max(60, $ttlMinutes) * 60)
                    ?? $supabase->publicUrl($path);
                if ($url) {
                    return $this->normalizeLocalUrl($url);
                }
            } catch (\Exception $e) {
                // Lanjut ke storage lokal jika gagal
            }
        }

        $disk = $this->materialsDisk();
        try {
            $storage = Storage::disk($disk);
            if (method_exists($storage, 'temporaryUrl')) {
                $ttl = (int) config('filesystems.temporary_url_ttl', 60);
                return $this->normalizeLocalUrl($storage->temporaryUrl($path, now()->addMinutes($ttl)));
            }
            return $this->normalizeLocalUrl($storage->url($path));
        } catch (\Exception $e) {
            return null;
        }
    }

    // Helper methods
    /**
     * Mengambil type icon.
     */
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

    /**
     * Mengambil type color.
     */
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
    /**
     * Memeriksa kelas sesi.
     */
    public function isClassSession(): bool
    {
        return $this->type === 'class_session';
    }

    // Get formatted session datetime
    /**
     * Accessor untuk atribut formatted sesi date.
     */
    public function getFormattedSessionDateAttribute(): ?string
    {
        if (!$this->session_date)
            return null;
        return $this->session_date->locale('id')->isoFormat('dddd, D MMMM YYYY');
    }

    // Get formatted session time
    /**
     * Accessor untuk atribut formatted sesi time.
     */
    public function getFormattedSessionTimeAttribute(): ?string
    {
        if (!$this->session_start_time || !$this->session_end_time)
            return null;
        return $this->session_start_time->format('H:i') . ' - ' . $this->session_end_time->format('H:i');
    }

    // Check if session is upcoming
    /**
     * Memeriksa upcoming.
     */
    public function isUpcoming(): bool
    {
        if (!$this->session_date)
            return false;
        return $this->session_date->gte(now()->startOfDay());
    }

    // Check if session is today
    /**
     * Memeriksa today.
     */
    public function isToday(): bool
    {
        if (!$this->session_date)
            return false;
        return $this->session_date->isToday();
    }
}
