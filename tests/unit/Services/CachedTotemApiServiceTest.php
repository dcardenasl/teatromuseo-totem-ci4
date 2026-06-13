<?php

namespace Tests\Unit\Services;

use App\Services\CachedTotemApiService;
use App\Services\TotemApiInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class CachedTotemApiServiceTest extends TestCase
{
    public function testMuseumIsCalledOnlyOncePerRequest(): void
    {
        $inner = $this->createMock(TotemApiInterface::class);
        $inner->expects(self::once())
            ->method('museum')
            ->willReturn(['title' => 'Museo']);

        $service = new CachedTotemApiService($inner);

        self::assertSame(['title' => 'Museo'], $service->museum());
        self::assertSame(['title' => 'Museo'], $service->museum());
    }

    public function testTechniqueCacheUsesId(): void
    {
        $inner = $this->createMock(TotemApiInterface::class);
        $inner->expects(self::once())
            ->method('technique')
            ->with(5)
            ->willReturn(['title' => 'Técnica 5']);

        $service = new CachedTotemApiService($inner);

        self::assertSame(['title' => 'Técnica 5'], $service->technique(5));
        self::assertSame(['title' => 'Técnica 5'], $service->technique(5));
    }
}
