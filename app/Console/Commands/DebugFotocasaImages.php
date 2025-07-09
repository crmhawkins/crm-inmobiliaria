<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Inmuebles;
use App\Http\Controllers\InmueblesController;

class DebugFotocasaImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:fotocasa-images {inmueble_id} {original_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug detallado del envío de imágenes a Fotocasa';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $inmuebleId = $this->argument('inmueble_id');
        $originalId = $this->argument('original_id');

        $this->info("=== DEBUG FOTOCASA IMAGES ===");
        $this->info("Inmueble ID: $inmuebleId");
        $this->info("Original ID: $originalId");

        $inmueble = Inmuebles::find($inmuebleId);
        if (!$inmueble) {
            $this->error("Inmueble no encontrado");
            return 1;
        }

        $this->info("Título: " . $inmueble->titulo);

        $controller = new InmueblesController();

        // Debug del método getPropertyDocumentsFromOriginalId
        $this->info("\n=== DEBUG: getPropertyDocumentsFromOriginalId ===");
        $documents = $controller->getPropertyDocumentsFromOriginalId($originalId);

        $this->info("Documentos encontrados: " . count($documents));
        foreach ($documents as $index => $doc) {
            $this->info("  Documento $index: " . json_encode($doc));
        }

        // Debug del payload completo
        $this->info("\n=== DEBUG: Payload completo ===");

        // Simular el envío pero solo para debug
        $fotocasaTypeId = $inmueble->tipo_vivienda_id;

        // Funciones para valores seguros
        $safeInt = fn($v) => is_null($v) ? 0 : (int)$v;
        $safeBool = fn($v) => is_null($v) ? false : (bool)$v;
        $safeString = fn($v) => is_null($v) ? '' : (string)$v;
        $safeFloat = fn($v) => is_null($v) ? null : (float)$v;

        // Obtener coordenadas desde la base de datos
        $coordinates = [
            'x' => $inmueble->longitude ?? -3.7038,
            'y' => $inmueble->latitude ?? 40.4168
        ];

        // Construcción payload según el esquema de la API de Fotocasa
        $payload = [
            "ExternalId" => $safeString($inmueble->id),
            "AgencyReference" => $safeString($inmueble->inmobiliaria),
            "TypeId" => $safeInt($fotocasaTypeId),
            "SubTypeId" => $safeInt($inmueble->building_subtype_id),
            "ContactTypeId" => 1,
            "PropertyAddress" => [
                [
                    "ZipCode" => $safeString($inmueble->cod_postal ?? ''),
                    "Street" => $safeString($inmueble->ubicacion),
                    "FloorId" => $safeInt($inmueble->floor_id),
                    "x" => $safeFloat($coordinates['x']),
                    "y" => $safeFloat($coordinates['y']),
                    "VisibilityModeId" => $safeInt($inmueble->visibility_mode_id)
                ]
            ],
            "PropertyFeature" => [
                [
                    "FeatureId" => 2, // Title
                    "TextValue" => $safeString($inmueble->titulo)
                ],
                [
                    "FeatureId" => 3, // Description
                    "TextValue" => $safeString($inmueble->descripcion ?? '')
                ]
            ],
            "PropertyContactInfo" => [
                [
                    "TypeId" => 1, // Email
                    "Value" => $safeString($inmueble->email ?? 'contact@example.com')
                ]
            ],
            "PropertyTransaction" => [
                [
                    "TransactionTypeId" => $safeInt($inmueble->transaction_type_id),
                    "Price" => $safeFloat($inmueble->valor_referencia ?? 0),
                    "ShowPrice" => $safeBool($inmueble->mostrar_precio ?? true)
                ]
            ],
            "PropertyDocument" => $documents
        ];

        $this->info("PropertyDocument en payload: " . json_encode($payload['PropertyDocument'], JSON_PRETTY_PRINT));
        $this->info("PropertyDocument count: " . count($payload['PropertyDocument']));
        $this->info("PropertyDocument type: " . gettype($payload['PropertyDocument']));
        $this->info("PropertyDocument is array: " . (is_array($payload['PropertyDocument']) ? 'true' : 'false'));
        $this->info("PropertyDocument empty: " . (empty($payload['PropertyDocument']) ? 'true' : 'false'));

        $this->info("\n=== Payload completo (solo estructura) ===");
        $this->info("Keys en payload: " . implode(', ', array_keys($payload)));
        foreach ($payload as $key => $value) {
            if ($key === 'PropertyDocument') {
                $this->info("  $key: " . count($value) . " elementos");
            } else {
                $this->info("  $key: " . (is_array($value) ? count($value) . " elementos" : gettype($value)));
            }
        }

        return 0;
    }
}
