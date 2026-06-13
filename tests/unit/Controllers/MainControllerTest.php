<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class MainControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testIndexRouteRendersSplash(): void
    {
        $result = $this->get('/');

        $result->assertStatus(200);
        $result->assertSee('Toca para comenzar');
    }

    public function testLanguageWithValidFromParameter(): void
    {
        $result = $this->get('language?from=menu');

        $result->assertStatus(200);
        $result->assertSee('Selecciona tu idioma');

        $body = (string) $result->getBody();
        self::assertStringContainsString('targetUrl', $body);
        self::assertStringContainsString('menu', $body);
    }

    public function testLanguageWithInvalidFromParameter(): void
    {
        $result = $this->get('language?from=<script>alert(1)</script>');

        $result->assertStatus(200);
        $result->assertSee('Selecciona tu idioma');

        $body = (string) $result->getBody();
        self::assertStringNotContainsString('<script>alert(1)</script>', $body);
    }

    public function testMenuRouteRendersGrid(): void
    {
        $result = $this->get('menu');

        $result->assertStatus(200);
        $result->assertSee('Menú');
        $result->assertSee('menu-card--museum');
    }

    public function testNotFoundRouteRendersFriendlyPage(): void
    {
        $result = $this->get('ruta-que-no-existe');

        $result->assertStatus(404);
        $result->assertSee('Página no encontrada');
    }
}
