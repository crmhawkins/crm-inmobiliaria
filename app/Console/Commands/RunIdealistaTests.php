<?php

namespace App\Console\Commands;

use App\Services\Idealista\IdealistaApiService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Tests\IdealistaApiTests;
use Throwable;

class RunIdealistaTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'idealista:run-tests
        {--output= : Ruta del archivo de salida (por defecto: test-results-{timestamp}.json)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ejecuta todos los tests de la API de Idealista y guarda los resultados';

    public function __construct(private readonly IdealistaApiService $api)
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("Iniciando tests de la API de Idealista...");

        try {
            $testRunner = new IdealistaApiTests($this->api);
            $results = $testRunner->runAllTests();

            $this->info("Se ejecutaron " . count($results) . " tests");

            // Mostrar resumen
            $successCount = count(array_filter($results, fn($r) => $results['success'] ?? false));
            $failureCount = count($results) - $successCount;

            $this->info("\n=== Resumen ===");
            $this->info("Total de tests: " . count($results));
            $this->info("Exitosos: {$successCount}");
            $this->info("Fallidos: {$failureCount}");

            // Mostrar detalles
            $this->newLine();
            $this->info("=== Detalles ===");
            foreach ($results as $result) {
                $status = $result['success'] ? '✓' : '✗';
                $statusColor = $result['success'] ? 'green' : 'red';
                $this->line("<fg={$statusColor}>{$status}</> {$result['name']} - Status: {$result['status_code']} - Tiempo: {$result['execution_time']}ms");

                if (!$result['success'] && $result['error']) {
                    $this->error("  Error: {$result['error']}");
                }
            }

            // Guardar resultados en JSON
            $outputFile = $this->option('output')
                ?: storage_path('app/test-results-' . date('Y-m-d_H-i-s') . '.json');

            file_put_contents($outputFile, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            $this->info("\nResultados guardados en: {$outputFile}");

            return self::SUCCESS;

        } catch (Throwable $e) {
            $this->error("Error ejecutando tests: " . $e->getMessage());
            $this->error($e->getTraceAsString());
            Log::error('Error en RunIdealistaTests', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return self::FAILURE;
        }
    }
}

