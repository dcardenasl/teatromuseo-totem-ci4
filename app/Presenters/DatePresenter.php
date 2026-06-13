<?php

declare(strict_types=1);

namespace App\Presenters;

use DateTimeImmutable;
use IntlDateFormatter;

/**
 * Localized date formatting used across Tótem presenters.
 */
final class DatePresenter
{
    /**
     * Get the full month name for a given month number and locale.
     */
    public function monthName(int $month, string $locale): string
    {
        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            null,
            null,
            'MMMM',
        );

        $date = DateTimeImmutable::createFromFormat('!m', (string) $month);

        return $date !== false ? (string) $formatter->format($date) : '';
    }

    /**
     * Format a course start date according to the locale conventions.
     */
    public function formatSchoolStart(string $dateString, string $locale): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);

        if ($date === false) {
            return '';
        }

        $day   = $date->format('d');
        $month = $this->monthName((int) $date->format('n'), $locale);
        $year  = $date->format('Y');

        return match ($locale) {
            'en' => sprintf($this->lang('Section.school_start_en'), $month, $day, $year),
            'fr' => sprintf($this->lang('Section.school_start_fr'), $day, $month, $year),
            'pt' => sprintf($this->lang('Section.school_start_pt'), $day, $month, $year),
            default => sprintf($this->lang('Section.school_start_es'), $day, $month, $year),
        };
    }

    /**
     * Safely resolve a language line to a string.
     */
    private function lang(string $key): string
    {
        $value = lang($key);

        return is_string($value) ? $value : '';
    }

    /**
     * Format a day number from a date string.
     */
    public function dayNumber(string $dateString): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);

        return $date !== false ? $date->format('j') : '';
    }

    /**
     * Format a month name from a date string.
     */
    public function monthNameFromDate(string $dateString, string $locale): string
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $dateString);

        if ($date === false) {
            return '';
        }

        return $this->monthName((int) $date->format('n'), $locale);
    }
}
