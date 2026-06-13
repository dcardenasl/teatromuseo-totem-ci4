<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Resolves human-readable slugs to API identifiers.
 *
 * Avoids N+1 queries by scanning a pre-fetched list of items.
 */
final class SlugResolver
{
    /**
     * Find the id for a given slug within a list of items.
     *
     * Each item must contain 'id' and 'slug' keys.
     *
     * @param list<array<string, mixed>> $items
     */
    public function resolveId(array $items, string $slug): ?int
    {
        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $itemSlug = isset($item['slug']) && (is_string($item['slug']) || is_numeric($item['slug']))
                ? (string) $item['slug']
                : '';

            if ($itemSlug === $slug && isset($item['id']) && is_numeric($item['id'])) {
                return (int) $item['id'];
            }
        }

        return null;
    }
}
