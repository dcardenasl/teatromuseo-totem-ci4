<?php

declare(strict_types=1);

namespace App\Presenters;

use App\Enums\SchoolCategory;
use App\Repositories\SchoolFallbackRepository;

/**
 * Transforms Teatro Escuela API data (or fallback data) into view-ready
 * shapes for the school screen.
 */
final class SchoolPresenter
{
    private DatePresenter $dates;
    private SchoolFallbackRepository $fallback;

    public function __construct(?DatePresenter $dates = null, ?SchoolFallbackRepository $fallback = null)
    {
        $this->dates    = $dates ?? new DatePresenter();
        $this->fallback = $fallback ?? new SchoolFallbackRepository();
    }

    /**
     * Build the full view context for the school screen.
     *
     * @param list<array<string, mixed>> $apiCourses
     * @return array<string, mixed>
     */
    public function present(array $apiCourses, string $locale): array
    {
        $fallbackStart = $this->fallbackStartDate($locale);
        $courses       = $this->presentCourses($apiCourses, $locale, $fallbackStart);

        return [
            'nav'        => [], // populated by the controller
            'section'    => $this->fallback->section($fallbackStart),
            'courses'    => $courses,
            'teachers'   => $this->fallback->teachers(),
            'students'   => $this->fallback->students(),
            'personPhoto' => 'assets/img/school/school_collage.webp',
        ];
    }

    /**
     * Transform API courses into view-ready cards.
     *
     * @param list<array<string, mixed>> $apiCourses
     * @return list<array<string, mixed>>
     */
    public function presentCourses(array $apiCourses, string $locale, string $fallbackStart): array
    {
        if ($apiCourses === []) {
            return array_slice($this->fallback->courses($fallbackStart), 0, 1);
        }

        $courses = [];
        foreach ($apiCourses as $course) {
            if (!is_array($course)) {
                continue;
            }

            $startDate = is_string($course['start_date'] ?? null) ? $course['start_date'] : '';

            $courses[] = [
                'tag'   => SchoolCategory::fromApi($course['school_category_id'] ?? null)->label(),
                'title' => is_string($course['title'] ?? null) ? $course['title'] : '',
                'start' => $startDate !== '' ? $this->dates->formatSchoolStart($startDate, $locale) : '',
                'copy'  => is_string($course['description'] ?? null) ? $course['description'] : '',
            ];
        }

        // The totem shows one course at a time due to screen space.
        return array_slice($courses, 0, 1);
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
     * Build a localized fallback start date string for mocked courses.
     */
    public function fallbackStartDate(string $locale): string
    {
        $monthName = $this->dates->monthName(4, $locale);

        return match ($locale) {
            'en' => sprintf($this->lang('Section.school_start_en'), $monthName, '20', '2026'),
            'fr' => sprintf($this->lang('Section.school_start_fr'), '20', $monthName, '2026'),
            'pt' => sprintf($this->lang('Section.school_start_pt'), '20', $monthName, '2026'),
            default => sprintf($this->lang('Section.school_start_es'), '20', $monthName, '2026'),
        };
    }
}
