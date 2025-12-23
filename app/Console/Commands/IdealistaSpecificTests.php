<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class IdealistaSpecificTests extends Command
{
    protected $contactId = 103004164; // Contact ID fijo (debe ser integer, no string)

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:test-specific
        {--output= : Ruta del archivo de salida (por defecto: specific-test-results-{timestamp}.csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta los tests específicos: Land01, Land02 y Building01';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Ejecutando tests específicos de Idealista ===\n");
        $this->info("Tests: Land01, Land02, Building01\n");

        try {
            // Obtener los 3 tests específicos
            $testCases = $this->getSpecificTestCases();

            $this->info("Se encontraron " . count($testCases) . " casos de prueba\n");

            // Crear un contacto primero
            $this->info("Creando contacto en Idealista...");
            $createdContact = $this->createContactIfNeeded();
            if ($createdContact) {
                $this->contactId = $createdContact;
                $this->info("✓ Contacto creado/obtenido con ID: {$this->contactId}\n");
            } else {
                $this->warn("⚠️  No se pudo crear/obtener contacto, usando ID por defecto: {$this->contactId}\n");
            }

            $results = [];
            $successCount = 0;
            $failureCount = 0;

            foreach ($testCases as $testCase) {
                $testId = $testCase['id'];
                $testName = $testCase['nombre'];
                $this->info("Ejecutando test {$testId}: {$testName}");

                // Asegurar que contactId esté presente en el body
                if (isset($testCase['body']) && is_array($testCase['body'])) {
                    $testCase['body']['contactId'] = (int)$this->contactId;
                }

                // Mostrar JSON enviado
                if (isset($testCase['body']) && $testCase['body'] !== null) {
                    $jsonSent = json_encode($testCase['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $this->line("  JSON enviado:");
                    $jsonLines = explode("\n", $jsonSent);
                    if (count($jsonLines) > 20) {
                        $this->line("  " . implode("\n  ", array_slice($jsonLines, 0, 20)));
                        $this->line("  ... (JSON truncado, ver CSV completo para ver todo el contenido)");
                    } else {
                        $this->line("  " . implode("\n  ", $jsonLines));
                    }
                } else {
                    $this->line("  JSON enviado: N/A (método " . ($testCase['metodo'] ?? 'GET') . " sin body)");
                }

                $startTime = microtime(true);
                $result = $this->executeTestCase($testCase);
                $endTime = microtime(true);
                $executionTime = round(($endTime - $startTime) * 1000, 2);

                // Si hay rate limit (429), reintentar el test
                $maxRetries = 5;
                $retryWaitTime = 60;
                $retryCount = 0;
                while ($result['status_code'] == 429 && $retryCount < $maxRetries) {
                    $retryCount++;
                    $this->warn("  ⚠️  Rate limit detectado (intento {$retryCount}/{$maxRetries}), esperando {$retryWaitTime} segundos...");
                    sleep($retryWaitTime);

                    $startTime = microtime(true);
                    $result = $this->executeTestCase($testCase);
                    $endTime = microtime(true);
                    $executionTime = round(($endTime - $startTime) * 1000, 2);
                }

                if ($retryCount >= $maxRetries && $result['status_code'] == 429) {
                    $this->error("  ✗ Fallo después de {$maxRetries} reintentos (5 minutos totales) por rate limit");
                }

                // Mostrar propertyId si se creó
                if ($result['success'] && isset($result['response']['propertyId'])) {
                    $propertyId = $result['response']['propertyId'];
                    $this->info("  ✓ Propiedad creada con ID: {$propertyId}");
                }

                // Crear fila de resultado
                $errorMsg = $result['error'] ?? '';
                if ($errorMsg && strlen($errorMsg) > 200) {
                    $errorMsg = substr($errorMsg, 0, 200) . '...';
                }

                $testPassed = $result['success'];
                $actualStatus = $result['status_code'] ?? 0;
                $expectedStatus = $testCase['expected_status'] ?? 200;

                // Determinar si el error es esperado
                $isExpectedError = !$testPassed && $actualStatus == $expectedStatus && $expectedStatus >= 400;

                $results[] = $this->createResultRow($testCase, $result, $testPassed, $errorMsg);

                if ($testPassed) {
                    $successCount++;
                    $this->line("  <fg=green>✓ Éxito</> (Status: <fg=green>{$actualStatus}</>)");
                } else {
                    $failureCount++;
                    $errorDisplay = $errorMsg ? " - {$errorMsg}" : '';

                    if ($isExpectedError) {
                        $this->line("  <fg=yellow>✗ Fallo Esperado</> (Status: <fg=yellow>{$actualStatus}</>{$errorDisplay})");
                    } else {
                        $this->line("  <fg=red>✗ Fallo Inesperado</> (Status: <fg=red>{$actualStatus}</>{$errorDisplay})");
                    }

                    if ($result['status_code'] == 400 && isset($result['response'])) {
                        $this->line("  Detalles del error:");
                        $this->line("  " . json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
                    }
                }

                // Mostrar respuesta para casos exitosos también
                if ($testPassed && isset($result['response'])) {
                    $this->line("  Respuesta:");
                    $jsonResponse = json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $jsonLines = explode("\n", $jsonResponse);
                    if (count($jsonLines) > 20) {
                        $this->line("  " . implode("\n  ", array_slice($jsonLines, 0, 20)));
                        $this->line("  ... (JSON truncado, ver CSV completo para ver todo el contenido)");
                    } else {
                        $this->line("  " . implode("\n  ", $jsonLines));
                    }
                }

                // Pausa entre requests
                usleep(100000); // 0.1 segundos
            }

            // Guardar archivo de resultados
            $outputFile = $this->option('output')
                ?: storage_path('app/specific-test-results-' . date('Y-m-d_H-i-s') . '.csv');

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
            Log::error('Error en IdealistaSpecificTests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Obtiene los 3 tests específicos: Land01, Land02, Building01
     */
    private function getSpecificTestCases(): array
    {
        $tests = [];

        // Land01: New property - Type - land - Features type - urban - Expected: 201
        // Según la documentación: "If type is 'urban', or 'countrybuildable', at least one classification field must be sent"
        // Campos de clasificación disponibles: classificationBlocks, classificationChalet, classificationCommercial,
        // classificationHotel, classificationIndustrial, classificationOffice, classificationOther, classificationPublic
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'urban';
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
        $payload['features']['classificationCommercial'] = true;
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
        // Campos de clasificación disponibles: classificationBlocks, classificationChalet, classificationCommercial,
        // classificationHotel, classificationIndustrial, classificationOffice, classificationOther, classificationPublic
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrybuildable';
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

        // Building01: New property - Type - building - Expected: 201
        // IMPORTANTE: Este test debe crear su propia propiedad, no reutilizar el ID de StorageRoom01
        // Según la documentación: "propertyTenants - Only allowed and mandatory for sale operation"
        $payload = $this->getBasicPropertyPayload('building', 'sale');
        // Agregar propertyTenants (obligatorio para sale operation en buildings)
        $payload['features']['propertyTenants'] = false;
        $tests[] = [
            'id' => 'Building01',
            'nombre' => 'New property - Type - building',
            'description' => 'Create new property with type = \'building\'. Features: send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/properties',
            'query' => [],
            'body' => $payload,
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
            'contactId' => (int)$this->contactId,
        ];

        // Campos específicos por tipo
        if ($type === 'land') {
            $payload['features'] = [
                'areaPlot' => 500.0,
                'roadAccess' => true,
                'accessType' => 'road',
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
                // propertyTenants es obligatorio para sale operation
                'propertyTenants' => false,
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
            $uniqueEmail = 'test.specific.' . time() . '@example.com';

            // Intentar crear un contacto de prueba
            $contactPayload = [
                'name' => 'Test Contact Specific',
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

