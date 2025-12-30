<?php

namespace App\Console\Commands;

use App\Http\Controllers\InmueblesController;
use App\Models\Inmuebles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncFotocasaProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:sync-properties
                            {--all : Sincronizar todas las propiedades}
                            {--limit=50 : Límite de propiedades a sincronizar}
                            {--dry-run : Ejecutar sin enviar a Fotocasa}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza propiedades locales con Fotocasa API (envía/actualiza propiedades)';

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

        $dryRun = $this->option('dry-run');
        $syncAll = $this->option('all');
        $limit = (int) $this->option('limit');

        $this->info('🔄 Iniciando sincronización con Fotocasa...');
        $this->newLine();

        // Obtener propiedades a sincronizar
        $query = Inmuebles::query();

        // Si no es --all, solo sincronizar las que tienen external_id o las más recientes
        if (!$syncAll) {
            // Priorizar propiedades con external_id (ya enviadas pero pueden necesitar actualización)
            // o propiedades sin external_id (nuevas)
            $query->where(function($q) {
                $q->whereNotNull('external_id')
                  ->orWhereNull('external_id');
            });
        }

        $totalProperties = $query->count();
        $properties = $query->limit($limit)->get();

        if ($properties->isEmpty()) {
            $this->warn('No se encontraron propiedades para sincronizar.');
            return self::SUCCESS;
        }

        $this->info("📊 Total de propiedades a sincronizar: {$properties->count()} de {$totalProperties}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  MODO DRY-RUN: No se enviarán datos a Fotocasa');
            $this->newLine();
        }

        $controller = new InmueblesController();
        $summary = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
        ];

        $processed = 0;
        $total = $properties->count();

        foreach ($properties as $inmueble) {
            $processed++;
            $this->line("Procesando {$processed}/{$total}: Propiedad ID {$inmueble->id}");
            try {
                if ($dryRun) {
                    $this->newLine();
                    $this->line("  [DRY-RUN] Propiedad ID: {$inmueble->id} - {$inmueble->titulo}");
                    $summary['skipped']++;
                } else {
                    // Asignar valores por defecto para campos faltantes
                    $updated = $this->setDefaultFields($inmueble);

                    if ($updated) {
                        $this->newLine();
                        $this->info("  ℹ️  Propiedad ID {$inmueble->id}: Se asignaron valores por defecto");
                    }

                    // Verificar campos requeridos críticos
                    $missingFields = $this->checkCriticalFields($inmueble);

                    if (!empty($missingFields)) {
                        $summary['failed']++;
                        $this->newLine();
                        $this->warn("  ⚠️  Propiedad ID {$inmueble->id} falta campos críticos: " . implode(', ', $missingFields));
                        Log::warning('Propiedad con campos críticos faltantes para Fotocasa', [
                            'inmueble_id' => $inmueble->id,
                            'missing_fields' => $missingFields,
                        ]);
                        continue;
                    }

                    // Enviar a Fotocasa
                    $this->newLine();
                    $this->line("  🔄 Enviando propiedad ID {$inmueble->id} a Fotocasa...");

                    try {
                        $response = $controller->sendToFotocasa($inmueble);

                        // sendToFotocasa devuelve un JsonResponse
                        $statusCode = $response->getStatusCode();
                        $responseData = $response->getData(true); // getData(true) devuelve array en lugar de objeto

                        $this->line("  📡 Status Code: {$statusCode}");

                        if ($statusCode === 200) {
                            $summary['success']++;
                            $this->info("  ✅ Propiedad ID {$inmueble->id} sincronizada correctamente");

                            // Actualizar external_id si no existe
                            if (!$inmueble->external_id) {
                                $inmueble->external_id = (string) $inmueble->id;
                                $inmueble->save();
                            }
                        } else {
                            $summary['failed']++;
                            $errorMsg = 'Error desconocido';

                            if ($statusCode === 422 && isset($responseData['errors'])) {
                                // Error de validación
                                $errors = json_encode($responseData['errors'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
                                $errorMsg = "Validación fallida:\n{$errors}";
                            } elseif (isset($responseData['message'])) {
                                $errorMsg = $responseData['message'];
                            } elseif (isset($responseData['error'])) {
                                $errorMsg = $responseData['error'];
                            } else {
                                $errorMsg = $response->getContent();
                            }

                            $this->error("  ❌ Propiedad ID {$inmueble->id} (Status {$statusCode}): {$errorMsg}");
                            Log::warning('Error sincronizando propiedad con Fotocasa', [
                                'inmueble_id' => $inmueble->id,
                                'status_code' => $statusCode,
                                'response' => $responseData,
                            ]);
                        }
                    } catch (\Exception $e) {
                        $summary['failed']++;
                        $this->error("  ❌ Propiedad ID {$inmueble->id}: " . $e->getMessage());
                        Log::error('Error al sincronizar con Fotocasa', [
                            'inmueble_id' => $inmueble->id,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    // Pequeña pausa para no sobrecargar la API
                    usleep(500000); // 0.5 segundos
                }
            } catch (\Exception $e) {
                $summary['failed']++;
                $this->error("  ❌ Error procesando propiedad ID {$inmueble->id}: " . $e->getMessage());
                Log::error('Error sincronizando propiedad con Fotocasa', [
                    'inmueble_id' => $inmueble->id,
                    'error' => $e->getMessage(),
                ]);
            }

            $this->newLine();
        }

        $this->newLine();

        // Resumen
        $this->info('📊 Resumen de sincronización:');
        $this->line("   ✅ Exitosas: {$summary['success']}");
        $this->line("   ❌ Fallidas: {$summary['failed']}");
        if ($dryRun) {
            $this->line("   ⏭️  Omitidas (dry-run): {$summary['skipped']}");
        }

        if ($dryRun) {
            $this->newLine();
            $this->comment('⚠️  MODO DRY-RUN: No se realizaron cambios reales.');
        }

        return self::SUCCESS;
    }

    /**
     * Asigna valores por defecto a campos faltantes
     */
    private function setDefaultFields(Inmuebles $inmueble): bool
    {
        $updated = false;

        // No asignar inmobiliaria - el método sendToFotocasa manejará el caso null
        // usando safeString que convierte null a string vacío

        // Asignar building_subtype_id basado en tipo_vivienda_id si no existe
        if (!$inmueble->building_subtype_id && $inmueble->tipo_vivienda_id) {
            $defaultSubtypes = [
                1 => 9,  // Flat -> Flat
                2 => 13, // House -> House
                3 => 48, // Commercial store -> Residential
                4 => 51, // Office -> Offices
                5 => 48, // Building -> Residential
                6 => 56, // Land -> Residential land
                7 => 62, // Industrial building -> Moto
                8 => 68, // Garage -> Moto
                12 => 90, // Storage room
            ];

            $inmueble->building_subtype_id = $defaultSubtypes[$inmueble->tipo_vivienda_id] ?? 9;
            $updated = true;
        }

        // Asignar visibility_mode_id por defecto
        if (!$inmueble->visibility_mode_id) {
            $inmueble->visibility_mode_id = 2; // Street
            $updated = true;
        }

        // Asignar coordenadas por defecto si no existen (Madrid centro)
        if (!$inmueble->latitude || !$inmueble->longitude) {
            $inmueble->latitude = 40.4168;
            $inmueble->longitude = -3.7038;
            $updated = true;
        }

        if ($updated) {
            $inmueble->save();
        }

        return $updated;
    }

    /**
     * Verifica campos críticos requeridos para Fotocasa
     */
    private function checkCriticalFields(Inmuebles $inmueble): array
    {
        $missing = [];

        // Campos críticos que no se pueden asignar automáticamente
        if (!$inmueble->tipo_vivienda_id) {
            $missing[] = 'tipo_vivienda_id';
        }

        if (!$inmueble->transaction_type_id) {
            $missing[] = 'transaction_type_id';
        }

        return $missing;
    }
}
