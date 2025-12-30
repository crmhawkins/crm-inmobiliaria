<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use App\Services\Fotocasa\FotocasaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FetchFotocasaProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:fetch-properties
                            {--test : Probar diferentes endpoints de la API}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Obtiene propiedades existentes desde Fotocasa API y las carga en el CRM';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $apiKey = env('API_KEY');

        if (!$apiKey) {
            $this->error('⚠️  API_KEY no configurada en el archivo .env');
            return self::FAILURE;
        }

        $this->info('🔍 Buscando propiedades en Fotocasa...');
        $this->newLine();

        $fotocasaClient = new FotocasaClient($apiKey);
        $baseUrl = $fotocasaClient->getBaseUrl();

        // Endpoints posibles para obtener propiedades
        $endpoints = [
            '/properties',
            '/property',
            '/property/list',
            '/properties/list',
            '/property/all',
            '/properties/all',
        ];

        if ($this->option('test')) {
            $this->info('🧪 Probando diferentes endpoints...');
            $this->newLine();

            foreach ($endpoints as $endpoint) {
                $this->line("Probando: {$baseUrl}{$endpoint}");
                try {
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json',
                        'Api-Key' => $apiKey,
                    ])->withOptions([
                        'verify' => false,
                        'timeout' => 10,
                    ])->get($baseUrl . $endpoint);

                    $status = $response->status();
                    $this->line("  Status: {$status}");

                    if ($response->successful()) {
                        $data = $response->json();
                        $this->info("  ✅ Éxito! Respuesta recibida");

                        // Si es un array de objetos con ExternalId, es la lista de propiedades
                        if (is_array($data) && !empty($data) && isset($data[0]['ExternalId'])) {
                            $this->info("  📋 Se encontraron " . count($data) . " propiedades");
                            $this->newLine();
                            $this->processPropertiesList($data, $fotocasaClient);
                            return self::SUCCESS;
                        } elseif (isset($data['properties']) || isset($data['data'])) {
                            $this->processProperties($data);
                            return self::SUCCESS;
                        }
                    } else {
                        $this->warn("  ⚠️  Error: " . $response->body());
                    }
                } catch (\Exception $e) {
                    $this->error("  ❌ Excepción: " . $e->getMessage());
                }
                $this->newLine();
            }

            $this->warn('⚠️  No se encontró ningún endpoint GET funcional en la API de Fotocasa.');
            $this->info('ℹ️  La API de Fotocasa parece ser solo para envío de datos (POST), no para obtener propiedades existentes.');
            $this->newLine();
            $this->info('💡 Sugerencias:');
            $this->line('   1. Contacta con el soporte de Fotocasa para verificar si hay un endpoint para obtener propiedades');
            $this->line('   2. Verifica si hay un panel de administración de Fotocasa donde puedas exportar las propiedades');
            $this->line('   3. Si las propiedades fueron creadas con ExternalId, puedes usar ese ID para rastrearlas');

            return self::FAILURE;
        }

        // Intentar el endpoint que funciona
        $this->info("Intentando obtener propiedades desde: {$baseUrl}/property");
        $this->newLine();

        $response = [];
        $triedParams = [];

        // Probar diferentes combinaciones de parámetros para incluir anuncios no publicados
        $paramCombinations = [
            ['includeUnpublished' => true, 'includeInactive' => true],
            ['includeUnpublished' => 'true', 'includeInactive' => 'true'],
            ['state' => 'all'],
            ['status' => 'all'],
            ['includeUnpublished' => true],
            ['includeUnpublished' => 'true'],
            ['includeInactive' => true],
            ['includeInactive' => 'true'],
            ['published' => false],
            ['published' => 'false'],
            ['all' => true],
            ['all' => 'true'],
            ['showAll' => true],
            ['showAll' => 'true'],
            [], // Sin parámetros
        ];

        foreach ($paramCombinations as $params) {
            $paramStr = empty($params) ? 'sin parámetros' : json_encode($params);
            $this->line("  Probando con: {$paramStr}");

            try {
                $testResponse = $fotocasaClient->getProperties($params);
                $triedParams[] = $paramStr;

                if (!empty($testResponse) && is_array($testResponse)) {
                    $this->info("  ✅ Encontradas " . count($testResponse) . " propiedades con: {$paramStr}");
                    $response = $testResponse;
                    break;
                } else {
                    $this->line("  ⚠️  Sin resultados con: {$paramStr}");
                }
            } catch (\Exception $e) {
                $this->line("  ❌ Error con {$paramStr}: " . $e->getMessage());
            }
        }

        $this->newLine();

        // La respuesta ya es un array del servicio
        if (is_array($response) && !empty($response)) {
            if (isset($response[0]['ExternalId'])) {
                $this->info('✅ Propiedades obtenidas correctamente: ' . count($response) . ' propiedades encontradas');
                $this->newLine();
                $this->processPropertiesList($response, $fotocasaClient);
            } else {
                $this->info('✅ Propiedades obtenidas correctamente');
                $this->processProperties($response);
            }
        } else {
            $this->warn('⚠️  No se encontraron propiedades en Fotocasa');
            $this->line('Parámetros probados: ' . implode(', ', $triedParams));
            $this->newLine();
            $this->info('💡 Ejecuta con --test para probar diferentes endpoints');
        }

        return self::SUCCESS;
    }

    /**
     * Procesa las propiedades obtenidas de Fotocasa
     */
    private function processProperties(array $data): void
    {
        $properties = $data['properties'] ?? $data['data'] ?? [];

        if (empty($properties)) {
            $this->warn('No se encontraron propiedades en la respuesta');
            return;
        }

        $this->info("📊 Procesando " . count($properties) . " propiedades...");
        $this->newLine();

        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        foreach ($properties as $property) {
            $externalId = $property['ExternalId'] ?? null;

            if (!$externalId) {
                $this->warn("  ⚠️  Propiedad sin ExternalId, omitiendo...");
                $summary['skipped']++;
                continue;
            }

            // Buscar si ya existe en el CRM por external_id
            $inmueble = Inmuebles::where('external_id', $externalId)->first();

            if ($inmueble) {
                // Actualizar propiedad existente
                $this->updatePropertyFromFotocasa($inmueble, $property);
                $summary['updated']++;
                $this->info("  ✅ Actualizada: ID {$inmueble->id} (ExternalId: {$externalId})");
            } else {
                // Crear nueva propiedad
                $inmueble = $this->createPropertyFromFotocasa($property);
                if ($inmueble) {
                    $summary['created']++;
                    $this->info("  ✅ Creada: ID {$inmueble->id} (ExternalId: {$externalId})");
                } else {
                    $summary['skipped']++;
                    $this->warn("  ⚠️  No se pudo crear propiedad con ExternalId: {$externalId}");
                }
            }
        }

        $this->newLine();
        $this->info('📊 Resumen:');
        $this->line("   ✅ Creadas: {$summary['created']}");
        $this->line("   🔄 Actualizadas: {$summary['updated']}");
        $this->line("   ⏭️  Omitidas: {$summary['skipped']}");
    }

    /**
     * Crea una nueva propiedad desde los datos de Fotocasa
     */
    private function createPropertyFromFotocasa(array $property): ?Inmuebles
    {
        try {
            // Mapear datos de Fotocasa al formato del CRM
            $data = $this->mapFotocasaToInmueble($property);

            return Inmuebles::create($data);
        } catch (\Exception $e) {
            Log::error('Error creando propiedad desde Fotocasa', [
                'property' => $property,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Actualiza una propiedad existente con datos de Fotocasa
     */
    private function updatePropertyFromFotocasa(Inmuebles $inmueble, array $property): void
    {
        try {
            $data = $this->mapFotocasaToInmueble($property);
            $inmueble->update($data);
        } catch (\Exception $e) {
            Log::error('Error actualizando propiedad desde Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'property' => $property,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Mapea datos de Fotocasa al formato del modelo Inmuebles
     */
    private function mapFotocasaToInmueble(array $property): array
    {
        $data = [
            'external_id' => $property['ExternalId'] ?? null,
        ];

        // Mapear características básicas
        if (isset($property['PropertyFeature'])) {
            foreach ($property['PropertyFeature'] as $feature) {
                $featureId = $feature['FeatureId'] ?? null;
                $value = $feature['DecimalValue'] ?? $feature['TextValue'] ?? $feature['BoolValue'] ?? null;

                switch ($featureId) {
                    case 1: // Surface
                        $data['m2'] = $value;
                        break;
                    case 2: // Title
                        $data['titulo'] = $value;
                        break;
                    case 3: // Description
                        $data['descripcion'] = $value;
                        break;
                    case 11: // Rooms
                        $data['habitaciones'] = $value;
                        break;
                    case 12: // Bathrooms
                        $data['banos'] = $value;
                        break;
                }
            }
        }

        // Mapear dirección
        if (isset($property['PropertyAddress'][0])) {
            $address = $property['PropertyAddress'][0];
            $data['cod_postal'] = $address['ZipCode'] ?? null;
            $data['ubicacion'] = ($address['Street'] ?? '') . ' ' . ($address['Number'] ?? '');
            $data['latitude'] = $address['y'] ?? null;
            $data['longitude'] = $address['x'] ?? null;
            $data['floor_id'] = $address['FloorId'] ?? null;
            $data['visibility_mode_id'] = $address['VisibilityModeId'] ?? null;
        }

        // Mapear transacción
        if (isset($property['PropertyTransaction'][0])) {
            $transaction = $property['PropertyTransaction'][0];
            $data['transaction_type_id'] = $transaction['TransactionTypeId'] ?? null;
            $data['valor_referencia'] = $transaction['Price'] ?? null;
        }

        // Mapear tipo de vivienda
        $data['tipo_vivienda_id'] = $property['TypeId'] ?? null;
        $data['building_subtype_id'] = $property['SubTypeId'] ?? null;

        return $data;
    }

    /**
     * Procesa la lista de propiedades obtenida (solo ExternalId y AgencyReference)
     * El ExternalId debería corresponder al ID del inmueble en el CRM
     */
    private function processPropertiesList(array $propertiesList, FotocasaClient $fotocasaClient): void
    {
        $this->info("📊 Procesando " . count($propertiesList) . " propiedades desde Fotocasa...");
        $this->newLine();

        $summary = [
            'linked' => 0,
            'created' => 0,
            'skipped' => 0,
            'failed' => 0,
        ];

        foreach ($propertiesList as $propertyRef) {
            $externalId = $propertyRef['ExternalId'] ?? null;

            if (!$externalId) {
                $summary['skipped']++;
                continue;
            }

            $this->line("Procesando ExternalId: {$externalId}");

            try {
                // El ExternalId debería ser el ID del inmueble en el CRM
                // Primero intentar buscar por ID directo
                $inmueble = Inmuebles::find($externalId);

                // Si no existe por ID, buscar por external_id
                if (!$inmueble) {
                    $inmueble = Inmuebles::where('external_id', $externalId)->first();
                }

                // Si no existe, buscar si hay una propiedad de Idealista que pueda coincidir
                if (!$inmueble) {
                    // Buscar propiedades de Idealista que puedan coincidir
                    // (por ejemplo, por código o referencia similar)
                    $inmueble = Inmuebles::whereNotNull('idealista_property_id')
                        ->where('idealista_code', $externalId)
                        ->orWhere('external_id', $externalId)
                        ->first();
                }

                if ($inmueble) {
                    // Propiedad encontrada - intentar obtener detalles completos de Fotocasa y actualizar
                    $this->line("  🔄 Intentando obtener detalles completos desde Fotocasa...");
                    $fullPropertyData = $this->fetchFullPropertyDetails($fotocasaClient, $externalId);

                    if ($fullPropertyData && !empty($fullPropertyData)) {
                        $this->updatePropertyFromFotocasa($inmueble, $fullPropertyData);
                        $this->info("  ✅ Actualizada con datos completos de Fotocasa");
                    } else {
                        // Si no se pueden obtener detalles, al menos vincular
                        $this->line("  ℹ️  No se pudieron obtener detalles completos, solo vinculando");
                    }

                    // Actualizar external_id si no está configurado
                    if (!$inmueble->external_id || $inmueble->external_id !== $externalId) {
                        $inmueble->external_id = $externalId;
                        $inmueble->save();
                    }
                    $summary['linked']++;
                    $this->info("  ✅ Vinculada con propiedad existente ID {$inmueble->id}");
                } else {
                    // No existe en el CRM - intentar obtener detalles completos desde Fotocasa
                    $this->line("  🔍 Propiedad no existe en CRM, obteniendo detalles completos desde Fotocasa...");
                    $fullPropertyData = $this->fetchFullPropertyDetails($fotocasaClient, $externalId);

                    if ($fullPropertyData && !empty($fullPropertyData)) {
                        // Usar los datos completos obtenidos
                        $this->line("  📝 Creando propiedad con datos completos de Fotocasa...");
                        try {
                            $inmueble = $this->createPropertyFromFotocasa($fullPropertyData);
                            if ($inmueble) {
                                $summary['created']++;
                                $this->info("  ✅ Creada: ID {$inmueble->id} (ExternalId: {$externalId}) con datos completos");
                            } else {
                                $summary['skipped']++;
                                $this->warn("  ⚠️  No se pudo crear propiedad con ExternalId: {$externalId}");
                            }
                        } catch (\Exception $e) {
                            $summary['failed']++;
                            $this->error("  ❌ Error creando propiedad: " . $e->getMessage());
                            Log::error('Error creando propiedad desde Fotocasa', [
                                'external_id' => $externalId,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    } else {
                        // Si no se pueden obtener detalles completos, usar los datos de la lista si están disponibles
                        if (!empty($propertyRef) && count($propertyRef) > 2) {
                            $this->line("  📝 Intentando crear con datos de la lista...");
                            try {
                                $inmueble = $this->createPropertyFromFotocasa($propertyRef);
                                if ($inmueble) {
                                    $summary['created']++;
                                    $this->info("  ✅ Creada: ID {$inmueble->id} (ExternalId: {$externalId})");
                                } else {
                                    $summary['skipped']++;
                                    $this->warn("  ⚠️  No se pudo crear propiedad con ExternalId: {$externalId} (datos insuficientes)");
                                }
                            } catch (\Exception $e) {
                                $summary['failed']++;
                                $this->error("  ❌ Error: " . $e->getMessage());
                            }
                        } else {
                            $summary['skipped']++;
                            $this->warn("  ⚠️  No se pudieron obtener datos completos. ExternalId: {$externalId}");
                            Log::info('Propiedad de Fotocasa sin datos completos', [
                                'external_id' => $externalId,
                                'property_data' => $propertyRef,
                            ]);
                        }
                    }
                }

            } catch (\Exception $e) {
                $summary['failed']++;
                $this->error("  ❌ Error: " . $e->getMessage());
                Log::error('Error procesando propiedad de Fotocasa', [
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->newLine();
        $this->info('📊 Resumen:');
        $this->line("   🔗 Vinculadas con propiedades existentes: {$summary['linked']}");
        $this->line("   ➕ Creadas nuevas propiedades: {$summary['created']}");
        $this->line("   ⏭️  Omitidas (solo en Fotocasa, sin datos): {$summary['skipped']}");
        $this->line("   ❌ Fallidas: {$summary['failed']}");
        $this->newLine();
        $this->info('ℹ️  Las propiedades vinculadas mantienen todos sus datos (imágenes, características, etc.) del CRM.');
    }

    /**
     * Intenta obtener los detalles completos de una propiedad desde Fotocasa
     * Prueba diferentes endpoints hasta encontrar uno que funcione
     */
    private function fetchFullPropertyDetails(FotocasaClient $fotocasaClient, string $externalId): ?array
    {
        $endpoints = [
            "/property/{$externalId}",
            "/property?ExternalId={$externalId}",
            "/property/{$externalId}/details",
            "/property/details/{$externalId}",
            "/properties/{$externalId}",
        ];

        foreach ($endpoints as $endpoint) {
            try {
                $data = $fotocasaClient->get($endpoint);

                // Verificar si la respuesta tiene datos útiles
                if (!empty($data) && is_array($data)) {
                    // Si tiene campos más allá de ExternalId y AgencyReference, considerarlo válido
                    $keys = array_keys($data);
                    $hasMoreData = count($keys) > 2 ||
                                   isset($data['PropertyFeature']) ||
                                   isset($data['PropertyAddress']) ||
                                   isset($data['PropertyTransaction']) ||
                                   isset($data['Description']) ||
                                   isset($data['Title']) ||
                                   isset($data['TypeId']);

                    if ($hasMoreData) {
                        Log::info('Detalles completos obtenidos de Fotocasa', [
                            'endpoint' => $endpoint,
                            'external_id' => $externalId,
                            'data_keys' => $keys,
                        ]);
                        return $data;
                    }
                }
            } catch (\Exception $e) {
                // Continuar probando otros endpoints
                Log::debug('Error obteniendo detalles de Fotocasa', [
                    'endpoint' => $endpoint,
                    'external_id' => $externalId,
                    'error' => $e->getMessage(),
                ]);
                continue;
            }
        }

        return null;
    }
}
