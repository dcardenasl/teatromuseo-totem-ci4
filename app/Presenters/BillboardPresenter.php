<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\TotemApiResult;
use DateTimeImmutable;

/**
 * Transforms real BFF event data into the billboard's view context. There is
 * no fallback content here anymore — a screen with nothing to show says so
 * (`Billboard.no_shows_*`), and a screen that couldn't reach the BFF at all
 * says that instead (`Common.content_unavailable_*`); it never invents a
 * show that doesn't exist.
 */
final class BillboardPresenter
{
    /** @var list<string> */
    private const TONES = [
        'event-card--tone-coral', 'event-card--tone-sky', 'event-card--tone-violet',
        'event-card--tone-wine', 'event-card--tone-moss',
    ];

    /**
     * The billboard is a "what's on" screen, not an archive: at most this
     * many cards, biased toward what's coming up next. If there aren't
     * enough upcoming shows to fill the screen, the most recently finished
     * ones fill the remainder — never an empty slot next to real content.
     */
    private const MAX_FEATURED_EVENTS = 5;

    private const CLOSING_QR_IMAGE = 'assets/img/school/teatroescuela-qr.webp';

    private const CLOSING_COLLAGE_IMAGE = 'assets/animations/billboard.webp';

    public function __construct(private readonly DatePresenter $dates = new DatePresenter())
    {
    }

    /**
     * `shows()` already requests `sort=agenda`, which the BFF orders as
     * upcoming-soonest-first then past-most-recent-first in one pass — so
     * today's show (being the soonest upcoming one) naturally lands first,
     * and simply taking the first N already satisfies "próximas en orden
     * ascendente; si faltan, completar con las más recientes en orden
     * descendente". No re-sorting needed here, only the cap.
     *
     * @return array{state:string, months:list<array<string,mixed>>, events:list<array<string,mixed>>}
     */
    public function presentList(TotemApiResult $result, string $locale): array
    {
        $monthsMap = [];
        $events = [];

        foreach (array_slice($result->list(), 0, self::MAX_FEATURED_EVENTS) as $index => $show) {
            $occurrenceAt = $this->occurrenceAt($show);
            $day = '';
            $dateLabel = '';
            $timeLabel = '';

            if ($occurrenceAt !== '') {
                $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $occurrenceAt)
                    ?: DateTimeImmutable::createFromFormat(DATE_ATOM, $occurrenceAt);

                if ($date !== false) {
                    $day = $date->format('j');
                    $monthName = $this->dates->monthName((int) $date->format('n'), $locale);
                    $monthsMap[$monthName][] = $day;
                    $weekday = $this->dates->weekdayName((int) $date->format('N'), $locale);
                    $dateLabel = mb_strtoupper(trim($weekday . ' ' . $day . ' ' . $this->dates->monthName((int) $date->format('n'), $locale)));
                    $timeLabel = $date->format('H.i') . ' H';
                }
            }

            $eventType = is_string($show['event_type'] ?? null) ? $show['event_type'] : '';

            $events[] = [
                'day' => $day,
                'tag' => $this->eventTypeLabel($eventType),
                'type' => $this->eventTypeLabel($eventType),
                'title' => is_string($show['title'] ?? null) ? $show['title'] : '',
                'copy' => $this->description($show),
                'class' => 'event-card--adult',
                'tone' => self::TONES[$index % count(self::TONES)],
                'slug' => $this->slugOrId($show),
                'image' => is_string($show['cover_image']['url'] ?? null) ? $show['cover_image']['url'] : '',
                'dateLabel' => $dateLabel,
                'timeLabel' => $timeLabel,
            ];
        }

        $months = [];
        foreach ($monthsMap as $title => $days) {
            $months[] = ['title' => $title, 'days' => array_values(array_unique($days))];
        }

        return ['state' => $result->state, 'months' => $months, 'events' => $events];
    }

    /**
     * A listing row carries `next_occurrence_at` while it still has one
     * upcoming, and only `last_occurrence_at` once it's fully in the past —
     * exactly one of the two is ever set, so this picks whichever exists.
     *
     * @param array<string, mixed> $show
     */
    private function occurrenceAt(array $show): string
    {
        if (is_string($show['next_occurrence_at'] ?? null) && $show['next_occurrence_at'] !== '') {
            return $show['next_occurrence_at'];
        }

        return is_string($show['last_occurrence_at'] ?? null) ? $show['last_occurrence_at'] : '';
    }

    /**
     * @return array<string, mixed>|null null when the source confirms there
     *     is nothing at this slug (a genuine 404, distinct from "unavailable").
     */
    public function presentDetail(TotemApiResult $result, string $locale): ?array
    {
        if ($result->data === null) {
            return null;
        }

        $show = $result->map();
        $eventType = is_string($show['event_type'] ?? null) ? $show['event_type'] : '';

        $occurrence = $this->nextOrFirstOccurrence($show['occurrences'] ?? []);
        $dateLabel = '';
        $timeLabel = '';
        if ($occurrence !== null && is_string($occurrence['start_time'] ?? null)) {
            $date = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $occurrence['start_time'])
                ?: DateTimeImmutable::createFromFormat(DATE_ATOM, $occurrence['start_time']);
            if ($date !== false) {
                $weekday = $this->dates->weekdayName((int) $date->format('N'), $locale);
                $month = $this->dates->monthName((int) $date->format('n'), $locale);
                $dateLabel = mb_strtoupper(trim($weekday . ' ' . $date->format('j') . ' ' . $month));
                $timeLabel = $date->format('H.i') . ' H';
            }
        }

        $images = [];
        if (is_string($show['cover_image']['url'] ?? null)) {
            $images[] = $show['cover_image']['url'];
        }
        foreach (($show['gallery_images'] ?? []) as $galleryImage) {
            if (is_array($galleryImage) && is_string($galleryImage['url'] ?? null)) {
                $images[] = $galleryImage['url'];
            }
        }

        return [
            'tags' => array_values(array_filter([$this->eventTypeLabel($eventType)])),
            'title' => is_string($show['title'] ?? null) ? $show['title'] : '',
            'image' => $images[0] ?? '',
            'images' => $images,
            'venue' => is_string($occurrence['venue_name'] ?? null) ? $occurrence['venue_name'] : '',
            'date' => $dateLabel,
            'time' => $timeLabel,
            'copy' => $this->description($show),
            'secondaryCopy' => null,
            'closingImage' => self::CLOSING_COLLAGE_IMAGE,
            'qrImage' => self::CLOSING_QR_IMAGE,
            'closingNote' => lang('Billboard.default_closing_note'),
        ];
    }

    private function eventTypeLabel(string $eventType): string
    {
        if ($eventType === '') {
            return '';
        }

        $key = 'Billboard.event_type_' . $eventType;
        $label = lang($key);

        return $label !== $key && is_string($label) ? $label : ucfirst(str_replace(['_', '-'], ' ', $eventType));
    }

    /**
     * The listing endpoint only returns `description` nested under
     * `localized` (its DETAIL fields allowlist is the only one that also
     * exposes a flat `description`) — check both so this works from either
     * call site.
     *
     * @param array<string, mixed> $show
     */
    private function description(array $show): string
    {
        if (is_string($show['localized']['description'] ?? null) && $show['localized']['description'] !== '') {
            return $show['localized']['description'];
        }

        return is_string($show['description'] ?? null) ? $show['description'] : '';
    }

    /** @param array<string, mixed> $show */
    private function slugOrId(array $show): string
    {
        if (is_string($show['slug'] ?? null) && $show['slug'] !== '') {
            return $show['slug'];
        }

        return isset($show['id']) ? (string) $show['id'] : '';
    }

    /**
     * @param mixed $occurrences
     * @return array<string, mixed>|null
     */
    private function nextOrFirstOccurrence(mixed $occurrences): ?array
    {
        if (!is_array($occurrences) || $occurrences === []) {
            return null;
        }

        $now = date('Y-m-d H:i:s');
        foreach ($occurrences as $occurrence) {
            if (is_array($occurrence) && is_string($occurrence['start_time'] ?? null) && $occurrence['start_time'] >= $now) {
                return $occurrence;
            }
        }

        $first = $occurrences[0];

        return is_array($first) ? $first : null;
    }
}
