<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Interface for the Tótem API consumer.
 *
 * All implementations must be resilient and return arrays, letting the
 * controllers decide how to render fallback data.
 */
interface TotemApiInterface
{
    /**
     * Get future shows.
     *
     * @return list<array<string, mixed>>
     */
    public function shows(): array;

    /**
     * Get puppet techniques list.
     *
     * @return list<array<string, mixed>>
     */
    public function techniques(): array;

    /**
     * Get a single technique by ID.
     *
     * @return array<string, mixed>
     */
    public function technique(int $id): array;

    /**
     * Get active courses/workshops.
     *
     * @return list<array<string, mixed>>
     */
    public function courses(): array;

    /**
     * Get museum page data.
     *
     * @return array<string, mixed>
     */
    public function museum(): array;

    /**
     * Get a comic history post by slug.
     *
     * @return array<string, mixed>
     */
    public function museumHistory(string $slug): array;
}
