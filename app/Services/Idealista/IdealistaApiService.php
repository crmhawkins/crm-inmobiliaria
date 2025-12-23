<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaApiService
{
    public function __construct(private readonly IdealistaClient $client)
    {
    }

    public function call(string $method, string $endpoint, array $query = [], ?array $body = null): array
    {
        $response = $this->client->request($method, $endpoint, array_filter([
            'headers' => $this->headers(),
            'query' => $query,
            'json' => $body,
        ]));

        $json = $response->throw()->json();

        // Agregar status code a la respuesta
        $json['status_code'] = $response->status();

        return $json;
    }

    private function headers(): array
    {
        $feedKey = config('services.idealista.feed_key');

        if (! $feedKey) {
            throw new RuntimeException('Configura IDEALISTA_FEED_KEY para poder llamar a la API.');
        }

        return [
            'feedKey' => $feedKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }
}

