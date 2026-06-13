<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Services\TotemApiInterface;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Health check controller for monitoring the kiosk status.
 *
 * Provides endpoint to verify API connectivity and system health.
 */
final class HealthController extends Controller
{
    private TotemApiInterface $api;

    public function __construct()
    {
        /** @var TotemApiInterface $api */
        $api       = service('totemApi');
        $this->api = $api;
    }

    /**
     * Health check endpoint.
     *
     * Returns JSON with status information including API connectivity.
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $apiStatus  = $this->checkApiStatus();
        $overall    = $apiStatus === 'reachable' ? 'ok' : 'error';
        $statusCode = $overall === 'ok' ? 200 : 503;

        $response = [
            'status'    => $overall,
            'api'       => $apiStatus,
            'timestamp' => date('c'),
        ];

        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON($response);
    }

    /**
     * Check if the API is reachable by making a lightweight call.
     *
     * @return string 'reachable' or 'unreachable'
     */
    private function checkApiStatus(): string
    {
        try {
            // Try to fetch courses as a lightweight endpoint
            $result = $this->api->courses();

            // If we get any response (even empty array), API is reachable
            return 'reachable';
        } catch (\Exception $e) {
            return 'unreachable';
        }
    }
}
