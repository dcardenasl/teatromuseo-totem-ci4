<?php

declare(strict_types=1);

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Health check controller for monitoring the kiosk status.
 *
 * Provides endpoint to verify API connectivity and system health.
 */
final class HealthController extends Controller
{
    /**
     * Health check endpoint.
     *
     * Returns JSON with status information including API connectivity.
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $apiStatus = 'unknown';

        try {
            // Check if we can instantiate the API service
            $api = service('totemApi');
            $apiStatus = $api !== null ? 'service_ok' : 'service_null';
        } catch (\Exception $e) {
            $apiStatus = 'error: ' . $e->getMessage();
        }

        $response = [
            'status'    => 'ok',
            'api'       => $apiStatus,
            'timestamp' => date('c'),
        ];

        return $this->response
            ->setStatusCode(200)
            ->setJSON($response);
    }
}
