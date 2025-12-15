<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;

class SupabaseStorageService
{
    public function upload(UploadedFile $file, string $folder = 'uploads'): array
    {
        $baseUrl = rtrim(config('services.supabase.url', ''), '/');
        $serviceKey = config('services.supabase.service_key');
        $bucket = config('services.supabase.bucket', 'course-uploads');
        $isPublic = (bool) config('services.supabase.public', true);

        // Fallback ke local storage jika Supabase tidak dikonfigurasi
        if (!$baseUrl || !$serviceKey || !$bucket) {
            return $this->uploadToLocalStorage($file, $folder);
        }

        $key = trim($folder, '/') . '/' . $this->generateFilename($file);
        $endpoint = "{$baseUrl}/storage/v1/object/{$bucket}/{$key}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => $file->getMimeType() ?: 'application/octet-stream',
            'x-upsert' => 'true',
        ])->withBody($file->get(), $file->getMimeType() ?: 'application/octet-stream')
            ->put($endpoint);

        if ($response->failed()) {
            // Jika gagal upload ke Supabase, fallback ke local storage
            return $this->uploadToLocalStorage($file, $folder);
        }

        return [
            'path' => $key,
            'public_url' => $isPublic ? $this->publicUrl($key) : null,
        ];
    }

    /**
     * Fallback upload ke local storage
     */
    protected function uploadToLocalStorage(UploadedFile $file, string $folder): array
    {
        $filename = $this->generateFilename($file);
        $path = trim($folder, '/') . '/' . $filename;

        // Simpan ke storage/app/public
        $storedPath = $file->storeAs($folder, $filename, 'public');

        return [
            'path' => $storedPath,
            'public_url' => asset('storage/' . $storedPath),
        ];
    }

    public function delete(?string $path): void
    {
        if (!$path) {
            return;
        }

        $baseUrl = rtrim(config('services.supabase.url', ''), '/');
        $serviceKey = config('services.supabase.service_key');
        $bucket = config('services.supabase.bucket', 'course-uploads');

        if (!$baseUrl || !$serviceKey || !$bucket) {
            return;
        }

        $objectPath = $this->toObjectPath($path);
        if (!$objectPath) {
            return;
        }

        $endpoint = "{$baseUrl}/storage/v1/object/{$bucket}/{$objectPath}";

        Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apikey' => $serviceKey,
        ])->delete($endpoint);
    }

    public function publicUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $baseUrl = rtrim(config('services.supabase.url', ''), '/');
        $bucket = config('services.supabase.bucket', 'course-uploads');
        if (!$baseUrl || !$bucket) {
            return null;
        }

        if ($this->isFullUrl($path)) {
            return $path;
        }

        return "{$baseUrl}/storage/v1/object/public/{$bucket}/{$path}";
    }

    protected function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid(date('YmdHis') . '_') . ($extension ? ".{$extension}" : '');
    }

    protected function toObjectPath(string $value): ?string
    {
        if (!$value) {
            return null;
        }

        if (!$this->isFullUrl($value)) {
            return ltrim($value, '/');
        }

        $bucket = config('services.supabase.bucket', 'course-uploads');
        $pattern = '#/storage/v1/object/(public/)?' . preg_quote($bucket, '#') . '/#';
        $path = preg_replace($pattern, '', $value);

        return $path ? ltrim($path, '/') : null;
    }

    protected function isFullUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
