<?php

namespace App\Console\Commands;

use App\Services\Fotocasa\FotocasaClient;
use Illuminate\Console\Command;

class TestFotocasaRestProperty extends Command
{
    protected $signature = 'fotocasa:test-rest-property
                            {--external-id= : ExternalId de la propiedad a obtener}
                            {--list : Listar todas las propiedades disponibles}';

    protected $description = 'Prueba la API REST de Fotocasa para obtener detalles de una propiedad';

    public function handle(): int
    {
        $this->info('🧪 Probando API REST de Fotocasa...');
        $this->newLine();

        $apiKey = env('API_KEY');
        if (!$apiKey) {
            $this->error('⚠️  API_KEY no configurada');
            return self::FAILURE;
        }

        $client = app(FotocasaClient::class);

        try {
            if ($this->option('list')) {
            $this->info('📋 Obteniendo lista de propiedades...');
            $properties = $client->getProperties(['size' => 10, 'includeUnpublished' => true]);

                if (empty($properties)) {
                    $this->warn('No se encontraron propiedades');
                    return self::FAILURE;
                }

                $this->info('✅ Propiedades encontradas:');
                $this->newLine();

                if (isset($properties[0]) && is_array($properties[0])) {
                    $this->line('Total propiedades: ' . count($properties));
                    $this->newLine();
                    $this->info('Primera propiedad completa:');
                    $this->displayPropertyDetails($properties[0]);
                } else {
                    $this->line('Respuesta completa:');
                    $this->line(json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                }

                return self::SUCCESS;
            }

            $externalId = $this->option('external-id');

            if (!$externalId) {
                $this->info('📋 Obteniendo primera propiedad disponible...');
                $properties = $client->getProperties(['size' => 1, 'includeUnpublished' => true]);

                if (empty($properties)) {
                    $this->error('No se encontraron propiedades');
                    return self::FAILURE;
                }

                $property = $properties[0];
                if (isset($property['ExternalId'])) {
                    $externalId = $property['ExternalId'];
                    $this->info("Usando ExternalId: {$externalId}");
                    $this->newLine();
                } else {
                    $this->error('No se pudo obtener ExternalId de las propiedades');
                    $this->line('Respuesta completa:');
                    $this->line(json_encode($properties, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    return self::FAILURE;
                }
            } else {
                $this->info("🔍 Buscando propiedad: {$externalId}");
                $this->newLine();

                $properties = $client->getProperties(['size' => 100, 'includeUnpublished' => true]);
                $property = null;

                foreach ($properties as $prop) {
                    if (isset($prop['ExternalId']) && (string)$prop['ExternalId'] === (string)$externalId) {
                        $property = $prop;
                        break;
                    }
                }

                if (!$property) {
                    $this->error("No se encontró la propiedad con ExternalId: {$externalId}");
                    return self::FAILURE;
                }
            }

            if (empty($property)) {
                $this->warn('⚠️  La propiedad no tiene datos');
                return self::FAILURE;
            }

            $this->info('✅✅ DATOS COMPLETOS ENCONTRADOS!');
            $this->newLine();
            $this->displayPropertyDetails($property);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->line('Trace: ' . $e->getTraceAsString());
            return self::FAILURE;
        }
    }

    private function displayPropertyDetails(array $property): void
    {
        $this->info('📊 Detalles de la propiedad:');
        $this->newLine();

        $fields = [
            'ExternalId' => 'ID Externo',
            'AgencyReference' => 'Referencia Agencia',
            'Title' => 'Título',
            'Description' => 'Descripción',
            'Price' => 'Precio',
            'Surface' => 'Superficie',
            'Rooms' => 'Habitaciones',
            'Bathrooms' => 'Baños',
            'Address' => 'Dirección',
            'ZipCode' => 'Código Postal',
            'City' => 'Ciudad',
            'Province' => 'Provincia',
            'CategoryId' => 'Categoría ID',
            'SubcategoryId' => 'Subcategoría ID',
            'TransactionTypeId' => 'Tipo Transacción',
            'State' => 'Estado',
        ];

        foreach ($fields as $field => $label) {
            if (isset($property[$field]) && $property[$field] !== null && $property[$field] !== '') {
                $value = $property[$field];
                if (is_array($value)) {
                    $value = json_encode($value, JSON_UNESCAPED_UNICODE);
                }
                if (is_string($value) && strlen($value) > 200) {
                    $value = substr($value, 0, 200) . '...';
                }
                $this->line("  <info>{$label}:</info> {$value}");
            }
        }

        if (isset($property['Images']) && is_array($property['Images'])) {
            $this->newLine();
            $this->line("  <info>Imágenes:</info> " . count($property['Images']) . " encontradas");
        }

        if (isset($property['Features']) && is_array($property['Features'])) {
            $this->newLine();
            $this->line("  <info>Características:</info> " . count($property['Features']) . " encontradas");
            foreach ($property['Features'] as $feature) {
                if (is_array($feature)) {
                    $this->line("    - " . json_encode($feature, JSON_UNESCAPED_UNICODE));
                } else {
                    $this->line("    - {$feature}");
                }
            }
        }

        $this->newLine();
        $this->info('📄 Datos completos (JSON):');
        $this->line(json_encode($property, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }
}
