<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Services\TotemApiResult;

/**
 * Transforms real CMS "teatroescuela" entries (fetched by the BFF) into the
 * school screen's view context.
 *
 * There is no fallback content here anymore: course cards and teacher cards
 * are derived from what the BFF actually returned. The three evergreen
 * editorial stats remain static presentation copy, while a screen with no
 * live content never substitutes invented courses or named-but-fictional
 * teachers.
 */
final class SchoolPresenter
{
    public function __construct(private readonly DatePresenter $dates = new DatePresenter())
    {
    }

    /** Cards, not screens: at most this many upcoming courses are shown at once. */
    private const MAX_FEATURED_COURSES = 3;

    /**
     * @return array{state:string, section:array<string,mixed>, courses:list<array<string,mixed>>, teachers:list<array<string,mixed>>, personPhoto:string}
     */
    public function present(TotemApiResult $result, string $locale): array
    {
        $today = date('Y-m-d');

        /** @var list<array{sortDate:string, card:array<string,mixed>, ficha:array<string,mixed>}> $upcoming */
        $upcoming = [];
        /** @var array<string, array<string, mixed>> $teachersByKey */
        $teachersByKey = [];

        foreach ($result->list() as $entry) {
            if (!is_array($entry)) {
                continue;
            }

            $ficha = $this->fichaBlockData($entry);
            $startDate = $this->normalizedDate($ficha['start_date'] ?? null);
            $endDate = $this->normalizedDate($ficha['end_date'] ?? null);

            // Only upcoming/ongoing courses are featured here — no historial.
            if (! $this->isUpcoming($startDate, $endDate, $today)) {
                continue;
            }

            $upcoming[] = [
                'sortDate' => $startDate !== '' ? $startDate : '9999-99-99',
                'card' => [
                    'tag' => $this->activityTypeLabel(is_string($ficha['activity_type'] ?? null) ? $ficha['activity_type'] : 'course'),
                    'title' => $this->localizedString($entry['localized']['title'] ?? null, $entry['title'] ?? null),
                    'start' => $startDate !== '' ? $this->dates->formatSchoolStart($startDate, $locale) : '',
                    'copy' => $this->localizedString(
                        $entry['localized']['excerpt'] ?? null,
                        is_string($ficha['summary'] ?? null) ? $ficha['summary'] : ($entry['excerpt'] ?? null),
                    ),
                    'image' => $this->coverImage($entry),
                ],
                'ficha' => $ficha,
            ];

            foreach ($this->instructorsOf($ficha) as $instructor) {
                $key = $instructor['name'] !== '' ? $instructor['name'] : null;
                if ($key !== null && !isset($teachersByKey[$key])) {
                    $teachersByKey[$key] = $instructor;
                }
            }
        }

        usort($upcoming, static fn (array $a, array $b): int => $a['sortDate'] <=> $b['sortDate']);
        $featured = array_slice($upcoming, 0, self::MAX_FEATURED_COURSES);
        $courses = array_map(static fn (array $item): array => $item['card'], $featured);
        $teachers = array_values($teachersByKey);

        return [
            'state' => $result->state,
            'section' => $this->section(),
            'courses' => $courses,
            'teachers' => $teachers,
            'personPhoto' => 'assets/img/teatro-escuela/collage.webp',
        ];
    }

    /**
     * The real per-entry cover, hydrated by the BFF the same way as every
     * other public-read image (`{source_kind, file_id, url, variants}`).
     * Falls back to the shared static poster only when an entry genuinely
     * has none set — never a substitute for a real, different photo per
     * course.
     *
     * @param array<string, mixed> $entry
     */
    private function coverImage(array $entry): string
    {
        if (is_string($entry['featured_image']['url'] ?? null) && $entry['featured_image']['url'] !== '') {
            return $entry['featured_image']['url'];
        }

        return 'assets/img/school/la-escuela-de-los-nuevos-comediantes.webp';
    }

    /**
     * A course is "upcoming" (featurable) unless it has a confirmed end (or,
     * lacking that, start) date that has already passed. Courses with no
     * dates at all are treated as always-current rather than excluded.
     */
    private function isUpcoming(string $startDate, string $endDate, string $today): bool
    {
        if ($endDate !== '') {
            return $endDate >= $today;
        }
        if ($startDate !== '') {
            return $startDate >= $today;
        }

        return true;
    }

    /**
     * Same static curated section chrome as the original design — the
     * "cifras clave" numbers were never live data even before this
     * migration (they came from `SchoolFallbackRepository::section()`
     * unconditionally, regardless of whether the courses API succeeded),
     * so restoring them here isn't reintroducing invented content: it's
     * the same evergreen marketing copy the museum always displayed,
     * distinct from the specific fabricated *people* (named teachers with
     * invented bios) that were removed. The 3-column layout
     * (`.school-stats`) is a fixed grid, so this must stay 3 items.
     *
     * @return array<string, mixed>
     */
    private function section(): array
    {
        return [
            'title' => lang('Menu.school'),
            'heroImage' => 'assets/img/menu/menu_escuela.webp',
            'heroAlt' => lang('Section.school_hero_alt'),
            'introCopy' => lang('Section.school_intro'),
            'stats' => [
                ['label' => lang('Section.school_stat_courses'), 'value' => '50'],
                ['label' => lang('Section.school_stat_teachers'), 'value' => '20'],
                ['label' => lang('Section.school_stat_students'), 'value' => '1000'],
            ],
            'teachersTitle' => lang('Section.school_teachers_title'),
            'coursesTitle' => lang('Section.school_courses_title'),
            'courseImage' => 'assets/img/school/la-escuela-de-los-nuevos-comediantes.webp',
            'courseTag' => lang('Section.school_course_feature_label'),
            'courseTitle' => lang('Section.course_title_placeholder'),
            'courseCopy' => '',
            'courseContactLabel' => lang('Section.course_contact_label'),
            'courseContact' => lang('Section.school_course_contact_value'),
            'courseQrLabel' => lang('Section.school_course_qr_label'),
            'courseQrImage' => 'assets/img/school/teatroescuela-qr.webp',
            'courseQrUrl' => 'https://teatromuseo.cl/teatro-escuela?utm_source=totem',
            'closingImage' => 'assets/animations/teatroescuela.webp',
            'logoPrimary' => 'assets/img/logos/ministerio_culturas_chile.webp',
            'logoSecondary' => 'assets/img/menu/menu_escuela.webp',
        ];
    }

    /**
     * @param array<string, mixed> $entry a raw `entries/teatroescuela` row from the BFF
     * @return array<string, mixed> the `block_data` of its `teatroescuela_ficha` block, or [] if absent
     */
    private function fichaBlockData(array $entry): array
    {
        foreach (($entry['blocks'] ?? []) as $block) {
            if (is_array($block) && ($block['block_key'] ?? null) === 'teatroescuela_ficha' && is_array($block['block_data'] ?? null)) {
                return $block['block_data'];
            }
        }

        return [];
    }

    /**
     * `instructors` is an `entry_reference_list` field (schema:
     * `teatromuseo-cms-domain`'s `TeatroMuseoBlockTypeSeeder`) resolved by
     * the BFF into full "personas" entries. Reads defensively across the
     * field names a generic CMS entry can carry, and never invents a name.
     *
     * @param array<string, mixed> $ficha
     * @return list<array{name:string, role:string, description:string, photo:string}>
     */
    private function instructorsOf(array $ficha): array
    {
        $genericRole = $this->langStr('Section.school_teacher_generic_role');
        $instructors = [];
        foreach (($ficha['instructors'] ?? []) as $reference) {
            if (!is_array($reference)) {
                continue;
            }

            $name = $this->localizedString(
                $reference['localized']['title'] ?? null,
                $reference['title'] ?? ($reference['name'] ?? null),
            );
            if ($name === '') {
                continue;
            }

            $bio = $this->localizedString(
                $reference['localized']['excerpt'] ?? null,
                $reference['excerpt'] ?? null,
            );
            $photo = is_string($reference['cover_image']['url'] ?? null)
                ? $reference['cover_image']['url']
                : (is_string($reference['featured_image_url'] ?? null) ? $reference['featured_image_url'] : '');

            $instructors[] = [
                'name' => $name,
                'role' => $genericRole,
                'description' => $bio !== '' ? $bio : $genericRole,
                'photo' => $photo,
            ];
        }

        return $instructors;
    }

    private function activityTypeLabel(string $activityType): string
    {
        $key = 'Section.school_activity_' . $activityType;
        $label = lang($key);

        return $label !== $key && is_string($label) ? $label : ucfirst($activityType);
    }

    private function localizedString(mixed $preferred, mixed $fallback): string
    {
        if (is_string($preferred) && trim($preferred) !== '') {
            return $preferred;
        }

        return is_string($fallback) ? $fallback : '';
    }

    /** Normalizes a `Y-m-d[ H:i:s]` or ISO-8601 date string to `Y-m-d`. */
    private function normalizedDate(mixed $value): string
    {
        if (!is_string($value) || trim($value) === '') {
            return '';
        }

        $timestamp = strtotime($value);

        return $timestamp !== false ? date('Y-m-d', $timestamp) : '';
    }

    /**
     * Resolves a translation line as a plain string, joining the plural-form
     * array shape `lang()` can return — self-contained so this class works
     * in plain unit tests that never bootstrap CI4's `title` helper.
     */
    private function langStr(string $key): string
    {
        $value = lang($key);

        return is_array($value) ? implode(' ', $value) : $value;
    }
}
