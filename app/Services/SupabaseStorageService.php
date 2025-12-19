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
        $bucket = $this->resolveBucket($path);

        if (!$baseUrl || !$serviceKey || !$bucket) {
            $parsed = $this->parseObjectUrl($path);
            $baseUrl = $baseUrl ?: rtrim($parsed['base_url'] ?? '', '/');
        }

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

    public function signedUrl(?string $path, int $expiresInSeconds = 3600): ?string
    {
        if (!$path) {
            return null;
        }

        $baseUrl = rtrim(config('services.supabase.url', ''), '/');
        $serviceKey = config('services.supabase.service_key');
        $bucket = $this->resolveBucket($path);

        if (!$baseUrl || !$serviceKey || !$bucket) {
            $parsed = $this->parseObjectUrl($path);
            $baseUrl = $baseUrl ?: rtrim($parsed['base_url'] ?? '', '/');
        }

        if (!$baseUrl || !$serviceKey || !$bucket) {
            return null;
        }

        $objectPath = $this->toObjectPath($path);
        if (!$objectPath) {
            return null;
        }

        $endpoint = "{$baseUrl}/storage/v1/object/sign/{$bucket}/{$objectPath}";

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $serviceKey,
            'apikey' => $serviceKey,
            'Content-Type' => 'application/json',
        ])->post($endpoint, [
            'expiresIn' => $expiresInSeconds,
        ]);

        if ($response->failed()) {
            return null;
        }

        $signedUrl = $response->json('signedURL')
            ?? $response->json('signedUrl')
            ?? $response->json('signed_url');

        if (!$signedUrl) {
            return null;
        }

        if ($this->isFullUrl($signedUrl)) {
            return $signedUrl;
        }

        return $baseUrl . '/' . ltrim($signedUrl, '/');
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

        $parsed = $this->parseObjectUrl($value);
        if (!$parsed) {
            return null;
        }

        return ltrim($parsed['path'], '/');
    }

    public function isFullUrl(string $value): bool
    {
        return str_starts_with($value, 'http://') || str_starts_with($value, 'https://');
    }

    protected function parseObjectUrl(string $value): ?array
    {
        if (!$this->isFullUrl($value)) {
            return null;
        }

        $parsedUrl = parse_url($value);
        $path = $parsedUrl['path'] ?? '';
        if (!str_contains($path, '/storage/v1/object/')) {
            return null;
        }

        $pattern = '#/storage/v1/object/(public/|authenticated/|sign/)?([^/]+)/(.+)$#';
        if (!preg_match($pattern, $path, $matches)) {
            return null;
        }

        $scheme = $parsedUrl['scheme'] ?? null;
        $host = $parsedUrl['host'] ?? null;
        if (!$scheme || !$host) {
            return null;
        }

        $port = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $baseUrl = $scheme . '://' . $host . $port;

        return [
            'base_url' => $baseUrl,
            'bucket' => $matches[2],
            'path' => $matches[3],
        ];
    }

    protected function resolveBucket(string $path): ?string
    {
        $parsed = $this->parseObjectUrl($path);
        if ($parsed) {
            return $parsed['bucket'];
        }

        return config('services.supabase.bucket', 'course-uploads');
    }

    public function fetchObject(string $path): ?array
    {
        $baseUrl = rtrim(config('services.supabase.url', ''), '/');
        $serviceKey = config('services.supabase.service_key');
        $bucket = $this->resolveBucket($path);

        if (!$baseUrl || !$serviceKey || !$bucket) {
            $parsed = $this->parseObjectUrl($path);
            $baseUrl = $baseUrl ?: rtrim($parsed['base_url'] ?? '', '/');
        }

        if (!$baseUrl || !$serviceKey || !$bucket) {
            return null;
        }

        $objectPath = $this->toObjectPath($path);
        if (!$objectPath) {
            return null;
        }

        $headers = [
            'Authorization' => 'Bearer ' . $serviceKey,
            'apikey' => $serviceKey,
        ];

        $endpoint = "{$baseUrl}/storage/v1/object/{$bucket}/{$objectPath}";
        $response = Http::withHeaders($headers)->get($endpoint);

        if ($response->failed()) {
            $authEndpoint = "{$baseUrl}/storage/v1/object/authenticated/{$bucket}/{$objectPath}";
            $response = Http::withHeaders($headers)->get($authEndpoint);
        }

        if ($response->failed()) {
            return null;
        }

        return [
            'body' => $response->body(),
            'content_type' => $response->header('Content-Type') ?? 'application/octet-stream',
            'object_path' => $objectPath,
        ];
    }
}
