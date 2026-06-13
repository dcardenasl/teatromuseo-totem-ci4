<?php

declare(strict_types=1);

namespace App\ValueObjects;

/**
 * Immutable value object representing an image asset path.
 *
 * Centralizes the "assets/img/" prefix and provides both relative and URL
 * representations.
 */
final class ImageAsset
{
    private const BASE_PATH = 'assets/img/';

    private string $relativePath;

    public function __construct(string $relativePath)
    {
        $relativePath = trim($relativePath, '/');

        $this->relativePath = str_starts_with($relativePath, self::BASE_PATH)
            ? $relativePath
            : self::BASE_PATH . $relativePath;
    }

    /**
     * Build from a path that may already include the base prefix.
     */
    public static function from(string $relativePath): self
    {
        return new self($relativePath);
    }

    /**
     * Relative path including the assets/img/ prefix.
     */
    public function path(): string
    {
        return $this->relativePath;
    }

    /**
     * Full URL for the asset.
     */
    public function url(): string
    {
        return base_url($this->relativePath);
    }

    /**
     * Check whether the file exists in the public directory.
     */
    public function exists(): bool
    {
        return is_file(FCPATH . $this->relativePath);
    }
}
