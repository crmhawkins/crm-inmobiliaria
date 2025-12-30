<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class DeleteAllProperties extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'properties:delete-all {--confirm : Confirma la eliminación sin preguntar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina todas las propiedades (inmuebles) y sus imágenes de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $count = Inmuebles::count();
        
        if ($count === 0) {
            $this->info('No hay propiedades para eliminar.');
            return self::SUCCESS;
        }

        $this->warn("⚠️  ADVERTENCIA: Se eliminarán {$count} propiedades y todas sus imágenes.");
        
        if (!$this->option('confirm')) {
            if (!$this->confirm('¿Estás seguro de que deseas continuar? Esta acción no se puede deshacer.')) {
                $this->info('Operación cancelada.');
                return self::SUCCESS;
            }
        }

        $this->info('Iniciando eliminación de propiedades...');
        
        $deletedImages = 0;
        $deletedProperties = 0;

        // Obtener todas las propiedades con sus imágenes
        $properties = Inmuebles::all();
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($properties as $property) {
            // Eliminar imágenes de la galería
            if ($property->galeria) {
                $galeria = json_decode($property->galeria, true);
                if (is_array($galeria)) {
                    foreach ($galeria as $imageUrl) {
                        if ($imageUrl) {
                            // Intentar eliminar del storage si es una ruta local
                            try {
                                if (strpos($imageUrl, 'storage/') !== false || strpos($imageUrl, 'public/') !== false) {
                                    $path = str_replace(['storage/', 'public/'], '', $imageUrl);
                                    if (Storage::disk('public')->exists($path)) {
                                        Storage::disk('public')->delete($path);
                                        $deletedImages++;
                                    }
                                }
                            } catch (\Exception $e) {
                                // Continuar aunque falle la eliminación de una imagen
                            }
                        }
                    }
                }
            }

            // Eliminar la propiedad
            try {
                $property->delete();
                $deletedProperties++;
            } catch (\Exception $e) {
                $this->error("Error eliminando propiedad ID {$property->id}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✅ Eliminación completada:");
        $this->line("   - Propiedades eliminadas: {$deletedProperties}");
        $this->line("   - Imágenes eliminadas: {$deletedImages}");

        return self::SUCCESS;
    }
}
