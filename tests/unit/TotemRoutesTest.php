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
        $body = (string) $result->getBody();

        $result->assertStatus(200);
        $result->assertSee('Menú');
        self::assertStringContainsString('href="' . base_url('menu') . '"', $body);
        $result->assertSee('INICIO');
    }

    public function testMuseumRoute(): void
    {
        $result = $this->get('museo');
        $result->assertStatus(200);
        $result->assertSee('Museo');
        $result->assertSee('Explora el museo');
        $result->assertSee('Historia');
        $result->assertSee('assets/img/museum/coleccion-card.webp');
        $result->assertSee('assets/img/museum/explora-el-museo-card.webp');
        $result->assertSee('assets/img/museum/historia-card.webp');
        $result->assertSee('assets/img/museum/visitas-guiadas-card.webp');
        $result->assertDontSee('assets/img/museo/el-museo/explora-el-museo.webp');
        $result->assertDontSee('assets/img/museo/historia/historia-editorial.webp');
    }

    public function testMuseumInfoMainRoute(): void
    {
        $result = $this->get('museo/el-museo');

        $result->assertStatus(200);
        $result->assertSee('Explora el museo');
        $result->assertSee('Historia de Teatromuseo');
        $result->assertSee('Historia de la Iglesia');
        $result->assertSee('Teatromuseo Hoy');
        $result->assertSee('assets/img/museo/el-museo/collage-nuestra-historia.webp');
        $result->assertSee('assets/img/museo/el-museo/collage-san-judas.webp');
        $result->assertSee('assets/img/museo/el-museo/collage-historia-actual.webp');
    }

    public function testMuseumInfoDetailRoutesUseCorrectCollages(): void
    {
        $history = $this->get('museo/el-museo/edificio');
        $history->assertStatus(200);
        $history->assertSee('Historia de Teatromuseo');
        $history->assertSee('assets/img/museo/el-museo/collage-nuestra-historia.webp');
        $history->assertDontSee('mock');

        $church = $this->get('museo/el-museo/institucion');
        $church->assertStatus(200);
        $church->assertSee('La Iglesia San Judas Tadeo');
        $church->assertSee('assets/img/museo/el-museo/collage-san-judas.webp');
        $church->assertDontSee('mock');
    }

    public function testCollectionRoute(): void
    {
        $result = $this->get('museo/coleccion');
        $body = (string) $result->getBody();

        $result->assertStatus(200);
        $result->assertSee('Colección');
        $result->assertSee('Títeres');
        $result->assertSee('Payasos');
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
        $result->assertSee('assets/img/museo/coleccion/titeres/titere.webp');
        $result->assertSee('assets/img/museo/coleccion/mascaras/mascara.webp');
        $result->assertSee('assets/img/museo/coleccion/payasos/payaso.webp');
        $result->assertDontSee('Teatro de payasos');

        self::assertTrue(
            strpos($body, 'collection-band--puppets') !== false
            && strpos($body, 'collection-band--masks') !== false
            && strpos($body, 'collection-band--clowns') !== false
            && strpos($body, 'collection-band--puppets') < strpos($body, 'collection-band--clowns')
            && strpos($body, 'collection-band--clowns') < strpos($body, 'collection-band--masks'),
            'La colección debería ordenar Títeres, Payasos y Máscaras.'
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
        $result->assertSee('assets/img/museo/historia/collage-circo.webp');
        $result->assertSee('assets/img/museo/historia/collage-teatro.webp');
        $result->assertDontSee('Tradición del Títere');
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
        $result->assertSee('Teatromuseo Hoy');
        $result->assertSee('Actualidad del museo');
        $result->assertSee('Lectura editorial');
        $result->assertSee('assets/img/museo/el-museo/collage-historia-actual.webp');
        $result->assertDontSee('mock');
    }

    public function testFriendsRoute(): void
    {
        $result = $this->get('amigos-de-teatromuseo');
        $result->assertStatus(200);
        $result->assertSee('Amigos de Teatromuseo');
    }
}
