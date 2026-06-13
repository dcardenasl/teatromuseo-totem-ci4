<?php

namespace Tests\Unit\Controllers;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;

/**
 * @internal
 */
final class HealthControllerTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testHealthEndpointReturnsOkStatus(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function testHealthEndpointReturnsJsonWithStatusField(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $result->assertSee('"status"');
        $result->assertSee('"ok"');
    }

    public function testHealthEndpointReturnsJsonWithApiField(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $result->assertSee('"api"');
        $result->assertSee('"reachable"');
    }

    public function testHealthEndpointReturnsJsonWithTimestamp(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $result->assertSee('"timestamp"');
    }

    public function testHealthEndpointReturnsJsonContentType(): void
    {
        $result = $this->get('health');

        $result->assertStatus(200);
        $result->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }
}
