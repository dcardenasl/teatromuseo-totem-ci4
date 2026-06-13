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
        self::assertSame('Taller', $context['courses'][0]['tag']);
        self::assertSame('Curso de prueba', $context['courses'][0]['title']);
        self::assertStringContainsString('mayo', $context['courses'][0]['start']);
    }
}
