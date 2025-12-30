<?php

namespace App\Services\Fotocasa;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class FotocasaClient
{
    /**
     * Base URL de la API de Fotocasa
     */
    private const BASE_URL = 'https://imports.gw.fotocasa.pro/api';

    /**
     * API Key para autenticación
     */
    private string $apiKey;

    /**
     * Timeout para las peticiones HTTP
     */
    private int $timeout;

    /**
     * Constructor
     */
    public function __construct(?string $apiKey = null, int $timeout = 30)
    {
        $this->apiKey = $apiKey ?? env('API_KEY', '');
        $this->timeout = $timeout;
    }

    /**
     * Obtiene la URL base de la API
     */
    public function getBaseUrl(): string
    {
        return self::BASE_URL;
    }

    /**
     * Realiza una petición GET a la API
     */
    public function get(string $endpoint, array $query = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions($this->getOptions())
                ->get($url, $query);

            return $this->handleResponse($response, 'GET', $url);
        } catch (\Exception $e) {
            Log::error('Error en petición GET a Fotocasa', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Realiza una petición POST a la API
     */
    public function post(string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions($this->getOptions())
                ->post($url, $data);

            return $this->handleResponse($response, 'POST', $url, $data);
        } catch (\Exception $e) {
            Log::error('Error en petición POST a Fotocasa', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Realiza una petición PUT a la API
     */
    public function put(string $endpoint, array $data = []): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions($this->getOptions())
                ->put($url, $data);

            return $this->handleResponse($response, 'PUT', $url, $data);
        } catch (\Exception $e) {
            Log::error('Error en petición PUT a Fotocasa', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Realiza una petición DELETE a la API
     */
    public function delete(string $endpoint): array
    {
        $url = $this->buildUrl($endpoint);

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->withOptions($this->getOptions())
                ->delete($url);

            return $this->handleResponse($response, 'DELETE', $url);
        } catch (\Exception $e) {
            Log::error('Error en petición DELETE a Fotocasa', [
                'endpoint' => $endpoint,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Obtiene la lista de propiedades
     *
     * @param array $query Parámetros de consulta (ej: ['state' => 'all', 'includeUnpublished' => true])
     */
    public function getProperties(array $query = []): array
    {
        return $this->get('/property', $query);
    }

    /**
     * Obtiene una propiedad específica por ExternalId
     */
    public function getProperty(string $externalId): array
    {
        return $this->get("/property/{$externalId}");
    }

    /**
     * Crea o actualiza una propiedad
     */
    public function createOrUpdateProperty(array $propertyData): array
    {
        return $this->post('/property', $propertyData);
    }

    /**
     * Elimina una propiedad
     */
    public function deleteProperty(string $externalId): array
    {
        return $this->delete("/property/{$externalId}");
    }

    /**
     * Obtiene los diccionarios (tipos de vivienda, transacciones, etc.)
     */
    public function getDictionaries(): array
    {
        return $this->get('/dictionaries');
    }

    /**
     * Obtiene los leads
     */
    public function getLeads(array $query = []): array
    {
        return $this->get('/leads', $query);
    }

    /**
     * Construye la URL completa del endpoint
     */
    private function buildUrl(string $endpoint): string
    {
        $endpoint = ltrim($endpoint, '/');
        return rtrim(self::BASE_URL, '/') . '/' . $endpoint;
    }

    /**
     * Obtiene los headers para las peticiones
     */
    private function getHeaders(): array
    {
        $headers = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        // Según la documentación de Fotocasa, la autenticación se hace con api-key
        if ($this->apiKey) {
            $headers['api-key'] = $this->apiKey;
        }

        return $headers;
    }

    /**
     * Obtiene las opciones para las peticiones HTTP
     */
    private function getOptions(): array
    {
        return [
            'verify' => env('FOTOCASA_VERIFY_SSL', false), // Por defecto no verificar SSL (común en desarrollo)
            'timeout' => $this->timeout,
        ];
    }

    /**
     * Maneja la respuesta de la API
     */
    private function handleResponse($response, string $method, string $url, ?array $payload = null): array
    {
        $statusCode = $response->status();
        $body = $response->body();
        $json = $response->json();

        // Si json es null o false, intentar decodificar manualmente
        if ($json === null || $json === false) {
            $json = json_decode($body, true);
        }

        // Si la respuesta es un string JSON escapado (común en algunas APIs)
        // Intentar decodificar dos veces
        if (is_string($json) && !empty($json)) {
            $json = json_decode($json, true);
        }

        // Si sigue siendo null o no es array, usar array vacío
        if ($json === null || !is_array($json)) {
            $json = [];
        }

        // Log de la respuesta
        Log::info('Respuesta de Fotocasa API', [
            'method' => $method,
            'url' => $url,
            'status_code' => $statusCode,
            'response_body' => $body,
            'response_body_length' => strlen($body),
            'response_json' => $json,
            'response_is_array' => is_array($json),
            'response_count' => is_array($json) ? count($json) : 0,
        ]);

        // Si hay payload, también loguearlo
        if ($payload !== null) {
            Log::debug('Payload enviado a Fotocasa', [
                'url' => $url,
                'payload' => $payload,
            ]);
        }

        // Si la respuesta no es exitosa, lanzar excepción
        if (!$response->successful()) {
            $errorMessage = is_array($json)
                ? ($json['message'] ?? $json['error'] ?? $json['Message'] ?? $body ?? 'Error desconocido')
                : ($body ?? 'Error desconocido');

            throw new \Exception("Fotocasa API Error ({$statusCode}): {$errorMessage}", $statusCode);
        }

        return $json;
    }
}
