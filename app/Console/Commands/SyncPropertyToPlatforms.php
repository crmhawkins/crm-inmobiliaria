<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use App\Services\PropertySyncService;
use Illuminate\Console\Command;

class SyncPropertyToPlatforms extends Command
{
    protected $signature = 'property:sync-platforms
                            {--id= : ID de la propiedad a sincronizar}
                            {--idealista-only : Sincronizar solo con Idealista}
                            {--fotocasa-only : Sincronizar solo con Fotocasa}
                            {--update : Actualizar propiedad existente en lugar de crear nueva}
                            {--all : Sincronizar todas las propiedades activas}';

    protected $description = 'Sincroniza propiedades con Idealista y/o Fotocasa';

    public function handle(): int
    {
        $this->info('🔄 Sincronizando propiedades con plataformas...');
        $this->newLine();

        $syncIdealista = !$this->option('fotocasa-only');
        $syncFotocasa = !$this->option('idealista-only');

        if ($this->option('all')) {
            return $this->syncAllProperties($syncIdealista, $syncFotocasa);
        }

        $id = $this->option('id');
        if (!$id) {
            $this->error('Debes especificar --id o usar --all');
            return self::FAILURE;
        }

        $inmueble = Inmuebles::find($id);
        if (!$inmueble) {
            $this->error("No se encontró la propiedad con ID: {$id}");
            return self::FAILURE;
        }

        return $this->syncProperty($inmueble, $syncIdealista, $syncFotocasa);
    }

    private function syncProperty(
        Inmuebles $inmueble,
        bool $syncIdealista,
        bool $syncFotocasa
    ): int {
        $this->info("Sincronizando propiedad ID: {$inmueble->id} - {$inmueble->titulo}");
        $this->newLine();

        $syncService = app(PropertySyncService::class);
        $update = $this->option('update');

        $results = [
            'idealista' => null,
            'fotocasa' => null,
        ];

        // Sincronizar con Idealista
        if ($syncIdealista) {
            $this->line('📤 Sincronizando con Idealista...');
            $results['idealista'] = $syncService->syncToIdealista($inmueble, $update);

            if ($results['idealista']['success']) {
                $this->info('  ✅ Sincronizado con Idealista correctamente');
                if (isset($results['idealista']['response']['propertyId'])) {
                    $this->line("  Property ID: {$results['idealista']['response']['propertyId']}");
                }
            } else {
                $this->error('  ❌ Error: ' . $results['idealista']['error']);
            }
            $this->newLine();
        }

        // Sincronizar con Fotocasa
        if ($syncFotocasa) {
            $this->line('📤 Sincronizando con Fotocasa...');

            // Usar el método del controller para construir el payload
            $controller = app(\App\Http\Controllers\InmueblesController::class);
            $payloadBuilder = function($inmueble) use ($controller) {
                return $controller->buildFotocasaPayload($inmueble);
            };

            $results['fotocasa'] = $syncService->syncToFotocasa($inmueble, $payloadBuilder);

            if ($results['fotocasa']['success']) {
                $this->info('  ✅ Sincronizado con Fotocasa correctamente');
                if (isset($results['fotocasa']['response']['ExternalId'])) {
                    $this->line("  External ID: {$results['fotocasa']['response']['ExternalId']}");
                }
            } else {
                $this->error('  ❌ Error: ' . $results['fotocasa']['error']);
            }
            $this->newLine();
        }

        // Resumen
        $this->info('📊 Resumen:');
        $successCount = 0;
        if ($syncIdealista) {
            $status = $results['idealista']['success'] ? '✅' : '❌';
            $this->line("  Idealista: {$status}");
            if ($results['idealista']['success']) $successCount++;
        }
        if ($syncFotocasa) {
            $status = $results['fotocasa']['success'] ? '✅' : '❌';
            $this->line("  Fotocasa: {$status}");
            if ($results['fotocasa']['success']) $successCount++;
        }

        return ($successCount > 0) ? self::SUCCESS : self::FAILURE;
    }

    private function syncAllProperties(bool $syncIdealista, bool $syncFotocasa): int
    {
        $this->info('Obteniendo propiedades activas...');

        $properties = Inmuebles::where('estado', '!=', 'eliminado')
            ->whereNotNull('titulo')
            ->whereNotNull('cod_postal')
            ->get();

        $total = $properties->count();
        $this->info("Total propiedades a sincronizar: {$total}");
        $this->newLine();

        if ($total === 0) {
            $this->warn('No hay propiedades para sincronizar');
            return self::FAILURE;
        }

        $syncService = app(PropertySyncService::class);
        $update = $this->option('update');

        $successCount = 0;
        $errorCount = 0;
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($properties as $inmueble) {
            try {
                if ($syncIdealista) {
                    $idealistaResult = $syncService->syncToIdealista($inmueble, $update);
                    if (!$idealistaResult['success']) {
                        $errorCount++;
                    } else {
                        $successCount++;
                    }
                }

                if ($syncFotocasa) {
                    $controller = app(\App\Http\Controllers\InmueblesController::class);
                    $payloadBuilder = function($inmueble) use ($controller) {
                        return $controller->buildFotocasaPayload($inmueble);
                    };

                    $fotocasaResult = $syncService->syncToFotocasa($inmueble, $payloadBuilder);
                    if (!$fotocasaResult['success']) {
                        $errorCount++;
                    } else {
                        $successCount++;
                    }
                }

                // Pequeña pausa para no sobrecargar las APIs
                usleep(200000); // 0.2 segundos
            } catch (\Exception $e) {
                $errorCount++;
                $this->newLine();
                $this->error("Error sincronizando propiedad ID {$inmueble->id}: " . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Sincronización completada:");
        $this->line("  Exitosas: {$successCount}");
        $this->line("  Errores: {$errorCount}");
        $this->line("  Total: {$total}");

        return $errorCount === 0 ? self::SUCCESS : self::FAILURE;
    }
}
