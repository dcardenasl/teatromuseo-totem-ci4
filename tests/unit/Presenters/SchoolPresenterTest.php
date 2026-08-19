<?php

namespace Tests\Unit\Presenters;

use App\Presenters\SchoolPresenter;
use App\Services\TotemApiResult;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SchoolPresenterTest extends TestCase
{
    public function testPresentReturnsAnHonestEmptyListWhenThereAreNoCourses(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::fresh([]), 'es');

        self::assertSame('fresh', $context['state']);
        self::assertSame([], $context['courses']);
        self::assertSame([], $context['teachers']);
        // The "cifras clave" stats are static curated copy (as in the
        // original design, never live data) — always all 3, regardless of
        // whether any course loaded. The layout is a fixed 3-column grid.
        self::assertCount(3, $context['section']['stats']);
    }

    public function testPresentReturnsUnavailableStateWhenTheSourceIsDown(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::unavailable(), 'es');

        self::assertSame('unavailable', $context['state']);
        self::assertSame([], $context['courses']);
    }

    public function testPresentMapsRealCourseFieldsFromTheCmsFicha(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::fresh([
            [
                'title' => 'Curso de prueba',
                'excerpt' => 'Resumen corto',
                'featured_image' => ['url' => 'https://files.example/curso-de-prueba.webp'],
                'blocks' => [
                    [
                        'block_key' => 'teatroescuela_ficha',
                        'block_data' => [
                            'activity_type' => 'workshop',
                            'start_date' => '2099-05-10',
                            'summary' => 'Descripción de prueba',
                            'instructors' => [],
                        ],
                    ],
                ],
            ],
        ]), 'es');

        self::assertCount(1, $context['courses']);
        $course = $context['courses'][0];
        self::assertSame('Taller', $course['tag']);
        self::assertSame('Curso de prueba', $course['title']);
        self::assertSame('Descripción de prueba', $course['copy']);
        self::assertStringContainsString('mayo', $course['start']);
        self::assertSame('https://files.example/curso-de-prueba.webp', $course['image']);
    }

    public function testPresentUsesTheGenericPosterOnlyWhenAnEntryHasNoRealCover(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::fresh([
            ['title' => 'Curso sin portada'],
        ]), 'es');

        self::assertSame('assets/img/school/la-escuela-de-los-nuevos-comediantes.webp', $context['courses'][0]['image']);
    }

    public function testPresentGivesEachCourseItsOwnRealCoverImage(): void
    {
        $presenter = new SchoolPresenter();
        $course = static fn (string $title, string $start, string $imageUrl): array => [
            'title' => $title,
            'featured_image' => ['url' => $imageUrl],
            'blocks' => [[
                'block_key' => 'teatroescuela_ficha',
                'block_data' => ['activity_type' => 'course', 'start_date' => $start, 'instructors' => []],
            ]],
        ];

        $context = $presenter->present(TotemApiResult::fresh([
            $course('Uno', '2099-01-01', 'https://files.example/uno.webp'),
            $course('Dos', '2099-02-01', 'https://files.example/dos.webp'),
        ]), 'es');

        self::assertSame('https://files.example/uno.webp', $context['courses'][0]['image']);
        self::assertSame('https://files.example/dos.webp', $context['courses'][1]['image']);
        self::assertNotSame($context['courses'][0]['image'], $context['courses'][1]['image']);
    }

    public function testPresentCollectsUniqueRealInstructorsAcrossCourses(): void
    {
        $presenter = new SchoolPresenter();
        $course = static fn (string $title, string $instructorName): array => [
            'title' => $title,
            'blocks' => [[
                'block_key' => 'teatroescuela_ficha',
                'block_data' => [
                    'activity_type' => 'course',
                    'instructors' => [
                        ['title' => $instructorName, 'excerpt' => 'Bio real'],
                    ],
                ],
            ]],
        ];

        $context = $presenter->present(TotemApiResult::fresh([
            $course('Curso uno', 'Ana Pérez'),
            $course('Curso dos', 'Ana Pérez'),
            $course('Curso tres', 'Bruno Díaz'),
        ]), 'es');

        self::assertCount(2, $context['teachers']);
        self::assertSame('Ana Pérez', $context['teachers'][0]['name']);
        self::assertSame('Bio real', $context['teachers'][0]['description']);
        self::assertSame('Bruno Díaz', $context['teachers'][1]['name']);
    }

    public function testPresentNeverInventsAnInstructorNameWhenTheReferenceIsUnresolved(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::fresh([
            [
                'title' => 'Curso',
                'blocks' => [[
                    'block_key' => 'teatroescuela_ficha',
                    'block_data' => ['instructors' => [[]]],
                ]],
            ],
        ]), 'es');

        self::assertSame([], $context['teachers']);
    }

    public function testPresentExcludesCoursesThatHaveAlreadyEnded(): void
    {
        $presenter = new SchoolPresenter();
        $course = static fn (string $title, ?string $start, ?string $end = null): array => [
            'title' => $title,
            'blocks' => [[
                'block_key' => 'teatroescuela_ficha',
                'block_data' => array_filter([
                    'activity_type' => 'course',
                    'start_date' => $start,
                    'end_date' => $end,
                    'instructors' => [],
                ], static fn ($v) => $v !== null),
            ]],
        ];

        $context = $presenter->present(TotemApiResult::fresh([
            $course('Curso terminado', '2020-01-01', '2020-01-31'),
            $course('Curso sin fechas', null),
            $course('Curso futuro', '2099-01-01'),
            $course('Curso en curso', '2020-01-01', '2099-12-31'),
        ]), 'es');

        $titles = array_column($context['courses'], 'title');
        self::assertNotContains('Curso terminado', $titles);
        self::assertContains('Curso sin fechas', $titles);
        self::assertContains('Curso futuro', $titles);
        self::assertContains('Curso en curso', $titles);
    }

    public function testPresentCapsFeaturedCoursesAtThreeSoonestFirst(): void
    {
        $presenter = new SchoolPresenter();
        $course = static fn (string $title, string $start): array => [
            'title' => $title,
            'blocks' => [[
                'block_key' => 'teatroescuela_ficha',
                'block_data' => ['activity_type' => 'course', 'start_date' => $start, 'instructors' => []],
            ]],
        ];

        $context = $presenter->present(TotemApiResult::fresh([
            $course('Cuarto', '2099-04-01'),
            $course('Segundo', '2099-02-01'),
            $course('Primero', '2099-01-01'),
            $course('Tercero', '2099-03-01'),
        ]), 'es');

        self::assertCount(3, $context['courses']);
        self::assertSame(['Primero', 'Segundo', 'Tercero'], array_column($context['courses'], 'title'));
    }

    public function testSectionStatsMatchTheOriginalDesignsThreeColumnLayout(): void
    {
        $presenter = new SchoolPresenter();
        $context = $presenter->present(TotemApiResult::fresh([]), 'es');

        self::assertSame([
            ['label' => 'Cursos', 'value' => '50'],
            ['label' => 'Maestros', 'value' => '20'],
            ['label' => 'Alumnos', 'value' => '1000'],
        ], $context['section']['stats']);
    }
}
