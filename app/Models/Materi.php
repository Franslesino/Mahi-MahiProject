<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use App\Models\Assignment;

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
    ];

    protected $casts = [
        'status_terkunci' => 'boolean',
        'is_preview' => 'boolean',
        'duration' => 'integer',
        'urutan' => 'integer',
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
            if (!empty($this->url_konten)) {
                return $this->url_konten;
            }
            if (!empty($this->file_url)) {
                return $this->generateUrl($this->file_url);
            }
        }
        return null;
    }

    // Accessor file_path untuk PDF
    public function getFilePathAttribute()
    {
        if ($this->type === 'pdf') {
            if (!empty($this->file_url)) {
                if (str_starts_with($this->file_url, 'storage/')) {
                    return $this->file_url;
                }
                return $this->file_url;
            }
            if (!empty($this->url_konten)) {
                return str_replace('/storage/', '', $this->url_konten);
            }
        }
        return null;
    }

    public function getFileUrlFullAttribute()
    {
        if (!empty($this->url_konten)) {
            return $this->url_konten;
        }
        if ($this->file_url) {
            return $this->generateUrl($this->file_url);
        }
        return null;
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

    protected function materialsDisk(): string
    {
        return config('filesystems.materials_disk', 'public');
    }

    protected function generateUrl(string $path): ?string
    {
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
            default => 'gray',
        };
    }
}

