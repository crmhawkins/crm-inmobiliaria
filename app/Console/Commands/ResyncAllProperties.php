<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use App\Services\Fotocasa\FotocasaClient;
use App\Services\Idealista\IdealistaPropertiesService;
use App\Services\Idealista\IdealistaPropertyMapper;
use App\Services\PropertySyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResyncAllProperties extends Command
{
    protected $signature = 'property:resync-all
                            {--force : Ejecutar sin confirmación}
                            {--fotocasa-only : Sincronizar solo desde Fotocasa}
                            {--idealista-only : Sincronizar solo desde Idealista}';

    protected $description = 'Elimina todas las propiedades del CRM y las vuelve a sincronizar desde Fotocasa e Idealista';

    public function handle(): int
    {
        $syncFotocasa = !$this->option('idealista-only');
        $syncIdealista = !$this->option('fotocasa-only');

        $this->info('⚠️  ADVERTENCIA: Esta operación eliminará TODAS las propiedades del CRM');
        $this->newLine();

        $totalProperties = Inmuebles::count();
        $this->line("Total de propiedades en el CRM: {$totalProperties}");

        if ($totalProperties === 0) {
            $this->info('No hay propiedades para eliminar');
        }

        if (!$this->option('force')) {
            if (!$this->confirm('¿Estás seguro de que quieres continuar? Esta acción no se puede deshacer.')) {
                $this->info('Operación cancelada');
                return self::FAILURE;
            }
        }

        // Paso 1: Obtener todas las propiedades de las APIs antes de eliminar
        $this->info('📥 Obteniendo propiedades de las APIs...');
        $this->newLine();

        $fotocasaProperties = [];
        $idealistaProperties = [];

        if ($syncFotocasa) {
            try {
                $this->line('Obteniendo propiedades de Fotocasa...');
                $fotocasaClient = app(FotocasaClient::class);
                $fotocasaProperties = $fotocasaClient->getProperties(['size' => 1000]);
                $this->info("  ✅ Obtenidas " . count($fotocasaProperties) . " propiedades de Fotocasa");
            } catch (\Exception $e) {
                $this->error("  ❌ Error obteniendo propiedades de Fotocasa: " . $e->getMessage());
                if (!$this->option('force')) {
                    if (!$this->confirm('¿Continuar solo con Idealista?')) {
                        return self::FAILURE;
                    }
                    $syncFotocasa = false;
                }
            }
            $this->newLine();
        }

        if ($syncIdealista) {
            try {
                $this->line('Obteniendo propiedades de Idealista...');
                $idealistaService = app(IdealistaPropertiesService::class);

                // Obtener todas las propiedades (paginando si es necesario)
                $idealistaProperties = [];
                $page = 1;
                $size = 50;
                $hasMore = true;

                while ($hasMore) {
                    // Obtener solo propiedades activas
                    $idealistaResponse = $idealistaService->list($page, $size, 'active');

                    // Extraer propiedades de la respuesta
                    $pageProperties = [];
                    if (isset($idealistaResponse['properties'])) {
                        $pageProperties = $idealistaResponse['properties'];
                    } elseif (isset($idealistaResponse['data']['properties'])) {
                        $pageProperties = $idealistaResponse['data']['properties'];
                    } elseif (isset($idealistaResponse['data']['data']['properties'])) {
                        $pageProperties = $idealistaResponse['data']['data']['properties'];
                    }

                    if (!empty($pageProperties)) {
                        // Filtrar solo propiedades activas (por si acaso)
                        $activeProperties = array_filter($pageProperties, function($prop) {
                            $state = $prop['state'] ?? null;
                            return $state === 'active';
                        });

                        $idealistaProperties = array_merge($idealistaProperties, $activeProperties);
                        $this->line("  Página {$page}: " . count($activeProperties) . " propiedades activas obtenidas");
                        $page++;

                        // Si obtenemos menos propiedades de las solicitadas, no hay más páginas
                        if (count($pageProperties) < $size) {
                            $hasMore = false;
                        }
                    } else {
                        $hasMore = false;
                    }

                    // Pequeña pausa entre páginas
                    if ($hasMore) {
                        usleep(500000); // 0.5 segundos
                    }
                }

                $this->info("  ✅ Total obtenidas: " . count($idealistaProperties) . " propiedades de Idealista");
            } catch (\Exception $e) {
                $this->error("  ❌ Error obteniendo propiedades de Idealista: " . $e->getMessage());
                if (!$this->option('force')) {
                    if (!$this->confirm('¿Continuar solo con Fotocasa?')) {
                        return self::FAILURE;
                    }
                    $syncIdealista = false;
                }
            }
            $this->newLine();
        }

        // Paso 2: Eliminar todas las propiedades
        $this->info('🗑️  Eliminando todas las propiedades del CRM...');

        try {
            $deletedCount = Inmuebles::count();
            DB::table('inmuebles')->delete();
            $this->info("  ✅ Eliminadas {$deletedCount} propiedades");
        } catch (\Exception $e) {
            $this->error("  ❌ Error eliminando propiedades: " . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();

        // Paso 3: Sincronizar desde Idealista PRIMERO
        if ($syncIdealista && !empty($idealistaProperties)) {
            $this->info('📤 Sincronizando propiedades desde Idealista...');
            $this->newLine();

            $mapper = app(IdealistaPropertyMapper::class);
            $propertiesService = app(IdealistaPropertiesService::class);
            $controller = app(\App\Http\Controllers\InmueblesController::class);

            $successCount = 0;
            $errorCount = 0;
            $total = count($idealistaProperties);
            $bar = $this->output->createProgressBar($total);
            $bar->start();

            foreach ($idealistaProperties as $idealistaProp) {
                try {
                    $propertyId = $idealistaProp['propertyId'] ?? null;
                    if (!$propertyId) {
                        $errorCount++;
                        $bar->advance();
                        continue;
                    }

                    // Obtener detalles completos de la propiedad individual
                    try {
                        $fullProperty = $propertiesService->find($propertyId);
                        // Usar los datos completos si están disponibles
                        if (!empty($fullProperty)) {
                            $idealistaProp = array_merge($idealistaProp, $fullProperty);
                        }
                    } catch (\Exception $e) {
                        // Continuar con los datos de la lista si no se puede obtener individual
                        Log::debug('No se pudieron obtener detalles completos de Idealista', [
                            'property_id' => $propertyId,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Obtener imágenes de cada propiedad
                    $images = [];
                    try {
                        $imagesResponse = $propertiesService->listImages($propertyId);
                        if (isset($imagesResponse['images'])) {
                            $images = $imagesResponse['images'];
                        } elseif (isset($imagesResponse['data']['images'])) {
                            $images = $imagesResponse['data']['images'];
                        }
                    } catch (\Exception $e) {
                        // Continuar sin imágenes si hay error
                        Log::debug('No se pudieron obtener imágenes de Idealista', [
                            'property_id' => $propertyId,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    // Usar el método del controller para sincronizar (que usa el mapper internamente)
                    // O usar el mapper directamente
                    $mapped = $mapper->map($idealistaProp, $images);

                    // Crear el inmueble en la base de datos
                    $attributes = $mapped['attributes'];

                    // Asignar tipo de vivienda si existe
                    if ($mapped['tipo_vivienda_label']) {
                        $tipoVivienda = \App\Models\TipoVivienda::where('nombre', $mapped['tipo_vivienda_label'])->first();
                        if ($tipoVivienda) {
                            $attributes['tipo_vivienda_id'] = $tipoVivienda->id;
                        }
                    }

                    // Asignar transaction_type_id, visibility_mode_id, floor_id, orientation_id si están en el mapeo
                    if (isset($mapped['transaction_type_id'])) {
                        $attributes['transaction_type_id'] = $mapped['transaction_type_id'];
                    }
                    if (isset($mapped['visibility_mode_id'])) {
                        $attributes['visibility_mode_id'] = $mapped['visibility_mode_id'];
                    }
                    if (isset($mapped['floor_id'])) {
                        $attributes['floor_id'] = $mapped['floor_id'];
                    }
                    if (isset($mapped['orientation_id'])) {
                        $attributes['orientation_id'] = $mapped['orientation_id'];
                    }

                    $inmueble = Inmuebles::create($attributes);

                    $successCount++;

                    // Pequeña pausa para no sobrecargar la API
                    usleep(300000); // 0.3 segundos
                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Error sincronizando propiedad de Idealista', [
                        'property_id' => $idealistaProp['propertyId'] ?? null,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("✅ Sincronización desde Idealista completada:");
            $this->line("  Exitosas: {$successCount}");
            $this->line("  Errores: {$errorCount}");
            $this->line("  Total: {$total}");
            $this->newLine();
        }

        // Paso 4: Sincronizar desde Fotocasa DESPUÉS
        if ($syncFotocasa && !empty($fotocasaProperties)) {
            $this->info('📤 Sincronizando referencias desde Fotocasa...');
            $this->newLine();

            $this->warn('⚠️  Nota: Fotocasa solo devuelve ExternalId y AgencyReference, no datos completos.');
            $this->line('   Se actualizarán los external_id de las propiedades existentes si coinciden.');
            $this->newLine();

            $updatedCount = 0;
            $createdCount = 0;
            $notFoundCount = 0;

            $bar = $this->output->createProgressBar(count($fotocasaProperties));
            $bar->start();

            foreach ($fotocasaProperties as $fotocasaProp) {
                $externalId = $fotocasaProp['ExternalId'] ?? null;
                $agencyRef = $fotocasaProp['AgencyReference'] ?? null;

                if (!$externalId) {
                    $bar->advance();
                    continue;
                }

                try {
                    // Buscar si existe una propiedad con este external_id o ID que coincida
                    $inmueble = Inmuebles::where('external_id', $externalId)
                        ->orWhere('id', $externalId)
                        ->first();

                    if ($inmueble) {
                        // Actualizar external_id si no estaba asignado
                        if (!$inmueble->external_id) {
                            $inmueble->update(['external_id' => $externalId]);
                            $updatedCount++;
                        }
                    } else {
                        // No crear propiedades sin datos completos
                        // Solo contar como no encontradas
                        $notFoundCount++;
                    }
                } catch (\Exception $e) {
                    Log::error('Error procesando propiedad de Fotocasa', [
                        'external_id' => $externalId,
                        'error' => $e->getMessage(),
                    ]);
                }

                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);
            $this->info("✅ Procesamiento de referencias de Fotocasa completado:");
            $this->line("  Actualizadas: {$updatedCount}");
            $this->line("  No encontradas (solo IDs, sin datos): {$notFoundCount}");
            $this->line("  Total procesadas: " . count($fotocasaProperties));
            $this->newLine();
        }

        // Resumen final
        $finalCount = Inmuebles::count();
        $this->info('📊 Resumen final:');
        $this->line("  Propiedades en el CRM: {$finalCount}");
        $this->newLine();

        if ($finalCount > 0) {
            $this->info('✅ Resincronización completada exitosamente');
            return self::SUCCESS;
        } else {
            $this->warn('⚠️  No se sincronizaron propiedades. Revisa los logs para más información.');
            return self::FAILURE;
        }
    }
}
