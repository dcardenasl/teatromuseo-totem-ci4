<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Outcome of a single BFF call: the data (when any) plus which of three
 * states produced it. Unlike the old contract — where every failure mode
 * (upstream down, 404, invalid JSON) collapsed to the same empty array —
 * callers can tell "nothing to show" apart from "we don't actually know".
 */
final class TotemApiResult
{
    /**
     * @param list<array<string, mixed>>|array<string, mixed>|null $data
     * @param 'fresh'|'stale'|'unavailable' $state
     */
    private function __construct(
        public readonly array|null $data,
        public readonly string $state,
    ) {
    }

    /** @param list<array<string, mixed>>|array<string, mixed>|null $data */
    public static function fresh(array|null $data): self
    {
        return new self($data, 'fresh');
    }

    /** @param list<array<string, mixed>>|array<string, mixed>|null $data */
    public static function stale(array|null $data): self
    {
        return new self($data, 'stale');
    }

    public static function unavailable(): self
    {
        return new self(null, 'unavailable');
    }

    /** The BFF answered (now or recently) — as opposed to "we have nothing at all". */
    public function isAvailable(): bool
    {
        return $this->state !== 'unavailable';
    }

    /** True for a confirmed-empty answer AND for a fully unavailable source. */
    public function isEmpty(): bool
    {
        return $this->data === null || $this->data === [];
    }

    /** @return list<array<string, mixed>> */
    public function list(): array
    {
        if ($this->data === null) {
            return [];
        }

        /** @var list<array<string, mixed>> */
        return array_is_list($this->data) ? $this->data : [];
    }

    /** @return array<string, mixed> */
    public function map(): array
    {
        if ($this->data === null || array_is_list($this->data)) {
            return [];
        }

        /** @var array<string, mixed> */
        return $this->data;
    }
}
