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
    private CURLRequest $client;

    public function __construct(?CURLRequest $client = null)
    {
        /** @var string $baseUrl */
        $baseUrl       = env('TOTEM_API_URL');
        $this->baseUrl = $baseUrl !== false && $baseUrl !== '' ? $baseUrl : 'http://localhost:8080/api/v1/totem';

        /** @var string $apiKey */
        $apiKey       = env('TOTEM_API_KEY');
        $this->apiKey = $apiKey !== false && $apiKey !== '' ? $apiKey : '';

        $this->client = $client ?? CIBaseServices::curlrequest([
            'base_URI' => $this->baseUrl,
            'timeout'  => 5,
        ]);
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
     * Execute a GET request safely.
     *
     * Logs non-2xx status codes and invalid JSON, but still returns an empty
     * array so the kiosk UI can fall back gracefully.
     *
     * @return list<array<string, mixed>>|array<string, mixed>
     */
    private function get(string $path, array $params = []): array
    {
        try {
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

            $response = $this->client->get(ltrim($path, '/'), $options);
            $status   = $response->getStatusCode();

            if ($status !== 200) {
                log_message('warning', "[TotemApiService] Non-2xx response from {$path}: HTTP {$status}");

                return [];
            }

            $raw  = (string) $response->getBody();
            $body = json_decode($raw, true);

            if (!is_array($body)) {
                log_message('warning', "[TotemApiService] Invalid JSON from {$path}");

                return [];
            }

            if (isset($body['data']) && is_array($body['data'])) {
                return $body['data'];
            }

            return $body;
        } catch (Exception $e) {
            log_message('error', '[TotemApiService] Error al consumir ' . $path . ': ' . $e->getMessage());

            return [];
        }
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
