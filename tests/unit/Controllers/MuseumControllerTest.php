<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class MuseumControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testMuseumMenuRouteRendersSections(): void
    {
        $result = $this->get('museo');

        $result->assertStatus(200);
        $result->assertSee('Explora el museo');
    }

    public function testMuseumTodayRouteRendersBlocks(): void
    {
        $result = $this->get('museo/el-museo/hoy');

        $result->assertStatus(200);
        $result->assertSee('Actualidad del museo');
    }

    public function testMuseumHistoryRouteRendersArchive(): void
    {
        $result = $this->get('museo/historia');

        $result->assertStatus(200);
        $result->assertSee('Historia');
    }
}
