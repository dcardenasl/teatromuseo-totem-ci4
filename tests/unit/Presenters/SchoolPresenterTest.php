<?php

namespace Tests\Unit\Presenters;

use App\Presenters\SchoolPresenter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class SchoolPresenterTest extends TestCase
{
    public function testPresentReturnsFallbackCoursesWhenApiIsEmpty(): void
    {
        $presenter = new SchoolPresenter();
        $context   = $presenter->present([], 'es');

        self::assertArrayHasKey('courses', $context);
        self::assertArrayHasKey('teachers', $context);
        self::assertArrayHasKey('students', $context);
        self::assertArrayHasKey('section', $context);
        self::assertCount(1, $context['courses']);
        self::assertSame('La Escuela de los Nuevos Comediantes', $context['featuredCourse']['title'] ?? null);
    }

    public function testPresentTransformsApiCourse(): void
    {
        $presenter = new SchoolPresenter();
        $context   = $presenter->present([
            [
                'school_category_id' => 1,
                'title'              => 'Curso de prueba',
                'start_date'         => '2026-05-10',
                'description'        => 'Descripción de prueba',
            ],
        ], 'es');

        self::assertCount(1, $context['courses']);
        self::assertSame('Curso de prueba', $context['featuredCourse']['title'] ?? null);
        self::assertSame('Taller', $context['courses'][0]['tag']);
        self::assertSame('Curso de prueba', $context['courses'][0]['title']);
        self::assertStringContainsString('mayo', $context['courses'][0]['start']);
    }

    public function testPresentPreservesMultipleApiCourses(): void
    {
        $presenter = new SchoolPresenter();
        $context   = $presenter->present([
            [
                'school_category_id' => 1,
                'title'              => 'Curso uno',
                'start_date'         => '2026-05-10',
                'description'        => 'Descripción uno',
            ],
            [
                'school_category_id' => 2,
                'title'              => 'Curso dos',
                'start_date'         => '2026-06-15',
                'description'        => 'Descripción dos',
            ],
        ], 'es');

        self::assertCount(2, $context['courses']);
        self::assertSame('Curso uno', $context['featuredCourse']['title'] ?? null);
        self::assertSame('Curso dos', $context['courses'][1]['title']);
    }
}
