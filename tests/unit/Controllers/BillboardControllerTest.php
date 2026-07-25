<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class BillboardControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testBillboardRouteRendersEvents(): void
    {
        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee('Cartelera');
        $result->assertSee('event-card');
    }

    public function testBillboardDetailRouteRendersFallbackShow(): void
    {
        $result = $this->get('cartelera/detalle/palabras-cantadas-del-viento');

        $result->assertStatus(200);
        $result->assertSee('Palabras cantadas del viento');
    }
}
