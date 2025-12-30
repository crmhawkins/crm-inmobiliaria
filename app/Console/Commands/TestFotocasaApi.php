<?php

namespace App\Console\Commands;

use App\Services\Fotocasa\FotocasaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestFotocasaApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:test-api
                            {--external-id= : ExternalId específico para probar}
                            {--list : Solo listar propiedades sin detalles}
                            {--full : Ejecutar todos los tests exhaustivos para obtener datos completos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prueba la API de Fotocasa para ver qué datos devuelve realmente';

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

        $fotocasaClient = new FotocasaClient($apiKey);
        $baseUrl = $fotocasaClient->getBaseUrl();
        $externalId = $this->option('external-id');
        $listOnly = $this->option('list');

        $this->info('🧪 Probando API de Fotocasa...');
        $this->newLine();

        $fullTest = $this->option('full');

        if ($fullTest) {
            // Ejecutar todos los tests exhaustivos
            $this->runFullTestSuite($fotocasaClient, $baseUrl, $apiKey, $externalId);
        } else {
            // Test 1: Listar todas las propiedades
            $this->info('📋 Test 1: Listar todas las propiedades (GET /api/property)');
            $this->testListProperties($fotocasaClient, $baseUrl, $apiKey);
            $this->newLine();

            if ($externalId) {
                // Test 2: Obtener detalles de una propiedad específica
                $this->info("📋 Test 2: Obtener detalles de propiedad ExternalId: {$externalId}");
                $this->testGetPropertyDetails($fotocasaClient, $baseUrl, $apiKey, $externalId);
                $this->newLine();
            }

            if (!$listOnly) {
                // Test 3: Probar diferentes endpoints para obtener detalles
                $this->info('📋 Test 3: Probar diferentes endpoints para obtener detalles');
                $this->testDetailEndpoints($fotocasaClient, $baseUrl, $apiKey);
            }
        }

        return self::SUCCESS;
    }

    private function testListProperties(FotocasaClient $fotocasaClient, string $baseUrl, string $apiKey): void
    {
        try {
            $data = $fotocasaClient->getProperties();

            $this->info("  ✅ Éxito!");
            $this->line("  Tipo de respuesta: " . gettype($data));

            if (is_array($data)) {
                $this->line("  Total de propiedades: " . count($data));

                if (!empty($data)) {
                    $this->line("  Estructura del primer elemento:");
                    $first = $data[0];
                    $this->displayDataStructure($first, 2);

                    // Mostrar algunos ExternalIds como ejemplo
                    $this->line("  Primeros 5 ExternalIds:");
                    $count = 0;
                    foreach ($data as $item) {
                        if ($count >= 5) break;
                        $extId = $item['ExternalId'] ?? 'N/A';
                        $this->line("    - {$extId}");
                        $count++;
                    }
                }
            } else {
                $this->line("  Contenido: " . json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Error: " . $e->getMessage());
        }
    }

    private function testGetPropertyDetails(FotocasaClient $fotocasaClient, string $baseUrl, string $apiKey, string $externalId): void
    {
        // Probar diferentes endpoints GET
        $getEndpoints = [
            "/property/{$externalId}",
            "/property?ExternalId={$externalId}",
            "/property/{$externalId}/details",
            "/property/details/{$externalId}",
            "/properties/{$externalId}",
            "/property/by-id/{$externalId}",
            "/property/id/{$externalId}",
            "/property/detail/{$externalId}",
            "/property/full/{$externalId}",
            "/properties/detail/{$externalId}",
            "/v1/property/{$externalId}",
            "/v2/property/{$externalId}",
            "/api/property/{$externalId}",
        ];

        $this->info("  🔍 Probando endpoints GET:");
        foreach ($getEndpoints as $endpoint) {
            $this->line("    Probando GET: {$endpoint}");
            try {
                $data = $fotocasaClient->get($endpoint);

                // Verificar si es un array de un solo elemento o un objeto
                if (is_array($data) && isset($data[0]) && count($data) == 1) {
                    $data = $data[0]; // Tomar el primer elemento si es un array de 1
                }

                $hasFullData = $this->hasCompletePropertyData($data);

                if ($hasFullData) {
                    $this->info("    ✅✅ DATOS COMPLETOS ENCONTRADOS EN: {$endpoint}!");
                    $this->displayDataStructure($data, 6);
                    $this->displayCompletePropertyInfo($data);

                    Log::info("Fotocasa API - Detalles completos encontrados", [
                        'endpoint' => $endpoint,
                        'method' => 'GET',
                        'external_id' => $externalId,
                        'data' => $data,
                    ]);
                    return; // Encontramos datos completos, salir
                } else {
                    $this->line("      ⚠️  Solo datos básicos");
                }
            } catch (\Exception $e) {
                $this->line("      ❌ " . substr($e->getMessage(), 0, 80));
            }
        }

        $this->newLine();
        $this->info("  🔍 Probando POST con ExternalId en body:");

        // Probar POST con ExternalId en el body
        $postPayloads = [
            ['ExternalId' => $externalId],
            ['externalId' => $externalId],
            ['id' => $externalId],
            ['propertyId' => $externalId],
            ['query' => ['ExternalId' => $externalId]],
        ];

        $postEndpoints = [
            "/property/detail",
            "/property/get",
            "/property/fetch",
            "/property/retrieve",
            "/properties/detail",
            "/property/by-external-id",
        ];

        foreach ($postEndpoints as $endpoint) {
            foreach ($postPayloads as $payload) {
                $this->line("    Probando POST: {$endpoint} con payload: " . json_encode($payload));
                try {
                    $data = $fotocasaClient->post($endpoint, $payload);

                    if (is_array($data) && isset($data[0]) && count($data) == 1) {
                        $data = $data[0];
                    }

                    $hasFullData = $this->hasCompletePropertyData($data);

                    if ($hasFullData) {
                        $this->info("    ✅✅ DATOS COMPLETOS ENCONTRADOS EN: {$endpoint} (POST)!");
                        $this->displayDataStructure($data, 6);
                        $this->displayCompletePropertyInfo($data);

                        Log::info("Fotocasa API - Detalles completos encontrados", [
                            'endpoint' => $endpoint,
                            'method' => 'POST',
                            'payload' => $payload,
                            'external_id' => $externalId,
                            'data' => $data,
                        ]);
                        return; // Encontramos datos completos, salir
                    } else {
                        $this->line("      ⚠️  Solo datos básicos");
                    }
                } catch (\Exception $e) {
                    $this->line("      ❌ " . substr($e->getMessage(), 0, 80));
                }
            }
        }

        $this->newLine();
        $this->warn("  ⚠️  No se encontró ningún endpoint que devuelva datos completos");
    }

    private function testDetailEndpoints(FotocasaClient $fotocasaClient, string $baseUrl, string $apiKey): void
    {
        // Obtener un ExternalId de ejemplo
        try {
            $data = $fotocasaClient->getProperties();

            if (is_array($data) && !empty($data) && isset($data[0]['ExternalId'])) {
                $exampleId = $data[0]['ExternalId'];
                $this->line("  Usando ExternalId de ejemplo: {$exampleId}");
                $this->testGetPropertyDetails($fotocasaClient, $baseUrl, $apiKey, $exampleId);
            }
        } catch (\Exception $e) {
            $this->warn("  No se pudo obtener un ExternalId de ejemplo: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta una suite completa de tests exhaustivos
     */
    private function runFullTestSuite(FotocasaClient $fotocasaClient, string $baseUrl, string $apiKey, ?string $externalId): void
    {
        $this->info('🔬 EJECUTANDO SUITE COMPLETA DE TESTS EXHAUSTIVOS');
        $this->newLine();

        // Test 1: Listar propiedades con diferentes parámetros
        $this->info('📋 Test 1: Listar propiedades con diferentes parámetros (incluyendo no publicadas)');
        $this->testListPropertiesWithParams($fotocasaClient);
        $this->newLine();

        // Test 2: Obtener detalles completos de propiedades
        $this->info('📋 Test 2: Intentar obtener detalles completos de propiedades');
        $this->testGetCompletePropertyDetails($fotocasaClient, $externalId);
        $this->newLine();

        // Test 3: Probar diferentes métodos HTTP
        $this->info('📋 Test 3: Probar diferentes métodos y endpoints');
        $this->testAlternativeEndpoints($fotocasaClient, $baseUrl, $apiKey);
    }

    /**
     * Prueba listar propiedades con diferentes parámetros
     */
    private function testListPropertiesWithParams(FotocasaClient $fotocasaClient): void
    {
        $paramCombinations = [
            ['params' => [], 'description' => 'Sin parámetros'],
            ['params' => ['includeUnpublished' => true], 'description' => 'includeUnpublished=true'],
            ['params' => ['includeUnpublished' => 'true'], 'description' => 'includeUnpublished="true"'],
            ['params' => ['includeInactive' => true], 'description' => 'includeInactive=true'],
            ['params' => ['state' => 'all'], 'description' => 'state=all'],
            ['params' => ['status' => 'all'], 'description' => 'status=all'],
            ['params' => ['all' => true], 'description' => 'all=true'],
            ['params' => ['showAll' => true], 'description' => 'showAll=true'],
            ['params' => ['includeUnpublished' => true, 'includeInactive' => true], 'description' => 'includeUnpublished + includeInactive'],
        ];

        $foundProperties = false;

        foreach ($paramCombinations as $combination) {
            $params = $combination['params'];
            $description = $combination['description'];
            $this->line("  Probando: {$description}");
            try {
                $data = $fotocasaClient->getProperties($params);

                if (is_array($data) && !empty($data)) {
                    $this->info("    ✅ Encontradas " . count($data) . " propiedades");
                    $foundProperties = true;

                    // Verificar si tiene datos completos
                    $first = $data[0];
                    $hasCompleteData = $this->hasCompletePropertyData($first);

                    if ($hasCompleteData) {
                        $this->info("    ✅✅ PRIMERA PROPIEDAD TIENE DATOS COMPLETOS!");
                        $this->displayCompletePropertyInfo($first);
                        break; // Encontramos datos completos, no necesitamos seguir
                    } else {
                        $this->warn("    ⚠️  Solo tiene datos básicos");
                        // Mostrar ExternalIds disponibles
                        $this->line("    ExternalIds encontrados:");
                        $count = 0;
                        foreach ($data as $item) {
                            if ($count >= 5) break;
                            $extId = $item['ExternalId'] ?? 'N/A';
                            $this->line("      - {$extId}");
                            $count++;
                        }
                    }
                } else {
                    $this->line("    ⚠️  Sin resultados");
                }
            } catch (\Exception $e) {
                $this->error("    ❌ Error: " . $e->getMessage());
            }
            $this->newLine();
        }

        if (!$foundProperties) {
            $this->warn("  ⚠️  No se encontraron propiedades con ningún parámetro");
        }
    }

    /**
     * Intenta obtener detalles completos de propiedades
     */
    private function testGetCompletePropertyDetails(FotocasaClient $fotocasaClient, ?string $externalId): void
    {
        // Primero obtener lista de propiedades
        try {
            $properties = $fotocasaClient->getProperties();

            if (!is_array($properties) || empty($properties)) {
                $this->warn("  ⚠️  No hay propiedades para probar");
                return;
            }

            // Si se proporcionó un externalId específico, usarlo
            if ($externalId) {
                $this->testGetPropertyDetails($fotocasaClient, $fotocasaClient->getBaseUrl(), env('API_KEY'), $externalId);
                return;
            }

            // Probar con los primeros 3 ExternalIds
            $this->line("  Probando con los primeros 3 ExternalIds encontrados:");
            $count = 0;
            foreach ($properties as $property) {
                if ($count >= 3) break;

                $extId = $property['ExternalId'] ?? null;
                if (!$extId) continue;

                $this->line("  Probando ExternalId: {$extId}");
                $this->testGetPropertyDetails($fotocasaClient, $fotocasaClient->getBaseUrl(), env('API_KEY'), $extId);
                $this->newLine();
                $count++;
            }
        } catch (\Exception $e) {
            $this->error("  ❌ Error: " . $e->getMessage());
        }
    }

    /**
     * Prueba endpoints alternativos
     */
    private function testAlternativeEndpoints(FotocasaClient $fotocasaClient, string $baseUrl, string $apiKey): void
    {
        $alternativeEndpoints = [
            '/properties',
            '/property/list',
            '/properties/list',
            '/property/all',
            '/properties/all',
            '/property/full',
            '/properties/full',
        ];

        foreach ($alternativeEndpoints as $endpoint) {
            $this->line("  Probando: {$endpoint}");
            try {
                $data = $fotocasaClient->get($endpoint);

                if (!empty($data) && is_array($data)) {
                    $this->info("    ✅ Éxito! " . count($data) . " elementos");
                    if (isset($data[0])) {
                        $hasComplete = $this->hasCompletePropertyData($data[0]);
                        $this->line("    " . ($hasComplete ? "✅✅ Datos completos" : "⚠️ Solo datos básicos"));
                    }
                } else {
                    $this->line("    ⚠️  Respuesta vacía o inválida");
                }
            } catch (\Exception $e) {
                $this->line("    ❌ Error: " . $e->getMessage());
            }
            $this->newLine();
        }
    }

    /**
     * Verifica si una propiedad tiene datos completos
     */
    private function hasCompletePropertyData(array $data): bool
    {
        // Si es un array indexado (lista), verificar el primer elemento
        if (isset($data[0]) && is_array($data[0])) {
            $data = $data[0];
        }

        // Verificar si tiene los campos importantes de datos completos
        // NO solo ExternalId y AgencyReference
        $hasComplete = isset($data['PropertyFeature']) ||
                       isset($data['PropertyAddress']) ||
                       isset($data['PropertyTransaction']) ||
                       isset($data['TypeId']) ||
                       isset($data['SubTypeId']) ||
                       (isset($data['Description']) && !empty($data['Description'])) ||
                       (isset($data['Title']) && !empty($data['Title']));

        // Si solo tiene ExternalId y AgencyReference, no es completo
        $keys = array_keys($data);
        $onlyBasic = count($keys) == 2 &&
                     in_array('ExternalId', $keys) &&
                     in_array('AgencyReference', $keys);

        return $hasComplete && !$onlyBasic;
    }

    /**
     * Muestra información completa de una propiedad
     */
    private function displayCompletePropertyInfo(array $data): void
    {
        $this->line("    📊 Información completa encontrada:");

        if (isset($data['PropertyFeature'])) {
            $this->line("      ✅ PropertyFeature: " . count($data['PropertyFeature']) . " características");
        }
        if (isset($data['PropertyAddress'])) {
            $this->line("      ✅ PropertyAddress: " . count($data['PropertyAddress']) . " direcciones");
        }
        if (isset($data['PropertyTransaction'])) {
            $this->line("      ✅ PropertyTransaction: " . count($data['PropertyTransaction']) . " transacciones");
        }
        if (isset($data['Description'])) {
            $desc = substr($data['Description'], 0, 50);
            $this->line("      ✅ Description: {$desc}...");
        }
        if (isset($data['Title'])) {
            $this->line("      ✅ Title: " . $data['Title']);
        }

        // Guardar JSON completo en log
        Log::info("Fotocasa API - Propiedad completa encontrada", [
            'data' => $data,
        ]);
    }

    private function displayDataStructure($data, int $indent = 0): void
    {
        $prefix = str_repeat(' ', $indent);

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (is_array($value) && !empty($value) && !isset($value[0])) {
                    // Es un objeto/array asociativo
                    $this->line("{$prefix}{$key}: [objeto con " . count($value) . " campos]");
                    $this->displayDataStructure($value, $indent + 2);
                } elseif (is_array($value) && isset($value[0])) {
                    // Es un array indexado
                    $this->line("{$prefix}{$key}: [array con " . count($value) . " elementos]");
                    if (count($value) > 0) {
                        $this->line("{$prefix}  Primer elemento:");
                        $this->displayDataStructure($value[0], $indent + 4);
                    }
                } else {
                    $displayValue = is_string($value) && strlen($value) > 100
                        ? substr($value, 0, 100) . '...'
                        : $value;
                    $this->line("{$prefix}{$key}: " . json_encode($displayValue, JSON_UNESCAPED_UNICODE));
                }
            }
        } else {
            $displayValue = is_string($data) && strlen($data) > 200
                ? substr($data, 0, 200) . '...'
                : $data;
            $this->line("{$prefix}" . json_encode($displayValue, JSON_UNESCAPED_UNICODE));
        }
    }
}
