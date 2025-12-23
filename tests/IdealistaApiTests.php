<?php

namespace Tests;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Log;

/**
 * Tests para la API de Idealista
 *
 * Este archivo contiene casos de prueba para los diferentes endpoints de la API de Idealista.
 * Ejecuta cada test y almacena los resultados.
 */
class IdealistaApiTests
{
    protected IdealistaApiService $api;
    protected array $results = [];

    public function __construct(IdealistaApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Ejecuta todos los tests
     */
    public function runAllTests(): array
    {
        $this->results = [];

        // Tests de Properties
        $this->testListProperties();
        $this->testGetProperty();
        $this->testCreateProperty();
        $this->testUpdateProperty();
        $this->testDeactivateProperty();
        $this->testReactivateProperty();
        $this->testCloneProperty();
        $this->testListImages();
        $this->testReplaceImages();
        $this->testDeleteImages();

        // Tests de Customer
        $this->testGetPublicationInfo();

        // Tests de Contacts
        $this->testListContacts();
        $this->testGetContact();
        $this->testCreateContact();
        $this->testUpdateContact();
        $this->testDeleteContact();

        // Tests de Videos
        $this->testListVideos();
        $this->testCreateVideo();
        $this->testDeleteVideo();

        // Tests de Virtual Tours
        $this->testListVirtualTours();
        $this->testCreateVirtualTour();
        $this->testDeleteVirtualTour();

        return $this->results;
    }

    /**
     * Ejecuta un test y almacena el resultado
     */
    protected function executeTest(string $name, string $method, string $endpoint, array $query = [], ?array $body = null, ?int $expectedStatus = null): void
    {
        $startTime = microtime(true);

        try {
            $response = $this->api->call($method, $endpoint, $query, $body);

            $statusCode = $response['status_code'] ?? null;
            $success = true;

            if ($expectedStatus && $statusCode !== $expectedStatus) {
                $success = false;
            } elseif ($statusCode >= 400) {
                $success = false;
            }

            $this->results[] = [
                'name' => $name,
                'method' => $method,
                'endpoint' => $endpoint,
                'status_code' => $statusCode,
                'success' => $success,
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2),
                'response' => $response,
                'error' => null,
            ];

        } catch (\Exception $e) {
            $this->results[] = [
                'name' => $name,
                'method' => $method,
                'endpoint' => $endpoint,
                'status_code' => null,
                'success' => false,
                'execution_time' => round((microtime(true) - $startTime) * 1000, 2),
                'response' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    // ========== PROPERTIES TESTS ==========

    protected function testListProperties(): void
    {
        $this->executeTest(
            'Listar propiedades',
            'GET',
            '/v1/properties',
            ['page' => 1, 'size' => 10],
            null,
            200
        );
    }

    protected function testGetProperty(): void
    {
        // Necesitarías un propertyId real para este test
        $this->executeTest(
            'Obtener propiedad por ID',
            'GET',
            '/v1/properties/1',
            [],
            null,
            200
        );
    }

    protected function testCreateProperty(): void
    {
        $payload = [
            'ExternalId' => 'TEST-' . time(),
            'AgencyReference' => 'REF-TEST-001',
            'TypeId' => 1,
            'SubTypeId' => 2,
            'ContactTypeId' => 1,
            'PropertyAddress' => [
                'Street' => 'Calle de Prueba',
                'Number' => '1',
                'PostalCode' => '28001',
                'City' => 'Madrid',
                'Province' => 'Madrid',
                'Country' => 'ES',
            ],
            'TransactionTypeId' => 1,
            'Price' => 200000,
        ];

        $this->executeTest(
            'Crear propiedad',
            'POST',
            '/v1/properties',
            [],
            $payload,
            201
        );
    }

    protected function testUpdateProperty(): void
    {
        $payload = [
            'Price' => 250000,
        ];

        $this->executeTest(
            'Actualizar propiedad',
            'PUT',
            '/v1/properties/1',
            [],
            $payload,
            200
        );
    }

    protected function testDeactivateProperty(): void
    {
        $this->executeTest(
            'Desactivar propiedad',
            'POST',
            '/v1/properties/1/deactivate',
            [],
            null,
            200
        );
    }

    protected function testReactivateProperty(): void
    {
        $this->executeTest(
            'Reactivar propiedad',
            'POST',
            '/v1/properties/1/reactivate',
            [],
            null,
            200
        );
    }

    protected function testCloneProperty(): void
    {
        $payload = [
            'operation' => 'rent',
        ];

        $this->executeTest(
            'Clonar propiedad',
            'POST',
            '/v1/properties/1/clone',
            [],
            $payload,
            201
        );
    }

    protected function testListImages(): void
    {
        $this->executeTest(
            'Listar imágenes de propiedad',
            'GET',
            '/v1/properties/1/images',
            [],
            null,
            200
        );
    }

    protected function testReplaceImages(): void
    {
        $payload = [
            'images' => [
                [
                    'TypeId' => 1,
                    'Url' => 'https://example.com/image.jpg',
                    'SortingId' => 1,
                ],
            ],
        ];

        $this->executeTest(
            'Reemplazar imágenes de propiedad',
            'PUT',
            '/v1/properties/1/images',
            [],
            $payload,
            200
        );
    }

    protected function testDeleteImages(): void
    {
        $this->executeTest(
            'Eliminar imágenes de propiedad',
            'DELETE',
            '/v1/properties/1/images',
            [],
            null,
            200
        );
    }

    // ========== CUSTOMER TESTS ==========

    protected function testGetPublicationInfo(): void
    {
        $this->executeTest(
            'Obtener información de publicación del cliente',
            'GET',
            '/v1/customer/publishinfo',
            [],
            null,
            200
        );
    }

    // ========== CONTACTS TESTS ==========

    protected function testListContacts(): void
    {
        $this->executeTest(
            'Listar contactos',
            'GET',
            '/v1/contacts',
            ['page' => 1, 'size' => 10],
            null,
            200
        );
    }

    protected function testGetContact(): void
    {
        $this->executeTest(
            'Obtener contacto por ID',
            'GET',
            '/v1/contacts/1',
            [],
            null,
            200
        );
    }

    protected function testCreateContact(): void
    {
        $payload = [
            'ContactTypeId' => 1,
            'ContactInfo' => [
                [
                    'ContactInfoTypeId' => 1,
                    'Value' => 'test@example.com',
                ],
            ],
        ];

        $this->executeTest(
            'Crear contacto',
            'POST',
            '/v1/contacts',
            [],
            $payload,
            201
        );
    }

    protected function testUpdateContact(): void
    {
        $payload = [
            'ContactInfo' => [
                [
                    'ContactInfoTypeId' => 1,
                    'Value' => 'updated@example.com',
                ],
            ],
        ];

        $this->executeTest(
            'Actualizar contacto',
            'PUT',
            '/v1/contacts/1',
            [],
            $payload,
            200
        );
    }

    protected function testDeleteContact(): void
    {
        $this->executeTest(
            'Eliminar contacto',
            'DELETE',
            '/v1/contacts/1',
            [],
            null,
            200
        );
    }

    // ========== VIDEOS TESTS ==========

    protected function testListVideos(): void
    {
        $this->executeTest(
            'Listar videos de propiedad',
            'GET',
            '/v1/properties/1/videos',
            [],
            null,
            200
        );
    }

    protected function testCreateVideo(): void
    {
        $payload = [
            'TypeId' => 8,
            'Url' => 'https://www.youtube.com/watch?v=test123',
            'SortingId' => 1,
        ];

        $this->executeTest(
            'Crear video para propiedad',
            'POST',
            '/v1/properties/1/videos',
            [],
            $payload,
            201
        );
    }

    protected function testDeleteVideo(): void
    {
        $this->executeTest(
            'Eliminar video de propiedad',
            'DELETE',
            '/v1/properties/1/videos/1',
            [],
            null,
            200
        );
    }

    // ========== VIRTUAL TOURS TESTS ==========

    protected function testListVirtualTours(): void
    {
        $this->executeTest(
            'Listar tours virtuales de propiedad',
            'GET',
            '/v1/properties/1/virtualtours',
            [],
            null,
            200
        );
    }

    protected function testCreateVirtualTour(): void
    {
        $payload = [
            'TypeId' => 7,
            'Url' => 'https://example.com/virtual-tour',
            'SortingId' => 1,
        ];

        $this->executeTest(
            'Crear tour virtual para propiedad',
            'POST',
            '/v1/properties/1/virtualtours',
            [],
            $payload,
            201
        );
    }

    protected function testDeleteVirtualTour(): void
    {
        $this->executeTest(
            'Eliminar tour virtual de propiedad',
            'DELETE',
            '/v1/properties/1/virtualtours/1',
            [],
            null,
            200
        );
    }
}

