<?php

namespace App\Services;

use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\CURLRequest;
use Exception;

/**
 * Servicio consumidor de la API REST del Teatro Museo para el Tótem Interactivo.
 * Realiza llamadas seguras server-side con la API Key configurada.
 */
class TotemApiService
{
    private string $baseUrl;
    private string $apiKey;
    private CURLRequest $client;

    public function __construct()
    {
        // Leer variables del .env con fallbacks adecuados
        $this->baseUrl = env('TOTEM_API_URL') ?: 'http://localhost:8080/api/v1/totem';
        $this->apiKey  = env('TOTEM_API_KEY') ?: '';
        
        // Inicializar el cliente CURL de CodeIgniter 4
        $this->client = Services::curlrequest([
            'base_URI' => $this->baseUrl,
            'timeout'  => 5, // Timeout estricto de 5 segundos
        ]);
    }

    /**
     * Realiza una petición GET de manera segura.
     * En caso de error, retorna un arreglo vacío para resiliencia en UI.
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

            if ($response->getStatusCode() === 200) {
                $body = json_decode($response->getBody(), true);
                if (is_array($body) && isset($body['data'])) {
                    return $body['data'];
                }
                return is_array($body) ? $body : [];
            }
        } catch (Exception $e) {
            // Registrar el error en los logs internos para auditoría silenciosa
            log_message('error', '[TotemApiService] Error al consumir ' . $path . ': ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Obtiene shows (funciones) futuros (máximo 5)
     */
    public function shows(): array
    {
        return $this->get('shows');
    }

    /**
     * Obtiene el catálogo de la colección
     */
    public function collection(array $params = []): array
    {
        return $this->get('collection', $params);
    }

    /**
     * Obtiene un objeto de la colección por su ID
     */
    public function collectionItem(int $id): array
    {
        return $this->get("collection/{$id}");
    }

    /**
     * Obtiene las técnicas de títere
     */
    public function techniques(): array
    {
        return $this->get('techniques');
    }

    /**
     * Obtiene el detalle de una técnica por ID
     */
    public function technique(int $id): array
    {
        return $this->get("technique/{$id}");
    }

    /**
     * Obtiene la lista de cursos / talleres vigentes
     */
    public function courses(): array
    {
        return $this->get('courses');
    }

    /**
     * Obtiene la información del museo (edificio, institución, actualidad)
     */
    public function museum(): array
    {
        return $this->get('museum');
    }

    /**
     * Obtiene una publicación de Historia Cómica por su slug
     */
    public function museumHistory(string $slug): array
    {
        return $this->get("museum-history/{$slug}");
    }

    /**
     * Obtiene las visitas guiadas / extensión
     */
    public function guidedVisits(): array
    {
        return $this->get('guided-visits');
    }
}
