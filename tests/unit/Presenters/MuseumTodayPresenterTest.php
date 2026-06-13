<?php

namespace Tests\Unit\Presenters;

use App\Presenters\MuseumTodayPresenter;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MuseumTodayPresenterTest extends TestCase
{
    public function testPresentUsesFallbackWhenBlocksAreEmpty(): void
    {
        $presenter = new MuseumTodayPresenter();
        $context   = $presenter->present([]);

        self::assertTrue($context['primary']['fallback']);
        self::assertArrayHasKey('stats', $context);
        self::assertArrayHasKey('actions', $context);
    }

    public function testPresentNormalizesApiBlocks(): void
    {
        $presenter = new MuseumTodayPresenter();
        $context   = $presenter->present([
            'page' => ['title' => 'Actualidad'],
            'blocks' => [
                ['title' => 'Bloque 1', 'content' => '<p>Contenido uno</p>'],
                ['title' => 'Bloque 2', 'content' => '<p>Contenido dos</p>'],
            ],
        ]);

        self::assertSame('Bloque 1', $context['primary']['title']);
        self::assertCount(1, $context['blocks']);
        self::assertSame('01', $context['primary']['index']);
    }
}
