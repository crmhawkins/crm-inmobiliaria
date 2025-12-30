<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use App\Services\Fotocasa\FotocasaClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TestFotocasaCompleteData extends Command
{
    protected $signature = 'fotocasa:test-complete-data
                            {--external-id= : ExternalId específico para probar}
                            {--limit=5 : Número de propiedades a verificar}';

    protected $description = 'Verifica si hay propiedades en el CRM que coincidan con Fotocasa y muestra sus datos completos';

    public function handle(): int
    {
        $apiKey = env('API_KEY');

        if (!$apiKey) {
            $this->error('⚠️  API_KEY no configurada en el archivo .env');
            return self::FAILURE;
        }

        $fotocasaClient = new FotocasaClient($apiKey);
        $externalId = $this->option('external-id');
        $limit = (int) $this->option('limit');

        $this->info('🔍 Buscando propiedades en Fotocasa y verificando datos completos en CRM...');
        $this->newLine();

        try {
            // Obtener lista de propiedades de Fotocasa
            $fotocasaProperties = $fotocasaClient->getProperties();

            if (!is_array($fotocasaProperties) || empty($fotocasaProperties)) {
                $this->warn('⚠️  No se encontraron propiedades en Fotocasa');
                return self::FAILURE;
            }

            $this->info("✅ Encontradas " . count($fotocasaProperties) . " propiedades en Fotocasa");
            $this->newLine();

            // Si se especificó un ExternalId, buscar solo ese
            if ($externalId) {
                $this->checkProperty($externalId);
                return self::SUCCESS;
            }

            // Verificar las primeras N propiedades
            $checked = 0;
            $foundWithData = 0;

            foreach ($fotocasaProperties as $fotocasaProp) {
                if ($checked >= $limit) break;

                $extId = $fotocasaProp['ExternalId'] ?? null;
                if (!$extId) continue;

                $checked++;
                $this->line("Verificando ExternalId: {$extId}");

                if ($this->checkProperty($extId)) {
                    $foundWithData++;
                }
                $this->newLine();
            }

            $this->info("📊 Resumen:");
            $this->line("   Verificadas: {$checked}");
            $this->line("   Con datos completos en CRM: {$foundWithData}");
            $this->line("   Sin datos completos: " . ($checked - $foundWithData));

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function checkProperty(string $externalId): bool
    {
        // Buscar en el CRM por ID o external_id
        $inmueble = Inmuebles::find($externalId);

        if (!$inmueble) {
            $inmueble = Inmuebles::where('external_id', $externalId)->first();
        }

        if (!$inmueble) {
            $this->warn("  ⚠️  No existe en el CRM");
            return false;
        }

        $this->info("  ✅ Encontrada en CRM (ID: {$inmueble->id})");

        // Verificar datos completos
        $hasCompleteData = false;
        $dataFields = [];

        if ($inmueble->titulo) {
            $dataFields[] = "Título: " . substr($inmueble->titulo, 0, 50);
            $hasCompleteData = true;
        }

        if ($inmueble->descripcion) {
            $dataFields[] = "Descripción: " . substr($inmueble->descripcion, 0, 100) . "...";
            $hasCompleteData = true;
        }

        if ($inmueble->m2) {
            $dataFields[] = "m²: {$inmueble->m2}";
            $hasCompleteData = true;
        }

        if ($inmueble->habitaciones) {
            $dataFields[] = "Habitaciones: {$inmueble->habitaciones}";
            $hasCompleteData = true;
        }

        if ($inmueble->banos) {
            $dataFields[] = "Baños: {$inmueble->banos}";
            $hasCompleteData = true;
        }

        if ($inmueble->galeria) {
            $galeria = json_decode($inmueble->galeria, true);
            if (is_array($galeria) && !empty($galeria)) {
                $dataFields[] = "Imágenes: " . count($galeria);
                $hasCompleteData = true;
            }
        }

        if ($inmueble->caracteristicas) {
            $caracteristicas = json_decode($inmueble->caracteristicas, true);
            if (is_array($caracteristicas) && !empty($caracteristicas)) {
                $dataFields[] = "Características: " . count($caracteristicas);
                $hasCompleteData = true;
            }
        }

        if ($inmueble->valor_referencia) {
            $dataFields[] = "Precio: {$inmueble->valor_referencia}";
            $hasCompleteData = true;
        }

        if ($inmueble->ubicacion) {
            $dataFields[] = "Ubicación: {$inmueble->ubicacion}";
            $hasCompleteData = true;
        }

        if ($hasCompleteData) {
            $this->info("  ✅✅ DATOS COMPLETOS ENCONTRADOS:");
            foreach ($dataFields as $field) {
                $this->line("     - {$field}");
            }
            return true;
        } else {
            $this->warn("  ⚠️  Existe pero sin datos completos");
            return false;
        }
    }
}
