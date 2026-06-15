<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class CollectionControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testCollectionMainRouteRendersBands(): void
    {
        $result = $this->get('museo/coleccion');

        $result->assertStatus(200);
        $result->assertSee('collection-band--puppets');
        $result->assertSee('collection-band--masks');
        $result->assertSee('collection-band--clowns');
    }

    public function testCollectionTechniquesRouteRendersList(): void
    {
        $result = $this->get('museo/coleccion/titeres/tecnicas');

        $result->assertStatus(200);
        $result->assertSee('Técnicas');
        $result->assertDontSee('content-panel');
    }

    public function testCollectionMasksTraditionsRouteRendersList(): void
    {
        $result = $this->get('museo/coleccion/mascaras/tradiciones');

        $result->assertStatus(200);
        $result->assertSee('Tradiciones de Máscaras');
        $result->assertDontSee('content-panel');
    }

    public function testObsoleteClownsRouteReturns404(): void
    {
        $result = $this->get('museo/coleccion/payasos');

        $result->assertStatus(404);
    }
}
