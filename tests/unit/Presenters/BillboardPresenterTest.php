<?php

namespace Tests\Unit\Presenters;

use App\Enums\Audience;
use App\Presenters\BillboardPresenter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class BillboardPresenterTest extends TestCase
{
    public function testPresentReturnsFallbackWhenApiIsEmpty(): void
    {
        $presenter = new BillboardPresenter();
        $context   = $presenter->present([], 'es');

        self::assertNotEmpty($context['months']);
        self::assertNotEmpty($context['events']);
    }

    public function testPresentTransformsApiShowToEventCard(): void
    {
        $presenter = new BillboardPresenter();
        $context   = $presenter->present([
            [
                'title'       => 'Obra de prueba',
                'description' => 'Resumen',
                'start_date'  => '2026-06-15',
                'audience_id' => Audience::KIDS->value,
                'slug'        => 'obra-prueba',
            ],
        ], 'es');

        self::assertCount(1, $context['events']);
        self::assertSame('event-card--kids', $context['events'][0]['class']);
        self::assertSame('obra-prueba', $context['events'][0]['slug']);
        self::assertNotEmpty($context['months']);
    }
}
