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
            // Simple HTTP check to API endpoint
            /** @var string|false $apiUrl */
            $apiUrl = env('TOTEM_API_URL');
            if ($apiUrl === false || $apiUrl === '' || !is_string($apiUrl)) {
                return 'unreachable';
            }

            // Try to make a simple HTTP request using the framework's native client
            $client = \Config\Services::curlrequest([
                'base_URI' => $apiUrl,
                'timeout'  => 5,
            ]);

            $response = $client->get('courses', [
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $httpCode = $response->getStatusCode();

            // If we got any response (even 404), the API is reachable
            return $httpCode >= 200 && $httpCode < 600 ? 'reachable' : 'unreachable';
        } catch (\Exception $e) {
            return 'unreachable';
        }
    }
}
