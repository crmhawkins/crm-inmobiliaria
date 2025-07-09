<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;
use App\Http\Controllers\InmueblesController;
use Illuminate\Support\Facades\Log;

class ImportProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:import {--limit=10 : Number of properties to import} {--dry-run : Test without saving to database}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import properties from JSON file and send to Fotocasa API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting property import from JSON...');

        try {
            // Leer el archivo JSON
            $jsonPath = base_path('viviendas2_formateado.json');
            if (!file_exists($jsonPath)) {
                $this->error('JSON file not found at: ' . $jsonPath);
                return 1;
            }

            $jsonContent = file_get_contents($jsonPath);
            $viviendas = json_decode($jsonContent, true);

            if (!$viviendas) {
                $this->error('Error parsing JSON file');
                return 1;
            }

            $limit = $this->option('limit');
            $dryRun = $this->option('dry-run');

            $this->info("Found " . count($viviendas) . " properties in JSON");
            $this->info("Importing first {$limit} properties" . ($dryRun ? ' (DRY RUN)' : ''));

            $imported = 0;
            $errors = [];
            $controller = new InmueblesController();

            $progressBar = $this->output->createProgressBar(min($limit, count($viviendas)));
            $progressBar->start();

            foreach ($viviendas as $id => $vivienda) {
                if ($imported >= $limit) break;

                try {
                    // Convertir datos del JSON al formato del CRM
                    $inmuebleData = $this->convertJsonToInmuebleData($vivienda, $id);

                    if (!$dryRun) {
                        // Crear el inmueble en la base de datos
                        $inmueble = Inmuebles::create($inmuebleData);

                        // Enviar a Fotocasa
                        $fotocasaResponse = $controller->sendToFotocasa($inmueble);

                        $this->line("\n✅ Imported: {$vivienda['titulo']} (ID: {$inmueble->id}) - Fotocasa: {$fotocasaResponse->getStatusCode()}");
                    } else {
                        $this->line("\n🔍 Would import: {$vivienda['titulo']}");
                    }

                    $imported++;

                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'titulo' => $vivienda['titulo'] ?? 'Sin título',
                        'error' => $e->getMessage()
                    ];

                    $this->line("\n❌ Error importing " . ($vivienda['titulo'] ?? 'property') . ": {$e->getMessage()}");

                    Log::error('Error importing property', [
                        'id' => $id,
                        'error' => $e->getMessage(),
                        'data' => $vivienda
                    ]);
                }

                $progressBar->advance();

                // Pausa pequeña para no sobrecargar la API
                if (!$dryRun) {
                    usleep(500000); // 0.5 segundos
                }
            }

            $progressBar->finish();

            $this->newLine(2);
            $this->info("Import completed!");
            $this->info("✅ Successfully imported: {$imported} properties");

            if (count($errors) > 0) {
                $this->warn("❌ Errors: " . count($errors));
                foreach ($errors as $error) {
                    $this->line("  - {$error['titulo']}: {$error['error']}");
                }
            }

            return 0;

        } catch (\Exception $e) {
            $this->error('General error: ' . $e->getMessage());
            Log::error('Error in import command', [
                'error' => $e->getMessage()
            ]);
            return 1;
        }
    }

    /**
     * Convertir datos del JSON al formato del CRM
     */
    private function convertJsonToInmuebleData($vivienda, $externalId)
    {
        // Extraer características
        $caracteristicas = $vivienda['caracteristicas'] ?? [];

        // Convertir precio
        $precio = $this->extractPrice($vivienda['precio'] ?? '0 €');

        // Convertir metros
        $metros = $this->extractNumber($caracteristicas['metros'] ?? '0 m²');

        // Convertir habitaciones
        $habitaciones = $this->extractNumber($caracteristicas['habitaciones'] ?? '0 habs.');

        // Convertir baños
        $banos = $this->extractNumber($caracteristicas['baños'] ?? '0 baños');

        // Determinar tipo de vivienda
        $tipoVivienda = $this->mapTipoVivienda($caracteristicas['tipo_inmueble'] ?? '');

        // Determinar building subtype
        $buildingSubtype = $this->mapBuildingSubtype($caracteristicas['tipo_inmueble'] ?? '');

        // Extraer dirección
        $direccion = implode(' ', $vivienda['direccion'] ?? []);

        // Determinar coordenadas (usar coordenadas de Algeciras por defecto)
        $coordinates = $this->getCoordinatesFromAddress($direccion);

        // Mapear características booleanas
        $booleanFeatures = $this->mapBooleanFeatures($caracteristicas);

        // Mapear estado de conservación
        $conservationStatus = $this->mapConservationStatus($caracteristicas['estado'] ?? '');

        // Mapear certificación energética
        $energyCert = $this->mapEnergyCertification($caracteristicas);

        return [
            'titulo' => $vivienda['titulo'] ?? 'Sin título',
            'descripcion' => $vivienda['descripcion'] ?? '',
            'm2' => $metros,
            'm2_construidos' => $metros,
            'valor_referencia' => $precio,
            'habitaciones' => $habitaciones,
            'banos' => $banos,
            'ubicacion' => $direccion,
            'cod_postal' => '11200', // Código postal de Algeciras
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'estado' => $conservationStatus,
            'disponibilidad' => 'disponible',
            'conservation_status' => $conservationStatus,
            'cert_energetico' => true,
            'cert_energetico_elegido' => $energyCert['scale'],
            'energy_certificate_status' => 'available',
            'year_built' => 2000, // Año por defecto
            'galeria' => json_encode([]), // Sin imágenes por ahora
            'otras_caracteristicas' => json_encode([]),
            'inmobiliaria' => 0, // Por defecto
            // Campos Fotocasa
            'tipo_vivienda_id' => $tipoVivienda,
            'building_subtype_id' => $buildingSubtype,
            'transaction_type_id' => 1, // Venta
            'visibility_mode_id' => 1, // Público
            'floor_id' => $this->mapFloor($caracteristicas['planta'] ?? ''),
            'orientation_id' => 1, // Norte por defecto
            'heating_type_id' => 1, // Gas natural por defecto
            'hot_water_type_id' => 1, // Gas natural por defecto
            // Campos de eficiencia energética
            'consumption_efficiency_scale' => $energyCert['consumption_scale'],
            'emissions_efficiency_scale' => $energyCert['emissions_scale'],
            'consumption_efficiency_value' => $energyCert['consumption_value'],
            'emissions_efficiency_value' => $energyCert['emissions_value'],
            // Campos booleanos
            'furnished' => $booleanFeatures['furnished'],
            'has_elevator' => $booleanFeatures['has_elevator'],
            'has_terrace' => $booleanFeatures['has_terrace'],
            'has_balcony' => false,
            'has_parking' => $booleanFeatures['has_parking'],
            'has_air_conditioning' => true, // Asumir que tiene aire acondicionado
            'has_heating' => true, // Asumir que tiene calefacción
            'has_security_door' => false,
            'has_equipped_kitchen' => true,
            'has_wardrobe' => true,
            'has_storage_room' => $booleanFeatures['has_storage_room'],
            'pets_allowed' => false,
            // Campos adicionales
            'terrace_surface' => 0,
            'has_private_garden' => $booleanFeatures['has_private_garden'],
            'has_yard' => false,
            'has_smoke_outlet' => false,
            'has_community_pool' => false,
            'has_private_pool' => $booleanFeatures['has_private_pool'],
            'has_loading_area' => false,
            'has_24h_access' => false,
            'has_internal_transport' => false,
            'has_alarm' => false,
            'has_access_code' => false,
            'has_free_parking' => false,
            'has_laundry' => false,
            'has_community_area' => false,
            'has_office_kitchen' => false,
            'has_jacuzzi' => false,
            'has_sauna' => false,
            'has_tennis_court' => false,
            'has_gym' => false,
            'has_sports_area' => false,
            'has_children_area' => false,
            'has_home_automation' => false,
            'has_internet' => true,
            'has_suite_bathroom' => false,
            'has_home_appliances' => true,
            'has_oven' => true,
            'has_washing_machine' => true,
            'has_microwave' => true,
            'has_fridge' => true,
            'has_tv' => false,
            'has_parquet' => false,
            'has_stoneware' => false,
            'nearby_public_transport' => true,
            'land_area' => 0,
            'mostrar_precio' => true,
        ];
    }

    /**
     * Extraer precio del string
     */
    private function extractPrice($priceString)
    {
        preg_match('/[\d.,]+/', $priceString, $matches);
        if (!empty($matches)) {
            return (float) str_replace(['.', ','], ['', '.'], $matches[0]);
        }
        return 0;
    }

    /**
     * Extraer número del string
     */
    private function extractNumber($string)
    {
        preg_match('/\d+/', $string, $matches);
        return !empty($matches) ? (int) $matches[0] : 0;
    }

    /**
     * Mapear tipo de vivienda a ID de Fotocasa
     */
    private function mapTipoVivienda($tipo)
    {
        $mapping = [
            'Piso' => 1,
            'Casa o chalet' => 2,
            'Casa adosada' => 2,
            'Dúplex' => 1,
            'Ático' => 1,
            'Estudio' => 1,
            'Loft' => 1,
        ];

        return $mapping[$tipo] ?? 1; // Flat por defecto
    }

    /**
     * Mapear building subtype
     */
    private function mapBuildingSubtype($tipo)
    {
        $mapping = [
            'Piso' => 9, // Flat
            'Casa o chalet' => 13, // House
            'Casa adosada' => 17, // Terraced house
            'Dúplex' => 3, // Duplex
            'Ático' => 5, // Penthouse
            'Estudio' => 6, // Studio
            'Loft' => 7, // Loft
        ];

        return $mapping[$tipo] ?? 9; // Flat por defecto
    }

    /**
     * Mapear planta
     */
    private function mapFloor($planta)
    {
        if (empty($planta)) return null;

        $mapping = [
            'Bajo' => 3, // Ground floor
            'Entresuelo' => 4, // Mezzanine
            '1' => 6, // First
            '2' => 7, // Second
            '3' => 8, // Third
            '4' => 9, // Fourth
            '5' => 10, // Fifth
            '6' => 11, // Sixth
            '7' => 12, // Seventh
            '8' => 13, // Eighth
            '9' => 14, // Ninth
            '10' => 15, // Tenth
            'Ático' => 22, // Penthouse
        ];

        return $mapping[$planta] ?? null;
    }

    /**
     * Mapear características booleanas
     */
    private function mapBooleanFeatures($caracteristicas)
    {
        return [
            'furnished' => strtolower($caracteristicas['amueblado'] ?? '') === 'sí',
            'has_elevator' => strtolower($caracteristicas['ascensor'] ?? '') === 'sí',
            'has_terrace' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'terraza') !== false,
            'has_parking' => !empty($caracteristicas['parking']),
            'has_storage_room' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'trastero') !== false,
            'has_private_garden' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'jardín') !== false,
            'has_private_pool' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'piscina') !== false,
        ];
    }

    /**
     * Mapear estado de conservación
     */
    private function mapConservationStatus($estado)
    {
        $mapping = [
            'Muy bien' => 'excelente',
            'Bien' => 'bueno',
            'Regular' => 'regular',
            'Necesita reforma' => 'necesita reforma',
        ];

        return $mapping[$estado] ?? 'bueno';
    }

    /**
     * Mapear certificación energética
     */
    private function mapEnergyCertification($caracteristicas)
    {
        $scaleMap = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'F' => 6,
            'G' => 7,
            'NC' => 0
        ];
        $consumo = $caracteristicas['consumo_energia'] ?? '';
        $emisiones = $caracteristicas['emisiones'] ?? '';
        $scale = 'G'; // Por defecto
        $consumption_scale = 7;
        $emissions_scale = 7;

        if (preg_match('/([A-G])/', $consumo, $m)) {
            $scale = $m[1];
            $consumption_scale = $scaleMap[$scale];
        }
        if (preg_match('/([A-G])/', $emisiones, $m)) {
            $emissions_scale = $scaleMap[$m[1]];
        }

        return [
            'scale' => $scale,
            'consumption_scale' => $consumption_scale,
            'emissions_scale' => $emissions_scale,
            'consumption_value' => 999,
            'emissions_value' => 999,
        ];
    }

    /**
     * Obtener coordenadas desde dirección
     */
    private function getCoordinatesFromAddress($address)
    {
        // Coordenadas por defecto de Algeciras
        $defaultCoords = [
            'lat' => 36.1408,
            'lng' => -5.4565
        ];

        if (empty($address)) {
            return $defaultCoords;
        }

        // Por ahora usar coordenadas por defecto para evitar llamadas a la API
        return $defaultCoords;
    }
}
