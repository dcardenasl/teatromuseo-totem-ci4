<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * School category identifiers used by the Teatro Escuela API.
 */
enum SchoolCategory: int
{
    case WORKSHOP   = 1;
    case PLAYS      = 2;
    case EDUCATION  = 3;

    /**
     * Build from an API value, defaulting to EDUCATION when unknown.
     */
    public static function fromApi(int|string|null $value): self
    {
        $int = is_numeric($value) ? (int) $value : null;

        return self::tryFrom($int ?? 0) ?? self::EDUCATION;
    }

    /**
     * Translation key for this category.
     */
    public function labelKey(): string
    {
        return match ($this) {
            self::WORKSHOP  => 'Menu.school_category_workshop',
            self::PLAYS     => 'Menu.school_category_plays',
            self::EDUCATION => 'Menu.school_category_education',
        };
    }

    /**
     * Localized label for this category.
     */
    public function label(): string
    {
        $value = lang($this->labelKey());

        return is_string($value) ? $value : '';
    }
}
