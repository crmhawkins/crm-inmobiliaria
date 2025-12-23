<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class IdealistaPropertyTests extends Command
{
    protected $lastCreatedPropertyId = null;
    protected $contactId = 103004164; // Contact ID fijo (debe ser integer, no string)

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:test-properties
        {--output= : Ruta del archivo de salida (por defecto: property-test-results-{timestamp}.csv)}
        {--test= : Ejecutar solo un test específico por ID (ej: Property03)}
        {--from= : Ejecutar todos los tests desde un ID específico hasta el final (ej: Land01)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta todos los casos de prueba de propiedades de la API de Idealista';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Ejecutando tests de Propiedades de Idealista ===\n");

        try {
            // Obtener todos los tests de propiedades
            $testCases = $this->getPropertyTestCases();

            // Filtrar por test específico o desde un test específico
            $testFilter = $this->option('test');
            $fromFilter = $this->option('from');

            if ($testFilter) {
                $testCases = array_filter($testCases, function($test) use ($testFilter) {
                    return $test['id'] === $testFilter;
                });
                if (empty($testCases)) {
                    $this->error("No se encontró el test con ID: {$testFilter}");
                    return self::FAILURE;
                }
                $this->info("Ejecutando solo el test: {$testFilter}\n");
            } elseif ($fromFilter) {
                // Ejecutar desde un test específico hasta el final
                $foundStart = false;
                $filteredCases = [];
                foreach ($testCases as $test) {
                    if ($test['id'] === $fromFilter) {
                        $foundStart = true;
                    }
                    if ($foundStart) {
                        $filteredCases[] = $test;
                    }
                }
                if (empty($filteredCases)) {
                    $this->error("No se encontró el test con ID: {$fromFilter}");
                    return self::FAILURE;
                }
                $testCases = $filteredCases;
                $this->info("Ejecutando tests desde {$fromFilter} hasta el final (" . count($testCases) . " tests)\n");
            }

            if (empty($testCases)) {
                $this->error("No se encontraron casos de prueba");
                return self::FAILURE;
            }

            $this->info("Se encontraron " . count($testCases) . " casos de prueba\n");

            // Crear un contacto primero si no existe (excepto para tests de auth)
            // Solo crear contacto si hay tests que lo necesiten
            $needsContact = true;
            if ($testFilter && in_array($testFilter, ['Property01', 'Property02', 'Property11', 'Property15', 'Property17', 'Property19'])) {
                $needsContact = false;
            }

            if ($needsContact) {
                $this->info("Creando contacto en Idealista...");
                $createdContact = $this->createContactIfNeeded();
                if ($createdContact) {
                    $this->contactId = $createdContact;
                    $this->info("✓ Contacto creado/obtenido con ID: {$this->contactId}\n");
                } else {
                    $this->warn("⚠ No se pudo crear/obtener contacto, intentando usar uno existente...\n");
                    // Intentar obtener el primer contacto disponible
                    $existingContact = $this->getExistingContact();
                    if ($existingContact) {
                        $this->contactId = $existingContact;
                        $this->info("✓ Usando contacto existente con ID: {$this->contactId}\n");
                    } else {
                        $this->error("✗ No se pudo obtener ningún contacto. Los tests fallarán.\n");
                    }
                }
            }

            // Preparar datos para el archivo de resultados
            $results = [];
            $successCount = 0;
            $failureCount = 0;

            // Ejecutar cada caso de prueba
            foreach ($testCases as $testCase) {
                $testId = $testCase['id'];
                $testName = $testCase['nombre'];
                $this->info("Ejecutando test {$testId}: {$testName}");

                // Reemplazar placeholders en endpoint
                if (isset($testCase['endpoint'])) {
                    if (strpos($testCase['endpoint'], '{propertyId}') !== false) {
                        if (isset($this->lastCreatedPropertyId)) {
                            $testCase['endpoint'] = str_replace('{propertyId}', $this->lastCreatedPropertyId, $testCase['endpoint']);
                        } else {
                            $this->warn("  ⚠️  Saltando test {$testId}: requiere un propertyId válido");
                            $results[] = $this->createResultRow($testCase, null, false, 'Requiere propertyId de test anterior');
                            continue;
                        }
                    }
                }

                // Asegurar que contactId esté presente en el body para tests de propiedades
                // IMPORTANTE: contactId debe ser integer, no string
                if (isset($testCase['body']) && is_array($testCase['body'])) {
                    // Si el endpoint es de propiedades (POST/PUT), asegurar que tenga contactId
                    if (isset($testCase['endpoint']) &&
                        (strpos($testCase['endpoint'], '/v1/properties') !== false) &&
                        in_array($testCase['metodo'] ?? '', ['POST', 'PUT'])) {
                        $testCase['body']['contactId'] = (int)$this->contactId; // Forzar integer
                    } elseif (isset($testCase['body']['contactId'])) {
                        // Para otros casos, actualizar si ya existe
                        $testCase['body']['contactId'] = (int)$this->contactId; // Forzar integer
                    }
                }

                // Mostrar JSON enviado en TODOS los tests (después de reemplazar placeholders)
                if (isset($testCase['body']) && $testCase['body'] !== null) {
                    $jsonSent = json_encode($testCase['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $this->line("  JSON enviado:");
                    // Mostrar solo las primeras líneas si es muy largo (máximo 20 líneas)
                    $jsonLines = explode("\n", $jsonSent);
                    if (count($jsonLines) > 20) {
                        $this->line("  " . implode("\n  ", array_slice($jsonLines, 0, 20)));
                        $this->line("  ... (JSON truncado, ver CSV completo para ver todo el contenido)");
                    } else {
                        $this->line("  " . implode("\n  ", $jsonLines));
                    }
                } else {
                    // Para GET y otros métodos sin body, mostrar query params si existen
                    if (!empty($testCase['query'] ?? [])) {
                        $this->line("  Query params: " . json_encode($testCase['query'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    } else {
                        $this->line("  JSON enviado: N/A (método " . ($testCase['metodo'] ?? 'GET') . " sin body)");
                    }
                }

                $startTime = microtime(true);
                $result = $this->executeTestCase($testCase);
                $endTime = microtime(true);
                $executionTime = round(($endTime - $startTime) * 1000, 2);

                // Si hay rate limit (429), reintentar el test hasta que funcione
                // Máximo 5 minutos de espera total (5 intentos de 1 minuto cada uno)
                $maxRetries = 5; // Máximo 5 reintentos
                $retryWaitTime = 60; // 1 minuto de espera entre reintentos
                $retryCount = 0;
                while ($result['status_code'] == 429 && $retryCount < $maxRetries) {
                    $retryCount++;
                    $this->warn("  ⚠️  Rate limit detectado (intento {$retryCount}/{$maxRetries}), esperando {$retryWaitTime} segundos...");
                    sleep($retryWaitTime);

                    // Reintentar el test
                    $startTime = microtime(true);
                    $result = $this->executeTestCase($testCase);
                    $endTime = microtime(true);
                    $executionTime = round(($endTime - $startTime) * 1000, 2);
                }

                if ($retryCount >= $maxRetries && $result['status_code'] == 429) {
                    $this->error("  ✗ Fallo después de {$maxRetries} reintentos (5 minutos totales) por rate limit");
                }

                // Guardar IDs creados para tests posteriores
                // IMPORTANTE: Solo actualizar lastCreatedPropertyId si el test crea una propiedad nueva
                // No actualizar para tests que usan propiedades existentes (GET, PUT, DELETE, etc.)
                if ($result['success'] && isset($result['response'])) {
                    if (isset($result['response']['propertyId'])) {
                        // Solo actualizar si es un POST (creación nueva) o si el test específicamente necesita actualizar el ID
                        $isCreateOperation = isset($testCase['metodo']) &&
                                            strtoupper($testCase['metodo']) === 'POST' &&
                                            isset($testCase['endpoint']) &&
                                            strpos($testCase['endpoint'], '/v1/properties') !== false &&
                                            strpos($testCase['endpoint'], '{propertyId}') === false;

                        if ($isCreateOperation) {
                            $this->lastCreatedPropertyId = $result['response']['propertyId'];
                            $this->info("  ✓ Propiedad creada con ID: {$this->lastCreatedPropertyId}");
                        }
                    }
                }

                // Crear fila de resultado
                $errorMsg = $result['error'] ?? '';
                if ($errorMsg && strlen($errorMsg) > 200) {
                    $errorMsg = substr($errorMsg, 0, 200) . '...';
                }

                // Un test es exitoso solo si el status code está en 200-299
                // Los tests de errores esperados (400, 401, 404, etc.) se muestran como fallo
                $testPassed = $result['success'];
                $actualStatus = $result['status_code'] ?? 0;
                $expectedStatus = $testCase['expected_status'] ?? 200;

                // Determinar si el error es esperado
                $isExpectedError = !$testPassed && $actualStatus == $expectedStatus && $expectedStatus >= 400;
                $isUnexpectedError = !$testPassed && !$isExpectedError;

                $results[] = $this->createResultRow($testCase, $result, $testPassed, $errorMsg);

                if ($testPassed) {
                    $successCount++;
                    // Verde para éxito
                    $this->line("  <fg=green>✓ Éxito</> (Status: <fg=green>{$actualStatus}</>)");
                } else {
                    $failureCount++;
                    $errorDisplay = $errorMsg ? " - {$errorMsg}" : '';

                    if ($isExpectedError) {
                        // Amarillo para error esperado (test de validación)
                        $this->line("  <fg=yellow>✗ Fallo Esperado</> (Status: <fg=yellow>{$actualStatus}</>{$errorDisplay})");
                    } else {
                        // Rojo para error inesperado
                        $this->line("  <fg=red>✗ Fallo Inesperado</> (Status: <fg=red>{$actualStatus}</>{$errorDisplay})");
                    }

                    // Mostrar respuesta completa para errores de validación
                    if ($result['status_code'] == 400 && isset($result['response'])) {
                        $this->line("  Detalles del error:");
                        $this->line("  " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    }
                }

                // Pausa entre requests (aumentada para evitar rate limiting)
                // Idealista permite 1000 requests por minuto = ~16.67 requests por segundo
                // Para ser seguros, esperamos 100ms entre requests (10 requests/segundo)
                usleep(100000); // 0.1 segundos (100ms)
            }

            // Guardar archivo de resultados
            $outputFile = $this->option('output')
                ?: storage_path('app/property-test-results-' . date('Y-m-d_H-i-s') . '.csv');

            $this->saveResultsToCsv($results, $outputFile);

            // Calcular errores esperados vs inesperados
            $expectedErrors = 0;
            $unexpectedErrors = 0;
            foreach ($results as $result) {
                if (!$result['Actual Result'] || !in_array($result['Actual Result'], [200, 201])) {
                    $expectedStatus = $result['Expected result'] ?? 200;
                    $actualStatus = $result['Actual Result'] ?? 0;
                    if ($actualStatus == $expectedStatus && $expectedStatus >= 400) {
                        $expectedErrors++;
                    } else {
                        $unexpectedErrors++;
                    }
                }
            }

            $this->line("\n<fg=cyan>=== Resumen ===</>");
            $this->line("Total de tests: " . count($testCases));
            $this->line("<fg=green>Exitosos: {$successCount}</>");
            $this->line("<fg=yellow>Fallos Esperados (validaciones): {$expectedErrors}</>");
            $this->line("<fg=red>Fallos Inesperados: {$unexpectedErrors}</>");
            $this->line("Total Fallidos: {$failureCount}");
            $this->line("Resultados guardados en: {$outputFile}");

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Error ejecutando tests: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            Log::error('Error en IdealistaPropertyTests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Obtiene todos los tests de propiedades
     */
    private function getPropertyTestCases(): array
    {
        $tests = [];

        // Property01: New property - Auth error - invalid token - Expected: 401
        $tests[] = [
            'id' => 'Property01',
            'nombre' => 'New property - Auth error - invalid token',
            'description' => 'Send a new property with the basic required fields and with an invalid token in the Authorization header',
            'expected_status' => 401,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'sale'),
            'custom_headers' => ['Authorization' => 'Bearer invalid_token'],
        ];

        // Property02: New property - Error - invalid feedkey - Expected: 401
        $tests[] = [
            'id' => 'Property02',
            'nombre' => 'New property - Error - invalid feedkey',
            'description' => 'Send a new property with the basic required fields and with an invalid value in the feedKey header',
            'expected_status' => 401,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'sale'),
            'custom_headers' => ['feedKey' => 'invalid_feedkey'],
        ];

        // Property03: New property - Operation - sale - Expected: 201
        $tests[] = [
            'id' => 'Property03',
            'nombre' => 'New property - Operation - sale',
            'description' => 'Send a new property with operation type = \'sale\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'sale'),
        ];

        // Property04: New property - Operation - rent - Expected: 201
        $tests[] = [
            'id' => 'Property04',
            'nombre' => 'New property - Operation - rent',
            'description' => 'Send a new property with operation type = \'rent\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'rent'),
        ];

        // Property05: New property - Scope - idealista - Expected: 201
        $tests[] = [
            'id' => 'Property05',
            'nombre' => 'New property - Scope - idealista',
            'description' => 'Send a new property with scope = \'idealista\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => array_merge($this->getBasicPropertyPayload('flat', 'sale'), ['scope' => 'idealista']),
        ];

        // Property06: New property - Scope - microsite - Expected: 201
        $tests[] = [
            'id' => 'Property06',
            'nombre' => 'New property - Scope - microsite',
            'description' => 'Send a new property with scope = \'microsite\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => array_merge($this->getBasicPropertyPayload('flat', 'sale'), ['scope' => 'microsite']),
        ];

        // Property07: New property - Visibility - full - Expected: 201
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['address']['visibility'] = 'full';
        $tests[] = [
            'id' => 'Property07',
            'nombre' => 'New property - Visibility - full',
            'description' => 'Send a new property with address visibility = \'full\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Property08: New property - Visibility - street - Expected: 201
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['address']['visibility'] = 'street';
        $tests[] = [
            'id' => 'Property08',
            'nombre' => 'New property - Visibility - street',
            'description' => 'Send a new property with address visibility = \'street\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Property09: New property - Visibility - hidden - Expected: 201
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['address']['visibility'] = 'hidden';
        $tests[] = [
            'id' => 'Property09',
            'nombre' => 'New property - Visibility - hidden',
            'description' => 'Send a new property with address visibility = \'hidden\' and the rest of mandatory fields',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Flat01: New property - Type - flat - Expected: 201
        $tests[] = [
            'id' => 'Flat01',
            'nombre' => 'New property - Type - flat',
            'description' => 'Create new property with type = \'flat\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'sale'),
        ];

        // Flat02: New property - Type - flat - Basic validation error - area - Expected: 400
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['features']['areaConstructed'] = 5.0; // < 11 (debe fallar, mínimo es 11)
        $tests[] = [
            'id' => 'Flat02',
            'nombre' => 'New property - Type - flat - Basic validation error - area',
            'description' => 'Create new property with type = \'flat\'. Features: send at least the basic required fields and areaConstructed < 10',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Flat03: New property - Type - flat - Business validation error - area - Expected: 400
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['features']['areaUsable'] = 80.0;
        $payload['features']['areaConstructed'] = 75.0; // < areaUsable (debe ser mayor)
        $tests[] = [
            'id' => 'Flat03',
            'nombre' => 'New property - Type - flat - Business validation error - area',
            'description' => 'Create new property with type = \'flat\'. Features: send at least the basic required fields and areaConstructed < areaUsable',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Flat04: New property - Type - flat - Business validation error - conservation/bathroomNumber - Expected: 400
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['features']['conservation'] = 'good';
        $payload['features']['bathroomNumber'] = 0;
        $tests[] = [
            'id' => 'Flat04',
            'nombre' => 'New property - Type - flat - Business validation error - conservation/bathroomNumber',
            'description' => 'Create new property with type = \'flat\'. Features: send at least the basic required fields, conservation = \'good\' and bathroomNumber = 0',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Flat05: New property - Type - flat - Business validation error – parkingAvailable/parkingIncludedInPrice - Expected: 400
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['features']['parkingAvailable'] = false;
        $payload['features']['parkingIncludedInPrice'] = true;
        $tests[] = [
            'id' => 'Flat05',
            'nombre' => 'New property - Type - flat - Business validation error – parkingAvailable/parkingIncludedInPrice',
            'description' => 'Create new property with type = \'flat\'. Features: send at least the basic required fields, parkingAvailable = false, parkingIncludedInPrice = true',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // House01: New property - Type - house - Expected: 201
        $tests[] = [
            'id' => 'House01',
            'nombre' => 'New property - Type - house',
            'description' => 'Create new property with type = \'house\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('house', 'sale'),
        ];

        // CountryHouse01: New property - Type - countryhouse - Expected: 201
        $tests[] = [
            'id' => 'CountryHouse01',
            'nombre' => 'New property - Type - countryhouse',
            'description' => 'Create new property with type = \'countryhouse\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('countryhouse', 'sale'),
        ];

        // Garage01: New property - Type - garage - Expected: 201
        $tests[] = [
            'id' => 'Garage01',
            'nombre' => 'New property - Type - garage',
            'description' => 'Create new property with type = \'garage\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('garage', 'sale'),
        ];

        // Office01: New property - Type - office - Expected: 201
        $tests[] = [
            'id' => 'Office01',
            'nombre' => 'New property - Type - office',
            'description' => 'Create new property with type = \'office\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('office', 'sale'),
        ];

        // Commercial01: New property - Type - commercial - Expected: 201
        $tests[] = [
            'id' => 'Commercial01',
            'nombre' => 'New property - Type - commercial',
            'description' => 'Create new property with type = \'commercial\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('commercial', 'sale'),
        ];

        // Commercial02: New property - Type - commercial - Valid transfer - Expected: 201
        $payload = $this->getBasicPropertyPayload('commercial', 'sale');
        $payload['features']['isATransfer'] = true;
        $payload['features']['commercialMainActivity'] = 'restaurant';
        $tests[] = [
            'id' => 'Commercial02',
            'nombre' => 'New property - Type - commercial - Valid transfer',
            'description' => 'Create new property with type = \'commercial\'. Features: send at least the basic required fields, isATransfer = true and any commercialMainActivity',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Commercial03: New property - Type - commercial - Business validation error - transfer - Expected: 400
        $payload = $this->getBasicPropertyPayload('commercial', 'sale');
        $payload['features']['isATransfer'] = true;
        // Sin commercialMainActivity
        $tests[] = [
            'id' => 'Commercial03',
            'nombre' => 'New property - Type - commercial - Business validation error - transfer',
            'description' => 'Create new property with type = \'commercial\'. Features: send at least the basic required fields, isATransfer = true',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Land01: New property - Type - land - Features type - urban - Expected: 201
        // El error dice "land classification must be provided if land type is land_urban or land_countrybuildable"
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'urban'; // Valores permitidos: urban, countrybuildable, countrynonbuildable
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        if (!isset($payload['features']['roadAccess'])) {
            $payload['features']['roadAccess'] = true;
        }
        if ($payload['features']['roadAccess']) {
            $payload['features']['accessType'] = 'road';
        }
        // Agregar allowedUse para la clasificación del terreno (obligatorio para urban y countrybuildable)
        // Valores permitidos: residential, commercial, industrial, agricultural, services
        $payload['features']['allowedUse'] = 'residential';
        $tests[] = [
            'id' => 'Land01',
            'nombre' => 'New property - Type - land - Features type - urban',
            'description' => 'Create new property with type = \'land\'. Features: send all the fields you have in your CRM that are compatible with features type = \'urban\'',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Land02: New property - Type - land - Features type - countrybuildable - Expected: 201
        // Según la documentación: "If type is 'urban', or 'countrybuildable', at least one classification field must be sent"
        // Campos de clasificación: classificationBlocks, classificationChalet, classificationCommercial,
        // classificationHotel, classificationIndustrial, classificationOffice, classificationOther, classificationPublic
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrybuildable'; // Valores permitidos: urban, countrybuildable, countrynonbuildable
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        if (!isset($payload['features']['roadAccess'])) {
            $payload['features']['roadAccess'] = true;
        }
        if ($payload['features']['roadAccess']) {
            $payload['features']['accessType'] = 'road';
        }
        // Agregar al menos un campo de clasificación (obligatorio para urban y countrybuildable)
        $payload['features']['classificationChalet'] = true;
        $tests[] = [
            'id' => 'Land02',
            'nombre' => 'New property - Type - land - Features type - countrybuildable',
            'description' => 'Create new property with type = \'land\'. Features: send all the fields you have in your CRM that are compatible with features type = \'countrybuildable\'',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Land03: New property - Type - land - Features type - countrynonbuildable - Expected: 201
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrynonbuildable';
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        // countrynonbuildable SÍ requiere roadAccess (según el error)
        if (!isset($payload['features']['roadAccess'])) {
            $payload['features']['roadAccess'] = false; // Puede ser false
        }
        // Si roadAccess es false, no debe tener accessType
        if (!$payload['features']['roadAccess']) {
            unset($payload['features']['accessType']);
        }
        $tests[] = [
            'id' => 'Land03',
            'nombre' => 'New property - Type - land - Features type - countrynonbuildable',
            'description' => 'Create new property with type = \'land\'. Features: send all the fields you have in your CRM that are compatible with features type = \'countrynonbuildable\'',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Land04: New property - Type - land - Business validation error - feature not compatible - Expected: 400
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrynonbuildable';
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        // countrynonbuildable requiere roadAccess
        $payload['features']['roadAccess'] = false;
        unset($payload['features']['accessType']); // No accessType si roadAccess es false
        $payload['features']['electricity'] = true; // No compatible con countrynonbuildable
        $tests[] = [
            'id' => 'Land04',
            'nombre' => 'New property - Type - land - Business validation error - feature not compatible with land type',
            'description' => 'Create new property with type = \'land\'. Features: send at least the basic required fields, features type = \'countrynonbuildable\' and electricity = true',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Land05: New property - Type - land - Business validation error - road access - Expected: 400
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        $payload['features']['type'] = 'urban'; // Necesita type para el test
        // NO landClassification
        $payload['features']['roadAccess'] = false;
        $payload['features']['accessType'] = 'road'; // No permitido si roadAccess = false
        $tests[] = [
            'id' => 'Land05',
            'nombre' => 'New property - Type - land - Business validation error - road access',
            'description' => 'Create new property with type = \'land\'. Features: send at least the basic required fields, roadAccess = false and any accessType',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // StorageRoom01: New property - Type - storage - Expected: 201
        $tests[] = [
            'id' => 'StorageRoom01',
            'nombre' => 'New property - Type - storage',
            'description' => 'Create new property with type = \'storage\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('storage', 'sale'),
        ];

        // Building01: New property - Type - building - Expected: 201
        $tests[] = [
            'id' => 'Building01',
            'nombre' => 'New property - Type - building',
            'description' => 'Create new property with type = \'building\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('building', 'sale'),
        ];

        // Building02: New property - Type - building - Basic validation error - classification - Expected: 400
        $payload = $this->getBasicPropertyPayload('building', 'sale');
        // Sin classification (debe fallar)
        $tests[] = [
            'id' => 'Building02',
            'nombre' => 'New property - Type - building - Basic validation error - classification',
            'description' => 'Create new property with type = \'building\'. Features: send at least the basic required fields, don\'t send any classification field',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
        ];

        // Room01: New property - Type - room - Operation - rent - Expected: 201
        $tests[] = [
            'id' => 'Room01',
            'nombre' => 'New property - Type - room - Operation - rent',
            'description' => 'Create new property with type = \'room\' and operation type = \'rent\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('room', 'rent'),
        ];

        // Room02: New property - Type - room - Operation - sale - Basic validation error - Expected: 400
        $tests[] = [
            'id' => 'Room02',
            'nombre' => 'New property - Type - room - Operation - sale - Basic validation error - operation',
            'description' => 'Create new property with type = \'room\' and operation type = \'sale\'. Features: send at least the basic required fields',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('room', 'sale'),
        ];

        // Property10: Find property - Expected: 200
        $tests[] = [
            'id' => 'Property10',
            'nombre' => 'Find property',
            'description' => 'Find any of the properties previously created',
            'expected_status' => 200,
            'metodo' => 'GET',
            'endpoint' => '/v1/properties/{propertyId}',
            'query' => [],
            'body' => null,
        ];

        // Property11: Find property - Error not found - Expected: 404
        $tests[] = [
            'id' => 'Property11',
            'nombre' => 'Find property - Error not found',
            'description' => 'Find a property using any property id not belonging to the office used for the tests',
            'expected_status' => 404,
            'metodo' => 'GET',
            'endpoint' => '/v1/properties/999999999',
            'query' => [],
            'body' => null,
        ];

        // Property12: Find all properties - Expected: 200
        $tests[] = [
            'id' => 'Property12',
            'nombre' => 'Find all properties',
            'description' => 'Find all properties of the office. Make sure to send proper page and size values',
            'expected_status' => 200,
            'metodo' => 'GET',
            'endpoint' => '/v1/properties',
            'query' => ['page' => 1, 'size' => 10],
            'body' => null,
        ];

        // Property13: Update property - Expected: 200
        // NOTA: La API requiere que type, operation y features estén presentes en el payload de actualización,
        // aunque no se puedan cambiar sus valores. Solo podemos actualizar otros campos como address y descriptions.
        // IMPORTANTE: Crear una propiedad flat con sale justo antes para asegurar que tenemos el propertyId correcto
        $tests[] = [
            'id' => 'Property13-Prep',
            'nombre' => 'New property - Prep for Property13',
            'description' => 'Create a flat with sale operation to use in Property13 update test',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $this->getBasicPropertyPayload('flat', 'sale'),
        ];

        $tests[] = [
            'id' => 'Property13',
            'nombre' => 'Update property',
            'description' => 'Update any feature of any of the properties previously created',
            'expected_status' => 200,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}',
            'query' => [],
            'body' => [
                'type' => 'flat', // Debe estar presente pero no se puede cambiar
                'address' => [
                    'streetName' => 'Calle de Prueba Actualizada',
                    'postalCode' => '28001',
                    'town' => 'Madrid',
                    'country' => 'Spain',
                    'latitude' => 40.4168,
                    'longitude' => -3.7038,
                    'precision' => 'exact',
                    'visibility' => 'full',
                ],
                'contactId' => (int)$this->contactId, // Debe ser integer, no string
                'operation' => [
                    'type' => 'sale', // Debe estar presente pero no se puede cambiar
                    'price' => 200000
                ],
                'features' => [
                    'rooms' => 3,
                    'bathroomNumber' => 2,
                    'areaUsable' => 80.0,
                    'areaConstructed' => 90.0,
                    'conservation' => 'good',
                    'energyCertificateRating' => 'G',
                    'liftAvailable' => false,
                    'windowsLocation' => 'external',
                ],
                'descriptions' => [
                    [
                        'language' => 'es',
                        'text' => 'Descripción actualizada para testing'
                    ]
                ]
            ],
        ];

        // Property14: Update property - Business errors - type - Expected: 400
        $tests[] = [
            'id' => 'Property14',
            'nombre' => 'Update property - Business errors - type',
            'description' => 'Update the type of any of the properties previously created',
            'expected_status' => 400,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}',
            'query' => [],
            'body' => [
                'type' => 'house',
                'address' => [
                    'streetName' => 'Calle de Prueba',
                    'postalCode' => '28001',
                    'town' => 'Madrid',
                    'country' => 'Spain',
                    'latitude' => 40.4168,
                    'longitude' => -3.7038,
                    'precision' => 'exact',
                    'visibility' => 'full',
                ],
                'contactId' => (int)$this->contactId, // Debe ser integer, no string
                'operation' => [
                    'type' => 'sale',
                    'price' => 200000
                ],
                'features' => [
                    'rooms' => 4,
                    'bathroomNumber' => 2,
                    'areaConstructed' => 150.0,
                    'conservation' => 'good',
                    'energyCertificateRating' => 'G',
                    'type' => 'independent', // Valores válidos: andar_moradia, independent, semidetached, terraced, villa
                    'areaPlot' => 500.0,
                ],
                'descriptions' => [['language' => 'es', 'text' => 'Test']],
            ],
        ];

        // Property15: Update property - Error not found - Expected: 404
        $tests[] = [
            'id' => 'Property15',
            'nombre' => 'Update property - Error not found',
            'description' => 'Update a property using any property id not belonging to the office used for the tests',
            'expected_status' => 404,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/999999999',
            'query' => [],
            'body' => [
                'type' => 'flat',
                'address' => [
                    'streetName' => 'Calle de Prueba',
                    'postalCode' => '28001',
                    'town' => 'Madrid',
                    'country' => 'Spain',
                    'latitude' => 40.4168,
                    'longitude' => -3.7038,
                    'precision' => 'exact',
                    'visibility' => 'full',
                ],
                'contactId' => (int)$this->contactId, // Debe ser integer, no string
                'operation' => [
                    'type' => 'sale',
                    'price' => 200000
                ],
                'features' => [
                    'rooms' => 3,
                    'bathroomNumber' => 2,
                    'areaConstructed' => 90.0,
                    'conservation' => 'good',
                    'liftAvailable' => false,
                    'energyCertificateRating' => 'G',
                    'windowsLocation' => 'external',
                ],
                'descriptions' => [
                    [
                        'language' => 'es',
                        'text' => 'Test'
                    ]
                ]
            ],
        ];

        // Property16: Deactivate property - Expected: 200
        $tests[] = [
            'id' => 'Property16',
            'nombre' => 'Deactivate property',
            'description' => 'Deactivate any of the properties previously created',
            'expected_status' => 200,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties/{propertyId}/deactivate',
            'query' => [],
            'body' => null,
        ];

        // Property17: Deactivate property - Error not found - Expected: 404
        $tests[] = [
            'id' => 'Property17',
            'nombre' => 'Deactivate property - Error not found',
            'description' => 'Deactivate a property using any property id not belonging to the office used for the tests',
            'expected_status' => 404,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties/999999999/deactivate',
            'query' => [],
            'body' => null,
        ];

        // Property18: Reactivate property - Expected: 200
        $tests[] = [
            'id' => 'Property18',
            'nombre' => 'Reactivate property',
            'description' => 'Reactivate any of the properties previously deactivated',
            'expected_status' => 200,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties/{propertyId}/reactivate',
            'query' => [],
            'body' => null,
        ];

        // Property19: Reactivate property - Error not found - Expected: 404
        $tests[] = [
            'id' => 'Property19',
            'nombre' => 'Reactivate property - Error not found',
            'description' => 'Reactivate a property using any property id not belonging to the office used for the tests',
            'expected_status' => 404,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties/999999999/reactivate',
            'query' => [],
            'body' => null,
        ];

        return $tests;
    }

    /**
     * Genera un payload básico de propiedad
     */
    private function getBasicPropertyPayload(string $type, string $operation): array
    {
        $payload = [
            'type' => $type,
            'operation' => [
                'type' => $operation,
                'price' => $operation === 'sale' ? 200000 : 800,
            ],
            'address' => [
                'streetName' => 'Calle de Prueba',
                'postalCode' => '28001',
                'town' => 'Madrid',
                'country' => 'Spain',
                'latitude' => 40.4168,
                'longitude' => -3.7038,
                'precision' => 'exact',
                'visibility' => 'full',
            ],
            'features' => [],
            'descriptions' => [
                [
                    'language' => 'es',
                    'text' => 'Propiedad de prueba para testing'
                ]
            ],
            'contactId' => (int)$this->contactId, // Debe ser integer, no string
        ];

        // Campos específicos por tipo
        if ($type === 'flat') {
            $payload['features'] = [
                'rooms' => 3,
                'bathroomNumber' => 2,
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'liftAvailable' => false,
                'windowsLocation' => 'external',
            ];
        } elseif ($type === 'house') {
            $payload['features'] = [
                'rooms' => 3,
                'bathroomNumber' => 2,
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'type' => 'independent',
                'areaPlot' => 500.0,
            ];
        } elseif ($type === 'countryhouse') {
            $payload['features'] = [
                'rooms' => 3,
                'bathroomNumber' => 2,
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'type' => 'countryhouse',
                'areaPlot' => 500.0,
            ];
        } elseif ($type === 'land') {
            $payload['features'] = [
                'areaPlot' => 500.0,
                'roadAccess' => true,
                'accessType' => 'road',
            ];
        } elseif ($type === 'garage') {
            $payload['features'] = [
                'garageCapacity' => 'car_compact',
            ];
        } elseif ($type === 'storage') {
            $payload['features'] = [
                'areaConstructed' => 20.0,
            ];
        } elseif ($type === 'office') {
            $payload['features'] = [
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'bathroomNumber' => 1,
                'conditionedAirType' => 'cold/heat',
                'energyCertificateRating' => 'G',
                'liftNumber' => 1,
                'officeBuilding' => false,
                'parkingSpacesNumber' => 0,
                'roomsSplitted' => 'openPlan',
                'windowsLocation' => 'external',
            ];
        } elseif ($type === 'commercial') {
            $payload['features'] = [
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'bathroomNumber' => 1,
                'energyCertificateRating' => 'G',
                'location' => 'on_the_street',
                'rooms' => 1,
                'type' => 'retail',
            ];
        } elseif ($type === 'building') {
            $payload['features'] = [
                'areaConstructed' => 500.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'floorsBuilding' => 3,
                'parkingSpacesNumber' => 0,
                'classificationChalet' => false,
                'classificationCommercial' => true,
                'classificationHotel' => false,
                'classificationIndustrial' => false,
                'classificationOffice' => false,
                'classificationOther' => false,
                // propertyTenants es obligatorio para sale operation según la documentación
                'propertyTenants' => false,
            ];
        } elseif ($type === 'room') {
            $nextMonth = date('Y-m', strtotime('+1 month'));
            $payload['features'] = [
                'areaConstructed' => 15.0,
                'bathroomNumber' => 1,
                'bedType' => 'single',
                'couplesAllowed' => false,
                'liftAvailable' => false,
                'minimalStay' => 2,
                'occupiedNow' => false,
                'petsAllowed' => false,
                'rooms' => 1,
                'smokingAllowed' => false,
                'tenantNumber' => 2,
                'type' => 'shared_flat',
                'availableFrom' => $nextMonth,
                'windowView' => 'street_view',
            ];
        }

        return $payload;
    }

    /**
     * Crea una fila de resultado para el CSV
     */
    private function createResultRow(array $testCase, ?array $result, bool $success, string $error = ''): array
    {
        $createdPropertyId = null;
        $actualResult = 'N/A';

        if ($result) {
            $actualResult = $result['status_code'] ?? 'N/A';
            if (isset($result['response'])) {
                $createdPropertyId = $result['response']['propertyId'] ?? null;
            }
        }

        // JSON enviado (formateado)
        $jsonSent = null;
        if (isset($testCase['body']) && $testCase['body'] !== null) {
            $jsonSent = json_encode($testCase['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        return [
            'Test' => $testCase['id'],
            'Test title' => $testCase['nombre'],
            'Test description' => $testCase['description'] ?? '',
            'Expected result' => $testCase['expected_status'] ?? '',
            'Actual Result' => $actualResult,
            'propertyId' => $createdPropertyId ?? '',
            'JSON sent' => $jsonSent ?? '',
            'Comments' => $error ?: ($success ? 'OK' : 'Failed'),
        ];
    }

    /**
     * Ejecuta un caso de prueba
     */
    private function executeTestCase(array $testCase): array
    {
        try {
            $method = strtoupper($testCase['metodo']);
            $endpoint = $testCase['endpoint'];
            $query = $testCase['query'] ?? [];
            $body = $testCase['body'] ?? null;

            // Obtener headers base
            $headers = $this->getHeaders();

            // Si hay headers personalizados (para tests de auth), usarlos
            if (isset($testCase['custom_headers'])) {
                $headers = array_merge($headers, $testCase['custom_headers']);
            }

            // Llamar directamente al cliente para obtener el status code incluso en errores
            $client = app(\App\Services\Idealista\IdealistaClient::class);
            $response = $client->request($method, $endpoint, [
                'headers' => $headers,
                'query' => $query,
                'json' => $body,
            ]);

            $statusCode = $response->status();
            $responseBody = $response->json();

            // Extraer mensaje de error si existe
            $errorMessage = null;
            if ($statusCode >= 400 && $responseBody) {
                if (isset($responseBody['message'])) {
                    $errorMessage = $responseBody['message'];
                } elseif (isset($responseBody['error'])) {
                    $errorMessage = is_string($responseBody['error']) ? $responseBody['error'] : json_encode($responseBody['error']);
                } elseif (isset($responseBody['errors']) && is_array($responseBody['errors'])) {
                    $errorMessages = [];
                    foreach ($responseBody['errors'] as $error) {
                        if (is_string($error)) {
                            if (preg_match('/message:\[([^\]]+)\]/', $error, $matches)) {
                                $errorMessages[] = $matches[1];
                            } else {
                                $errorMessages[] = $error;
                            }
                        } elseif (is_array($error)) {
                            $errorMessages[] = json_encode($error, JSON_UNESCAPED_UNICODE);
                        }
                    }
                    $errorMessage = !empty($errorMessages) ? implode('; ', $errorMessages) : 'Validation Error';
                } elseif (isset($responseBody['validationMessages'])) {
                    $errorMessage = json_encode($responseBody['validationMessages'], JSON_UNESCAPED_UNICODE);
                }
            }

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status_code' => $statusCode,
                'response' => $responseBody,
                'error' => $errorMessage,
            ];

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $statusCode = $e->response ? $e->response->status() : 0;
            $responseBody = $e->response ? $e->response->json() : null;

            // Extraer mensaje de error detallado
            $errorMessage = $e->getMessage();
            if ($responseBody) {
                if (isset($responseBody['message'])) {
                    $errorMessage = $responseBody['message'];
                } elseif (isset($responseBody['error'])) {
                    $errorMessage = is_string($responseBody['error']) ? $responseBody['error'] : json_encode($responseBody['error']);
                } elseif (isset($responseBody['errors']) && is_array($responseBody['errors'])) {
                    $errorMessages = [];
                    foreach ($responseBody['errors'] as $error) {
                        if (is_string($error)) {
                            if (preg_match('/message:\[([^\]]+)\]/', $error, $matches)) {
                                $errorMessages[] = $matches[1];
                            } else {
                                $errorMessages[] = $error;
                            }
                        } elseif (is_array($error)) {
                            $errorMessages[] = json_encode($error, JSON_UNESCAPED_UNICODE);
                        }
                    }
                    $errorMessage = !empty($errorMessages) ? implode('; ', $errorMessages) : 'Validation Error';
                } elseif (isset($responseBody['validationMessages'])) {
                    $errorMessage = json_encode($responseBody['validationMessages'], JSON_UNESCAPED_UNICODE);
                }
            }

            return [
                'success' => false,
                'status_code' => $statusCode,
                'response' => $responseBody,
                'error' => $errorMessage,
            ];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'status_code' => 0,
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Crea un contacto en Idealista si es necesario
     */
    private function createContactIfNeeded(): ?int
    {
        try {
            $client = app(\App\Services\Idealista\IdealistaClient::class);
            $headers = $this->getHeaders();

            // Generar email único para evitar conflictos
            $uniqueEmail = 'test.property.' . time() . '@example.com';

            // Intentar crear un contacto de prueba
            $contactPayload = [
                'name' => 'Test Contact Property',
                'email' => $uniqueEmail,
                'primaryPhoneNumber' => '699493816',
            ];

            $response = $client->request('POST', '/v1/contacts', [
                'headers' => $headers,
                'json' => $contactPayload,
            ]);

            if ($response->status() === 201) {
                $responseBody = $response->json();
                return $responseBody['contactId'] ?? null;
            }

            // Si el contacto ya existe (409) o hay otro error, intentar obtener uno existente
            return $this->getExistingContact();
        } catch (Throwable $e) {
            $this->warn("Error creando contacto: " . $e->getMessage());
            return $this->getExistingContact();
        }
    }

    /**
     * Obtiene un contacto existente de Idealista
     */
    private function getExistingContact(): ?int
    {
        try {
            $client = app(\App\Services\Idealista\IdealistaClient::class);
            $headers = $this->getHeaders();

            $listResponse = $client->request('GET', '/v1/contacts', [
                'headers' => $headers,
                'query' => ['page' => 1, 'size' => 10],
            ]);

            if ($listResponse->status() === 200) {
                $listBody = $listResponse->json();
                if (isset($listBody['content']) && is_array($listBody['content']) && !empty($listBody['content'])) {
                    return $listBody['content'][0]['contactId'] ?? null;
                }
            }

            return null;
        } catch (Throwable $e) {
            return null;
        }
    }

    /**
     * Obtiene los headers para las peticiones
     */
    private function getHeaders(): array
    {
        $feedKey = config('services.idealista.feed_key');

        if (!$feedKey) {
            throw new \RuntimeException('Falta configurar IDEALISTA_FEED_KEY.');
        }

        return [
            'feedKey' => $feedKey,
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * Guarda los resultados en un archivo CSV
     */
    private function saveResultsToCsv(array $results, string $outputFile): void
    {
        $fp = fopen($outputFile, 'w');

        if (!$fp) {
            throw new \RuntimeException("No se pudo crear el archivo de resultados: {$outputFile}");
        }

        // Escribir BOM para UTF-8 (para que Excel lo lea correctamente)
        fwrite($fp, "\xEF\xBB\xBF");

        // Escribir encabezados
        if (!empty($results)) {
            fputcsv($fp, array_keys($results[0]), ';');

            // Escribir datos
            foreach ($results as $row) {
                fputcsv($fp, $row, ';');
            }
        }

        fclose($fp);
    }
}

