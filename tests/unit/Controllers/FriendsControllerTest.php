<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class FriendsControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testFriendsRouteRendersSection(): void
    {
        $result = $this->get('amigos-de-teatromuseo');

        $result->assertStatus(200);
        $result->assertSee('Amigos de Teatromuseo');
        $result->assertDontSee('content-panel');
        $result->assertDontSee('stat-card');
        $result->assertDontSee('section-hero__visual--friends');
    }

    public function testExtensionRouteRendersContact(): void
    {
        $result = $this->get('extension');

        $result->assertStatus(200);
        $result->assertSee('Contacto');
    }
}
