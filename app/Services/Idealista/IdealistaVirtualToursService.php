<?php

namespace App\Services\Idealista;

use RuntimeException;

class IdealistaVirtualToursService
{
    public function __construct(
        private readonly IdealistaClient $client
    ) {
    }

    /**
     * Obtiene los tours virtuales de una propiedad
     *
     * @param int $propertyId
     * @return array
     */
    public function find(int $propertyId): array
    {
        $response = $this->client->request('GET', "/v1/properties/{$propertyId}/virtualtours", [
            'headers' => $this->headers(),
        ]);

        return $response->throw()->json();
    }

    /**
     * Crea un nuevo tour virtual para una propiedad
     *
     * Nota: Para publicar tours virtuales, el cliente debe contactar primero
     * con su account manager de Idealista para activar el servicio.
     *
     * Proveedores permitidos:
     * - Virtual tour 3D: Matterport y VistaPlayer3d
     * - Otros formatos: Immoviewer, Spectando, Floorplanner, Realisti_co,
     *   Goldmark, Floorfy, Fastout, Panotour, Everpano, Toursvirtuales360,
     *   KeepEyeOnBall, Inmovilla, Abitarepn, Pano2VR, Plushglobalmedia,
     *   Vizor.io, Nodalview, Gothru, Guru360, Creotour, Habiteo, Vitrio,
     *   Plug-in.studio, Ppgstudios, 360forcurious, Roundme, Virtualitour,
     *   Sircase, Divein.studio, Casagest24, Spherical, Gizmo-3d, Kuula,
     *   Emporda360, Vista360, Clicktours, Espaciosvirtuales.es, Cloudpano,
     *   Bizionar y Matterport360.
     *
     * Importante:
     * - Solo se acepta un tour 3D y un tour virtual por propiedad
     * - Si se envía un tour y la propiedad ya tiene otro, el primero será reemplazado
     * - Un tour virtual creado por Idealista no puede ser reemplazado, eliminado o encontrado
     *
     * @param int $propertyId
     * @param array $payload Datos del tour virtual (url, type, provider, etc)
     * @return array
     */
    public function create(int $propertyId, array $payload): array
    {
        $response = $this->client->request('POST', "/v1/properties/{$propertyId}/virtualtours", [
            'headers' => $this->headers(),
            'json' => $payload,
        ]);

        return $response->throw()->json();
    }

    /**
     * Desactiva un tour virtual
     *
     * @param int $propertyId
     * @param array $payload Datos del tour virtual a desactivar
     * @return array
     */
    public function deactivate(int $propertyId, array $payload): array
    {
        $response = $this->client->request('POST', "/v1/properties/{$propertyId}/virtualtours/deactivate", [
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

