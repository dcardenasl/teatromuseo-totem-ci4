<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Audience identifiers used by the billboard API.
 */
enum Audience: int
{
    case NATIONAL     = 1;
    case INTERNATIONAL = 2;
    case KIDS         = 3;
    case GENERAL      = 4;

    /**
     * Build from an API value, defaulting to GENERAL when unknown.
     */
    public static function fromApi(int|string|null $value): self
    {
        $int = is_numeric($value) ? (int) $value : null;

        return self::tryFrom($int ?? 0) ?? self::GENERAL;
    }

    /**
     * Translation key for the audience label.
     */
    public function labelKey(): string
    {
        return match ($this) {
            self::NATIONAL      => 'Menu.audience_national',
            self::INTERNATIONAL => 'Menu.audience_international',
            self::KIDS          => 'Menu.audience_kids',
            self::GENERAL       => 'Menu.audience_general',
        };
    }

    /**
     * Localized label for this audience.
     */
    public function label(): string
    {
        $value = lang($this->labelKey());

        return is_string($value) ? $value : '';
    }

    /**
     * CSS modifier class used by event cards.
     */
    public function cssClass(): string
    {
        return match ($this) {
            self::NATIONAL      => 'event-card--national',
            self::INTERNATIONAL => 'event-card--international',
            self::KIDS          => 'event-card--kids',
            self::GENERAL       => 'event-card--adult',
        };
    }
}
