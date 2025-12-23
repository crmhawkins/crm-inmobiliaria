<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use App\Services\Idealista\IdealistaPropertiesService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class IdealistaImageTests extends Command
{
    protected $lastCreatedPropertyId = null;
    protected $contactId = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:test-images
        {--output= : Ruta del archivo de salida (por defecto: image-test-results-{timestamp}.csv)}
        {--test= : Ejecutar solo un test específico por ID (ej: Image01)}
        {--from= : Ejecutar todos los tests desde un ID específico hasta el final (ej: Image01)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta todos los casos de prueba de imágenes de la API de Idealista';

    public function __construct(
        private readonly IdealistaApiService $api,
        private readonly IdealistaPropertiesService $propertiesService
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Ejecutando tests de Imágenes de Idealista ===\n");

        try {
            // Obtener todos los tests de imágenes
            $testCases = $this->getImageTestCases();

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

            // Crear un contacto y una propiedad primero para los tests
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

            // Crear una propiedad para usar en los tests
            $needsProperty = true;

            if ($needsProperty) {
                $this->info("Creando propiedad de prueba...");
                try {
                    $propertyId = $this->createTestProperty();
                    if ($propertyId) {
                        $this->lastCreatedPropertyId = $propertyId;
                        $this->info("✓ Propiedad creada con ID: {$this->lastCreatedPropertyId}\n");
                    } else {
                        $this->error("✗ No se pudo crear la propiedad de prueba. Algunos tests fallarán.\n");
                        $this->warn("  Intentando usar el último propertyId de tests anteriores...\n");
                    }
                } catch (Throwable $e) {
                    $this->error("✗ Error creando propiedad de prueba: " . $e->getMessage() . "\n");
                    $this->error("  Trace: " . substr($e->getTraceAsString(), 0, 500) . "\n");
                }
            }

            $successCount = 0;
            $failureCount = 0;
            $results = [];

            foreach ($testCases as $testCase) {
                $testId = $testCase['id'];
                $this->info("Ejecutando test {$testId}: {$testCase['nombre']}");

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

                // Reemplazar placeholders en body
                if (isset($testCase['body']) && is_array($testCase['body'])) {
                    $testCase['body'] = $this->replacePlaceholders($testCase['body']);
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

                // Ejecutar el test
                $startTime = microtime(true);
                $result = $this->executeTestCase($testCase);
                $endTime = microtime(true);
                $executionTime = round(($endTime - $startTime) * 1000, 2);

                // Si hay rate limit (429), reintentar el test hasta que funcione
                $maxRetries = 5;
                $retryWaitTime = 60;
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
                    $this->error("  ✗ Fallo después de {$maxRetries} reintentos ({$maxRetries} minutos totales) por rate limit");
                }

                // Crear fila de resultado
                $errorMsg = $result['error'] ?? '';
                if ($errorMsg && strlen($errorMsg) > 200) {
                    $errorMsg = substr($errorMsg, 0, 200) . '...';
                }

                // Un test es exitoso solo si el status code está en 200-299
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

                    // Mostrar respuesta para éxitos también
                    if (isset($result['response']) && $result['response'] !== null) {
                        $this->line("  Respuesta:");
                        $responseJson = json_encode($result['response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                        $responseLines = explode("\n", $responseJson);
                        if (count($responseLines) > 30) {
                            $this->line("  " . implode("\n  ", array_slice($responseLines, 0, 30)));
                            $this->line("  ... (respuesta truncada, ver CSV completo para ver todo el contenido)");
                        } else {
                            $this->line("  " . implode("\n  ", $responseLines));
                        }
                    }
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

                // Pausa entre requests para evitar rate limiting
                usleep(100000); // 0.1 segundos (100ms)
            }

            // Guardar archivo de resultados
            $outputFile = $this->option('output')
                ?: storage_path('app/image-test-results-' . date('Y-m-d_H-i-s') . '.csv');

            $this->saveResultsToCsv($results, $outputFile);

            // Calcular errores esperados vs inesperados
            $expectedErrors = 0;
            $unexpectedErrors = 0;
            foreach ($results as $result) {
                if (!$result['Actual Result'] || !in_array($result['Actual Result'], [200, 201, 202])) {
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
            Log::error('Error en IdealistaImageTests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Obtiene todos los tests de imágenes
     */
    private function getImageTestCases(): array
    {
        $tests = [];

        // Image01: New images - Expected: 202
        $tests[] = [
            'id' => 'Image01',
            'nombre' => 'New images',
            'description' => 'Create images for an existing property. Send exactly two images in the request',
            'expected_status' => 202,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => [
                'images' => [
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'living'
                    ],
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'kitchen'
                    ]
                ]
            ],
        ];

        // Image02: Find all images - Expected: 200
        $tests[] = [
            'id' => 'Image02',
            'nombre' => 'Find all images',
            'description' => 'Find all images for a property that has images',
            'expected_status' => 200,
            'metodo' => 'GET',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => null,
        ];

        // Image03: Update order - Expected: 202
        // El orden se determina por la posición en el array, NO por el campo 'order'
        $tests[] = [
            'id' => 'Image03',
            'nombre' => 'Update order',
            'description' => 'Call the same endpoint of test Image01 swapping the order of the images in the body of the request',
            'expected_status' => 202,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => [
                'images' => [
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'kitchen' // Primera posición (orden invertido)
                    ],
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'living' // Segunda posición
                    ]
                ]
            ],
        ];

        // Image04: Update label - Expected: 202
        $tests[] = [
            'id' => 'Image04',
            'nombre' => 'Update label',
            'description' => 'Call the same endpoint of test Image01 changing the label of the images in the body of the request',
            'expected_status' => 202,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => [
                'images' => [
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'bedroom' // Label cambiado
                    ],
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'bathroom' // Label cambiado
                    ]
                ]
            ],
        ];

        // Image05: Delete single image - Expected: 202
        // Para eliminar una imagen, simplemente no la incluimos en el array
        $tests[] = [
            'id' => 'Image05',
            'nombre' => 'Delete single image',
            'description' => 'Delete a single image for the property used in the test Image01. To do that, call the same endpoint but stop sending the image you want to be deleted',
            'expected_status' => 202,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => [
                'images' => [
                    [
                        'url' => 'https://via.placeholder.com/800x600.jpg',
                        'label' => 'living' // Solo una imagen (la otra se elimina)
                    ]
                ]
            ],
        ];

        // Image06: Delete all images - Expected: 200
        $tests[] = [
            'id' => 'Image06',
            'nombre' => 'Delete all images',
            'description' => 'Delete all the images of a property using the delete all endpoint',
            'expected_status' => 200,
            'metodo' => 'DELETE',
            'endpoint' => '/v1/properties/{propertyId}/images',
            'query' => [],
            'body' => null,
        ];

        return $tests;
    }

    /**
     * Ejecuta un caso de prueba
     */
    private function executeTestCase(array $testCase): array
    {
        try {
            $method = $testCase['metodo'];
            $endpoint = $testCase['endpoint'];
            $query = $testCase['query'] ?? [];
            $body = $testCase['body'] ?? null;
            $customHeaders = $testCase['custom_headers'] ?? [];

            // Usar el cliente directamente con headers personalizados si es necesario
            $httpClient = $this->getHttpClientWithCustomHeaders($customHeaders);

            $response = $httpClient->send($method, $endpoint, array_filter([
                'query' => $query,
                'json' => $body,
            ]));

            $statusCode = $response->status();
            $responseData = $response->json();

            return [
                'success' => $statusCode >= 200 && $statusCode < 300,
                'status_code' => $statusCode,
                'response' => $responseData,
                'error' => $responseData['message'] ?? ($responseData['errors'][0] ?? ''),
            ];
        } catch (\Illuminate\Http\Client\RequestException $e) {
            $statusCode = $e->response->status();
            $responseData = $e->response->json();

            return [
                'success' => false,
                'status_code' => $statusCode,
                'response' => $responseData,
                'error' => $responseData['message'] ?? ($responseData['errors'][0] ?? $e->getMessage()),
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
     * Obtiene un cliente HTTP con headers personalizados
     */
    private function getHttpClientWithCustomHeaders(array $customHeaders): \Illuminate\Http\Client\PendingRequest
    {
        $client = app(\App\Services\Idealista\IdealistaClient::class);
        $httpFactory = app(\Illuminate\Http\Client\Factory::class);

        // Obtener token (el cliente lo maneja automáticamente)
        $token = $client->getAccessToken();

        $feedKey = config('services.idealista.feed_key');
        if (!$feedKey) {
            throw new \RuntimeException('Falta configurar IDEALISTA_FEED_KEY.');
        }

        $baseUrl = config('services.idealista.host_template', 'https://partners-sandbox.idealista.%s');
        $country = config('services.idealista.country', 'com');
        $baseUrl = rtrim(sprintf($baseUrl, $country), '/');

        // Verificar si se debe deshabilitar SSL (para desarrollo)
        $verifySsl = config('services.idealista.verify_ssl', true);
        if (is_string($verifySsl)) {
            $verifySsl = !in_array(strtolower($verifySsl), ['false', '0', 'no']);
        }

        $httpClient = $httpFactory
            ->baseUrl($baseUrl)
            ->withToken($token, 'Bearer')
            ->withOptions(['verify' => $verifySsl])
            ->withHeaders([
                'feedKey' => $feedKey,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ]);

        // Agregar headers personalizados (sobrescribirán los anteriores si hay conflicto)
        if (!empty($customHeaders)) {
            $httpClient = $httpClient->withHeaders($customHeaders);
        }

        return $httpClient;
    }

    /**
     * Crea un contacto si es necesario
     */
    private function createContactIfNeeded(): ?int
    {
        try {
            $client = app(\App\Services\Idealista\IdealistaClient::class);
            $headers = $this->getHeaders();

            // Generar email único para evitar conflictos
            $uniqueEmail = 'test.image.' . time() . '@example.com';

            // Intentar crear un contacto de prueba
            $contactPayload = [
                'name' => 'Test Contact Image',
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
     * Crea una propiedad de prueba
     */
    private function createTestProperty(): ?int
    {
        try {
            $payload = [
                'type' => 'flat',
                'operation' => [
                    'type' => 'sale',
                    'price' => 200000
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
                'contactId' => (int)$this->contactId,
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
                        'text' => 'Propiedad de prueba para tests de imágenes'
                    ]
                ]
            ];

            $response = $this->propertiesService->create($payload);
            $propertyId = $response['propertyId'] ?? null;

            if ($propertyId) {
                Log::info('Propiedad de prueba creada para tests de imágenes', [
                    'property_id' => $propertyId
                ]);
            }

            return $propertyId;
        } catch (Throwable $e) {
            Log::error('Error creando propiedad de prueba', ['error' => $e->getMessage()]);
            return null;
        }
    }

    /**
     * Reemplaza placeholders en el body
     */
    private function replacePlaceholders(array $body): array
    {
        $replaced = [];
        foreach ($body as $key => $value) {
            if (is_array($value)) {
                $replaced[$key] = $this->replacePlaceholders($value);
            } elseif (is_string($value) && strpos($value, '{propertyId}') !== false) {
                $replaced[$key] = str_replace('{propertyId}', $this->lastCreatedPropertyId, $value);
            } else {
                $replaced[$key] = $value;
            }
        }
        return $replaced;
    }

    /**
     * Crea una fila de resultado para el CSV
     */
    private function createResultRow(array $testCase, ?array $result, bool $success, string $error = ''): array
    {
        $actualResult = 'N/A';
        if ($result) {
            $actualResult = $result['status_code'] ?? 'N/A';
        }

        $jsonSent = null;
        if (isset($testCase['body']) && $testCase['body'] !== null) {
            $jsonSent = json_encode($testCase['body'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (strlen($jsonSent) > 1000) {
                $jsonSent = substr($jsonSent, 0, 1000) . '... (truncado)';
            }
        }

        return [
            'Test ID' => $testCase['id'],
            'Test Name' => $testCase['nombre'],
            'Description' => $testCase['description'] ?? '',
            'Method' => $testCase['metodo'],
            'Endpoint' => $testCase['endpoint'],
            'Expected result' => $testCase['expected_status'] ?? '',
            'Actual Result' => $actualResult,
            'JSON sent' => $jsonSent ?? '',
            'Comments' => $error ?: ($success ? 'OK' : 'Failed'),
        ];
    }

    /**
     * Guarda los resultados en un archivo CSV
     */
    private function saveResultsToCsv(array $results, string $filename): void
    {
        if (empty($results)) {
            return;
        }

        $file = fopen($filename, 'w');

        // Escribir encabezados
        $headers = array_keys($results[0]);
        fputcsv($file, $headers);

        // Escribir datos
        foreach ($results as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }
}

