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
        $result->assertSee('Principal');
    }

    public function testMuseumRoute(): void
    {
        $result = $this->get('museo');
        $result->assertStatus(200);
        $result->assertSee('Explora el museo');
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
