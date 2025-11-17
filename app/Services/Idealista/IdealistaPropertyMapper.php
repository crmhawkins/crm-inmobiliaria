<?php

namespace App\Services\Idealista;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class IdealistaPropertyMapper
{
    /**
     * Devuelve los atributos listos para guardar y el label del tipo de vivienda.
     */
    public function map(array $property, ?array $images = null): array
    {
        $features = Arr::get($property, 'features', []);
        $tipoLabel = $this->mapTipoViviendaLabel(Arr::get($property, 'type'));

        $attributes = [
            'titulo' => $this->buildTitle($property),
            'descripcion' => $this->extractDescription($property),
            'valor_referencia' => $this->toFloat(Arr::get($property, 'operation.price')),
            'm2' => $this->toFloat($this->firstAvailable($features, ['areaUsable', 'areaConstructed'])),
            'm2_construidos' => $this->toFloat(Arr::get($features, 'areaConstructed')),
            'habitaciones' => Arr::get($features, 'rooms'),
            'banos' => Arr::get($features, 'bathroomNumber'),
            'ubicacion' => $this->formatAddress(Arr::get($property, 'address', [])),
            'cod_postal' => Arr::get($property, 'address.postalCode'),
            'latitude' => Arr::get($property, 'address.latitude'),
            'longitude' => Arr::get($property, 'address.longitude'),
            'estado' => $this->mapConservation(Arr::get($features, 'conservation')),
            'disponibilidad' => $this->mapAvailability(Arr::get($property, 'state')),
            'conservation_status' => Arr::get($features, 'conservation'),
            'cert_energetico' => Arr::has($features, 'energyCertificateRating') ? 1 : null,
            'cert_energetico_elegido' => $this->normalizeEnergyScale(Arr::get($features, 'energyCertificateRating')),
            'energy_certificate_status' => Arr::has($features, 'energyCertificateRating') ? 'available' : null,
            'consumption_efficiency_value' => Arr::get($features, 'energyCertificatePerformance'),
            'consumption_efficiency_scale' => $this->mapEnergyScaleToInteger(Arr::get($features, 'energyCertificateRating')),
            'emissions_efficiency_value' => Arr::get($features, 'energyCertificateEmissionsValue'),
            'emissions_efficiency_scale' => $this->mapEnergyScaleToInteger(Arr::get($features, 'energyCertificateEmissionsRating')),
            'year_built' => Arr::get($features, 'builtYear'),
            'furnished' => $this->mapFurnished($features),
            'has_elevator' => Arr::get($features, 'liftAvailable'),
            'has_terrace' => Arr::get($features, 'terrace'),
            'has_balcony' => Arr::get($features, 'balcony'),
            'has_parking' => Arr::get($features, 'parkingAvailable'),
            'has_storage_room' => Arr::get($features, 'storage'),
            'has_private_garden' => Arr::get($features, 'garden'),
            'has_private_pool' => Arr::get($features, 'pool'),
            'has_air_conditioning' => Arr::get($features, 'conditionedAir'),
            'has_equipped_kitchen' => $this->mapEquippedKitchen($features),
            'has_home_appliances' => $this->mapEquippedKitchen($features),
            'has_wardrobe' => Arr::get($features, 'wardrobes'),
            'pets_allowed' => Arr::get($features, 'petsAllowed'),
            'land_area' => $this->toFloat(Arr::get($features, 'areaPlot')),
            'mostrar_precio' => ! (bool) Arr::get($features, 'hiddenPrice', false),
            'otras_caracteristicas' => null,
            'galeria' => $this->mapImages($images),
            'external_id' => Arr::get($property, 'code') ?: Arr::get($property, 'propertyId'),
            'idealista_property_id' => Arr::get($property, 'propertyId'),
            'idealista_code' => Arr::get($property, 'code'),
            'idealista_payload' => $this->encodeJson($property),
        ];

        // Preservar galeria incluso si está vacío
        $galeriaValue = $attributes['galeria'] ?? json_encode([], JSON_UNESCAPED_SLASHES);
        $attributes = array_filter(
            $attributes,
            static fn ($value) => $value !== null
        );
        // Asegurar que galeria siempre esté presente
        $attributes['galeria'] = $galeriaValue;

        return [
            'attributes' => $attributes,
            'tipo_vivienda_label' => $tipoLabel,
            'transaction_type_id' => $this->mapTransactionType(Arr::get($property, 'operation.type')),
            'visibility_mode_id' => $this->mapVisibilityMode(Arr::get($property, 'address.visibility')),
            'floor_id' => $this->mapFloor(Arr::get($property, 'address.floor')),
            'orientation_id' => $this->mapOrientation($features),
        ];
    }

    private function mapTipoViviendaLabel(?string $type): ?string
    {
        return match (Str::lower((string) $type)) {
            'flat' => 'Piso',
            'house' => 'Casa o chalet',
            'countryhouse' => 'Casa rural',
            'garage' => 'Garaje',
            'office' => 'Oficina',
            'commercial' => 'Local comercial',
            'land' => 'Terreno',
            'storage' => 'Trastero',
            'building' => 'Edificio',
            'room' => 'Habitación',
            default => $type ? Str::ucfirst($type) : null,
        };
    }

    private function buildTitle(array $property): string
    {
        $reference = Arr::get($property, 'reference');
        $code = Arr::get($property, 'code');
        $type = Arr::get($property, 'type');
        $town = Arr::get($property, 'address.town');

        if ($reference) {
            return $reference;
        }

        if ($code) {
            return $code;
        }

        if ($type && $town) {
            return sprintf('%s en %s', Str::headline($type), $town);
        }

        return 'Propiedad Idealista';
    }

    private function extractDescription(array $property): string
    {
        $descriptions = Arr::get($property, 'descriptions', []);
        if (! is_array($descriptions)) {
            return '';
        }

        $preferred = $this->firstDescriptionByLanguage($descriptions, 'es')
            ?? $this->firstDescriptionByLanguage($descriptions, 'en')
            ?? ($descriptions[0]['text'] ?? '');

        return $preferred ?? '';
    }

    private function firstDescriptionByLanguage(array $descriptions, string $lang): ?string
    {
        foreach ($descriptions as $description) {
            if (Arr::get($description, 'language') === $lang) {
                return $description['text'] ?? null;
            }
        }

        return null;
    }

    private function toFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    private function firstAvailable(array $source, array $keys)
    {
        foreach ($keys as $key) {
            if (Arr::has($source, $key)) {
                return Arr::get($source, $key);
            }
        }

        return null;
    }

    private function formatAddress(array $address): ?string
    {
        if (empty($address)) {
            return null;
        }

        $parts = array_filter([
            Arr::get($address, 'streetName'),
            Arr::get($address, 'streetNumber'),
            Arr::get($address, 'town'),
            Arr::get($address, 'postalCode'),
        ]);

        return implode(', ', $parts) ?: null;
    }

    private function mapConservation(?string $conservation): ?string
    {
        return match ($conservation) {
            'good' => 'bueno',
            'toRestore' => 'necesita reforma',
            'fullyReformed' => 'reformado',
            'new_development_in_construction' => 'obra nueva (en construcción)',
            'new_development_finished' => 'obra nueva (terminada)',
            default => $conservation,
        };
    }

    private function mapAvailability(?string $state): string
    {
        return match ($state) {
            'active' => 'disponible',
            'pending' => 'pendiente',
            default => 'no disponible',
        };
    }

    private function mapFurnished(array $features): ?bool
    {
        return match (Arr::get($features, 'equipment')) {
            'equipped_kitchen_and_furnished' => true,
            'equipped_kitchen_and_not_furnished',
            'not_equipped' => false,
            default => null,
        };
    }

    private function mapEquippedKitchen(array $features): ?bool
    {
        return match (Arr::get($features, 'equipment')) {
            'equipped_kitchen_and_furnished',
            'equipped_kitchen_and_not_furnished' => true,
            'not_equipped' => false,
            default => null,
        };
    }

    private function mapTransactionType(?string $type): ?int
    {
        return match ($type) {
            'sale' => 1,
            'rent' => 3,
            default => null,
        };
    }

    private function mapVisibilityMode(?string $visibility): ?int
    {
        return match ($visibility) {
            'full' => 1,
            'street' => 2,
            'hidden' => 3,
            default => null,
        };
    }

    private function mapFloor(?string $floor): ?int
    {
        if ($floor === null || $floor === '') {
            return null;
        }

        $normalized = Str::lower($floor);

        $mapping = [
            'st' => 1, // sótano
            'ss' => 2, // semisótano
            'bj' => 3,
            'en' => 4,
        ];

        if (array_key_exists($normalized, $mapping)) {
            return $mapping[$normalized];
        }

        if (is_numeric($normalized)) {
            return match ((int) $normalized) {
                1 => 6,
                2 => 7,
                3 => 8,
                4 => 9,
                5 => 10,
                6 => 11,
                7 => 12,
                8 => 13,
                9 => 14,
                10 => 15,
                default => null,
            };
        }

        return null;
    }

    private function mapOrientation(array $features): ?int
    {
        $candidates = [
            'orientationNorth' => 3,
            'orientationSouth' => 8,
            'orientationEast' => 5,
            'orientationWest' => 2,
        ];

        foreach ($candidates as $key => $value) {
            if (Arr::get($features, $key)) {
                return $value;
            }
        }

        return null;
    }

    private function encodeJson($value): string
    {
        return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    private function normalizeEnergyScale(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        $lower = strtolower($trimmed);

        // Valores inválidos que deben convertirse a null
        $invalidValues = ['unknown', 'in_process', 'in-process', 'pending', 'not_available', 'not-available', ''];

        if (in_array($lower, $invalidValues, true)) {
            return null;
        }

        // Valores válidos típicos de certificación energética
        $validValues = ['a', 'b', 'c', 'd', 'e', 'f', 'g'];

        if (in_array($lower, $validValues, true)) {
            return strtoupper($trimmed);
        }

        // Si no es un valor conocido, retornar null para evitar errores
        return null;
    }

    /**
     * Convierte una letra de certificación energética (A-G) a un número entero (1-7)
     * para campos de tipo integer en la base de datos.
     */
    private function mapEnergyScaleToInteger(?string $value): ?int
    {
        if ($value === null) {
            return null;
        }

        $trimmed = trim($value);
        $upper = strtoupper($trimmed);

        // Valores inválidos que deben convertirse a null
        $invalidValues = ['UNKNOWN', 'IN_PROCESS', 'IN-PROCESS', 'PENDING', 'NOT_AVAILABLE', 'NOT-AVAILABLE', ''];

        if (in_array($upper, $invalidValues, true)) {
            return null;
        }

        // Mapeo de letras a números: A=1, B=2, C=3, D=4, E=5, F=6, G=7
        $scaleMap = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'F' => 6,
            'G' => 7,
        ];

        return $scaleMap[$upper] ?? null;
    }

    /**
     * Mapea las imágenes de Idealista al formato esperado por el campo galeria.
     * El formato es un JSON con un array asociativo donde las claves son números (sortingId) y los valores son URLs.
     */
    private function mapImages(?array $images): string
    {
        if (empty($images) || !is_array($images)) {
            return json_encode([], JSON_UNESCAPED_SLASHES);
        }

        $galeria = [];
        $sortingId = 1;

        foreach ($images as $image) {
            // Idealista puede devolver las imágenes en diferentes formatos
            // Intentamos extraer la URL de diferentes formas posibles
            $url = null;

            if (is_string($image)) {
                // Si es directamente una URL string
                $url = $image;
            } elseif (is_array($image)) {
                // Si es un array, buscamos campos comunes como 'url', 'imageUrl', 'src', etc.
                $url = Arr::get($image, 'url')
                    ?? Arr::get($image, 'imageUrl')
                    ?? Arr::get($image, 'src')
                    ?? Arr::get($image, 'image')
                    ?? Arr::get($image, 'path');
            }

            if ($url && filter_var($url, FILTER_VALIDATE_URL)) {
                $galeria[$sortingId] = $url;
                $sortingId++;
            }
        }

        return json_encode($galeria, JSON_UNESCAPED_SLASHES);
    }
}

