<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Config\Services as CIBaseServices;
use CodeIgniter\HTTP\CURLRequest;
use Exception;

/**
 * Servicio consumidor de la API REST del Teatro Museo para el Tótem Interactivo.
 * Realiza llamadas server-side con la API Key configurada y registra errores.
 */
class TotemApiService implements TotemApiInterface
{
    private string $baseUrl;
    private string $apiKey;
    private ?CURLRequest $client = null;

    public function __construct(?CURLRequest $client = null)
    {
        /** @var string|false|null $baseUrl */
        $baseUrl       = env('TOTEM_API_URL');
        $this->baseUrl = ($baseUrl !== false && $baseUrl !== null && $baseUrl !== '') ? $baseUrl : 'http://localhost:8080/api/v1/totem';

        /** @var string|false|null $apiKey */
        $apiKey       = env('TOTEM_API_KEY');
        $this->apiKey = ($apiKey !== false && $apiKey !== null && $apiKey !== '') ? $apiKey : '';

        // Store injected client, otherwise create lazily
        $this->client = $client;
    }

    /**
     * Get or create the HTTP client lazily.
     */
    private function getClient(): ?CURLRequest
    {
        if ($this->client === null) {
            try {
                $this->client = CIBaseServices::curlrequest([
                    'base_URI' => $this->baseUrl,
                    'timeout'  => 5,
                ]);
            } catch (\Exception $e) {
                // If we can't create the client, return null
                // The get() method will handle this gracefully
                return null;
            }
        }

        return $this->client;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function shows(): array
    {
        return $this->asList($this->get('shows'));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function techniques(): array
    {
        return $this->asList($this->get('techniques'));
    }

    /**
     * @return array<string, mixed>
     */
    public function technique(int $id): array
    {
        return $this->asMap($this->get("technique/{$id}"));
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function courses(): array
    {
        return $this->asList($this->get('courses'));
    }

    /**
     * @return array<string, mixed>
     */
    public function museum(): array
    {
        return $this->asMap($this->get('museum'));
    }

    /**
     * @return array<string, mixed>
     */
    public function museumHistory(string $slug): array
    {
        return $this->asMap($this->get("museum-history/{$slug}"));
    }

    /**
     * @return array<string, list<array<string, mixed>>>
     */
    public function collection(): array
    {
        $res = $this->get('collection');

        return isset($res['items_by_category']) && is_array($res['items_by_category'])
            ? $res['items_by_category']
            : [];
    }

    public function collectionItem(int $id): array
    {
        return $this->asMap($this->get("collection/{$id}"));
    }

    /**
     * Execute a GET request safely.
     *
     * Logs non-2xx status codes and invalid JSON, but still returns an empty
     * array so the kiosk UI can fall back gracefully.
     *
     * @return list<array<string, mixed>>|array<string, mixed>
     */
    private function get(string $path, array $params = []): array
    {
        $startTime = microtime(true);
        $endpoint  = ltrim($path, '/');

        try {
            $client = $this->getClient();
            if ($client === null) {
                return [];
            }

            $headers = [
                'Accept' => 'application/json',
            ];

            if ($this->apiKey !== '') {
                $headers['X-Totem-Key'] = $this->apiKey;
            }

            $options = [
                'headers' => $headers,
            ];

            if ($params !== []) {
                $options['query'] = $params;
            }

            $response   = $client->get($endpoint, $options);
            $status     = $response->getStatusCode();
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);

            if ($status !== 200) {
                $this->logApiCall($endpoint, $durationMs, $status, 'non_2xx_response');

                return [];
            }

            $raw  = (string) $response->getBody();
            $body = json_decode($raw, true);

            if (!is_array($body)) {
                $this->logApiCall($endpoint, $durationMs, $status, 'invalid_json');

                return [];
            }

            $this->logApiCall($endpoint, $durationMs, $status, null);

            if (isset($body['data']) && is_array($body['data'])) {
                return $body['data'];
            }

            return $body;
        } catch (Exception $e) {
            $durationMs = (int) ((microtime(true) - $startTime) * 1000);
            $this->logApiCall($endpoint, $durationMs, 0, $e->getMessage());

            return [];
        }
    }

    /**
     * Log API call with structured JSON format.
     *
     * @param string      $endpoint   API endpoint called
     * @param int         $durationMs Response time in milliseconds
     * @param int         $status     HTTP status code (0 for network errors)
     * @param string|null $error      Error message or type, null if success
     */
    private function logApiCall(string $endpoint, int $durationMs, int $status, ?string $error): void
    {
        $logEntry = [
            'timestamp' => date('c'),
            'service'   => 'totem_api',
            'endpoint'  => $endpoint,
            'duration'  => $durationMs,
            'status'    => $status,
            'success'   => $error === null && $status === 200,
        ];

        if ($error !== null) {
            $logEntry['error'] = $error;
        }

        $level = $error !== null || $status !== 200 ? 'warning' : 'debug';
        log_message($level, '[TotemApiService] ' . json_encode($logEntry, JSON_UNESCAPED_SLASHES));
    }

    /**
     * @param list<array<string, mixed>>|array<string, mixed> $data
     * @return list<array<string, mixed>>
     */
    private function asList(array $data): array
    {
        if ($data === []) {
            return [];
        }

        if (array_is_list($data)) {
            $filtered = array_filter($data, static fn ($item): bool => is_array($item));

            return array_values(array_map(
                static fn (array $item): array => $item,
                $filtered,
            ));
        }

        return [];
    }

    /**
     * @param list<array<string, mixed>>|array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function asMap(array $data): array
    {
        if ($data === []) {
            return [];
        }

        if (!array_is_list($data)) {
            /** @var array<string, mixed> $data */
            return $data;
        }

        return [];
    }
}
