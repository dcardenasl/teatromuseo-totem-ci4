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

        $weekday = $this->weekdayName((int) $date->format('N'), $locale);
        $day   = $date->format('d');
        $month = $this->monthName((int) $date->format('n'), $locale);
        $year  = $date->format('Y');

        return match ($locale) {
            'en' => sprintf($this->lang('Section.school_start_en'), $weekday, $month, $day, $year),
            'fr' => sprintf($this->lang('Section.school_start_fr'), $weekday, $day, $month, $year),
            'pt' => sprintf($this->lang('Section.school_start_pt'), $weekday, $day, $month, $year),
            default => sprintf($this->lang('Section.school_start_es'), $weekday, $day, $month, $year),
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
     * Format a weekday name from a date string.
     */
    public function weekdayName(int $weekday, string $locale): string
    {
        if ($weekday < 1 || $weekday > 7) {
            return '';
        }

        $formatter = new IntlDateFormatter(
            $locale,
            IntlDateFormatter::LONG,
            IntlDateFormatter::NONE,
            null,
            null,
            'EEEE',
        );

        $date = (new DateTimeImmutable('2026-01-05'))->modify('+' . ($weekday - 1) . ' days');

        return (string) $formatter->format($date);
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
