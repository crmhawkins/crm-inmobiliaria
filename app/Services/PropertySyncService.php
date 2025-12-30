<?php

namespace App\Services;

use App\Models\Inmuebles;
use App\Services\Fotocasa\FotocasaClient;
use App\Services\Idealista\IdealistaPropertiesService;
use App\Services\Idealista\IdealistaPropertyCreator;
use Illuminate\Support\Facades\Log;

class PropertySyncService
{
    private FotocasaClient $fotocasaClient;
    private IdealistaPropertiesService $idealistaPropertiesService;
    private IdealistaPropertyCreator $idealistaPropertyCreator;

    public function __construct(
        FotocasaClient $fotocasaClient = null,
        IdealistaPropertiesService $idealistaPropertiesService = null,
        IdealistaPropertyCreator $idealistaPropertyCreator = null
    ) {
        $this->fotocasaClient = $fotocasaClient ?? app(FotocasaClient::class);
        $this->idealistaPropertiesService = $idealistaPropertiesService ?? app(IdealistaPropertiesService::class);
        $this->idealistaPropertyCreator = $idealistaPropertyCreator ?? app(IdealistaPropertyCreator::class);
    }

    /**
     * Sincroniza una propiedad con Fotocasa
     *
     * @param Inmuebles $inmueble
     * @param callable $payloadBuilder Función que construye el payload para Fotocasa
     * @return array ['success' => bool, 'response' => mixed, 'error' => string|null]
     */
    public function syncToFotocasa(Inmuebles $inmueble, callable $payloadBuilder): array
    {
        try {
            $payload = $payloadBuilder($inmueble);

            Log::info('Sincronizando propiedad con Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'external_id' => $inmueble->external_id,
            ]);

            $response = $this->fotocasaClient->createOrUpdateProperty($payload);

            // Actualizar external_id si viene en la respuesta
            if (isset($response['ExternalId']) && !$inmueble->external_id) {
                $inmueble->update(['external_id' => $response['ExternalId']]);
            }

            Log::info('Propiedad sincronizada con Fotocasa exitosamente', [
                'inmueble_id' => $inmueble->id,
                'external_id' => $inmueble->external_id,
            ]);

            return [
                'success' => true,
                'response' => $response,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error sincronizando propiedad con Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sincroniza una propiedad con Idealista
     *
     * @param Inmuebles $inmueble
     * @param bool $update Si es true, actualiza la propiedad existente; si es false, crea una nueva
     * @return array ['success' => bool, 'response' => mixed, 'error' => string|null]
     */
    public function syncToIdealista(Inmuebles $inmueble, bool $update = false): array
    {
        try {
            // Cargar el vendedor si existe para obtener el contactId
            if ($inmueble->vendedor_id) {
                $inmueble->load('vendedor');
            }

            // Convertir el inmueble al formato de Idealista
            $idealistaPayload = $this->idealistaPropertyCreator->toIdealistaFormat($inmueble);

            Log::info('Sincronizando propiedad con Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
                'update' => $update,
            ]);

            if ($update && $inmueble->idealista_property_id) {
                // Actualizar propiedad existente
                $response = $this->idealistaPropertiesService->update(
                    $inmueble->idealista_property_id,
                    $idealistaPayload
                );
            } else {
                // Crear nueva propiedad
                $response = $this->idealistaPropertiesService->create($idealistaPayload);
            }

            // Actualizar el inmueble con los datos de Idealista
            $updateData = [
                'idealista_property_id' => $response['propertyId'] ?? $inmueble->idealista_property_id,
                'idealista_code' => $response['code'] ?? $inmueble->idealista_code,
                'idealista_payload' => json_encode($response),
                'idealista_synced_at' => now(),
            ];

            $inmueble->update($updateData);

            // Subir imágenes a Idealista si hay
            $images = $this->idealistaPropertyCreator->prepareImages($inmueble);
            if (!empty($images) && $inmueble->idealista_property_id) {
                try {
                    $this->syncImagesToIdealista($inmueble, $images);
                } catch (\Exception $e) {
                    Log::warning('Error sincronizando imágenes con Idealista', [
                        'inmueble_id' => $inmueble->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Propiedad sincronizada con Idealista exitosamente', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return [
                'success' => true,
                'response' => $response,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error sincronizando propiedad con Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Sincroniza una propiedad con ambas plataformas (Idealista y Fotocasa)
     *
     * @param Inmuebles $inmueble
     * @param callable $fotocasaPayloadBuilder Función que construye el payload para Fotocasa
     * @param bool $updateIdealista Si es true, actualiza en Idealista; si es false, crea nueva
     * @return array ['idealista' => array, 'fotocasa' => array]
     */
    public function syncToBoth(
        Inmuebles $inmueble,
        callable $fotocasaPayloadBuilder,
        bool $updateIdealista = false
    ): array {
        $results = [
            'idealista' => null,
            'fotocasa' => null,
        ];

        // Sincronizar con Idealista (no bloquea si falla)
        try {
            $results['idealista'] = $this->syncToIdealista($inmueble, $updateIdealista);
        } catch (\Exception $e) {
            Log::error('Error crítico sincronizando con Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
            ]);
            $results['idealista'] = [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }

        // Sincronizar con Fotocasa (no bloquea si falla)
        try {
            $results['fotocasa'] = $this->syncToFotocasa($inmueble, $fotocasaPayloadBuilder);
        } catch (\Exception $e) {
            Log::error('Error crítico sincronizando con Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
            ]);
            $results['fotocasa'] = [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }

        return $results;
    }

    /**
     * Sincroniza imágenes con Idealista
     *
     * @param Inmuebles $inmueble
     * @param array $images
     * @return void
     */
    private function syncImagesToIdealista(Inmuebles $inmueble, array $images): void
    {
        if (empty($images) || !$inmueble->idealista_property_id) {
            return;
        }

        try {
            $imagesService = app(\App\Services\Idealista\IdealistaPropertiesService::class);
            $imagesService->replaceImages($inmueble->idealista_property_id, $images);
        } catch (\Exception $e) {
            Log::warning('Error reemplazando imágenes en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Verifica si una propiedad está sincronizada con alguna plataforma
     *
     * @param Inmuebles $inmueble
     * @return array ['idealista' => bool, 'fotocasa' => bool]
     */
    public function getSyncStatus(Inmuebles $inmueble): array
    {
        return [
            'idealista' => !empty($inmueble->idealista_property_id),
            'fotocasa' => !empty($inmueble->external_id),
        ];
    }

    /**
     * Desactiva una propiedad en Idealista
     *
     * @param Inmuebles $inmueble
     * @return array ['success' => bool, 'response' => mixed, 'error' => string|null]
     */
    public function deactivateInIdealista(Inmuebles $inmueble): array
    {
        if (!$inmueble->idealista_property_id) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'La propiedad no está sincronizada con Idealista',
            ];
        }

        try {
            $response = $this->idealistaPropertiesService->deactivate($inmueble->idealista_property_id);

            Log::info('Propiedad desactivada en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return [
                'success' => true,
                'response' => $response,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error desactivando propiedad en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Elimina una propiedad de Fotocasa
     *
     * @param Inmuebles $inmueble
     * @return array ['success' => bool, 'response' => mixed, 'error' => string|null]
     */
    public function deleteFromFotocasa(Inmuebles $inmueble): array
    {
        if (!$inmueble->external_id) {
            return [
                'success' => false,
                'response' => null,
                'error' => 'La propiedad no tiene external_id de Fotocasa',
            ];
        }

        try {
            $response = $this->fotocasaClient->deleteProperty($inmueble->external_id);

            Log::info('Propiedad eliminada de Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'external_id' => $inmueble->external_id,
            ]);

            // Limpiar el external_id del inmueble
            $inmueble->update(['external_id' => null]);

            return [
                'success' => true,
                'response' => $response,
                'error' => null,
            ];
        } catch (\Exception $e) {
            Log::error('Error eliminando propiedad de Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'external_id' => $inmueble->external_id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
