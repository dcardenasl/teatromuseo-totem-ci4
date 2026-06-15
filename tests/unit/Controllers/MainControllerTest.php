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
        $result = $this->get('language?from=museo');

        $result->assertStatus(200);
        $result->assertSee('Selecciona tu idioma');

        $body = (string) $result->getBody();
        self::assertStringContainsString('data-on-select="' . base_url('museo') . '"', $body);
        self::assertStringContainsString('data-on-cancel="' . base_url('museo') . '"', $body);
    }

    public function testLanguageFromSplashSelectsMenuButCancelsToSplash(): void
    {
        $result = $this->get('language?from=/');

        $result->assertStatus(200);
        $result->assertSee('Selecciona tu idioma');

        $body = (string) $result->getBody();
        self::assertStringContainsString('data-on-select="' . base_url('menu') . '"', $body);
        self::assertStringContainsString('data-on-cancel="' . base_url('/') . '"', $body);
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
