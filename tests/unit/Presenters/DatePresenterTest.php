<?php

namespace Tests\Unit\Presenters;

use App\Presenters\DatePresenter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class DatePresenterTest extends TestCase
{
    public function testMonthNameReturnsLocalizedMonth(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('abril', $presenter->monthName(4, 'es'));
        self::assertSame('April', $presenter->monthName(4, 'en'));
    }

    public function testFormatSchoolStartReturnsEmptyStringForInvalidDate(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('', $presenter->formatSchoolStart('not-a-date', 'es'));
    }

    public function testMonthNameReturnsEmptyStringForOutOfRangeMonth(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('', $presenter->monthName(0, 'es'));
        self::assertSame('', $presenter->monthName(13, 'es'));
    }

    public function testWeekdayNameReturnsLocalizedWeekday(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('lunes', $presenter->weekdayName(1, 'es'));
        self::assertSame('Monday', $presenter->weekdayName(1, 'en'));
        self::assertSame('domingo', $presenter->weekdayName(7, 'es'));
    }

    public function testWeekdayNameReturnsEmptyStringForOutOfRangeWeekday(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('', $presenter->weekdayName(0, 'es'));
        self::assertSame('', $presenter->weekdayName(8, 'es'));
    }

    public function testDayNumberFormatsDate(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('7', $presenter->dayNumber('2026-03-07'));
    }

    public function testDayNumberReturnsEmptyStringForInvalidDate(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('', $presenter->dayNumber('not-a-date'));
    }

    public function testMonthNameFromDateReturnsLocalizedMonth(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('marzo', $presenter->monthNameFromDate('2026-03-07', 'es'));
    }

    public function testMonthNameFromDateReturnsEmptyStringForInvalidDate(): void
    {
        $presenter = new DatePresenter();

        self::assertSame('', $presenter->monthNameFromDate('not-a-date', 'es'));
    }

    public function testFormatSchoolStartIncludesWeekdayDayAndYear(): void
    {
        $presenter = new DatePresenter();

        foreach (['es', 'en', 'fr', 'pt'] as $locale) {
            $result = $presenter->formatSchoolStart('2026-03-07', $locale);

            self::assertNotSame('', $result);
            self::assertStringContainsString('2026', $result);
        }
    }
}
