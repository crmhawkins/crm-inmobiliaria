<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;
use Illuminate\Support\Facades\Log;

class DebugImageMapping extends Command
{
    protected $signature = 'debug:image-mapping {inmueble_id}';
    protected $description = 'Debug del mapeo de imágenes para un inmueble específico';

    public function handle()
    {
        $inmuebleId = $this->argument('inmueble_id');
        $inmueble = Inmuebles::find($inmuebleId);

        if (!$inmueble) {
            $this->error("Inmueble con ID {$inmuebleId} no encontrado");
            return;
        }

        $this->info("=== DEBUG IMAGE MAPPING ===");
        $this->info("Inmueble ID: {$inmueble->id}");
        $this->info("Título: {$inmueble->titulo}");
        $this->info("External ID: {$inmueble->external_id}");

        // Verificar archivo de propiedades
        $jsonPathProps = base_path('viviendas2_formateado.json');
        if (!file_exists($jsonPathProps)) {
            $this->error("Archivo viviendas2_formateado.json no encontrado");
            return;
        }

        $propsContent = file_get_contents($jsonPathProps);
        $props = json_decode($propsContent, true);

        if (!$props) {
            $this->error("Error al parsear viviendas2_formateado.json");
            return;
        }

        $this->info("Total propiedades en JSON: " . count($props));

        // Buscar coincidencia por título
        $propertyId = null;
        $titulo = $inmueble->titulo;

        foreach ($props as $id => $prop) {
            if (isset($prop['titulo']) && $prop['titulo'] === $titulo) {
                $propertyId = (string)$id;
                $this->info("¡COINCIDENCIA ENCONTRADA!");
                $this->info("Property ID: {$propertyId}");
                $this->info("Título en JSON: {$prop['titulo']}");
                break;
            }
        }

        if (!$propertyId) {
            $this->warn("No se encontró coincidencia por título");
            $this->info("Título buscado: '{$titulo}'");
            $this->info("Primeros 5 títulos en JSON:");
            $count = 0;
            foreach ($props as $id => $prop) {
                if ($count >= 5) break;
                $this->info("  {$id}: '{$prop['titulo']}'");
                $count++;
            }
        }

        // Verificar archivo de imágenes
        $jsonPathImages = base_path('imagenes_original.json');
        if (!file_exists($jsonPathImages)) {
            $this->error("Archivo imagenes_original.json no encontrado");
            return;
        }

        $imagesContent = file_get_contents($jsonPathImages);
        $images = json_decode($imagesContent, true);

        if (!$images) {
            $this->error("Error al parsear imagenes_original.json");
            return;
        }

        $this->info("Total propiedades con imágenes: " . count($images));

        if ($propertyId && isset($images[$propertyId])) {
            $propertyImages = $images[$propertyId];
            $this->info("¡IMÁGENES ENCONTRADAS!");
            $this->info("Total imágenes: " . count($propertyImages));
            $this->info("Primeras 3 imágenes:");
            $count = 0;
            foreach ($propertyImages as $key => $url) {
                if ($count >= 3) break;
                $this->info("  {$key}: {$url}");
                $count++;
            }
        } else {
            $this->warn("No se encontraron imágenes para Property ID: {$propertyId}");
            $this->info("Keys disponibles en imagenes_original.json:");
            $this->info("  " . implode(', ', array_slice(array_keys($images), 0, 10)));
        }
    }
}
