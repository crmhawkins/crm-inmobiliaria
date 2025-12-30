<?php

namespace App\Console\Commands;

use App\Models\Inmuebles;
use Illuminate\Console\Command;

class ShowPropertyDetails extends Command
{
    protected $signature = 'property:show-details
                            {--id= : ID de la propiedad en la base de datos}
                            {--external-id= : ExternalId de la propiedad}';

    protected $description = 'Muestra los detalles completos de una propiedad desde la base de datos local';

    public function handle(): int
    {
        $id = $this->option('id');
        $externalId = $this->option('external-id');

        if (!$id && !$externalId) {
            $this->info('📋 Buscando primera propiedad con datos completos...');
            $property = Inmuebles::whereNotNull('titulo')
                ->whereNotNull('descripcion')
                ->whereNotNull('valor_referencia')
                ->first();
        } elseif ($id) {
            $property = Inmuebles::find($id);
        } else {
            $property = Inmuebles::where('external_id', $externalId)->first();
        }

        if (!$property) {
            $this->error('No se encontró la propiedad');
            return self::FAILURE;
        }

        $this->info('✅✅ PROPIEDAD ENCONTRADA!');
        $this->newLine();
        $this->displayPropertyDetails($property);

        return self::SUCCESS;
    }

    private function displayPropertyDetails(Inmuebles $property): void
    {
        $this->info('📊 Detalles de la propiedad:');
        $this->newLine();

        $details = [
            'ID' => $property->id,
            'ExternalId' => $property->external_id,
            'Título' => $property->titulo,
            'Descripción' => $property->descripcion,
            'Precio/Valor Referencia' => $property->valor_referencia ? number_format($property->valor_referencia, 2, ',', '.') . ' €' : 'N/A',
            'Superficie (m²)' => $property->m2,
            'Superficie Construida (m²)' => $property->m2_construidos,
            'Habitaciones' => $property->habitaciones,
            'Baños' => $property->banos,
            'Ubicación' => $property->ubicacion,
            'Código Postal' => $property->cod_postal,
            'Estado' => $property->estado,
            'Tipo Vivienda ID' => $property->tipo_vivienda_id,
            'Certificado Energético' => $property->cert_energetico,
            'Disponibilidad' => $property->disponibilidad,
            'Referencia Catastral' => $property->referencia_catastral,
        ];

        foreach ($details as $label => $value) {
            if ($value !== null && $value !== '') {
                if (is_string($value) && strlen($value) > 300) {
                    $value = substr($value, 0, 300) . '...';
                }
                $this->line("  <info>{$label}:</info> {$value}");
            }
        }

        if ($property->galeria) {
            $this->newLine();
            $galeria = is_string($property->galeria) ? json_decode($property->galeria, true) : $property->galeria;
            if (is_array($galeria) && !empty($galeria)) {
                $this->line("  <info>Imágenes:</info> " . count($galeria) . " encontradas");
            }
        }

        if ($property->otras_caracteristicas) {
            $this->newLine();
            $this->line("  <info>Otras Características:</info>");
            $caracteristicas = is_string($property->otras_caracteristicas)
                ? json_decode($property->otras_caracteristicas, true)
                : $property->otras_caracteristicas;
            if (is_array($caracteristicas)) {
                foreach ($caracteristicas as $key => $value) {
                    $this->line("    - {$key}: {$value}");
                }
            } else {
                $this->line("    {$property->otras_caracteristicas}");
            }
        }

        $this->newLine();
        $this->info('✅ Detalles mostrados correctamente');
    }
}
