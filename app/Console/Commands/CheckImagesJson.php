<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckImagesJson extends Command
{
    protected $signature = 'check:images-json';
    protected $description = 'Verificar contenido del archivo imagenes_original.json';

    public function handle()
    {
        $jsonPath = base_path('imagenes_original.json');

        if (!file_exists($jsonPath)) {
            $this->error('Archivo imagenes_original.json no encontrado');
            return 1;
        }

        $jsonContent = file_get_contents($jsonPath);
        $imagenes = json_decode($jsonContent, true);

        if (!$imagenes) {
            $this->error('Error al parsear imagenes_original.json');
            return 1;
        }

        $keys = array_keys($imagenes);
        sort($keys, SORT_NUMERIC);

        $this->info('Claves en imagenes_original.json: ' . implode(', ', $keys));
        $this->info('Total de propiedades con imágenes: ' . count($keys));

        // Verificar si existe el ID 1
        if (isset($imagenes['1'])) {
            $this->info('✓ ID 1 existe con ' . count($imagenes['1']) . ' imágenes');
            $this->info('Primeras 3 URLs del ID 1:');
            $count = 0;
            foreach ($imagenes['1'] as $key => $url) {
                if ($count < 3) {
                    $this->info("  $key: $url");
                    $count++;
                }
            }
        } else {
            $this->error('✗ ID 1 NO existe en imagenes_original.json');
        }

        return 0;
    }
}
