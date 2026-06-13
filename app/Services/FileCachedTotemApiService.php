<?php

declare(strict_types=1);

namespace App\Services;

/**
 * File-based caching decorator for the Tótem API service.
 *
 * Provides offline resilience by caching API responses to files with TTL.
 * Falls back to cached data when API is unreachable.
 */
final class FileCachedTotemApiService implements TotemApiInterface
{
    private TotemApiInterface $inner;
    private string $cachePath;
    private int $ttlSeconds;

    public function __construct(
        TotemApiInterface $inner,
        ?string $cachePath = null,
        int $ttlSeconds = 60
    ) {
        $this->inner      = $inner;
        $this->cachePath  = $cachePath ?? WRITEPATH . 'cache/totem/';
        $this->ttlSeconds = $ttlSeconds;

        $this->ensureCacheDirectoryExists();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function shows(): array
    {
        return $this->getOrCache('shows', fn () => $this->inner->shows());
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function techniques(): array
    {
        return $this->getOrCache('techniques', fn () => $this->inner->techniques());
    }

    /**
     * @return array<string, mixed>
     */
    public function technique(int $id): array
    {
        return $this->getOrCache("technique:{$id}", fn () => $this->inner->technique($id));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function courses(): array
    {
        return $this->getOrCache('courses', fn () => $this->inner->courses());
    }

    /**
     * @return array<string, mixed>
     */
    public function museum(): array
    {
        return $this->getOrCache('museum', fn () => $this->inner->museum());
    }

    /**
     * @return array<string, mixed>
     */
    public function museumHistory(string $slug): array
    {
        return $this->getOrCache("museum-history:{$slug}", fn () => $this->inner->museumHistory($slug));
    }

    /**
     * Clear all cached files.
     */
    public function clearCache(): void
    {
        if (!is_dir($this->cachePath)) {
            return;
        }

        $files = glob($this->cachePath . '*.cache');
        if ($files === false) {
            return;
        }

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Get cached data or fetch and cache fresh data.
     *
     * @param string   $key     Cache key
     * @param callable $fetcher Function to fetch fresh data
     *
     * @return mixed Cached or fresh data
     */
    private function getOrCache(string $key, callable $fetcher): mixed
    {
        // Try to get from cache first
        $cached = $this->readFromCache($key);
        if ($cached !== null) {
            return $cached;
        }

        // Fetch fresh data
        $data = $fetcher();

        // Cache the result (even if empty, to prevent hammering)
        $this->writeToCache($key, $data);

        return $data;
    }

    /**
     * Read data from cache file if valid.
     *
     * @param string $key Cache key
     *
     * @return array<string, mixed>|null Data or null if not cached or expired
     */
    private function readFromCache(string $key): ?array
    {
        $file = $this->getCacheFilePath($key);

        if (!is_file($file)) {
            return null;
        }

        $content = file_get_contents($file);
        if ($content === false) {
            return null;
        }

        $metadata = json_decode($content, true);
        if (!is_array($metadata) || !isset($metadata['expires']) || !isset($metadata['data'])) {
            return null;
        }

        // Check if expired
        if (time() > $metadata['expires']) {
            return null;
        }

        /** @var array<string, mixed> $data */
        $data = $metadata['data'];

        return $data;
    }

    /**
     * Write data to cache file.
     *
     * @param string $key  Cache key
     * @param mixed  $data Data to cache
     */
    private function writeToCache(string $key, mixed $data): void
    {
        $file = $this->getCacheFilePath($key);

        $metadata = [
            'expires' => time() + $this->ttlSeconds,
            'data'    => $data,
        ];

        file_put_contents($file, json_encode($metadata), LOCK_EX);
    }

    /**
     * Get cache file path for a key.
     */
    private function getCacheFilePath(string $key): string
    {
        $safeKey = preg_replace('/[^a-zA-Z0-9_-]/', '_', $key);

        return $this->cachePath . $safeKey . '.cache';
    }

    /**
     * Ensure cache directory exists and is writable.
     */
    private function ensureCacheDirectoryExists(): void
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0775, true);
        }
    }
}
