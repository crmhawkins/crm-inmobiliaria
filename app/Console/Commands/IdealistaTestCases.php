<?php

namespace App\Console\Commands;

use App\Models\Clientes;
use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class IdealistaTestCases extends Command
{
    protected $lastCreatedContactId = null;
    protected $lastCreatedPropertyId = null;
    protected $lastPropertyIdForImages = null;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:test-cases
        {--output= : Ruta del archivo de salida (por defecto: test-results-{timestamp}.csv)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta todos los casos de prueba de la API de Idealista según los CSV y guarda los resultados para rellenar el Excel';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("=== Ejecutando tests de Idealista ===");
        $this->info("Basado en los CSV: contacts, images, properties\n");

        try {
            // Obtener todos los tests hardcodeados según los CSV
            $testCases = $this->getAllTestCases();

            if (empty($testCases)) {
                $this->error("No se encontraron casos de prueba");
                return self::FAILURE;
            }

            $this->info("Se encontraron " . count($testCases) . " casos de prueba\n");

            // Preparar datos para el archivo de resultados
            $results = [];
            $successCount = 0;
            $failureCount = 0;

            // Ejecutar cada caso de prueba
            foreach ($testCases as $testCase) {
                $testId = $testCase['id'];
                $testName = $testCase['nombre'];
                $this->info("Ejecutando test {$testId}: {$testName}");

                // Usar siempre el contact ID 103004164 como solicitado
                $contactIdToUse = 103004164;

                // Reemplazar placeholders en endpoint
                if (isset($testCase['endpoint'])) {
                    if (strpos($testCase['endpoint'], '{contactId}') !== false) {
                        $testCase['endpoint'] = str_replace('{contactId}', $contactIdToUse, $testCase['endpoint']);
                    }

                    if (strpos($testCase['endpoint'], '{propertyId}') !== false) {
                        if (isset($this->lastCreatedPropertyId)) {
                            $testCase['endpoint'] = str_replace('{propertyId}', $this->lastCreatedPropertyId, $testCase['endpoint']);
                        } elseif (isset($this->lastPropertyIdForImages)) {
                            $testCase['endpoint'] = str_replace('{propertyId}', $this->lastPropertyIdForImages, $testCase['endpoint']);
                        } else {
                            $this->warn("  ⚠️  Saltando test {$testId}: requiere un propertyId válido");
                            $results[] = $this->createResultRow($testCase, null, false, 'Requiere propertyId de test anterior');
                            continue;
                        }
                    }
                }

                // Asegurar que contactId esté presente en el body para tests de propiedades
                if (isset($testCase['body']) && is_array($testCase['body'])) {
                    // Si el endpoint es de propiedades (POST/PUT), asegurar que tenga contactId
                    if (isset($testCase['endpoint']) &&
                        (strpos($testCase['endpoint'], '/v1/properties') !== false) &&
                        in_array($testCase['metodo'] ?? '', ['POST', 'PUT'])) {
                        $testCase['body']['contactId'] = $contactIdToUse;
                    } elseif (isset($testCase['body']['contactId'])) {
                        // Para otros casos, actualizar si ya existe
                        $testCase['body']['contactId'] = $contactIdToUse;
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

                // Guardar IDs creados para tests posteriores
                if ($result['success'] && isset($result['response'])) {
                    if (isset($result['response']['contactId'])) {
                        $this->lastCreatedContactId = $result['response']['contactId'];
                        $this->info("  ✓ Contacto creado con ID: {$this->lastCreatedContactId}");
                    }
                    if (isset($result['response']['propertyId'])) {
                        $this->lastCreatedPropertyId = $result['response']['propertyId'];
                        if (!isset($this->lastPropertyIdForImages)) {
                            $this->lastPropertyIdForImages = $result['response']['propertyId'];
                        }
                        $this->info("  ✓ Propiedad creada con ID: {$this->lastCreatedPropertyId}");
                    }
                }

                // Crear fila de resultado
                $errorMsg = $result['error'] ?? '';
                if ($errorMsg && strlen($errorMsg) > 200) {
                    $errorMsg = substr($errorMsg, 0, 200) . '...';
                }
                $results[] = $this->createResultRow($testCase, $result, $result['success'], $errorMsg);

                if ($result['success']) {
                    $successCount++;
                    $this->info("  ✓ Éxito (Status: {$result['status_code']})");
                } else {
                    $failureCount++;
                    $errorDisplay = $errorMsg ? " - {$errorMsg}" : '';
                    $this->error("  ✗ Fallo (Status: {$result['status_code']}{$errorDisplay})");
                }

                // Pausa entre requests (aumentada para evitar rate limiting)
                usleep(3000000); // 3 segundos
            }

            // Guardar archivo de resultados
            $outputFile = $this->option('output')
                ?: storage_path('app/test-results-' . date('Y-m-d_H-i-s') . '.csv');

            $this->saveResultsToCsv($results, $outputFile);

            $this->info("\n=== Resumen ===");
            $this->info("Total de tests: " . count($testCases));
            $this->info("Exitosos: {$successCount}");
            $this->info("Fallidos: {$failureCount}");
            $this->info("Resultados guardados en: {$outputFile}");
            $this->info("\nPuedes copiar los datos de este CSV al Excel original para rellenar las columnas 'Actual Result', 'contactId', 'propertyId', 'JSON sent' y 'Comments'");

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Error ejecutando tests: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            Log::error('Error en IdealistaTestCases', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }

    /**
     * Obtiene todos los tests hardcodeados según los CSV
     * IMPORTANTE: El orden importa - las propiedades deben crearse antes de las imágenes
     */
    private function getAllTestCases(): array
    {
        $tests = [];

        // ===== TESTS DE CONTACTOS (del CSV Copia de IDEALISTA test-cases.csv) =====

        // Contact01: New contact - Create new contact. Send all the fields you have in your CRM - Expected: 201
        // Obtener un cliente real del CRM que no tenga idealista_contact_id (para evitar duplicados)
        $clienteReal = Clientes::whereNull('idealista_contact_id')
            ->whereNotNull('email')
            ->whereNotNull('telefono')
            ->whereNotNull('nombre_completo')
            ->first();

        if (!$clienteReal) {
            // Si no hay cliente sin contact_id, usar cualquier cliente con datos completos
            $clienteReal = Clientes::whereNotNull('email')
                ->whereNotNull('telefono')
                ->whereNotNull('nombre_completo')
                ->first();
        }

        // Mapear campos del CRM a la API de Idealista
        // La API de Idealista requiere: name, email, primaryPhoneNumber
        // El primaryPhoneNumber debe ser solo números (5-12 dígitos), sin prefijos como +
        $telefonoCompleto = trim(($clienteReal->telefono_prefijo ?? '') . ($clienteReal->telefono ?? ''));
        if (empty($telefonoCompleto)) {
            $telefonoCompleto = '699493816'; // Fallback si no hay teléfono
        }

        // Limpiar el teléfono: quitar todos los caracteres no numéricos (+, espacios, guiones, etc.)
        $telefonoLimpio = preg_replace('/[^0-9]/', '', $telefonoCompleto);

        // Asegurar que tenga entre 5 y 12 dígitos (requisito de la API)
        if (strlen($telefonoLimpio) < 5) {
            $telefonoLimpio = '699493816'; // Fallback si es muy corto
        } elseif (strlen($telefonoLimpio) > 12) {
            // Si es muy largo, tomar los últimos 12 dígitos
            $telefonoLimpio = substr($telefonoLimpio, -12);
        }

        $contactPayload = [
            'name' => $clienteReal->nombre_completo ?? 'Test Contact',
            'email' => $clienteReal->email ?? 'test@example.com',
            'primaryPhoneNumber' => $telefonoLimpio,
        ];

        // Agregar campos adicionales si la API los acepta (según documentación de Idealista)
        // Nota: La API puede aceptar lastName, pero lo verificamos en el test

        $tests[] = [
            'id' => 'Contact01',
            'nombre' => 'New contact',
            'description' => 'Create new contact. Send all the fields you have in your CRM',
            'expected_status' => 201,
            'metodo' => 'POST',
            'endpoint' => '/v1/contacts',
            'query' => [],
            'body' => $contactPayload,
        ];

        // Contact02: New contact - Basic validation error - email missing - Expected: 400
        $tests[] = [
            'id' => 'Contact02',
            'nombre' => 'New contact - Basic validation error - email missing',
            'description' => 'Create a new contact. Don\'t send an email',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/contacts',
            'query' => [],
            'body' => [
                'name' => 'Test Contact',
                'primaryPhoneNumber' => '699493816'
            ],
        ];

        // Contact03: New contact - Basic validation error - email format - Expected: 400
        $tests[] = [
            'id' => 'Contact03',
            'nombre' => 'New contact - Basic validation error - email format',
            'description' => 'Create a new contact. Send an invalid email, for example \'test@test\'',
            'expected_status' => 400,
            'metodo' => 'POST',
            'endpoint' => '/v1/contacts',
            'query' => [],
            'body' => [
                'name' => 'Test Contact',
                'email' => 'test@test',
                'primaryPhoneNumber' => '699493816'
            ],
        ];

        // Contact04: Update contact - Expected: 200
        $tests[] = [
            'id' => 'Contact04',
            'nombre' => 'Update contact',
            'description' => 'Update the information of a contact previously created',
            'expected_status' => 200,
            'metodo' => 'PUT',
            'endpoint' => '/v1/contacts/{contactId}',
            'query' => [],
            'body' => [
                'name' => 'Updated Contact Name',
                'email' => 'updated@example.com',
                'primaryPhoneNumber' => '699493817'
            ],
        ];

        // Contact05: Find contact - Expected: 200
        $tests[] = [
            'id' => 'Contact05',
            'nombre' => 'Find contact',
            'description' => 'Find a contact previously created using the endpoint by id',
            'expected_status' => 200,
            'metodo' => 'GET',
            'endpoint' => '/v1/contacts/{contactId}',
            'query' => [],
            'body' => null,
        ];

        // Contact06: Find all contacts - Expected: 200
        $tests[] = [
            'id' => 'Contact06',
            'nombre' => 'Find all contacts',
            'description' => 'Find all contacts using the find all endpoint. Make sure to send proper page and size values',
            'expected_status' => 200,
            'metodo' => 'GET',
            'endpoint' => '/v1/contacts',
            'query' => ['page' => 1, 'size' => 10],
            'body' => null,
        ];

        // ===== TESTS DE PROPIEDADES (del CSV Copia de IDEALISTA test-cases - property.csv) =====
        // IMPORTANTE: Estos deben ejecutarse ANTES de las imágenes para tener propertyId

        // Property03: New property - Operation - sale - Expected: 201
        // (Property01 y Property02 son tests de errores de auth que requieren headers especiales, los saltamos por ahora)
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
        // Usar payload básico pero completo (sin campos incompatibles)
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
        // El test debe tener areaConstructed y energyCertificateRating (requeridos), pero areaConstructed < 10 para que falle
        $payload = $this->getBasicPropertyPayload('flat', 'sale');
        $payload['features']['areaConstructed'] = 5.0; // < 10 (debe fallar)
        // energyCertificateRating ya está en getBasicPropertyPayload para flat
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
        $payload['features']['areaConstructed'] = 70.0; // < areaUsable
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
        // Para transfer, la operation debe ser 'sale' o 'rent' (no 'transfer'), pero isATransfer = true
        // commercialMainActivity debe ser uno de los valores específicos permitidos
        $payload = $this->getBasicPropertyPayload('commercial', 'sale');
        // NO cambiar operation.type a 'transfer' (debe ser 'sale' o 'rent')
        $payload['features']['isATransfer'] = true;
        $payload['features']['commercialMainActivity'] = 'restaurant'; // Valor válido de la lista
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
        // Para land se requiere areaPlot y roadAccess (ya están en getBasicPropertyPayload)
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'urban';
        // Asegurar que areaPlot y roadAccess estén presentes (ya están en getBasicPropertyPayload)
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        if (!isset($payload['features']['roadAccess'])) {
            $payload['features']['roadAccess'] = true;
        }
        // Asegurar que accessType esté presente si roadAccess es true
        if ($payload['features']['roadAccess']) {
            $payload['features']['accessType'] = 'road';
        }
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
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrybuildable';
        // Asegurar que areaPlot y roadAccess estén presentes
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        if (!isset($payload['features']['roadAccess'])) {
            $payload['features']['roadAccess'] = true;
        }
        // Asegurar que accessType esté presente si roadAccess es true
        if ($payload['features']['roadAccess']) {
            $payload['features']['accessType'] = 'road';
        }
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
        // Para countrynonbuildable necesita areaPlot pero NO roadAccess
        $payload = $this->getBasicPropertyPayload('land', 'sale');
        $payload['features']['type'] = 'countrynonbuildable';
        // Asegurar que areaPlot esté presente
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        // countrynonbuildable NO necesita roadAccess ni accessType
        unset($payload['features']['roadAccess']);
        unset($payload['features']['accessType']);
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
        // Asegurar que areaPlot esté presente
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        // countrynonbuildable NO necesita roadAccess ni accessType
        unset($payload['features']['roadAccess']);
        unset($payload['features']['accessType']);
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
        // Asegurar que areaPlot esté presente
        if (!isset($payload['features']['areaPlot'])) {
            $payload['features']['areaPlot'] = 500.0;
        }
        $payload['features']['roadAccess'] = false;
        $payload['features']['accessType'] = 'road'; // No permitido si roadAccess = false (debe ser eliminado o no estar presente)
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
        $payload = $this->getBasicPropertyPayload('building', 'sale');
        // NO agregar classification (no está permitido)
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

        // Building02: New property - Type - building - Basic validation error - classification - Expected: 400
        $payload = $this->getBasicPropertyPayload('building', 'sale');
        // Sin classification
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
        // Para actualizar, se requiere TODOS los campos obligatorios: type, address, contactId, y features completos
        $tests[] = [
            'id' => 'Property13',
            'nombre' => 'Update property',
            'description' => 'Update any feature of any of the properties previously created',
            'expected_status' => 200,
            'metodo' => 'PUT',
            'endpoint' => '/v1/properties/{propertyId}',
            'query' => [],
            'body' => [
                'type' => 'flat', // Requerido
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
                'contactId' => 103004164, // Requerido
                'operation' => [
                    'type' => 'rent',
                    'price' => 600
                ],
                'features' => [
                    'rooms' => 3,
                    'bathroomNumber' => 2,
                    'areaConstructed' => 90.0, // Requerido
                    'conservation' => 'good', // Requerido
                    'liftAvailable' => false, // Requerido
                    'energyCertificateRating' => 'G',
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
        // Para house necesita todos los campos requeridos, pero NO debe tener liftAvailable
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
                'contactId' => 103004164,
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
                    'type' => 'chalet', // Requerido para house
                ],
                'descriptions' => [['language' => 'es', 'text' => 'Test']],
            ],
        ];

        // Property15: Update property - Error not found - Expected: 404
        // Necesita todos los campos requeridos para flat
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
                'contactId' => 103004164,
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

        // ===== TESTS DE IMÁGENES (del CSV Copia de IDEALISTA test-cases - images.csv) =====
        // IMPORTANTE: Estos deben ejecutarse DESPUÉS de las propiedades para tener propertyId

        // Image01: New images - Create images for an existing property. Send exactly two images - Expected: 202
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
                        'url' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800',
                        'label' => 'facade' // Valores permitidos: appraisalplan, archive, atmosphere, balcony, etc.
                    ],
                    [
                        'url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800',
                        'label' => 'living' // El orden se determina por la posición en el array
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
                    ['url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800', 'label' => 'living'], // Primera posición
                    ['url' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800', 'label' => 'facade'] // Segunda posición (orden invertido)
                ]
            ],
        ];

        // Image04: Update label - Expected: 202
        // El orden se determina por la posición en el array, NO por el campo 'order'
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
                    ['url' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800', 'label' => 'living'],
                    ['url' => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800', 'label' => 'bedroom']
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
                    ['url' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800', 'label' => 'facade'] // Solo una imagen (la otra se elimina)
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
            'contactId' => 103004164, // Usar un contactId real del sistema
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
            // Countryhouse necesita un type específico dentro de features
            $payload['features'] = [
                'rooms' => 3,
                'bathroomNumber' => 2,
                'areaUsable' => 80.0,
                'areaConstructed' => 90.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'type' => 'countryhouse', // Valores permitidos: countryhouse, village, castle, palace, etc.
                'areaPlot' => 500.0,
            ];
        } elseif ($type === 'land') {
            // Land: requiere roadAccess y accessType cuando roadAccess es true
            // accessType debe ser: urban, road, track, highway, unknown (no paved)
            $payload['features'] = [
                'areaPlot' => 500.0,
                'roadAccess' => true,
                'accessType' => 'road', // Valores permitidos: urban, road, track, highway, unknown
            ];
        } elseif ($type === 'garage') {
            // Garage: NO areaUsable, SÍ garageCapacity
            // Valores permitidos: unknown, car_compact, car_sedan, motorcycle, car_and_motorcycle, two_cars_and_more
            $payload['features'] = [
                'garageCapacity' => 'car_compact',
            ];
        } elseif ($type === 'storage') {
            // Storage: NO areaUsable, SÍ areaConstructed
            $payload['features'] = [
                'areaConstructed' => 20.0,
            ];
        } elseif ($type === 'office') {
            // Office: requiere muchos campos específicos
            // conditionedAirType: notAvailable, cold, cold/heat, preInstallation
            // roomsSplitted: openPlan, withScreens, withWalls, unknown
            // windowsLocation: required para office también
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
                'windowsLocation' => 'external', // Requerido para office
            ];
        } elseif ($type === 'commercial') {
            // Commercial: requiere energyCertificateRating, location, rooms, type
            // location: on_top_floor, in_a_mall, on_the_street, mezzanine, underground, other, unknown
            // type: retail, industrial
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
            // Building: NO areaUsable ni classification, SÍ areaConstructed y TODOS los classification como booleanos
            // NO classificationResidential (solo los otros)
            // Para sale operation, SÍ requiere tenants
            $payload['features'] = [
                'areaConstructed' => 500.0,
                'conservation' => 'good',
                'energyCertificateRating' => 'G',
                'floorsBuilding' => 3,
                'parkingSpacesNumber' => 0,
                // TODOS los classification son requeridos como booleanos (SIN Residential)
                'classificationChalet' => false,
                'classificationCommercial' => true, // Al menos uno debe ser true
                'classificationHotel' => false,
                'classificationIndustrial' => false,
                'classificationOffice' => false,
                'classificationOther' => false,
            ];
            // Para sale operation, SÍ requiere tenants (el error actual dice que es obligatorio)
            if ($operation === 'sale') {
                $payload['features']['tenants'] = 1;
            }
        } elseif ($type === 'room') {
            // Room: NO areaUsable ni conservation, SÍ muchos campos específicos
            // availableFrom debe ser YYYY-MM (patrón: ^[0-9]{4}-(0[1-9]|1[012])$)
            // minimalStay debe ser >= 2
            // type debe ser: shared_flat, shared_chalet (no individual)
            $nextMonth = date('Y-m', strtotime('+1 month')); // Formato YYYY-MM
            $payload['features'] = [
                'areaConstructed' => 15.0,
                'bathroomNumber' => 1,
                'bedType' => 'single',
                'couplesAllowed' => false,
                'liftAvailable' => false,
                'minimalStay' => 2, // Debe ser >= 2
                'occupiedNow' => false,
                'petsAllowed' => false,
                'rooms' => 1,
                'smokingAllowed' => false,
                'tenantNumber' => 2, // Debe ser >= 2 (no 1)
                'type' => 'shared_flat', // Valores: shared_flat, shared_chalet
                'availableFrom' => $nextMonth, // Formato YYYY-MM (ej: 2026-01)
                'windowView' => 'street_view', // Requerido para room: street_view, courtyard_view, no_window
            ];
        }

        return $payload;
    }

    /**
     * Genera un payload completo de propiedad con todos los campos
     */
    private function getFullPropertyPayload(string $type, string $operation): array
    {
        $payload = $this->getBasicPropertyPayload($type, $operation);

        // Agregar más campos opcionales
        $payload['features']['builtYear'] = 2000;
        $payload['features']['terrace'] = true;
        $payload['features']['balcony'] = true;
        $payload['features']['parkingAvailable'] = true;
        $payload['features']['conditionedAir'] = true;
        $payload['features']['furnished'] = false;

        if ($type === 'flat') {
            $payload['features']['terraceSurface'] = 10.0;
        }

        return $payload;
    }

    /**
     * Crea una fila de resultado para el CSV
     */
    private function createResultRow(array $testCase, ?array $result, bool $success, string $error = ''): array
    {
        $createdContactId = null;
        $createdPropertyId = null;
        $actualResult = 'N/A';

        if ($result) {
            $actualResult = $result['status_code'] ?? 'N/A';
            if (isset($result['response'])) {
                $createdContactId = $result['response']['contactId'] ?? null;
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
            'contactId' => $createdContactId ?? '',
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

            // Llamar directamente al cliente para obtener el status code incluso en errores
            $client = app(\App\Services\Idealista\IdealistaClient::class);
            $response = $client->request($method, $endpoint, [
                'headers' => $this->getHeaders(),
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
                    // Idealista devuelve errores como array de strings
                    $errorMessages = [];
                    foreach ($responseBody['errors'] as $error) {
                        if (is_string($error)) {
                            // Extraer el mensaje del formato ValidationMessage
                            if (preg_match('/message:\[([^\]]+)\]/', $error, $matches)) {
                                $errorMessages[] = $matches[1];
                            } else {
                                $errorMessages[] = $error;
                            }
                        }
                    }
                    $errorMessage = !empty($errorMessages) ? implode('; ', array_slice($errorMessages, 0, 2)) : 'Validation Error';
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
                    // Idealista devuelve errores como array de strings
                    $errorMessages = [];
                    foreach ($responseBody['errors'] as $error) {
                        if (is_string($error)) {
                            // Extraer el mensaje del formato ValidationMessage
                            if (preg_match('/message:\[([^\]]+)\]/', $error, $matches)) {
                                $errorMessages[] = $matches[1];
                            } else {
                                $errorMessages[] = $error;
                            }
                        }
                    }
                    $errorMessage = !empty($errorMessages) ? implode('; ', array_slice($errorMessages, 0, 2)) : 'Validation Error';
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
