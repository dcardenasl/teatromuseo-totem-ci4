<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Enums\Audience;
use App\Repositories\BillboardFallbackRepository;
use DateTimeImmutable;

/**
 * Transforms billboard API data (or fallback data) into view-ready
 * months and event cards.
 */
final class BillboardPresenter
{
    private BillboardFallbackRepository $fallback;

    public function __construct(?BillboardFallbackRepository $fallback = null)
    {
        $this->fallback = $fallback ?? new BillboardFallbackRepository();
    }

    /**
     * Build the full view context for the billboard screen.
     *
     * @param list<array<string, mixed>> $apiShows
     * @return array<string, mixed>
     */
    public function present(array $apiShows, string $locale): array
    {
        $fallbackEvents = $this->fallback->events();

        if ($apiShows === []) {
            return [
                'nav'    => [],
                'months' => $this->fallback->months(),
                'events' => $fallbackEvents,
            ];
        }

        $monthsMap = [];
        $events    = [];
        $fallbackCount = count($fallbackEvents);

        foreach ($apiShows as $index => $show) {
            if (!is_array($show)) {
                continue;
            }

            $template = $fallbackCount > 0 ? $fallbackEvents[$index % $fallbackCount] : [];
            $startDate = is_string($show['start_date'] ?? null) ? $show['start_date'] : '';

            if ($startDate !== '') {
                $date = DateTimeImmutable::createFromFormat('Y-m-d', $startDate);
                if ($date !== false) {
                    $monthName = $this->monthName((int) $date->format('n'), $locale);
                    $day       = $date->format('j');
                    $monthsMap[$monthName][] = $day;
                }
            }

            $audience = Audience::fromApi($show['audience_id'] ?? null);

            $events[] = [
                'tag'       => $audience->label(),
                'type'      => lang('Billboard.event_type_theatre'),
                'title'     => is_string($show['title'] ?? null) ? $show['title'] : '',
                'copy'      => is_string($show['description'] ?? null) ? $show['description'] : '',
                'class'     => $audience->cssClass(),
                'tone'      => is_string($template['tone'] ?? null) ? $template['tone'] : '',
                'slug'      => is_string($show['slug'] ?? null) ? $show['slug'] : (is_numeric($show['id'] ?? null) ? (string) $show['id'] : '1'),
                'image'     => is_string($template['image'] ?? null) ? $template['image'] : '',
                'dateLabel' => is_string($template['dateLabel'] ?? null) ? $template['dateLabel'] : '',
                'timeLabel' => is_string($template['timeLabel'] ?? null) ? $template['timeLabel'] : '',
            ];
        }

        $months = [];
        foreach ($monthsMap as $title => $days) {
            $months[] = [
                'title' => $title,
                'days'  => array_unique($days),
            ];
        }

        return [
            'nav'    => [],
            'months' => $months,
            'events' => $events,
        ];
    }

    /**
     * Localized month name with a fallback to Spanish.
     */
    private function monthName(int $monthNum, string $locale): string
    {
        $formatter = new \IntlDateFormatter(
            $locale,
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            null,
            null,
            'MMMM',
        );

        $date = DateTimeImmutable::createFromFormat('!m', (string) $monthNum);

        return $date !== false ? (string) $formatter->format($date) : '';
    }
}
