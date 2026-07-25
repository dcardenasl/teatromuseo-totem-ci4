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
            'nav'            => [], // populated by the controller
            'section'        => $this->fallback->section($fallbackStart),
            'courses'        => $courses,
            'featuredCourse' => $courses[0] ?? null,
            'teachers'       => $this->fallback->teachers(),
            'students'       => $this->fallback->students(),
            'personPhoto'    => 'assets/img/teatro-escuela/collage.webp',
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
            return $this->fallback->courses($fallbackStart);
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

        return $courses;
    }

    /**
     * Build a localized fallback start date string for mocked courses.
     */
    public function fallbackStartDate(string $locale): string
    {
        return $this->dates->formatSchoolStart('2026-04-20', $locale);
    }
}
