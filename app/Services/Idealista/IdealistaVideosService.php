<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaVideosService
{
    public function __construct(
        private readonly IdealistaClient $client
    ) {
    }

    /**
     * Lista todos los videos de una propiedad
     *
     * @param int $propertyId
     * @return array
     */
    public function list(int $propertyId): array
    {
        $response = $this->client->request('GET', "/v1/properties/{$propertyId}/videos", [
            'headers' => $this->headers(),
        ]);

        return $response->throw()->json();
    }

    /**
     * Obtiene un video específico de una propiedad
     *
     * @param int $propertyId
     * @param int $videoId
     * @return array
     */
    public function find(int $propertyId, int $videoId): array
    {
        $response = $this->client->request('GET', "/v1/properties/{$propertyId}/videos/{$videoId}", [
            'headers' => $this->headers(),
        ]);

        return $response->throw()->json();
    }

    /**
     * Crea un nuevo video para una propiedad
     * Nota: Idealista NO acepta plataformas de streaming (Youtube, Vimeo, etc)
     * Solo URLs directas que permitan descargar el archivo
     * Formatos: MP4, AVI, MOV, WMV, MPEG, FLV, 3GP
     * Tamaño máximo: 750MB por video
     * Máximo: 6 videos por propiedad
     *
     * @param int $propertyId
     * @param array $payload Datos del video (url, title, description, etc)
     * @return array
     */
    public function create(int $propertyId, array $payload): array
    {
        $response = $this->client->request('POST', "/v1/properties/{$propertyId}/videos", [
            'headers' => $this->headers(),
            'json' => $payload,
        ]);

        return $response->throw()->json();
    }

    /**
     * Actualiza un video existente
     *
     * @param int $propertyId
     * @param int $videoId
     * @param array $payload Datos actualizados del video
     * @return array
     */
    public function update(int $propertyId, int $videoId, array $payload): array
    {
        $response = $this->client->request('PUT', "/v1/properties/{$propertyId}/videos/{$videoId}", [
            'headers' => $this->headers(),
            'json' => $payload,
        ]);

        return $response->throw()->json();
    }

    /**
     * Elimina un video
     *
     * @param int $propertyId
     * @param int $videoId
     * @return array
     */
    public function delete(int $propertyId, int $videoId): array
    {
        $response = $this->client->request('DELETE', "/v1/properties/{$propertyId}/videos/{$videoId}", [
            'headers' => $this->headers(),
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

