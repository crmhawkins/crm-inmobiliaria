<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;
use App\Http\Controllers\InmueblesController;

class TestFotocasaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fotocasa:test-images {inmueble_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test sending a specific property to Fotocasa with detailed image logging';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inmuebleId = $this->argument('inmueble_id');

        $this->info("Buscando inmueble con ID: {$inmuebleId}");

        $inmueble = Inmuebles::find($inmuebleId);

        if (!$inmueble) {
            $this->error("No se encontró el inmueble con ID: {$inmuebleId}");
            return 1;
        }

        $this->info("Inmueble encontrado: {$inmueble->titulo}");
        $this->info("ID en base de datos: {$inmueble->id}");

        // Verificar si existe el archivo imagenes.json
        $jsonPath = base_path('imagenes.json');
        if (!file_exists($jsonPath)) {
            $this->error("Archivo imagenes.json no encontrado en: {$jsonPath}");
            return 1;
        }

        $this->info("Archivo imagenes.json encontrado");

        // Leer y verificar el contenido del archivo imagenes.json
        $jsonContent = file_get_contents($jsonPath);
        $imagenes = json_decode($jsonContent, true);

        if (!$imagenes) {
            $this->error("Error al parsear imagenes.json");
            return 1;
        }

        $this->info("Archivo imagenes.json parseado correctamente");
        $this->info("Claves disponibles en imagenes.json: " . implode(', ', array_keys($imagenes)));

        // Verificar si existe la propiedad en imagenes.json
        $propertyId = (string)$inmueble->id;
        if (!isset($imagenes[$propertyId])) {
            $this->warn("No se encontraron imágenes para la propiedad ID: {$propertyId}");
            $this->info("Claves disponibles: " . implode(', ', array_keys($imagenes)));
        } else {
            $propertyImages = $imagenes[$propertyId];
            $this->info("Imágenes encontradas para la propiedad: " . count($propertyImages));
            foreach ($propertyImages as $key => $url) {
                $this->line("  - {$key}: {$url}");
            }
        }

        // Crear instancia del controlador
        $controller = new InmueblesController();

        $this->info("\nEnviando propiedad a Fotocasa...");

        // Enviar a Fotocasa
        $response = $controller->sendToFotocasa($inmueble);

        $this->info("Respuesta de Fotocasa:");
        $this->line("Status Code: " . $response->getStatusCode());
        $this->line("Response Body: " . $response->getContent());

        if ($response->getStatusCode() === 200) {
            $this->info("✅ Propiedad enviada exitosamente a Fotocasa");
        } else {
            $this->error("❌ Error al enviar propiedad a Fotocasa");
        }

        $this->info("\nRevisa los logs en storage/logs/laravel.log para más detalles");

        return 0;
    }
}
