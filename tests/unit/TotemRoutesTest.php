<?php

namespace Tests;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class TotemRoutesTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function testIndexRoute(): void
    {
        $result = $this->get('/');
        $result->assertStatus(200);
        $result->assertSee('Toca para comenzar');
        $result->assertSee('window.TOTEM_SYSTEM_MESSAGES');
        $result->assertSee('Rotate your device');
        $result->assertSee('Gira tu dispositivo');
        $result->assertSee('Still there?');
        $result->assertSee('¿Sigues ahí?');
    }

    public function testLanguageRoute(): void
    {
        $result = $this->get('language');
        $result->assertStatus(200);
        $result->assertSee('Selecciona tu idioma');
    }

    public function testMenuRoute(): void
    {
        $result = $this->get('menu');
        $result->assertStatus(200);
        $result->assertSee('Menú');
    }

    public function testMuseumRoute(): void
    {
        $result = $this->get('museo');
        $result->assertStatus(200);
        $result->assertSee('Explora el museo');
    }

    public function testCollectionRoute(): void
    {
        $result = $this->get('museo/coleccion');
        $body = (string) $result->getBody();

        $result->assertStatus(200);
        $result->assertSee('Colección');
        $result->assertSee('Títeres');
        $result->assertSee('Máscaras');
        $result->assertSee('En exhibición');
        $result->assertSee('Técnicas');
        $result->assertSee('Historia');
        $result->assertSee('Tradiciones');
        $result->assertSee('collection-pill--disabled');
        $result->assertSee('museo/historia');
        $result->assertSee('museo/coleccion/titeres/exhibicion');
        $result->assertSee('museo/coleccion/mascaras/exhibicion');
        $result->assertSee('museo/coleccion/mascaras/tradiciones');
        $result->assertDontSee('Teatro de payasos');

        self::assertTrue(
            strpos($body, 'collection-band--puppets') !== false
            && strpos($body, 'collection-band--masks') !== false
            && strpos($body, 'collection-band--clowns') !== false
            && strpos($body, 'collection-band--puppets') < strpos($body, 'collection-band--masks')
            && strpos($body, 'collection-band--masks') < strpos($body, 'collection-band--clowns'),
            'La colección debería ordenar Títeres, Máscaras y Payasos.'
        );
    }

    public function testHistoryRoute(): void
    {
        $result = $this->get('museo/historia');

        $result->assertStatus(200);
        $result->assertSee('Historia');
        $result->assertSee('Archivo editorial');
        $result->assertSee('Historia del Circo');
        $result->assertSee('Historia de los Payasos');
        $result->assertSee('Tradición del Títere');
        $result->assertDontSee('Entrada editorial');
    }

    public function testPuppetsExhibitRoute(): void
    {
        $result = $this->get('museo/coleccion/titeres/exhibicion');

        $result->assertStatus(200);
        $result->assertSee('Títeres en exhibición');
    }

    public function testMasksExhibitRoute(): void
    {
        $result = $this->get('museo/coleccion/mascaras/exhibicion');

        $result->assertStatus(200);
        $result->assertSee('Máscaras en exhibición');
    }

    public function testMasksTraditionsRoute(): void
    {
        $result = $this->get('museo/coleccion/mascaras/tradiciones');

        $result->assertStatus(200);
        $result->assertSee('Tradiciones de Máscaras');
        $result->assertSee('Comedia del Arte');
        $result->assertSee('Comedia del Andes');
    }

    public function testTheaterSchoolRoute(): void
    {
        $result = $this->get('teatro-escuela');
        $result->assertStatus(200);
        $result->assertSee('Teatro escuela');
    }

    public function testBillboardRoute(): void
    {
        $result = $this->get('cartelera');
        $result->assertStatus(200);
        $result->assertSee('Cartelera');
    }

    public function testGuidedVisitsRoute(): void
    {
        $result = $this->get('visitas-guiadas');
        $result->assertStatus(302);
    }

    public function testMuseumTodayRoute(): void
    {
        $result = $this->get('museo/el-museo/actualidad');
        $result->assertStatus(200);
        $result->assertSee('Actualidad del museo');
        $result->assertSee('Lectura editorial');
        $result->assertDontSee('mock');
    }

    public function testFriendsRoute(): void
    {
        $result = $this->get('amigos-de-teatromuseo');
        $result->assertStatus(200);
        $result->assertSee('Amigos de Teatromuseo');
    }
}
