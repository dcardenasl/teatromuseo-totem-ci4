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
     * Check if the BFF is reachable via its own `/ready` probe (unauthenticated,
     * excluded from its throttle bucket by design — see its `Config\Filters`).
     *
     * @return string 'reachable' or 'unreachable'
     */
    private function checkApiStatus(): string
    {
        try {
            /** @var string|false $bffBaseUrl */
            $bffBaseUrl = env('TOTEM_BFF_BASE_URL');
            if ($bffBaseUrl === false || $bffBaseUrl === '' || !is_string($bffBaseUrl)) {
                return 'unreachable';
            }

            $timeout = getenv('TOTEM_BFF_TIMEOUT_SECONDS');

            $client = \Config\Services::curlrequest([
                'baseURI' => rtrim($bffBaseUrl, '/') . '/',
                'timeout'  => is_numeric($timeout) ? (int) $timeout : 5,
            ]);

            $response = $client->get('ready', [
                'headers' => ['Accept' => 'application/json'],
            ]);
            $httpCode = $response->getStatusCode();

            return $httpCode >= 200 && $httpCode < 600 ? 'reachable' : 'unreachable';
        } catch (\Exception $e) {
            return 'unreachable';
        }
    }
}
