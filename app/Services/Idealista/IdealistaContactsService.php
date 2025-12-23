<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaContactsService
{
    public function __construct(
        private readonly IdealistaClient $client
    ) {
    }

    /**
     * Lista todos los contactos asociados al feedKey
     *
     * @param int $page Número de página (mínimo 1)
     * @param int $size Número de contactos por página (1-100)
     * @return array
     */
    public function list(int $page = 1, int $size = 50): array
    {
        $response = $this->client->request('GET', '/v1/contacts', [
            'headers' => $this->headers(),
            'query' => [
                'page' => max(1, $page),
                'size' => min(100, max(1, $size)),
            ],
        ]);

        return $response->throw()->json();
    }

    /**
     * Obtiene un contacto específico por su ID
     *
     * @param int $contactId
     * @return array
     */
    public function find(int $contactId): array
    {
        $response = $this->client->request('GET', "/v1/contacts/{$contactId}", [
            'headers' => $this->headers(),
        ]);

        return $response->throw()->json();
    }

    /**
     * Crea un nuevo contacto
     *
     * @param array $payload Datos del contacto
     * @return array
     */
    public function create(array $payload): array
    {
        $response = $this->client->request('POST', '/v1/contacts', [
            'headers' => $this->headers(),
            'json' => $payload,
        ]);

        return $response->throw()->json();
    }

    /**
     * Actualiza un contacto existente
     * Nota: Si el contacto pertenece a un agente, no se puede modificar
     *
     * @param int $contactId
     * @param array $payload Datos actualizados del contacto
     * @return array
     */
    public function update(int $contactId, array $payload): array
    {
        $response = $this->client->request('PUT', "/v1/contacts/{$contactId}", [
            'headers' => $this->headers(),
            'json' => $payload,
        ]);

        return $response->throw()->json();
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

