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
    /** @var array<string, list<string>> */
    private const MONTH_NAMES_FALLBACK = [
        'es' => ['enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'],
        'en' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        'fr' => ['janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre'],
        'pt' => ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho', 'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'],
    ];

    /**
     * Get the full month name for a given month number and locale.
     */
    public function monthName(int $month, string $locale): string
    {
        if ($month < 1 || $month > 12) {
            return '';
        }

        if (! class_exists(IntlDateFormatter::class)) {
            return self::MONTH_NAMES_FALLBACK[$locale][$month - 1] ?? self::MONTH_NAMES_FALLBACK['es'][$month - 1];
        }

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
    /** @var array<string, list<string>> */
    private const WEEKDAY_NAMES_FALLBACK = [
        'es' => ['lunes', 'martes', 'miércoles', 'jueves', 'viernes', 'sábado', 'domingo'],
        'en' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'fr' => ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'],
        'pt' => ['segunda-feira', 'terça-feira', 'quarta-feira', 'quinta-feira', 'sexta-feira', 'sábado', 'domingo'],
    ];

    public function weekdayName(int $weekday, string $locale): string
    {
        if ($weekday < 1 || $weekday > 7) {
            return '';
        }

        if (! class_exists(IntlDateFormatter::class)) {
            return self::WEEKDAY_NAMES_FALLBACK[$locale][$weekday - 1] ?? self::WEEKDAY_NAMES_FALLBACK['es'][$weekday - 1];
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
