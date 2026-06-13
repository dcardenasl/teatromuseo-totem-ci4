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

    public function testHealthEndpointReturnsJson(): void
    {
        $result = $this->get('health');

        $result->assertHeader('Content-Type', 'application/json; charset=UTF-8');
    }

    public function testHealthEndpointReturnsJsonWithStatusField(): void
    {
        $result = $this->get('health');

        $result->assertSee('"status"');

        $body = (string) $result->getBody();
        $hasOk    = strpos($body, '"ok"') !== false;
        $hasError = strpos($body, '"error"') !== false;
        $this->assertTrue(
            $hasOk || $hasError,
            'Response should contain either "ok" or "error" status'
        );
    }

    public function testHealthEndpointReturnsJsonWithApiField(): void
    {
        $result = $this->get('health');

        $result->assertSee('"api"');

        $body = (string) $result->getBody();
        $hasReachable   = strpos($body, '"reachable"') !== false;
        $hasUnreachable = strpos($body, '"unreachable"') !== false;
        $this->assertTrue(
            $hasReachable || $hasUnreachable,
            'Response should contain either "reachable" or "unreachable" api status'
        );
    }

    public function testHealthEndpointReturnsJsonWithTimestamp(): void
    {
        $result = $this->get('health');

        $result->assertSee('"timestamp"');
    }

    public function testHealthEndpointReturnsValidHttpStatus(): void
    {
        $result = $this->get('health');

        // Should return either 200 (ok) or 503 (error) depending on API status
        $status = $result->response()->getStatusCode();
        $this->assertTrue(
            $status === 200 || $status === 503,
            "Expected status 200 or 503, got {$status}"
        );
    }
}
