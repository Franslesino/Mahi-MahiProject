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

        if (!$baseUrl || !$serviceKey || !$bucket) {
            throw new \RuntimeException('Supabase storage belum dikonfigurasi.');
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
            throw new \RuntimeException('Gagal upload ke Supabase: ' . $response->body());
        }

        return [
            'path' => $key,
            'public_url' => $isPublic ? $this->publicUrl($key) : null,
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

    public function generateFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        return uniqid(date('YmdHis') . '_') . ($extension ? ".{$extension}" : '');
    }

    public function toObjectPath(string $value): ?string
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

    public function isFullUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }
}
