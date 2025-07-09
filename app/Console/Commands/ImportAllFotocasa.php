<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Inmuebles;
use App\Http\Controllers\InmueblesController;
use Illuminate\Support\Facades\DB;

class ImportAllFotocasa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:all-fotocasa';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa todas las propiedades del JSON y las sube al CRM y a Fotocasa, uniendo imágenes.';

    public function handle()
    {
        $jsonPath = base_path('viviendas2_formateado.json');

        if (!file_exists($jsonPath)) {
            $this->error('No se encuentra el archivo viviendas2_formateado.json');
            return 1;
        }

        $props = json_decode(file_get_contents($jsonPath), true);

        if (!$props) {
            $this->error('Error al parsear el JSON de propiedades.');
            return 1;
        }

        $controller = new InmueblesController();
        $ok = 0; $fail = 0; $errores = [];

        $this->info("Iniciando importación de " . count($props) . " propiedades...");

        foreach ($props as $id => $prop) {
            DB::beginTransaction();
            try {
                $this->info("Procesando propiedad ID: $id - " . ($prop['titulo'] ?? 'Sin título'));

                // Convertir datos del JSON al formato del CRM usando la lógica del controlador
                $inmuebleData = $controller->convertJsonToInmuebleData($prop, $id);

                // Crear el inmueble en la base de datos
                $inmueble = Inmuebles::create($inmuebleData);

                $this->info("  ✓ Propiedad creada en CRM con ID: " . $inmueble->id);

                // Enviar a Fotocasa usando el método del controlador
                $fotocasaResponse = $controller->sendToFotocasa($inmueble);

                if ($fotocasaResponse->getStatusCode() === 200) {
                    $this->info("  ✓ Propiedad enviada a Fotocasa correctamente");
                } else {
                    $this->warn("  ⚠ Error en Fotocasa: " . $fotocasaResponse->getStatusCode() . " - " . $fotocasaResponse->getContent());
                }

                DB::commit();
                $ok++;

            } catch (\Throwable $e) {
                DB::rollBack();
                $fail++;
                $errores[] = "ID $id: " . $e->getMessage();
                $this->error("  ✗ Error en propiedad $id: " . $e->getMessage());
            }

            // Pausa pequeña para no sobrecargar las APIs
            usleep(500000); // 0.5 segundos
        }

        $this->info("\n" . str_repeat("=", 50));
        $this->info("RESUMEN DE IMPORTACIÓN");
        $this->info(str_repeat("=", 50));
        $this->info("✓ Propiedades procesadas correctamente: $ok");
        $this->info("✗ Propiedades con errores: $fail");

        if ($fail > 0) {
            $this->warn("\nErrores encontrados:");
            foreach ($errores as $err) {
                $this->warn("  - $err");
            }
        }

        return 0;
    }
}
