<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaCustomerService
{
    public function __construct(
        private readonly IdealistaClient $client
    ) {
    }

    /**
     * Obtiene información de publicación del cliente
     *
     * Retorna información relacionada con la cuenta del cliente:
     * - Si la cuenta está activa
     * - Número de anuncios publicados
     * - Cuántos anuncios puede publicar el cliente
     *
     * @return array
     */
    public function getPublicationInfo(): array
    {
        $response = $this->client->request('GET', '/v1/customer/publishinfo', [
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

