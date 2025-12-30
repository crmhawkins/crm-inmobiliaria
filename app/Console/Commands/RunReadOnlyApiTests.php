<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RunReadOnlyApiTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:tests-readonly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta tests de solo lectura (GET) para las APIs de Idealista y Fotocasa';

    protected array $results = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🧪 Ejecutando tests de solo lectura (GET) para las APIs...');
        $this->newLine();

        // Tests de Idealista
        $this->info('📡 Tests de Idealista API:');
        $this->runIdealistaTests();

        $this->newLine();

        // Tests de Fotocasa
        $this->info('📡 Tests de Fotocasa API:');
        $this->runFotocasaTests();

        // Resumen
        $this->newLine();
        $this->displaySummary();

        return self::SUCCESS;
    }

    private function runIdealistaTests(): void
    {
        try {
            $apiService = app(IdealistaApiService::class);

            // Test 1: Listar propiedades
            $this->testIdealistaEndpoint(
                'Listar propiedades',
                function() use ($apiService) {
                    return $apiService->call('GET', '/v1/properties', ['page' => 1, 'size' => 10]);
                }
            );

            // Test 2: Obtener información de publicación del cliente
            $this->testIdealistaEndpoint(
                'Obtener información de publicación',
                function() use ($apiService) {
                    return $apiService->call('GET', '/v1/customer/publishinfo');
                }
            );

            // Test 3: Listar contactos
            $this->testIdealistaEndpoint(
                'Listar contactos',
                function() use ($apiService) {
                    return $apiService->call('GET', '/v1/contacts', ['page' => 1, 'size' => 10]);
                }
            );

            // Test 4: Obtener propiedades activas (usando el servicio de propiedades)
            try {
                $propertiesService = app(\App\Services\Idealista\IdealistaPropertiesService::class);
                $response = $propertiesService->list(1, 10, 'active');
                $this->testIdealistaEndpoint(
                    'Obtener propiedades activas (PropertiesService)',
                    function() use ($response) {
                        return ['status_code' => 200, 'data' => $response];
                    }
                );
            } catch (\Exception $e) {
                $this->warn("   ⚠️  Obtener propiedades activas - Error: {$e->getMessage()}");
            }

        } catch (\Exception $e) {
            $this->error("Error en tests de Idealista: {$e->getMessage()}");
            Log::error('Error en tests de Idealista', ['error' => $e->getMessage()]);
        }
    }

    private function runFotocasaTests(): void
    {
        $apiKey = env('API_KEY');

        if (!$apiKey) {
            $this->warn('⚠️  API_KEY no configurada. Saltando tests de Fotocasa.');
            return;
        }

        // Nota: Fotocasa parece ser principalmente para enviar datos (POST)
        // Si hay endpoints GET, se pueden agregar aquí
        $this->info('   ℹ️  Fotocasa API es principalmente para envío de datos (POST).');
        $this->info('   ℹ️  No hay endpoints GET disponibles para testear.');
    }

    private function testIdealistaEndpoint(string $name, callable $test): void
    {
        $startTime = microtime(true);

        try {
            $response = $test();
            $statusCode = $response['status_code'] ?? null;
            $success = $statusCode >= 200 && $statusCode < 400;
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->results[] = [
                'api' => 'Idealista',
                'name' => $name,
                'success' => $success,
                'status_code' => $statusCode,
                'execution_time' => $executionTime,
                'error' => null,
            ];

            if ($success) {
                $this->line("   ✅ {$name} - Status: {$statusCode} - Tiempo: {$executionTime}ms");
            } else {
                $this->warn("   ⚠️  {$name} - Status: {$statusCode} - Tiempo: {$executionTime}ms");
            }

        } catch (\Exception $e) {
            $executionTime = round((microtime(true) - $startTime) * 1000, 2);

            $this->results[] = [
                'api' => 'Idealista',
                'name' => $name,
                'success' => false,
                'status_code' => null,
                'execution_time' => $executionTime,
                'error' => $e->getMessage(),
            ];

            $this->error("   ❌ {$name} - Error: {$e->getMessage()}");
        }
    }

    private function displaySummary(): void
    {
        $this->info('📊 Resumen de Tests:');
        $this->newLine();

        $total = count($this->results);
        $successful = count(array_filter($this->results, fn($r) => $r['success']));
        $failed = $total - $successful;

        $this->table(
            ['API', 'Test', 'Estado', 'Status Code', 'Tiempo (ms)', 'Error'],
            array_map(function($result) {
                return [
                    $result['api'],
                    $result['name'],
                    $result['success'] ? '✅ OK' : '❌ FALLÓ',
                    $result['status_code'] ?? 'N/A',
                    $result['execution_time'],
                    $result['error'] ?? '-',
                ];
            }, $this->results)
        );

        $this->newLine();
        $this->info("Total: {$total} | Exitosos: {$successful} | Fallidos: {$failed}");
    }
}
