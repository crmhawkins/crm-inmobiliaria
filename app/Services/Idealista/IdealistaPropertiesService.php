<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaPropertiesService
{
    public function __construct(
        private readonly IdealistaClient $client
    ) {
    }

    public function list(int $page = 1, int $size = 50, ?string $state = null): array
    {
        $response = $this->client->request('GET', '/v1/properties', [
            'headers' => $this->headers(),
            'query' => array_filter([
                'page' => $page,
                'size' => $size,
                'state' => $state,
            ], static fn ($value) => $value !== null),
        ]);

        return $response->throw()->json();
    }

    public function find(int $propertyId): array
    {
        return $this->client->request('GET', "/v1/properties/{$propertyId}", [
            'headers' => $this->headers(),
        ])->throw()->json();
    }

    public function create(array $payload): array
    {
        return $this->client->request('POST', '/v1/properties', [
            'headers' => $this->headers(),
            'json' => $payload,
        ])->throw()->json();
    }

    public function update(int $propertyId, array $payload): array
    {
        return $this->client->request('PUT', "/v1/properties/{$propertyId}", [
            'headers' => $this->headers(),
            'json' => $payload,
        ])->throw()->json();
    }

    public function deactivate(int $propertyId): array
    {
        return $this->client->request('POST', "/v1/properties/{$propertyId}/deactivate", [
            'headers' => $this->headers(),
        ])->throw()->json();
    }

    public function reactivate(int $propertyId): array
    {
        return $this->client->request('POST', "/v1/properties/{$propertyId}/reactivate", [
            'headers' => $this->headers(),
        ])->throw()->json();
    }

    public function cloneProperty(int $propertyId, array $payload): array
    {
        return $this->client->request('POST', "/v1/properties/{$propertyId}/clone", [
            'headers' => $this->headers(),
            'json' => $payload,
        ])->throw()->json();
    }

    public function listImages(int $propertyId): array
    {
        return $this->client->request('GET', "/v1/properties/{$propertyId}/images", [
            'headers' => $this->headers(),
        ])->throw()->json();
    }

    public function replaceImages(int $propertyId, array $payload): array
    {
        // Idealista acepta solo URLs públicas en formato JSON
        // No acepta archivos directamente (multipart/form-data)
        return $this->client->request('PUT', "/v1/properties/{$propertyId}/images", [
            'headers' => $this->headers(),
            'json' => $payload,
        ])->throw()->json();
    }

    public function deleteImages(int $propertyId): array
    {
        return $this->client->request('DELETE', "/v1/properties/{$propertyId}/images", [
            'headers' => $this->headers(),
        ])->throw()->json();
    }

    private function headers(): array
    {
        $feedKey = config('services.idealista.feed_key');

        if (! $feedKey) {
            throw new RuntimeException('Falta configurar IDEALISTA_FEED_KEY.');
        }

        return [
            'feedKey' => $feedKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}

