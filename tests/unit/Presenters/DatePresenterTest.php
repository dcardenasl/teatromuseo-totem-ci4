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
}
