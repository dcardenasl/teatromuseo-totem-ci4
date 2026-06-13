<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Request-scoped memoizing decorator for the Tótem API service.
 *
 * Prevents duplicate HTTP calls within a single request (e.g. museum()
 * called from multiple controllers/presenters).
 */
final class CachedTotemApiService implements TotemApiInterface
{
    private TotemApiInterface $inner;

    /**
     * @var array<string, list<array<string, mixed>>|array<string, mixed>>
     */
    private array $cache = [];

    public function __construct(TotemApiInterface $inner)
    {
        $this->inner = $inner;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function shows(): array
    {
        $key = __METHOD__;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->shows();
        }

        /** @var list<array<string, mixed>> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function techniques(): array
    {
        $key = __METHOD__;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->techniques();
        }

        /** @var list<array<string, mixed>> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }

    /**
     * @return array<string, mixed>
     */
    public function technique(int $id): array
    {
        $key = __METHOD__ . ':' . $id;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->technique($id);
        }

        /** @var array<string, mixed> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function courses(): array
    {
        $key = __METHOD__;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->courses();
        }

        /** @var list<array<string, mixed>> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }

    /**
     * @return array<string, mixed>
     */
    public function museum(): array
    {
        $key = __METHOD__;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->museum();
        }

        /** @var array<string, mixed> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }

    /**
     * @return array<string, mixed>
     */
    public function museumHistory(string $slug): array
    {
        $key = __METHOD__ . ':' . $slug;
        if (!array_key_exists($key, $this->cache)) {
            $this->cache[$key] = $this->inner->museumHistory($slug);
        }

        /** @var array<string, mixed> $cached */
        $cached = $this->cache[$key];

        return $cached;
    }
}
