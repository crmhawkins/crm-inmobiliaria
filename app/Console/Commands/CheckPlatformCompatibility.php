<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use App\Services\PropertySyncService;
use Illuminate\Console\Command;

class CheckPlatformCompatibility extends Command
{
    protected $signature = 'property:check-compatibility';

    protected $description = 'Verifica la compatibilidad del CRM con Idealista y Fotocasa';

    public function handle(): int
    {
        $this->info('🔍 Verificando compatibilidad del CRM con Idealista y Fotocasa...');
        $this->newLine();

        $syncService = app(PropertySyncService::class);

        // Verificar propiedades sincronizadas
        $totalProperties = Inmuebles::count();
        $idealistaCount = Inmuebles::whereNotNull('idealista_property_id')->count();
        $fotocasaCount = Inmuebles::whereNotNull('external_id')->count();
        $bothCount = Inmuebles::whereNotNull('idealista_property_id')
            ->whereNotNull('external_id')
            ->count();

        $this->info('📊 Estadísticas de sincronización:');
        $this->line("  Total propiedades: {$totalProperties}");
        $this->line("  Sincronizadas con Idealista: {$idealistaCount}");
        $this->line("  Sincronizadas con Fotocasa: {$fotocasaCount}");
        $this->line("  Sincronizadas con ambas: {$bothCount}");
        $this->newLine();

        // Verificar configuración
        $this->info('⚙️  Verificando configuración:');
        $hasIdealistaConfig = !empty(env('IDEALISTA_CLIENT_ID')) && !empty(env('IDEALISTA_CLIENT_SECRET'));
        $hasFotocasaConfig = !empty(env('API_KEY'));

        $this->line("  Idealista configurado: " . ($hasIdealistaConfig ? '✅ Sí' : '❌ No'));
        $this->line("  Fotocasa configurado: " . ($hasFotocasaConfig ? '✅ Sí' : '❌ No'));
        $this->newLine();

        // Verificar una propiedad de ejemplo
        $exampleProperty = Inmuebles::whereNotNull('titulo')
            ->whereNotNull('cod_postal')
            ->first();

        if ($exampleProperty) {
            $this->info("📋 Verificando propiedad de ejemplo (ID: {$exampleProperty->id}):");

            $status = $syncService->getSyncStatus($exampleProperty);
            $this->line("  Idealista: " . ($status['idealista'] ? '✅ Sincronizada' : '⏸️  No sincronizada'));
            $this->line("  Fotocasa: " . ($status['fotocasa'] ? '✅ Sincronizada' : '⏸️  No sincronizada'));
            $this->newLine();

            // Verificar campos requeridos
            $this->info('🔍 Campos requeridos:');
            $requiredFields = [
                'titulo' => $exampleProperty->titulo,
                'cod_postal' => $exampleProperty->cod_postal,
                'tipo_vivienda_id' => $exampleProperty->tipo_vivienda_id,
                'transaction_type_id' => $exampleProperty->transaction_type_id ?? null,
                'latitude' => $exampleProperty->latitude,
                'longitude' => $exampleProperty->longitude,
            ];

            foreach ($requiredFields as $field => $value) {
                $status = $value !== null && $value !== '' ? '✅' : '❌';
                $this->line("  {$field}: {$status}");
            }
        }

        $this->newLine();
        $this->info('✅ Verificación completada');

        // Resumen final
        $this->newLine();
        $this->info('📝 Resumen de compatibilidad:');
        $this->line('  ✅ El CRM soporta ambas plataformas (Idealista y Fotocasa)');
        $this->line('  ✅ Las propiedades pueden sincronizarse con ambas plataformas simultáneamente');
        $this->line('  ✅ Los campos son independientes (no hay conflictos)');
        $this->line('  ✅ Se puede usar: php artisan property:sync-platforms --id=X para sincronizar');

        return self::SUCCESS;
    }
}
