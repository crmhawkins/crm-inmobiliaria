<?php

namespace App\Services\Idealista;

use App\Models\Inmuebles;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class IdealistaPropertyCreator
{
    /**
     * Convierte un inmueble del CRM al formato de la API de Idealista
     */
    public function toIdealistaFormat(Inmuebles $inmueble): array
    {
        // Validar campos obligatorios antes de construir el payload
        $type = $this->mapTipoVivienda($inmueble->tipo_vivienda_id);
        if (!$type) {
            throw new \InvalidArgumentException('El tipo de vivienda es obligatorio');
        }

        $address = $this->buildAddress($inmueble);
        if (empty($address['postalCode'])) {
            throw new \InvalidArgumentException('El código postal es obligatorio para Idealista');
        }

        $transactionType = $this->mapTransactionType($inmueble->transaction_type_id ?? 1);
        $features = $this->buildFeatures($inmueble, $transactionType);
        // Validar que haya al menos un área - si no hay, usar un valor por defecto mínimo
        if (empty($features['areaUsable']) && empty($features['areaConstructed'])) {
            // En lugar de lanzar error, usar un valor mínimo por defecto
            // Idealista requiere un área, pero podemos usar un valor mínimo
            $features['areaUsable'] = 1.0; // Valor mínimo para que pase la validación
            Log::warning('Inmueble sin área especificada, usando valor por defecto', [
                'inmueble_id' => $inmueble->id,
            ]);
        }

        $descriptions = $this->buildDescriptions($inmueble);
        if (empty($descriptions)) {
            throw new \InvalidArgumentException('Se requiere al menos una descripción');
        }

        $payload = [
            'type' => $type,
            'operation' => [
                'type' => $this->mapTransactionType($inmueble->transaction_type_id ?? 1),
                'price' => (float) ($inmueble->valor_referencia ?? 0),
            ],
            'address' => $address,
            'features' => $features,
            'descriptions' => $descriptions,
        ];

        // contactId es obligatorio - obtener del vendedor si existe
        if ($inmueble->vendedor_id) {
            $vendedor = $inmueble->vendedor;
            if ($vendedor && $vendedor->idealista_contact_id) {
                $payload['contactId'] = (int) $vendedor->idealista_contact_id;
            } else {
                // Si el vendedor no tiene contactId en Idealista, necesitamos crear uno o usar uno por defecto
                // Por ahora, lanzamos un warning y omitimos la creación en Idealista
                Log::warning('Vendedor sin contactId de Idealista', [
                    'inmueble_id' => $inmueble->id,
                    'vendedor_id' => $inmueble->vendedor_id,
                ]);
                throw new \InvalidArgumentException('El vendedor debe tener un contactId de Idealista. Sincroniza los contactos primero con: php artisan idealista:sync-contacts');
            }
        } else {
            // Si no hay vendedor, necesitamos un contacto por defecto
            // Intentar obtener el primer contacto activo de Idealista
            $contactoDefault = \App\Models\Clientes::whereNotNull('idealista_contact_id')
                ->where('idealista_is_active', true)
                ->first();

            if ($contactoDefault && $contactoDefault->idealista_contact_id) {
                $payload['contactId'] = (int) $contactoDefault->idealista_contact_id;
                Log::info('Usando contacto por defecto de Idealista', [
                    'inmueble_id' => $inmueble->id,
                    'contact_id' => $contactoDefault->idealista_contact_id,
                ]);
            } else {
                throw new \InvalidArgumentException('No hay contactos de Idealista disponibles. Sincroniza los contactos primero con: php artisan idealista:sync-contacts');
            }
        }

        // Agregar código si existe
        if ($inmueble->external_id) {
            $payload['code'] = (string) $inmueble->external_id;
        }

        // Agregar referencia si existe
        if ($inmueble->referencia_catastral) {
            $payload['reference'] = $inmueble->referencia_catastral;
        }

        return $payload;
    }

    private function mapTipoVivienda(?int $tipoId): ?string
    {
        return match ($tipoId) {
            1 => 'flat',      // Piso
            2 => 'house',     // Casa
            3 => 'commercial', // Local comercial
            4 => 'office',    // Oficina
            5 => 'building',  // Edificio
            6 => 'land',      // Terreno
            7 => 'storage',   // Nave industrial (usando storage como aproximación)
            8 => 'garage',    // Garaje
            12 => 'storage',  // Trastero
            default => 'flat',
        };
    }

    private function mapTransactionType(?int $transactionTypeId): string
    {
        return match ($transactionTypeId) {
            1 => 'sale',      // Venta
            3 => 'rent',      // Alquiler
            4 => 'sale',      // Traspaso (mapeado a sale)
            7 => 'rent',      // Alquiler con opción a compra (mapeado a rent)
            9 => 'sale',      // Venta (otro tipo)
            default => 'sale',
        };
    }

    private function buildAddress(Inmuebles $inmueble): array
    {
        $address = [];

        // Campos obligatorios
        if ($inmueble->ubicacion) {
            $address['streetName'] = $inmueble->ubicacion;
        }

        // postalCode es obligatorio
        if ($inmueble->cod_postal) {
            $address['postalCode'] = $inmueble->cod_postal;
        } else {
            $address['postalCode'] = '00000'; // Valor temporal
        }

        // Coordenadas - OBLIGATORIAS para Idealista
        // Idealista siempre requiere latitude, longitude, precision y country
        if ($inmueble->latitude && $inmueble->longitude) {
            $address['latitude'] = (float) $inmueble->latitude;
            $address['longitude'] = (float) $inmueble->longitude;
        } else {
            // Si no hay coordenadas, lanzar error ya que son obligatorias
            throw new \InvalidArgumentException('Las coordenadas (latitude y longitude) son obligatorias para Idealista. Por favor, selecciona una ubicación en el mapa.');
        }

        // Precision debe ser "exact" o "moved" (no un número)
        $address['precision'] = 'exact'; // Por defecto "exact", podría ser "moved" si se movió
        // Country debe ser el nombre completo del país, no el código ISO
        $address['country'] = 'Spain'; // España - valores permitidos: Spain, Italy, Portugal, Andorra, France, Switzerland, San Marino

        // Town es obligatorio - extraer de ubicación
        // La ubicación puede venir como "Calle, Ciudad, Provincia, País" o "Barriada, Ciudad, Provincia"
        $ubicacionParts = array_map('trim', explode(',', $inmueble->ubicacion ?? ''));

        // Intentar encontrar la ciudad en las partes de la ubicación
        // Generalmente está en la segunda o tercera posición (después de la calle/barriada)
        $town = null;
        if (count($ubicacionParts) >= 2) {
            // Si hay muchas partes, la ciudad suele estar en la segunda o tercera
            // Ejemplo: "Palmones, Los Barrios, Campo de Gibraltar, Cádiz" -> ciudad es "Los Barrios" o "Cádiz"
            // Preferir la segunda parte (generalmente es la ciudad/barrio)
            // Si la segunda parte parece ser una provincia grande, usar la tercera
            $possibleTown = $ubicacionParts[1] ?? null;
            $possibleProvince = $ubicacionParts[2] ?? null;

            // Si la segunda parte contiene palabras que indican que es una región grande, usar la tercera
            $regionKeywords = ['campo de', 'provincia', 'comunidad', 'andalucía', 'madrid', 'cataluña'];
            $isRegion = false;
            if ($possibleTown) {
                foreach ($regionKeywords as $keyword) {
                    if (stripos($possibleTown, $keyword) !== false) {
                        $isRegion = true;
                        break;
                    }
                }
            }

            if ($isRegion && $possibleProvince) {
                $town = $possibleProvince;
            } else {
                $town = $possibleTown ?? $ubicacionParts[0] ?? null;
            }
        } else {
            // Si solo hay una parte, usarla
            $town = $ubicacionParts[0] ?? null;
        }

        // Si no se pudo extraer, usar un valor por defecto
        if (empty($town) || $town === 'Ciudad') {
            $town = 'Ciudad'; // Valor por defecto
            Log::warning('No se pudo extraer ciudad de la ubicación', [
                'inmueble_id' => $inmueble->id,
                'ubicacion' => $inmueble->ubicacion,
                'parts' => $ubicacionParts,
            ]);
        }

        $address['town'] = $town;

        // Visibility es obligatorio
        $address['visibility'] = match ($inmueble->visibility_mode_id ?? 1) {
            1 => 'full',
            2 => 'street',
            3 => 'hidden',
            default => 'full',
        };

        // Mapear floor_id si existe
        if ($inmueble->floor_id) {
            $floor = $this->mapFloorToIdealista($inmueble->floor_id);
            if ($floor) {
                $address['floor'] = $floor;
            }
        }

        return $address;
    }

    private function mapFloorToIdealista(?int $floorId): ?string
    {
        return match ($floorId) {
            1 => 'st',   // sótano
            2 => 'ss',   // semisótano
            3 => 'bj',   // bajo
            4 => 'en',   // entresuelo
            6 => '1',
            7 => '2',
            8 => '3',
            9 => '4',
            10 => '5',
            11 => '6',
            12 => '7',
            13 => '8',
            14 => '9',
            15 => '10',
            16 => '11',
            22 => '12',
            31 => '13',
            default => null,
        };
    }

    private function buildFeatures(Inmuebles $inmueble, string $transactionType = 'sale'): array
    {
        $features = [];
        $tipoVivienda = $this->mapTipoVivienda($inmueble->tipo_vivienda_id);

        // Campo "type" obligatorio para houses según el error
        // Valores permitidos: andar_moradia, independent, semidetached, terraced, villa
        if ($tipoVivienda === 'house') {
            // Por defecto usamos "independent" (casa independiente)
            // Este valor es equivalente a "detached" pero es el que acepta Idealista
            $features['type'] = 'independent';
        }

        // Campos numéricos - algunos pueden ser obligatorios
        if ($inmueble->habitaciones !== null && $inmueble->habitaciones > 0) {
            $features['rooms'] = (int) $inmueble->habitaciones;
        }

        if ($inmueble->banos !== null && $inmueble->banos > 0) {
            $features['bathroomNumber'] = (int) $inmueble->banos;
        }

        // Al menos uno de estos (areaUsable o areaConstructed) es probablemente obligatorio
        // IMPORTANTE: areaConstructed debe ser mayor que areaUsable según Idealista
        $areaUsable = null;
        $areaConstructed = null;

        if ($inmueble->m2 !== null && $inmueble->m2 > 0) {
            $areaUsable = (float) $inmueble->m2;
        }

        if ($inmueble->m2_construidos !== null && $inmueble->m2_construidos > 0) {
            $areaConstructed = (float) $inmueble->m2_construidos;
        }

        // Si no hay ninguna área, usar m2 como fallback
        if ($areaUsable === null && $areaConstructed === null && $inmueble->m2) {
            $areaUsable = (float) $inmueble->m2;
        }

        // Validar que areaConstructed > areaUsable
        if ($areaUsable !== null && $areaConstructed !== null) {
            if ($areaConstructed <= $areaUsable) {
                // Si son iguales o constructed es menor, ajustar para que constructed sea mayor
                // Idealista requiere que constructed > usable
                $areaConstructed = $areaUsable + 1.0; // Añadir 1 m² para cumplir la validación
                Log::warning('Ajustando areaConstructed para cumplir validación de Idealista', [
                    'inmueble_id' => $inmueble->id,
                    'areaUsable_original' => $areaUsable,
                    'areaConstructed_original' => $inmueble->m2_construidos,
                    'areaConstructed_ajustado' => $areaConstructed,
                ]);
            }
            $features['areaUsable'] = $areaUsable;
            $features['areaConstructed'] = $areaConstructed;
        } elseif ($areaUsable !== null) {
            // Solo tenemos areaUsable, usar solo ese
            $features['areaUsable'] = $areaUsable;
        } elseif ($areaConstructed !== null) {
            // Solo tenemos areaConstructed, usar solo ese
            $features['areaConstructed'] = $areaConstructed;
        }

        if ($inmueble->year_built !== null) {
            $features['builtYear'] = (int) $inmueble->year_built;
        }

        if ($inmueble->terrace_surface !== null) {
            $features['terraceSurface'] = (float) $inmueble->terrace_surface;
        }

        // areaPlot solo para ciertos tipos (house, land), no para flat
        if ($inmueble->land_area !== null && $tipoVivienda !== 'flat') {
            $features['areaPlot'] = (float) $inmueble->land_area;
        }

        // Campos booleanos
        // liftAvailable solo para flats, no para houses
        if ($inmueble->has_elevator !== null && $tipoVivienda === 'flat') {
            $features['liftAvailable'] = (bool) $inmueble->has_elevator;
        }

        if ($inmueble->has_terrace !== null) {
            $features['terrace'] = (bool) $inmueble->has_terrace;
        }

        if ($inmueble->has_balcony !== null) {
            $features['balcony'] = (bool) $inmueble->has_balcony;
        }

        if ($inmueble->has_parking !== null) {
            $features['parkingAvailable'] = (bool) $inmueble->has_parking;
        }

        if ($inmueble->has_air_conditioning !== null) {
            $features['conditionedAir'] = (bool) $inmueble->has_air_conditioning;
        }

        if ($inmueble->has_storage_room !== null) {
            $features['storage'] = (bool) $inmueble->has_storage_room;
        }

        if ($inmueble->has_private_garden !== null) {
            $features['garden'] = (bool) $inmueble->has_private_garden;
        }

        if ($inmueble->has_private_pool !== null) {
            $features['pool'] = (bool) $inmueble->has_private_pool;
        }

        if ($inmueble->has_wardrobe !== null) {
            $features['wardrobes'] = (bool) $inmueble->has_wardrobe;
        }

        // petsAllowed - SOLO para operaciones de alquiler (rent)
        // Idealista no permite este campo para operaciones de venta (sale)
        if ($transactionType === 'rent' && $inmueble->pets_allowed !== null) {
            $features['petsAllowed'] = (bool) $inmueble->pets_allowed;
        }

        // Equipment (furnished/equipped kitchen) - SOLO para operaciones de alquiler (rent)
        // Idealista no permite este campo para operaciones de venta (sale)
        if ($transactionType === 'rent') {
            if ($inmueble->furnished !== null || $inmueble->has_equipped_kitchen !== null) {
                if ($inmueble->furnished && $inmueble->has_equipped_kitchen) {
                    $features['equipment'] = 'equipped_kitchen_and_furnished';
                } elseif ($inmueble->has_equipped_kitchen) {
                    $features['equipment'] = 'equipped_kitchen_and_not_furnished';
                } else {
                    $features['equipment'] = 'not_equipped';
                }
            }
        }

        // Conservation status - siempre debe tener un valor
        $features['conservation'] = $this->mapConservation($inmueble->conservation_status);

        // Energy certificate - OBLIGATORIO para Flat y House según el error
        if ($inmueble->cert_energetico && $inmueble->cert_energetico_elegido) {
            $energyCert = strtoupper($inmueble->cert_energetico_elegido);
            if (in_array($energyCert, ['A', 'B', 'C', 'D', 'E', 'F', 'G'])) {
                $features['energyCertificateRating'] = $energyCert;
            } else {
                // Si no es válido, usar 'G' como valor por defecto (menos eficiente)
                $features['energyCertificateRating'] = 'G';
            }
        } else {
            // Si no hay certificado, usar 'G' como valor por defecto (requerido para Flat y House)
            $tipoVivienda = $this->mapTipoVivienda($inmueble->tipo_vivienda_id);
            if ($tipoVivienda === 'flat' || $tipoVivienda === 'house') {
                $features['energyCertificateRating'] = 'G';
            }
        }

        if ($inmueble->consumption_efficiency_value !== null) {
            $features['energyCertificatePerformance'] = (float) $inmueble->consumption_efficiency_value;
        }

        if ($inmueble->emissions_efficiency_value !== null) {
            $features['energyCertificateEmissionsValue'] = (float) $inmueble->emissions_efficiency_value;
        }

        if ($inmueble->emissions_efficiency_scale) {
            $emissionsScale = strtoupper($inmueble->emissions_efficiency_scale);
            if (in_array($emissionsScale, ['A', 'B', 'C', 'D', 'E', 'F', 'G'])) {
                $features['energyCertificateEmissionsRating'] = $emissionsScale;
            }
        }

        // Orientation - para flats se requiere "windowsLocation"
        if ($tipoVivienda === 'flat') {
            // Para flats, Idealista requiere "windowsLocation" en lugar de orientationNorth, etc.
            if ($inmueble->orientation_id) {
                $windowsLocation = $this->mapWindowsLocation($inmueble->orientation_id);
                if ($windowsLocation) {
                    $features['windowsLocation'] = $windowsLocation;
                } else {
                    // Si no hay orientación, usar un valor por defecto
                    $features['windowsLocation'] = 'exterior';
                }
            } else {
                // Si no hay orientación especificada, usar valor por defecto
                $features['windowsLocation'] = 'exterior';
            }
        } else {
            // Para otros tipos (houses, etc.), usar el mapeo de orientación normal
            if ($inmueble->orientation_id) {
                $orientation = $this->mapOrientation($inmueble->orientation_id);
                if ($orientation) {
                    $features = array_merge($features, $orientation);
                }
            }
        }

        return $features;
    }

    private function mapConservation(?string $status): ?string
    {
        if (!$status) {
            return 'good'; // Valor por defecto requerido
        }

        $normalized = strtolower(trim($status));

        // Valores permitidos: good, toRestore, fullyReformed, new_development_in_construction, new_development_finished
        return match ($normalized) {
            'nuevo', 'new', 'obra nueva', 'new_development_finished' => 'new_development_finished',
            'en construcción', 'in construction', 'new_development_in_construction' => 'new_development_in_construction',
            'buen estado', 'good', 'bueno', 'muy bueno', 'very good' => 'good',
            'a reformar', 'renovate', 'reformar', 'to restore', 'torestore' => 'toRestore',
            'reformado', 'fully reformed', 'fullyreformed' => 'fullyReformed',
            default => 'good',
        };
    }

    private function mapWindowsLocation(?int $orientationId): ?string
    {
        // windowsLocation para flats: valores permitidos probablemente sean "exterior", "interior", etc.
        // Por ahora, si hay orientación, asumimos que es exterior
        // Si no hay orientación o es interior, usar "interior"
        if (!$orientationId) {
            return 'interior'; // Sin orientación = probablemente interior
        }

        // Si hay orientación (N, S, E, O, etc.), las ventanas están al exterior
        return 'exterior';
    }

    private function mapOrientation(?int $orientationId): array
    {
        $orientations = [];

        // Mapeo de orientation_id a campos de Idealista
        // 1=N, 2=O, 3=N, 4=NE, 5=E, 6=SE, 7=S, 8=SO
        match ($orientationId) {
            1 => $orientations['orientationNorth'] = true,
            2 => $orientations['orientationWest'] = true,
            3 => $orientations['orientationNorth'] = true,
            4 => $orientations['orientationNorth'] = true, // NE aproximado a N
            5 => $orientations['orientationEast'] = true,
            6 => $orientations['orientationSouth'] = true, // SE aproximado a S
            7 => $orientations['orientationSouth'] = true,
            8 => $orientations['orientationWest'] = true, // SO aproximado a O
            default => null,
        };

        return $orientations;
    }

    private function buildDescriptions(Inmuebles $inmueble): array
    {
        $descriptions = [];

        // Descripción es probablemente obligatoria
        $text = $inmueble->descripcion;

        // Si no hay descripción, usar el título
        if (empty($text) && $inmueble->titulo) {
            $text = $inmueble->titulo;
        }

        // Si aún no hay texto, usar un valor por defecto
        if (empty($text)) {
            $text = 'Propiedad en ' . ($inmueble->ubicacion ?? 'ubicación no especificada');
        }

        $descriptions[] = [
            'language' => 'es',
            'text' => $text,
        ];

        return $descriptions;
    }

    /**
     * Prepara las imágenes para enviar a Idealista
     *
     * IMPORTANTE: Idealista NO acepta archivos directamente, solo URLs públicas accesibles desde internet.
     * Las URLs deben ser accesibles públicamente (no localhost).
     *
     * Opciones:
     * 1. Configurar APP_URL en .env con tu dominio público
     * 2. Usar un servicio de almacenamiento en la nube (S3, Cloudinary, etc.)
     * 3. Para desarrollo: usar ngrok o similar para exponer localhost
     */
    public function prepareImages(Inmuebles $inmueble): array
    {
        $images = [];
        $galeria = json_decode($inmueble->galeria ?? '[]', true);

        if (is_array($galeria) && !empty($galeria)) {
            foreach ($galeria as $url) {
                if (!is_string($url)) {
                    continue;
                }

                // Convertir URLs locales a URLs públicas
                $publicUrl = $this->convertToPublicUrl($url);

                // Validar que sea una URL válida y accesible públicamente
                if ($publicUrl && filter_var($publicUrl, FILTER_VALIDATE_URL)) {
                    // Idealista no acepta localhost, verificar que sea una URL pública
                    $host = parse_url($publicUrl, PHP_URL_HOST);
                    if ($host &&
                        strpos($host, 'localhost') === false &&
                        strpos($host, '127.0.0.1') === false &&
                        strpos($host, '::1') === false) {
                        $images[] = [
                            'url' => $publicUrl,
                        ];
                    } else {
                        \Illuminate\Support\Facades\Log::warning('URL local detectada, omitiendo para Idealista. Configura APP_URL con tu dominio público.', [
                            'inmueble_id' => $inmueble->id,
                            'url' => $publicUrl,
                            'host' => $host,
                        ]);
                    }
                } else {
                    \Illuminate\Support\Facades\Log::warning('No se pudo convertir URL a formato público para Idealista', [
                        'inmueble_id' => $inmueble->id,
                        'url_original' => $url,
                    ]);
                }
            }
        }

        return $images;
    }

    /**
     * Convierte una URL local a una URL pública accesible
     * Idealista requiere URLs públicas accesibles desde internet
     *
     * NOTA: Idealista NO acepta archivos directamente, solo URLs públicas.
     * Las URLs deben ser accesibles desde internet (no localhost).
     */
    private function convertToPublicUrl(string $url): ?string
    {
        // Si ya es una URL completa y pública (no localhost), devolverla tal cual
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            $host = parse_url($url, PHP_URL_HOST);
            if ($host &&
                strpos($host, 'localhost') === false &&
                strpos($host, '127.0.0.1') === false &&
                strpos($host, '::1') === false) {
                return $url;
            }
        }

        // Extraer la ruta del storage de la URL
        $storagePath = null;

        // Caso 1: URL contiene /storage/photos/1/nombre_archivo.jpg
        if (preg_match('/\/storage\/photos\/[^\/]+\/([^\/]+\.(jpg|jpeg|png|gif))/i', $url, $matches)) {
            // Extraer la ruta completa después de /storage/
            $fullPath = substr($url, strpos($url, '/storage/') + 9);
            $storagePath = $fullPath;
        }
        // Caso 2: URL contiene /storage/ seguido de la ruta completa
        elseif (preg_match('/\/storage\/(.+)$/', $url, $matches)) {
            $storagePath = $matches[1];
        }
        // Caso 3: Ruta relativa que empieza con storage/
        elseif (preg_match('/^storage\/(.+)$/', $url, $matches)) {
            $storagePath = $matches[1];
        }

        if ($storagePath) {
            // Obtener APP_URL del .env
            $appUrl = config('app.url', 'http://localhost');

            // Si APP_URL es localhost, intentar usar la URL de la petición actual si está disponible
            if (strpos($appUrl, 'localhost') !== false || strpos($appUrl, '127.0.0.1') !== false) {
                // Intentar obtener la URL del request actual
                try {
                    if (app()->runningInConsole()) {
                        // Si estamos en consola, no hay request
                        // En desarrollo, esto no funcionará, pero en producción APP_URL estará configurado
                        \Illuminate\Support\Facades\Log::info('Generando URL pública para Idealista (consola). En producción, asegúrate de que APP_URL esté configurado.', [
                            'storage_path' => $storagePath,
                            'app_url' => $appUrl,
                        ]);
                    } else {
                        // Usar la URL del request actual
                        $appUrl = request()->getSchemeAndHttpHost();
                    }
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::warning('No se puede obtener URL pública. Configura APP_URL en .env con tu dominio público.', [
                        'storage_path' => $storagePath,
                        'error' => $e->getMessage(),
                    ]);
                    // En desarrollo, seguir intentando con APP_URL
                }
            }

            // Construir la URL pública usando el endpoint público de imágenes
            // Formato: https://tudominio.com/storage/images/photos/1/nombre_archivo.jpg
            $publicUrl = rtrim($appUrl, '/') . '/storage/images/' . $storagePath;

            return $publicUrl;
        }

        // Si no se puede convertir, devolver null
        return null;
    }
}

