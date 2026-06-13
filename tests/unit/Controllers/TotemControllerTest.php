<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class TotemControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

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

    public function testTheaterSchoolRouteRendersCourseSection(): void
    {
        $result = $this->get('teatro-escuela');

        $result->assertStatus(200);
        $result->assertSee('Teatro escuela');
        $result->assertSee('school-course');
    }

    public function testBillboardRouteRendersEvents(): void
    {
        $result = $this->get('cartelera');

        $result->assertStatus(200);
        $result->assertSee('Cartelera');
        $result->assertSee('event-card');
    }
}
