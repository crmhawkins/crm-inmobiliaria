<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\Clientes;
use App\Models\Caracteristicas;
use App\Events\InmuebleCreated;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class InmueblesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Inmuebles::query()->with(['tipoVivienda', 'vendedor']);

        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
        }

        if ($request->filled('valor_min')) {
            $query->where('valor_referencia', '>=', $request->valor_min);
        }
        if ($request->filled('valor_max')) {
            $query->where('valor_referencia', '<=', $request->valor_max);
        }

        if ($request->filled('m2_min')) {
            $query->where('m2', '>=', $request->m2_min);
        }
        if ($request->filled('m2_max')) {
            $query->where('m2', '<=', $request->m2_max);
        }

        if ($request->filled('habitaciones')) {
            $query->whereIn('habitaciones', $request->habitaciones);
        }

        if ($request->filled('banos')) {
            $query->whereIn('banos', $request->banos);
        }

        if ($request->filled('estado')) {
            $query->whereIn('estado', $request->estado);
        }

        if ($request->filled('disponibilidad')) {
            $query->whereIn('disponibilidad', $request->disponibilidad);
        }

        if ($request->filled('tipo_vivienda')) {
            $query->whereIn('tipo_vivienda_id', $request->tipo_vivienda);
        }

        if ($request->filled('caracteristicas')) {
            foreach ($request->caracteristicas as $car) {
                $query->where('otras_caracteristicas', 'like', '%"' . $car . '"%');
            }
        }

        $inmuebles = $query->paginate(12);
        $tiposVivienda = \App\Models\TipoVivienda::all();
        $caracteristicas = \App\Models\Caracteristicas::all();

        return view('inmuebles.index', compact('inmuebles', 'tiposVivienda', 'caracteristicas'));
    }
    public function show($id)
    {
        $inmueble = Inmuebles::with('tipoVivienda')->findOrFail($id);

        // Decodificar otras_caracteristicas de forma segura
        $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];

        $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

        return view('inmuebles.show', compact('inmueble', 'caracteristicas'));
    }

    public function adminShow($id)
    {
        $inmueble = Inmuebles::with(['tipoVivienda', 'vendedor'])->findOrFail($id);

        // Decodificar otras_caracteristicas de forma segura
        $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];

        $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

        return view('inmuebles.admin-show', compact('inmueble', 'caracteristicas'));
    }

    public function publicShow($id)
    {
        $inmueble = Inmuebles::with(['tipoVivienda', 'vendedor'])->findOrFail($id);

        // Decodificar otras_caracteristicas de forma segura
        $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];
        $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

        // Extraer solo la zona/barriada de la dirección
        $inmueble->ubicacion_publica = $this->extractZone($inmueble->ubicacion);

        return view('inmuebles.public-show', compact('inmueble', 'caracteristicas'));
    }

    /**
     * Extraer solo la zona/barriada de la dirección completa
     */
    private function extractZone($ubicacion)
    {
        if (!$ubicacion) {
            return 'Ubicación no especificada';
        }

        // Lista de palabras comunes de direcciones que queremos eliminar
        $removePatterns = [
            '/^(Calle|C\\.|\/Cal)/i',  // Calle
            '/^(Avda\\.|Avda|Avenida)/i',  // Avenida
            '/^(Pl\\.|Plaza)/i',  // Plaza
            '/^(Travesía|Trv\\.)/i',  // Travesía
            '/^[A-Za-z]+\\s+[A-Za-z]+/',  // Nombres de calle seguidos
            '/^\\d+/',  // Números al inicio
            '/^\\d+\\w*/',  // Números con letras
        ];

        $parts = explode(',', $ubicacion);
        
        // Si hay comas, tomar la última parte (generalmente es la zona)
        if (count($parts) > 1) {
            $zone = trim(end($parts));
            
            // Limpiar palabras comunes
            foreach ($removePatterns as $pattern) {
                $zone = preg_replace($pattern, '', $zone);
                $zone = trim($zone);
            }
            
            return $zone ?: trim(end($parts));
        }

        // Si no hay comas, intentar extraer la parte después de números o nombres de calle
        $cleaned = $ubicacion;
        foreach ($removePatterns as $pattern) {
            $cleaned = preg_replace($pattern, '', $cleaned);
            $cleaned = trim($cleaned);
        }

        // Si después de limpiar queda algo, usarlo; si no, usar la original
        return $cleaned ?: $ubicacion;
    }

    /**
     * Aplicar marca de agua a una imagen guardada en el servidor
     */
    private function applyWatermark($imagePath)
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        // Detectar tipo de imagen
        $imageInfo = getimagesize($imagePath);
        if (!$imageInfo) {
            return false;
        }

        $type = $imageInfo[2];
        $width = $imageInfo[0];
        $height = $imageInfo[1];

        // Crear imagen según el tipo
        switch ($type) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($imagePath);
                $saveFunction = 'imagejpeg';
                $saveParams = [$image, $imagePath, 90];
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($imagePath);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                $saveFunction = 'imagepng';
                $saveParams = [$image, $imagePath];
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($imagePath);
                $saveFunction = 'imagegif';
                $saveParams = [$image, $imagePath];
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        // Configuración de la marca de agua
        $text = 'SAYCO';
        $fontSize = max(120, min($width, $height) / 7); // Tamaño grande y proporcional
        $opacity = 40; // Opacidad baja
        $textColor = imagecolorallocatealpha($image, 255, 255, 255, $opacity);

        // Calcular posición centrada
        $fontSizeInt = 5; // Fuente built-in más grande
        $charWidth = imagefontwidth($fontSizeInt) * strlen($text);
        $charHeight = imagefontheight($fontSizeInt);
        $x = ($width / 2) - ($charWidth / 2);
        $y = ($height / 2) - ($charHeight / 2);

        // Dibujar texto múltiples veces para efecto bold/visible
        for ($offset = -3; $offset <= 3; $offset++) {
            imagestring($image, $fontSizeInt, $x + $offset, $y, $text, $textColor);
            imagestring($image, $fontSizeInt, $x, $y + $offset, $text, $textColor);
        }

        // Guardar imagen con marca de agua
        call_user_func_array($saveFunction, $saveParams);
        imagedestroy($image);

        return true;
    }

    public function create()
    {
        $caracteristicas = Caracteristicas::all();
        $vendedores = Clientes::where('inmobiliaria', 1)->get();

        return view('inmuebles.create', compact('caracteristicas', 'vendedores'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'm2' => 'nullable|numeric|min:0',
            'm2_construidos' => 'nullable|numeric|min:0',
            'valor_referencia' => 'nullable|numeric|min:0',
            'habitaciones' => 'nullable|integer|min:0',
            'banos' => 'nullable|integer|min:0',
            'tipo_vivienda_id' => 'required|integer|in:1,2,3,4,5,6,7,8,12',
            'building_subtype_id' => 'required|integer|min:1',
            'ubicacion' => 'nullable|string',
            'cod_postal' => 'nullable|string',
            'referencia_catastral' => 'nullable|string',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'estado' => 'nullable|string',
            'disponibilidad' => 'nullable|string',
            'conservation_status' => 'nullable|string',
            'cert_energetico' => 'nullable|boolean',
            'cert_energetico_elegido' => 'nullable|string',
            'energy_certificate_status' => 'nullable|string',
            'year_built' => 'nullable|integer|min:1800|max:' . date('Y'),
            'imagen_principal' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max
            'galeria' => 'nullable|array',
            'galeria.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB max per image
            'otras_caracteristicas' => 'nullable|array',
            // Campos Fotocasa
            'transaction_type_id' => 'nullable|integer|min:1',
            'visibility_mode_id' => 'nullable|integer|in:1,2,3',
            'floor_id' => 'nullable|integer|in:1,3,4,6,7,8,9,10,11,12,13,14,15,16,22,31',
            'orientation_id' => 'nullable|integer|in:1,2,3,4,5,6,7,8',
            'heating_type_id' => 'nullable|integer|in:1,2,3,4,5,6',
            'hot_water_type_id' => 'nullable|integer|in:1,2,3,4,5,6',
            // Campos de eficiencia energética
            'consumption_efficiency_scale' => 'nullable|string|in:A,B,C,D,E,F,G',
            'emissions_efficiency_scale' => 'nullable|string|in:A,B,C,D,E,F,G',
            'consumption_efficiency_value' => 'nullable|numeric|min:0',
            'emissions_efficiency_value' => 'nullable|numeric|min:0',
            // Campos booleanos
            'furnished' => 'nullable|boolean',
            'has_elevator' => 'nullable|boolean',
            'has_terrace' => 'nullable|boolean',
            'has_balcony' => 'nullable|boolean',
            'has_parking' => 'nullable|boolean',
            'has_air_conditioning' => 'nullable|boolean',
            'has_heating' => 'nullable|boolean',
            'has_security_door' => 'nullable|boolean',
            'has_equipped_kitchen' => 'nullable|boolean',
            'has_wardrobe' => 'nullable|boolean',
            'has_storage_room' => 'nullable|boolean',
            'pets_allowed' => 'nullable|boolean',
            // Campos adicionales
            'terrace_surface' => 'nullable|numeric|min:0',
            'has_private_garden' => 'nullable|boolean',
            'has_yard' => 'nullable|boolean',
            'has_smoke_outlet' => 'nullable|boolean',
            'has_community_pool' => 'nullable|boolean',
            'has_private_pool' => 'nullable|boolean',
            'has_loading_area' => 'nullable|boolean',
            'has_24h_access' => 'nullable|boolean',
            'has_internal_transport' => 'nullable|boolean',
            'has_alarm' => 'nullable|boolean',
            'has_access_code' => 'nullable|boolean',
            'has_free_parking' => 'nullable|boolean',
            'has_laundry' => 'nullable|boolean',
            'has_community_area' => 'nullable|boolean',
            'has_office_kitchen' => 'nullable|boolean',
            'has_jacuzzi' => 'nullable|boolean',
            'has_sauna' => 'nullable|boolean',
            'has_tennis_court' => 'nullable|boolean',
            'has_gym' => 'nullable|boolean',
            'has_sports_area' => 'nullable|boolean',
            'has_children_area' => 'nullable|boolean',
            'has_home_automation' => 'nullable|boolean',
            'has_internet' => 'nullable|boolean',
            'has_suite_bathroom' => 'nullable|boolean',
            'has_home_appliances' => 'nullable|boolean',
            'has_oven' => 'nullable|boolean',
            'has_washing_machine' => 'nullable|boolean',
            'has_microwave' => 'nullable|boolean',
            'has_fridge' => 'nullable|boolean',
            'has_tv' => 'nullable|boolean',
            'has_parquet' => 'nullable|boolean',
            'has_stoneware' => 'nullable|boolean',
            'nearby_public_transport' => 'nullable|boolean',
            'land_area' => 'nullable|numeric|min:0',
            'mostrar_precio' => 'nullable|boolean',
        ]);

        // Procesar la imagen principal si se subió
        $galeria = [];
        if ($request->hasFile('imagen_principal')) {
            $imagen = $request->file('imagen_principal');
            $nombreArchivo = time() . '_' . Str::random(10) . '.' . $imagen->getClientOriginalExtension();

            // Guardar la imagen en storage/app/public/photos/1/
            $ruta = $imagen->storeAs('photos/1', $nombreArchivo, 'public');
            
            // Aplicar marca de agua a la imagen
            $this->applyWatermark(storage_path('app/public/' . $ruta));

            // Crear la URL completa
            $url = asset('storage/' . $ruta);

            // Guardar en el formato JSON que usa el sistema
            $galeria = ['1' => $url];
        }

        // Procesar la galería de imágenes si se subieron
        if ($request->hasFile('galeria')) {
            $galeriaFiles = $request->file('galeria');
            $sortingId = 1;

            foreach ($galeriaFiles as $file) {
                if ($file && $file->isValid()) {
                    $nombreArchivo = time() . '_' . Str::random(10) . '_' . $sortingId . '.' . $file->getClientOriginalExtension();

                    // Guardar la imagen en storage/app/public/photos/1/
                    $ruta = $file->storeAs('photos/1', $nombreArchivo, 'public');
                    
                    // Aplicar marca de agua a la imagen
                    $this->applyWatermark(storage_path('app/public/' . $ruta));

                    // Crear la URL completa
                    $url = asset('storage/' . $ruta);

                    // Agregar a la galería
                    $galeria[$sortingId] = $url;
                    $sortingId++;
                }
            }
        }

        $inmueble = Inmuebles::create([
            'titulo' => $request->titulo,
            'descripcion' => $request->descripcion,
            'm2' => $request->m2,
            'm2_construidos' => $request->m2_construidos,
            'valor_referencia' => $request->valor_referencia,
            'habitaciones' => $request->habitaciones,
            'banos' => $request->banos,
            'ubicacion' => $request->ubicacion,
            'cod_postal' => $request->cod_postal,
            'referencia_catastral' => $request->referencia_catastral,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'estado' => $request->estado,
            'disponibilidad' => $request->disponibilidad,
            'conservation_status' => $request->conservation_status,
            'cert_energetico' => $request->cert_energetico,
            'cert_energetico_elegido' => $request->cert_energetico_elegido,
            'energy_certificate_status' => $request->energy_certificate_status,
            'year_built' => $request->year_built,
            'galeria' => json_encode($galeria),
            'otras_caracteristicas' => json_encode($request->otras_caracteristicas ?? []),
            'inmobiliaria' => session('inmobiliaria') === 'sayco' ? 1 : 0,
            // Campos requeridos para Fotocasa con valores por defecto
            'tipo_vivienda_id' => $request->tipo_vivienda_id ?? 1, // Flat por defecto
            'building_subtype_id' => $request->building_subtype_id, // Requerido del formulario
            'transaction_type_id' => $request->transaction_type_id ?? 1, // Venta por defecto
            'visibility_mode_id' => $request->visibility_mode_id ?? 1, // Público por defecto
            'floor_id' => $request->floor_id,
            'orientation_id' => $request->orientation_id,
            'heating_type_id' => $request->heating_type_id,
            'hot_water_type_id' => $request->hot_water_type_id,
            // Campos booleanos
            'furnished' => $request->boolean('furnished'),
            'has_elevator' => $request->boolean('has_elevator'),
            'has_terrace' => $request->boolean('has_terrace'),
            'has_balcony' => $request->boolean('has_balcony'),
            'has_parking' => $request->boolean('has_parking'),
            'has_air_conditioning' => $request->boolean('has_air_conditioning'),
            'has_heating' => $request->boolean('has_heating'),
            'has_security_door' => $request->boolean('has_security_door'),
            'has_equipped_kitchen' => $request->boolean('has_equipped_kitchen'),
            'has_wardrobe' => $request->boolean('has_wardrobe'),
            'has_storage_room' => $request->boolean('has_storage_room'),
            'pets_allowed' => $request->boolean('pets_allowed'),
            // Campos adicionales
            'terrace_surface' => $request->terrace_surface,
            'has_private_garden' => $request->boolean('has_private_garden'),
            'has_yard' => $request->boolean('has_yard'),
            'has_smoke_outlet' => $request->boolean('has_smoke_outlet'),
            'has_community_pool' => $request->boolean('has_community_pool'),
            'has_private_pool' => $request->boolean('has_private_pool'),
            'has_loading_area' => $request->boolean('has_loading_area'),
            'has_24h_access' => $request->boolean('has_24h_access'),
            'has_internal_transport' => $request->boolean('has_internal_transport'),
            'has_alarm' => $request->boolean('has_alarm'),
            'has_access_code' => $request->boolean('has_access_code'),
            'has_free_parking' => $request->boolean('has_free_parking'),
            'has_laundry' => $request->boolean('has_laundry'),
            'has_community_area' => $request->boolean('has_community_area'),
            'has_office_kitchen' => $request->boolean('has_office_kitchen'),
            'has_jacuzzi' => $request->boolean('has_jacuzzi'),
            'has_sauna' => $request->boolean('has_sauna'),
            'has_tennis_court' => $request->boolean('has_tennis_court'),
            'has_gym' => $request->boolean('has_gym'),
            'has_sports_area' => $request->boolean('has_sports_area'),
            'has_children_area' => $request->boolean('has_children_area'),
            'has_home_automation' => $request->boolean('has_home_automation'),
            'has_internet' => $request->boolean('has_internet'),
            'has_suite_bathroom' => $request->boolean('has_suite_bathroom'),
            'has_home_appliances' => $request->boolean('has_home_appliances'),
            'has_oven' => $request->boolean('has_oven'),
            'has_washing_machine' => $request->boolean('has_washing_machine'),
            'has_microwave' => $request->boolean('has_microwave'),
            'has_fridge' => $request->boolean('has_fridge'),
            'has_tv' => $request->boolean('has_tv'),
            'has_parquet' => $request->boolean('has_parquet'),
            'has_stoneware' => $request->boolean('has_stoneware'),
            'nearby_public_transport' => $request->boolean('nearby_public_transport'),
            'land_area' => $request->land_area,
            // Campos de eficiencia energética
            'consumption_efficiency_scale' => $this->getEnergyScaleValue($request->consumption_efficiency_scale),
            'emissions_efficiency_scale' => $this->getEnergyScaleValue($request->emissions_efficiency_scale),
            'consumption_efficiency_value' => $request->consumption_efficiency_value,
            'emissions_efficiency_value' => $request->emissions_efficiency_value,
            'mostrar_precio' => $request->boolean('mostrar_precio'),
        ]);

        // Disparar evento para enviar alertas a clientes
        event(new InmuebleCreated($inmueble));

        // Enviar a Fotocasa
        $fotocasaResponse = $this->sendToFotocasa($inmueble);
        //dd($fotocasaResponse);
        // Si hay error en Fotocasa, logearlo pero continuar
        if ($fotocasaResponse->getStatusCode() !== 200) {
            Log::warning('Error sending to Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'status' => $fotocasaResponse->getStatusCode(),
                'response' => $fotocasaResponse->getContent()
            ]);
        }

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
    }

    public function sendToFotocasa(Inmuebles $inmueble)
    {
        // Usar directamente el tipo_vivienda_id como TypeId de Fotocasa
        $fotocasaTypeId = $inmueble->tipo_vivienda_id;

        // Diccionarios completos según la documentación de la API de Fotocasa
        $buildingTypes = [1,2,3,4,5,6,7,8,12];

        $buildingSubtypes = [
            1 => [2,3,5,6,7,9,10,11],       // Flat: Triplex, Duplex, Penthouse, Studio, Loft, Flat, Apartment, Ground floor
            2 => [13,17,19,20,24,27],       // House: House, Terraced house, Paired house, Chalet, Rustic house, Bungalow
            3 => [48,49,50,51,72],          // Commercial store: Residential, Others, Mixed residential, Offices, Hotel
            4 => [56,60,91],                // Office: Residential land, Industrial land, Rustic land
            5 => [48,49,50,51,72],          // Building: Residential, Others, Mixed residential, Offices, Hotel
            6 => [56,60,91],                // Land: Residential land, Industrial land, Rustic land
            7 => [62,63],                   // Industrial building: Moto, Double, Individual
            8 => [68,69,70],                // Garage: Moto, Double, Individual
            12 => [90],                     // Storage room: Suelos
        ];

        $transactionTypes = [1,3,4,7,9];
        $visibilityModes = [1,2,3];
        $floorTypes = [1,3,4,6,7,8,9,10,11,12,13,14,15,16,22,31];
        $energyLabels = ['A','B','C','D','E','F','G','NC'];

        // Validación completa
        $rules = [
            'id' => ['required'],
            'inmobiliaria' => ['nullable'],
            'tipo_vivienda_id' => ['required','integer'],
            'building_subtype_id' => ['required','integer', function($attr, $val, $fail) use ($buildingSubtypes, $fotocasaTypeId) {
                if (!isset($buildingSubtypes[$fotocasaTypeId]) || !in_array($val, $buildingSubtypes[$fotocasaTypeId])) {
                    $fail("El campo $attr no es válido para el TypeId dado.");
                }
            }],
            'transaction_type_id' => ['required','integer', function($attr, $val, $fail) use ($transactionTypes) {
                if (!in_array($val, $transactionTypes)) {
                    $fail("El campo $attr debe ser un tipo válido.");
                }
            }],
            'visibility_mode_id' => ['required','integer', 'in:1,2,3'],
            'floor_id' => ['nullable','integer', 'in:1,3,4,6,7,8,9,10,11,12,13,14,15,16,22,31'],
            'orientation_id' => ['nullable','integer', 'min:0'],
            'energy_certificate_status' => ['nullable'],
            'conservation_status' => ['nullable'],

            'cod_postal' => ['nullable','string'],
            'ubicacion' => ['nullable','string'],

            'valor_referencia' => ['nullable','integer'],
            'mostrar_precio' => ['nullable','boolean'],

            'email' => ['nullable','email'],
            'telefono' => ['nullable','string'],

            'latitude' => ['required','numeric','between:-90,90'],
            'longitude' => ['required','numeric','between:-180,180'],
        ];

        $validator = Validator::make($inmueble->toArray(), $rules);

        if ($validator->fails()) {
            // Agregar mensaje personalizado para coordenadas
            if ($validator->errors()->has('latitude') || $validator->errors()->has('longitude')) {
                $validator->errors()->add('coordinates', 'Debes seleccionar una ubicación en el mapa.');
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Funciones para valores seguros
        $safeInt = fn($v) => is_null($v) ? 0 : (int)$v;
        $safeBool = fn($v) => is_null($v) ? false : (bool)$v;
        $safeString = fn($v) => is_null($v) ? '' : (string)$v;
        $safeFloat = fn($v) => is_null($v) ? null : (float)$v;

        // Obtener coordenadas desde la base de datos
        $coordinates = [
            'x' => $inmueble->longitude ?? -3.7038, // Longitud de Madrid por defecto
            'y' => $inmueble->latitude ?? 40.4168   // Latitud de Madrid por defecto
        ];

        // Construcción payload según el esquema de la API de Fotocasa
        $payload = [
            "ExternalId" => $safeString($inmueble->id),
            "AgencyReference" => $safeString($inmueble->inmobiliaria),
            "TypeId" => $safeInt($fotocasaTypeId),
            "SubTypeId" => $safeInt($inmueble->building_subtype_id),
            "ContactTypeId" => 1, // Agency contact type

            "PropertyAddress" => [
                [
                    "ZipCode" => $safeString($inmueble->cod_postal ?? ''),
                    "Street" => $safeString($this->extractZone($inmueble->ubicacion)),
                    "FloorId" => $safeInt($inmueble->floor_id),
                    "x" => $safeFloat($coordinates['x']),
                    "y" => $safeFloat($coordinates['y']),
                    "VisibilityModeId" => $safeInt($inmueble->visibility_mode_id)
                ]
            ],

            "PropertyFeature" => array_merge([
                [
                    "FeatureId" => 1, // Surface
                    "DecimalValue" => $safeFloat($inmueble->m2 ?? 0)
                ],
                [
                    "FeatureId" => 2, // Title
                    "TextValue" => $safeString($inmueble->titulo)
                ],
                [
                    "FeatureId" => 3, // Description
                    "TextValue" => $safeString($inmueble->descripcion ?? '')
                ],
                [
                    "FeatureId" => 11, // Rooms
                    "DecimalValue" => $safeFloat($inmueble->habitaciones ?? 0)
                ],
                [
                    "FeatureId" => 12, // Bathrooms
                    "DecimalValue" => $safeFloat($inmueble->banos ?? 0)
                ],
                [
                    "FeatureId" => 30, // Furnished
                    "BoolValue" => $safeBool($inmueble->furnished)
                ],
                [
                    "FeatureId" => 231, // Year built
                    "DecimalValue" => $safeFloat($inmueble->year_built ?? 0)
                ],
                [
                    "FeatureId" => 22, // Elevator
                    "BoolValue" => $safeBool($inmueble->has_elevator)
                ],
                [
                    "FeatureId" => 258, // Wardrobe
                    "BoolValue" => $safeBool($inmueble->has_wardrobe)
                ],
                [
                    "FeatureId" => 314, // Equipped kitchen
                    "BoolValue" => $safeBool($inmueble->has_equipped_kitchen)
                ],
                [
                    "FeatureId" => 254, // Air conditioner
                    "BoolValue" => $safeBool($inmueble->has_air_conditioning)
                ],
                [
                    "FeatureId" => 23, // Parking
                    "BoolValue" => $safeBool($inmueble->has_parking)
                ],
                [
                    "FeatureId" => 294, // Security door
                    "BoolValue" => $safeBool($inmueble->has_security_door)
                ],
                [
                    "FeatureId" => 297, // Balcony
                    "BoolValue" => $safeBool($inmueble->has_balcony)
                ],
                [
                    "FeatureId" => 313, // Pets allowed
                    "BoolValue" => $safeBool($inmueble->pets_allowed)
                ],
                // Nuevos campos adicionales
                [
                    "FeatureId" => 27, // Terrace
                    "BoolValue" => $safeBool($inmueble->has_terrace)
                ],
                [
                    "FeatureId" => 62, // Terrace surface
                    "DecimalValue" => $safeFloat($inmueble->terrace_surface ?? 0)
                ],
                [
                    "FeatureId" => 298, // Private garden
                    "BoolValue" => $safeBool($inmueble->has_private_garden)
                ],
                [
                    "FeatureId" => 263, // Yard
                    "BoolValue" => $safeBool($inmueble->has_yard)
                ],
                [
                    "FeatureId" => 311, // Smoke outlet
                    "BoolValue" => $safeBool($inmueble->has_smoke_outlet)
                ],
                [
                    "FeatureId" => 300, // Community pool
                    "BoolValue" => $safeBool($inmueble->has_community_pool)
                ],
                [
                    "FeatureId" => 25, // Private pool
                    "BoolValue" => $safeBool($inmueble->has_private_pool)
                ],
                [
                    "FeatureId" => 204, // Loading area
                    "BoolValue" => $safeBool($inmueble->has_loading_area)
                ],
                [
                    "FeatureId" => 207, // 24h access
                    "BoolValue" => $safeBool($inmueble->has_24h_access)
                ],
                [
                    "FeatureId" => 208, // Internal transport
                    "BoolValue" => $safeBool($inmueble->has_internal_transport)
                ],
                [
                    "FeatureId" => 235, // Alarm
                    "BoolValue" => $safeBool($inmueble->has_alarm)
                ],
                [
                    "FeatureId" => 131, // Access code
                    "BoolValue" => $safeBool($inmueble->has_access_code)
                ],
                [
                    "FeatureId" => 206, // Free parking
                    "BoolValue" => $safeBool($inmueble->has_free_parking)
                ],
                [
                    "FeatureId" => 257, // Laundry
                    "BoolValue" => $safeBool($inmueble->has_laundry)
                ],
                [
                    "FeatureId" => 301, // Community area
                    "BoolValue" => $safeBool($inmueble->has_community_area)
                ],
                [
                    "FeatureId" => 289, // Office kitchen
                    "BoolValue" => $safeBool($inmueble->has_office_kitchen)
                ],
                [
                    "FeatureId" => 274, // Jacuzzi
                    "BoolValue" => $safeBool($inmueble->has_jacuzzi)
                ],
                [
                    "FeatureId" => 277, // Sauna
                    "BoolValue" => $safeBool($inmueble->has_sauna)
                ],
                [
                    "FeatureId" => 310, // Tennis court
                    "BoolValue" => $safeBool($inmueble->has_tennis_court)
                ],
                [
                    "FeatureId" => 309, // Gym
                    "BoolValue" => $safeBool($inmueble->has_gym)
                ],
                [
                    "FeatureId" => 302, // Sports area
                    "BoolValue" => $safeBool($inmueble->has_sports_area)
                ],
                [
                    "FeatureId" => 303, // Children area
                    "BoolValue" => $safeBool($inmueble->has_children_area)
                ],
                [
                    "FeatureId" => 142, // Home automation
                    "BoolValue" => $safeBool($inmueble->has_home_automation)
                ],
                [
                    "FeatureId" => 286, // Internet
                    "BoolValue" => $safeBool($inmueble->has_internet)
                ],
                [
                    "FeatureId" => 260, // Suite bathroom
                    "BoolValue" => $safeBool($inmueble->has_suite_bathroom)
                ],
                [
                    "FeatureId" => 259, // Home appliances
                    "BoolValue" => $safeBool($inmueble->has_home_appliances)
                ],
                [
                    "FeatureId" => 288, // Oven
                    "BoolValue" => $safeBool($inmueble->has_oven)
                ],
                [
                    "FeatureId" => 293, // Washing machine
                    "BoolValue" => $safeBool($inmueble->has_washing_machine)
                ],
                [
                    "FeatureId" => 287, // Microwave
                    "BoolValue" => $safeBool($inmueble->has_microwave)
                ],
                [
                    "FeatureId" => 292, // Fridge
                    "BoolValue" => $safeBool($inmueble->has_fridge)
                ],
                [
                    "FeatureId" => 291, // TV
                    "BoolValue" => $safeBool($inmueble->has_tv)
                ],
                [
                    "FeatureId" => 290, // Parquet
                    "BoolValue" => $safeBool($inmueble->has_parquet)
                ],
                [
                    "FeatureId" => 295, // Stoneware
                    "BoolValue" => $safeBool($inmueble->has_stoneware)
                ],
                [
                    "FeatureId" => 176, // Nearby public transport
                    "BoolValue" => $safeBool($inmueble->nearby_public_transport)
                ],
                [
                    "FeatureId" => 69, // Land area
                    "DecimalValue" => $safeFloat($inmueble->land_area ?? 0)
                ],
                // Orientación
                [
                    "FeatureId" => 28, // Orientation
                    "DecimalValue" => $safeFloat($inmueble->orientation_id ?? 0)
                ],
                // Calefacción
                [
                    "FeatureId" => 29, // Has heating
                    "BoolValue" => $safeBool($inmueble->has_heating)
                ],
                [
                    "FeatureId" => 320, // Heating type
                    "DecimalValue" => $safeFloat($inmueble->heating_type_id ?? 0)
                ],
                // Agua caliente
                [
                    "FeatureId" => 321, // Hot water type
                    "DecimalValue" => $safeFloat($inmueble->hot_water_type_id ?? 0)
                ],
                // Estado de conservación
                [
                    "FeatureId" => 249, // Conservation status
                    "DecimalValue" => $this->getConservationStatusValue($inmueble->conservation_status)
                ]
            ], $this->getEnergyFeatures($inmueble)),

            "PropertyContactInfo" => [
                [
                    "TypeId" => 1, // Email
                    "Value" => $safeString($inmueble->email ?? 'contact@example.com')
                ],
                [
                    "TypeId" => 2, // Phone
                    "Value" => $safeString($inmueble->telefono ?? '+34 123 456 789')
                ]
            ],

            "PropertyTransaction" => [
                [
                    "TransactionTypeId" => $safeInt($inmueble->transaction_type_id),
                    "Price" => $safeFloat($inmueble->valor_referencia ?? 0),
                    "ShowPrice" => $safeBool($inmueble->mostrar_precio ?? true)
                ]
            ],

            // Añadir imágenes desde el archivo imagenes.json
            "PropertyDocument" => $this->getPropertyDocumentsFromJson($inmueble)
        ];

        // Log del payload antes de enviar (para debugging)
        Log::info('Payload enviado a Fotocasa', [
            'inmueble_id' => $inmueble->id,
            'titulo' => $inmueble->titulo,
            'property_documents_count' => count($payload['PropertyDocument']),
            'property_documents' => $payload['PropertyDocument']
        ]);

        // Petición HTTP a Fotocasa
        $url = 'https://imports.gw.fotocasa.pro/api/property';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Api-Key' => env('API_KEY'),
            ])->withOptions([
                'verify' => false, // Deshabilitar verificación SSL para desarrollo
                'timeout' => 30,   // Timeout de 30 segundos
            ])->post($url, $payload);

            // Log de la respuesta de Fotocasa
            Log::info('Respuesta de Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_json' => $response->json()
            ]);

            // Retorna JSON con la respuesta de la API o error
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en petición a Fotocasa API', [
                'error' => $e->getMessage(),
                'inmueble_id' => $inmueble->id,
                'url' => $url
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Error de conexión con Fotocasa API: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getEnergyScaleValue($scale)
    {
        // Convertir escala de eficiencia energética según la documentación de Fotocasa
        $scaleMap = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'F' => 6,
            'G' => 7,
            'NC' => 0 // No certificado
        ];

        return $scaleMap[strtoupper($scale)] ?? 0;
    }

    public function getEnergyCertificateStatus($status)
    {
        // Convertir estado del certificado energético según la documentación de Fotocasa
        $statusMap = [
            'available' => 1,    // Disponible
            'pending' => 2,      // Pendiente
            'exempt' => 3,       // Exento
            'vigente' => 1,      // Vigente (equivalente a disponible)
            'pendiente' => 2,    // Pendiente
            'exento' => 3        // Exento
        ];

        return $statusMap[strtolower($status)] ?? 1; // Por defecto disponible
    }

    public function getEnergyFeatures(Inmuebles $inmueble)
    {
        $features = [];

        // Convertir valores numéricos de la BD a escalas correctas
        $consumptionScale = $this->convertNumericToScale($inmueble->consumption_efficiency_scale);
        $emissionsScale = $this->convertNumericToScale($inmueble->emissions_efficiency_scale);

        if ($consumptionScale > 0) {
            $features[] = [
                "FeatureId" => 323, // Consumption efficiency scale
                "DecimalValue" => $consumptionScale
            ];
        }

        if ($emissionsScale > 0) {
            $features[] = [
                "FeatureId" => 324, // Emissions efficiency scale
                "DecimalValue" => $emissionsScale
            ];
        }

        if ($inmueble->consumption_efficiency_value) {
            $features[] = [
                "FeatureId" => 325, // Consumption efficiency value
                "DecimalValue" => (float)$inmueble->consumption_efficiency_value
            ];
        }

        if ($inmueble->emissions_efficiency_value) {
            $features[] = [
                "FeatureId" => 326, // Emissions efficiency value
                "DecimalValue" => (float)$inmueble->emissions_efficiency_value
            ];
        }

        if ($inmueble->energy_certificate_status) {
            $features[] = [
                "FeatureId" => 327, // Energy certificate
                "DecimalValue" => $this->getEnergyCertificateStatus($inmueble->energy_certificate_status)
            ];
        }

        return $features;
    }

    public function convertNumericToScale($numericValue)
    {
        // Si es un string (A, B, C, etc.), convertirlo a número
        if (is_string($numericValue)) {
            return $this->getEnergyScaleValue($numericValue);
        }

        // Si es un número, validar que esté en el rango correcto (1-7)
        if (is_numeric($numericValue) && $numericValue >= 1 && $numericValue <= 7) {
            return (int)$numericValue;
        }

        return 0; // Valor inválido
    }

    public function getConservationStatusValue($status)
    {
        // Convertir estado de conservación según la documentación de Fotocasa
        $statusMap = [
            'excelente' => 1,      // Excelente
            'muy bueno' => 2,      // Muy bueno
            'bueno' => 3,          // Bueno
            'regular' => 4,        // Regular
            'necesita reforma' => 6, // Necesita reforma
            'renovado' => 6,       // Renovado
            // Valores en inglés
            'good' => 1,
            'pretty good' => 2,
            'almost new' => 3,
            'needs renovation' => 4,
            'renovated' => 6
        ];

        return $statusMap[strtolower($status)] ?? 1; // Por defecto bueno
    }

    public function getPropertyDocumentsFromJson(Inmuebles $inmueble)
    {
        $documents = [];

        // Leer el archivo imagenes_original.json
        $jsonPath = base_path('imagenes_original.json');
        if (!file_exists($jsonPath)) {
            Log::warning('Archivo imagenes_original.json no encontrado', [
                'path' => $jsonPath,
                'inmueble_id' => $inmueble->id
            ]);
            return $documents;
        }

        $jsonContent = file_get_contents($jsonPath);
        $imagenes = json_decode($jsonContent, true);

        if (!$imagenes) {
            Log::warning('Error al parsear imagenes_original.json', [
                'inmueble_id' => $inmueble->id,
                'json_content_length' => strlen($jsonContent)
            ]);
            return $documents;
        }

        // Buscar las imágenes de esta propiedad por su ID original del JSON
        // Como no tenemos el ID original almacenado, vamos a buscar por título
        $propertyId = null;
        $titulo = $inmueble->titulo;

        Log::info("Buscando imágenes para inmueble", [
            'inmueble_id' => $inmueble->id,
            'titulo' => $titulo,
            'external_id' => $inmueble->external_id
        ]);

        // Buscar en el JSON de propiedades para encontrar el ID original
        $jsonPathProps = base_path('viviendas2_formateado.json');
        if (file_exists($jsonPathProps)) {
            $propsContent = file_get_contents($jsonPathProps);
            $props = json_decode($propsContent, true);

            if ($props) {
                Log::info("JSON de propiedades cargado", [
                    'total_properties' => count($props),
                    'first_few_keys' => array_slice(array_keys($props), 0, 5)
                ]);

                foreach ($props as $id => $prop) {
                    if (isset($prop['titulo']) && $prop['titulo'] === $titulo) {
                        $propertyId = (string)$id;
                        Log::info("¡Coincidencia encontrada!", [
                            'inmueble_id' => $inmueble->id,
                            'titulo_buscado' => $titulo,
                            'property_id_encontrado' => $propertyId,
                            'titulo_en_json' => $prop['titulo']
                        ]);
                        break;
                    }
                }

                if (!$propertyId) {
                    Log::warning("No se encontró coincidencia por título", [
                        'inmueble_id' => $inmueble->id,
                        'titulo_buscado' => $titulo,
                        'primeros_titulos_en_json' => array_slice(array_column($props, 'titulo'), 0, 5)
                    ]);
                }
            } else {
                Log::warning("Error al parsear viviendas2_formateado.json");
            }
        } else {
            Log::warning("Archivo viviendas2_formateado.json no encontrado", [
                'path' => $jsonPathProps
            ]);
        }

        if (!$propertyId || !isset($imagenes[$propertyId])) {
            Log::info("No se encontraron imágenes para la propiedad", [
                'inmueble_id' => $inmueble->id,
                'titulo' => $titulo,
                'property_id_found' => $propertyId,
                'available_keys' => array_keys($imagenes)
            ]);
            return $documents;
        }

        $propertyImages = $imagenes[$propertyId];
        $sortingId = 1;

        Log::info("Procesando imágenes para propiedad", [
            'inmueble_id' => $inmueble->id,
            'property_id_in_json' => $propertyId,
            'total_images_found' => count($propertyImages),
            'images' => $propertyImages
        ]);
        Log::info("Procesando imágenes para propiedad", [
            'inmueble_id' => $inmueble->id,
            'property_id_in_json' => $propertyId,
            'total_images_found' => count($propertyImages),
            'images' => $propertyImages
        ]);
        foreach ($propertyImages as $imageKey => $imageUrl) {
            // Convertir URL a rule=original
            $originalUrl = $this->convertToOriginalUrl($imageUrl);

            Log::info("Procesando imagen", [
                'inmueble_id' => $inmueble->id,
                'image_key' => $imageKey,
                'original_url' => $imageUrl,
                'converted_url' => $originalUrl
            ]);

            // Agregar imagen directamente sin verificar accesibilidad
            $documents[] = [
                "TypeId" => 1, // Image
                "Url" => $originalUrl,
                "SortingId" => $sortingId
            ];
            $sortingId++;

            Log::info("Imagen agregada", [
                'inmueble_id' => $inmueble->id,
                'url' => $originalUrl,
                'sorting_id' => $sortingId - 1
            ]);
        }
        Log::info("Procesadas " . count($documents) . " imágenes para la propiedad ID: {$propertyId}", [
            'inmueble_id' => $inmueble->id,
            'total_images_processed' => count($propertyImages),
            'valid_images_count' => count($documents),
            'documents' => $documents
        ]);

        return $documents;
    }

    /**
     * Convertir URL de imagen a formato original
     */
    private function convertToOriginalUrl($url)
    {
        // Si ya es una URL original, devolverla tal como está
        if (strpos($url, '?rule=original') !== false) {
            return $url;
        }

        // Si es una URL con regla específica, convertirla a original
        if (preg_match('/^(https:\/\/static\.fotocasa\.es\/images\/ads\/[a-f0-9-]+)/', $url, $matches)) {
            return $matches[1] . '?rule=original';
        }

        return $url;
    }

    /**
     * Enviar propiedad a Fotocasa con ID original para mapear imágenes correctamente
     */
    public function sendToFotocasaWithOriginalId(Inmuebles $inmueble, $originalId)
    {
        // Usar directamente el tipo_vivienda_id como TypeId de Fotocasa
        $fotocasaTypeId = $inmueble->tipo_vivienda_id;

        // Diccionarios completos según la documentación de la API de Fotocasa
        $buildingTypes = [1,2,3,4,5,6,7,8,12];

        $buildingSubtypes = [
            1 => [2,3,5,6,7,9,10,11],       // Flat: Triplex, Duplex, Penthouse, Studio, Loft, Flat, Apartment, Ground floor
            2 => [13,17,19,20,24,27],       // House: House, Terraced house, Paired house, Chalet, Rustic house, Bungalow
            3 => [48,49,50,51,72],          // Commercial store: Residential, Others, Mixed residential, Offices, Hotel
            4 => [56,60,91],                // Office: Residential land, Industrial land, Rustic land
            5 => [48,49,50,51,72],          // Building: Residential, Others, Mixed residential, Offices, Hotel
            6 => [56,60,91],                // Land: Residential land, Industrial land, Rustic land
            7 => [62,63],                   // Industrial building: Moto, Double, Individual
            8 => [68,69,70],                // Garage: Moto, Double, Individual
            12 => [90],                     // Storage room: Suelos
        ];

        $transactionTypes = [1,3,4,7,9];
        $visibilityModes = [1,2,3];
        $floorTypes = [1,3,4,6,7,8,9,10,11,12,13,14,15,16,22,31];
        $energyLabels = ['A','B','C','D','E','F','G','NC'];

        // Validación completa
        $rules = [
            'id' => ['required'],
            'inmobiliaria' => ['nullable'],
            'tipo_vivienda_id' => ['required','integer'],
            'building_subtype_id' => ['required','integer', function($attr, $val, $fail) use ($buildingSubtypes, $fotocasaTypeId) {
                if (!isset($buildingSubtypes[$fotocasaTypeId]) || !in_array($val, $buildingSubtypes[$fotocasaTypeId])) {
                    $fail("El campo $attr no es válido para el TypeId dado.");
                }
            }],
            'transaction_type_id' => ['required','integer', function($attr, $val, $fail) use ($transactionTypes) {
                if (!in_array($val, $transactionTypes)) {
                    $fail("El campo $attr debe ser un tipo válido.");
                }
            }],
            'visibility_mode_id' => ['required','integer', 'in:1,2,3'],
            'floor_id' => ['nullable','integer', 'in:1,3,4,6,7,8,9,10,11,12,13,14,15,16,22,31'],
            'orientation_id' => ['nullable','integer', 'min:0'],
            'energy_certificate_status' => ['nullable'],
            'conservation_status' => ['nullable'],

            'cod_postal' => ['nullable','string'],
            'ubicacion' => ['nullable','string'],

            'valor_referencia' => ['nullable','integer'],
            'mostrar_precio' => ['nullable','boolean'],

            'email' => ['nullable','email'],
            'telefono' => ['nullable','string'],

            'latitude' => ['required','numeric','between:-90,90'],
            'longitude' => ['required','numeric','between:-180,180'],
        ];

        $validator = Validator::make($inmueble->toArray(), $rules);

        if ($validator->fails()) {
            // Agregar mensaje personalizado para coordenadas
            if ($validator->errors()->has('latitude') || $validator->errors()->has('longitude')) {
                $validator->errors()->add('coordinates', 'Debes seleccionar una ubicación en el mapa.');
            }
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Funciones para valores seguros
        $safeInt = fn($v) => is_null($v) ? 0 : (int)$v;
        $safeBool = fn($v) => is_null($v) ? false : (bool)$v;
        $safeString = fn($v) => is_null($v) ? '' : (string)$v;
        $safeFloat = fn($v) => is_null($v) ? null : (float)$v;

        // Obtener coordenadas desde la base de datos
        $coordinates = [
            'x' => $inmueble->longitude ?? -3.7038, // Longitud de Madrid por defecto
            'y' => $inmueble->latitude ?? 40.4168   // Latitud de Madrid por defecto
        ];

        // Construcción payload según el esquema de la API de Fotocasa
        $payload = [
            "ExternalId" => $safeString($inmueble->id),
            "AgencyReference" => $safeString($inmueble->inmobiliaria),
            "TypeId" => $safeInt($fotocasaTypeId),
            "SubTypeId" => $safeInt($inmueble->building_subtype_id),
            "ContactTypeId" => 1, // Agency contact type

            "PropertyAddress" => [
                [
                    "ZipCode" => $safeString($inmueble->cod_postal ?? ''),
                    "Street" => $safeString($this->extractZone($inmueble->ubicacion)),
                    "FloorId" => $safeInt($inmueble->floor_id),
                    "x" => $safeFloat($coordinates['x']),
                    "y" => $safeFloat($coordinates['y']),
                    "VisibilityModeId" => $safeInt($inmueble->visibility_mode_id)
                ]
            ],

            "PropertyFeature" => array_merge([
                [
                    "FeatureId" => 1, // Surface
                    "DecimalValue" => $safeFloat($inmueble->m2 ?? 0)
                ],
                [
                    "FeatureId" => 2, // Title
                    "TextValue" => $safeString($inmueble->titulo)
                ],
                [
                    "FeatureId" => 3, // Description
                    "TextValue" => $safeString($inmueble->descripcion ?? '')
                ],
                [
                    "FeatureId" => 11, // Rooms
                    "DecimalValue" => $safeFloat($inmueble->habitaciones ?? 0)
                ],
                [
                    "FeatureId" => 12, // Bathrooms
                    "DecimalValue" => $safeFloat($inmueble->banos ?? 0)
                ],
                [
                    "FeatureId" => 30, // Furnished
                    "BoolValue" => $safeBool($inmueble->furnished)
                ],
                [
                    "FeatureId" => 231, // Year built
                    "DecimalValue" => $safeFloat($inmueble->year_built ?? 0)
                ],
                [
                    "FeatureId" => 22, // Elevator
                    "BoolValue" => $safeBool($inmueble->has_elevator)
                ],
                [
                    "FeatureId" => 258, // Wardrobe
                    "BoolValue" => $safeBool($inmueble->has_wardrobe)
                ],
                [
                    "FeatureId" => 314, // Equipped kitchen
                    "BoolValue" => $safeBool($inmueble->has_equipped_kitchen)
                ],
                [
                    "FeatureId" => 254, // Air conditioner
                    "BoolValue" => $safeBool($inmueble->has_air_conditioning)
                ],
                [
                    "FeatureId" => 23, // Parking
                    "BoolValue" => $safeBool($inmueble->has_parking)
                ],
                [
                    "FeatureId" => 294, // Security door
                    "BoolValue" => $safeBool($inmueble->has_security_door)
                ],
                [
                    "FeatureId" => 297, // Balcony
                    "BoolValue" => $safeBool($inmueble->has_balcony)
                ],
                [
                    "FeatureId" => 313, // Pets allowed
                    "BoolValue" => $safeBool($inmueble->pets_allowed)
                ],
                // Nuevos campos adicionales
                [
                    "FeatureId" => 27, // Terrace
                    "BoolValue" => $safeBool($inmueble->has_terrace)
                ],
                [
                    "FeatureId" => 62, // Terrace surface
                    "DecimalValue" => $safeFloat($inmueble->terrace_surface ?? 0)
                ],
                [
                    "FeatureId" => 298, // Private garden
                    "BoolValue" => $safeBool($inmueble->has_private_garden)
                ],
                [
                    "FeatureId" => 263, // Yard
                    "BoolValue" => $safeBool($inmueble->has_yard)
                ],
                [
                    "FeatureId" => 311, // Smoke outlet
                    "BoolValue" => $safeBool($inmueble->has_smoke_outlet)
                ],
                [
                    "FeatureId" => 300, // Community pool
                    "BoolValue" => $safeBool($inmueble->has_community_pool)
                ],
                [
                    "FeatureId" => 25, // Private pool
                    "BoolValue" => $safeBool($inmueble->has_private_pool)
                ],
                [
                    "FeatureId" => 204, // Loading area
                    "BoolValue" => $safeBool($inmueble->has_loading_area)
                ],
                [
                    "FeatureId" => 207, // 24h access
                    "BoolValue" => $safeBool($inmueble->has_24h_access)
                ],
                [
                    "FeatureId" => 208, // Internal transport
                    "BoolValue" => $safeBool($inmueble->has_internal_transport)
                ],
                [
                    "FeatureId" => 235, // Alarm
                    "BoolValue" => $safeBool($inmueble->has_alarm)
                ],
                [
                    "FeatureId" => 131, // Access code
                    "BoolValue" => $safeBool($inmueble->has_access_code)
                ],
                [
                    "FeatureId" => 206, // Free parking
                    "BoolValue" => $safeBool($inmueble->has_free_parking)
                ],
                [
                    "FeatureId" => 257, // Laundry
                    "BoolValue" => $safeBool($inmueble->has_laundry)
                ],
                [
                    "FeatureId" => 301, // Community area
                    "BoolValue" => $safeBool($inmueble->has_community_area)
                ],
                [
                    "FeatureId" => 289, // Office kitchen
                    "BoolValue" => $safeBool($inmueble->has_office_kitchen)
                ],
                [
                    "FeatureId" => 274, // Jacuzzi
                    "BoolValue" => $safeBool($inmueble->has_jacuzzi)
                ],
                [
                    "FeatureId" => 277, // Sauna
                    "BoolValue" => $safeBool($inmueble->has_sauna)
                ],
                [
                    "FeatureId" => 310, // Tennis court
                    "BoolValue" => $safeBool($inmueble->has_tennis_court)
                ],
                [
                    "FeatureId" => 309, // Gym
                    "BoolValue" => $safeBool($inmueble->has_gym)
                ],
                [
                    "FeatureId" => 302, // Sports area
                    "BoolValue" => $safeBool($inmueble->has_sports_area)
                ],
                [
                    "FeatureId" => 303, // Children area
                    "BoolValue" => $safeBool($inmueble->has_children_area)
                ],
                [
                    "FeatureId" => 142, // Home automation
                    "BoolValue" => $safeBool($inmueble->has_home_automation)
                ],
                [
                    "FeatureId" => 286, // Internet
                    "BoolValue" => $safeBool($inmueble->has_internet)
                ],
                [
                    "FeatureId" => 260, // Suite bathroom
                    "BoolValue" => $safeBool($inmueble->has_suite_bathroom)
                ],
                [
                    "FeatureId" => 259, // Home appliances
                    "BoolValue" => $safeBool($inmueble->has_home_appliances)
                ],
                [
                    "FeatureId" => 288, // Oven
                    "BoolValue" => $safeBool($inmueble->has_oven)
                ],
                [
                    "FeatureId" => 293, // Washing machine
                    "BoolValue" => $safeBool($inmueble->has_washing_machine)
                ],
                [
                    "FeatureId" => 287, // Microwave
                    "BoolValue" => $safeBool($inmueble->has_microwave)
                ],
                [
                    "FeatureId" => 292, // Fridge
                    "BoolValue" => $safeBool($inmueble->has_fridge)
                ],
                [
                    "FeatureId" => 291, // TV
                    "BoolValue" => $safeBool($inmueble->has_tv)
                ],
                [
                    "FeatureId" => 290, // Parquet
                    "BoolValue" => $safeBool($inmueble->has_parquet)
                ],
                [
                    "FeatureId" => 295, // Stoneware
                    "BoolValue" => $safeBool($inmueble->has_stoneware)
                ],
                [
                    "FeatureId" => 176, // Nearby public transport
                    "BoolValue" => $safeBool($inmueble->nearby_public_transport)
                ],
                [
                    "FeatureId" => 69, // Land area
                    "DecimalValue" => $safeFloat($inmueble->land_area ?? 0)
                ],
                // Orientación
                [
                    "FeatureId" => 28, // Orientation
                    "DecimalValue" => $safeFloat($inmueble->orientation_id ?? 0)
                ],
                // Calefacción
                [
                    "FeatureId" => 29, // Has heating
                    "BoolValue" => $safeBool($inmueble->has_heating)
                ],
                [
                    "FeatureId" => 320, // Heating type
                    "DecimalValue" => $safeFloat($inmueble->heating_type_id ?? 0)
                ],
                // Agua caliente
                [
                    "FeatureId" => 321, // Hot water type
                    "DecimalValue" => $safeFloat($inmueble->hot_water_type_id ?? 0)
                ],
                // Estado de conservación
                [
                    "FeatureId" => 249, // Conservation status
                    "DecimalValue" => $this->getConservationStatusValue($inmueble->conservation_status)
                ]
            ], $this->getEnergyFeatures($inmueble)),

            "PropertyContactInfo" => [
                [
                    "TypeId" => 1, // Email
                    "Value" => $safeString($inmueble->email ?? 'contact@example.com')
                ],
                [
                    "TypeId" => 2, // Phone
                    "Value" => $safeString($inmueble->telefono ?? '+34 123 456 789')
                ]
            ],

            "PropertyTransaction" => [
                [
                    "TransactionTypeId" => $safeInt($inmueble->transaction_type_id),
                    "Price" => $safeFloat($inmueble->valor_referencia ?? 0),
                    "ShowPrice" => $safeBool($inmueble->mostrar_precio ?? true)
                ]
            ],

            // Añadir imágenes usando el ID original del JSON
            "PropertyDocument" => $this->getPropertyDocumentsFromOriginalId($originalId)
        ];

        // Log del payload antes de enviar (para debugging)
        Log::info('Payload enviado a Fotocasa', [
            'inmueble_id' => $inmueble->id,
            'original_id' => $originalId,
            'titulo' => $inmueble->titulo,
            'property_documents_count' => count($payload['PropertyDocument']),
            'property_documents' => $payload['PropertyDocument']
        ]);

        // DEBUG: Log detallado del payload completo
        Log::info('DEBUG - Payload completo enviado a Fotocasa', [
            'inmueble_id' => $inmueble->id,
            'original_id' => $originalId,
            'full_payload' => json_encode($payload, JSON_PRETTY_PRINT),
            'property_documents_section' => $payload['PropertyDocument'],
            'property_documents_count' => count($payload['PropertyDocument']),
            'property_documents_type' => gettype($payload['PropertyDocument']),
            'property_documents_is_array' => is_array($payload['PropertyDocument']),
            'property_documents_empty' => empty($payload['PropertyDocument'])
        ]);

        // Petición HTTP a Fotocasa
        $url = 'https://imports.gw.fotocasa.pro/api/property';

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Api-Key' => env('API_KEY'),
            ])->withOptions([
                'verify' => false, // Deshabilitar verificación SSL para desarrollo
                'timeout' => 30,   // Timeout de 30 segundos
            ])->post($url, $payload);

            // Log de la respuesta de Fotocasa
            Log::info('Respuesta de Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'original_id' => $originalId,
                'status_code' => $response->status(),
                'response_body' => $response->body(),
                'response_json' => $response->json()
            ]);

            // Retorna JSON con la respuesta de la API o error
            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'status' => $response->status(),
                    'message' => $response->body()
                ], $response->status());
            }
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en petición a Fotocasa API', [
                'error' => $e->getMessage(),
                'inmueble_id' => $inmueble->id,
                'original_id' => $originalId,
                'url' => $url
            ]);

            return response()->json([
                'success' => false,
                'status' => 500,
                'message' => 'Error de conexión con Fotocasa API: ' . $e->getMessage()
            ], 500);
        }
    }

    public function importFromJson()
    {
        try {
            // Leer el archivo JSON
            $jsonPath = base_path('viviendas2_formateado.json');
            if (!file_exists($jsonPath)) {
                return response()->json(['error' => 'Archivo JSON no encontrado'], 404);
            }

            $jsonContent = file_get_contents($jsonPath);
            $viviendas = json_decode($jsonContent, true);

            if (!$viviendas) {
                return response()->json(['error' => 'Error al parsear el JSON'], 400);
            }

            $imported = 0;
            $errors = [];
            $results = [];

            foreach ($viviendas as $id => $vivienda) {
                try {
                    // Convertir datos del JSON al formato del CRM
                    $inmuebleData = $this->convertJsonToInmuebleData($vivienda, $id);

                    // Crear el inmueble en la base de datos
                    $inmueble = Inmuebles::create($inmuebleData);
                    
                    // Disparar evento para enviar alertas a clientes
                    event(new InmuebleCreated($inmueble));

                    // Enviar a Fotocasa
                    $fotocasaResponse = $this->sendToFotocasa($inmueble);

                    $results[] = [
                        'id' => $id,
                        'titulo' => $vivienda['titulo'],
                        'inmueble_id' => $inmueble->id,
                        'fotocasa_status' => $fotocasaResponse->getStatusCode(),
                        'fotocasa_response' => $fotocasaResponse->getContent()
                    ];

                    $imported++;

                    // Pausa pequeña para no sobrecargar la API
                    usleep(500000); // 0.5 segundos

                } catch (\Exception $e) {
                    $errors[] = [
                        'id' => $id,
                        'titulo' => $vivienda['titulo'] ?? 'Sin título',
                        'error' => $e->getMessage()
                    ];

                    Log::error('Error importing property', [
                        'id' => $id,
                        'error' => $e->getMessage(),
                        'data' => $vivienda
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'imported' => $imported,
                'errors' => $errors,
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error('Error in importFromJson', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error general en la importación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convertir datos del JSON al formato del CRM
     */
    public function convertJsonToInmuebleData($vivienda, $externalId)
    {
        // Extraer características
        $caracteristicas = $vivienda['caracteristicas'] ?? [];

        // Convertir precio
        $precio = $this->extractPrice($vivienda['precio'] ?? '0 €');

        // Convertir metros
        $metros = $this->extractNumber($caracteristicas['metros'] ?? '0 m²');

        // Convertir habitaciones
        $habitaciones = $this->extractNumber($caracteristicas['habitaciones'] ?? '0 habs.');

        // Convertir baños
        $banos = $this->extractNumber($caracteristicas['baños'] ?? '0 baños');

        // Determinar tipo de vivienda
        $tipoVivienda = $this->mapTipoVivienda($caracteristicas['tipo_inmueble'] ?? '');

        // Determinar building subtype
        $buildingSubtype = $this->mapBuildingSubtype($caracteristicas['tipo_inmueble'] ?? '');

        // Extraer dirección
        $direccion = implode(' ', $vivienda['direccion'] ?? []);

        // Determinar coordenadas (usar coordenadas de Algeciras por defecto)
        $coordinates = $this->getCoordinatesFromAddress($direccion);

        // Mapear características booleanas
        $booleanFeatures = $this->mapBooleanFeatures($caracteristicas);

        // Mapear estado de conservación
        $conservationStatus = $this->mapConservationStatus($caracteristicas['estado'] ?? '');

        // Mapear certificación energética
        $energyCert = $this->mapEnergyCertification($caracteristicas);

        // Obtener imágenes del archivo imagenes.json
        $galeria = $this->getImagesFromJsonForProperty($externalId);

        return [
            'titulo' => $vivienda['titulo'] ?? 'Sin título',
            'descripcion' => $vivienda['descripcion'] ?? '',
            'm2' => $metros,
            'm2_construidos' => $metros,
            'valor_referencia' => $precio,
            'habitaciones' => $habitaciones,
            'banos' => $banos,
            'ubicacion' => $direccion,
            'cod_postal' => '11200', // Código postal de Algeciras
            'latitude' => $coordinates['lat'],
            'longitude' => $coordinates['lng'],
            'estado' => $conservationStatus,
            'disponibilidad' => 'disponible',
            'conservation_status' => $conservationStatus,
            'cert_energetico' => true,
            'cert_energetico_elegido' => $energyCert['scale'],
            'energy_certificate_status' => 'available',
            'year_built' => 2000, // Año por defecto
            'galeria' => json_encode($galeria), // Imágenes del archivo imagenes.json
            'otras_caracteristicas' => json_encode([]),
            'inmobiliaria' => session('inmobiliaria') === 'sayco' ? 1 : 0,
            // Campos Fotocasa
            'tipo_vivienda_id' => $tipoVivienda,
            'building_subtype_id' => $buildingSubtype,
            'transaction_type_id' => 1, // Venta
            'visibility_mode_id' => 1, // Público
            'floor_id' => $this->mapFloor($caracteristicas['planta'] ?? ''),
            'orientation_id' => 1, // Norte por defecto
            'heating_type_id' => 1, // Gas natural por defecto
            'hot_water_type_id' => 1, // Gas natural por defecto
            // Campos de eficiencia energética
            'consumption_efficiency_scale' => $energyCert['consumption_scale'],
            'emissions_efficiency_scale' => $energyCert['emissions_scale'],
            'consumption_efficiency_value' => $energyCert['consumption_value'],
            'emissions_efficiency_value' => $energyCert['emissions_value'],
            // Campos booleanos
            'furnished' => $booleanFeatures['furnished'],
            'has_elevator' => $booleanFeatures['has_elevator'],
            'has_terrace' => $booleanFeatures['has_terrace'],
            'has_balcony' => false,
            'has_parking' => $booleanFeatures['has_parking'],
            'has_air_conditioning' => true, // Asumir que tiene aire acondicionado
            'has_heating' => true, // Asumir que tiene calefacción
            'has_security_door' => false,
            'has_equipped_kitchen' => true,
            'has_wardrobe' => true,
            'has_storage_room' => $booleanFeatures['has_storage_room'],
            'pets_allowed' => false,
            // Campos adicionales
            'terrace_surface' => 0,
            'has_private_garden' => $booleanFeatures['has_private_garden'],
            'has_yard' => false,
            'has_smoke_outlet' => false,
            'has_community_pool' => false,
            'has_private_pool' => $booleanFeatures['has_private_pool'],
            'has_loading_area' => false,
            'has_24h_access' => false,
            'has_internal_transport' => false,
            'has_alarm' => false,
            'has_access_code' => false,
            'has_free_parking' => false,
            'has_laundry' => false,
            'has_community_area' => false,
            'has_office_kitchen' => false,
            'has_jacuzzi' => false,
            'has_sauna' => false,
            'has_tennis_court' => false,
            'has_gym' => false,
            'has_sports_area' => false,
            'has_children_area' => false,
            'has_home_automation' => false,
            'has_internet' => true,
            'has_suite_bathroom' => false,
            'has_home_appliances' => true,
            'has_oven' => true,
            'has_washing_machine' => true,
            'has_microwave' => true,
            'has_fridge' => true,
            'has_tv' => false,
            'has_parquet' => false,
            'has_stoneware' => false,
            'nearby_public_transport' => true,
            'land_area' => 0,
            'mostrar_precio' => true,
        ];
    }

    /**
     * Obtener imágenes del archivo imagenes_original.json para una propiedad específica
     */
    private function getImagesFromJsonForProperty($propertyId)
    {
        $galeria = [];

        // Leer el archivo imagenes_original.json
        $jsonPath = base_path('imagenes_original.json');
        if (!file_exists($jsonPath)) {
            return $galeria;
        }

        $jsonContent = file_get_contents($jsonPath);
        $imagenes = json_decode($jsonContent, true);

        if (!$imagenes || !isset($imagenes[$propertyId])) {
            return $galeria;
        }

        $propertyImages = $imagenes[$propertyId];
        $sortingId = 1;

        foreach ($propertyImages as $imageKey => $imageUrl) {
            // Las URLs ya están en formato original, no necesitan conversión
            $originalUrl = $imageUrl;

            // Agregar a la galería
            $galeria[$sortingId] = $originalUrl;
            $sortingId++;
        }

        return $galeria;
    }

    /**
     * Extraer precio del string
     */
    private function extractPrice($priceString)
    {
        preg_match('/[\d.]+/', $priceString, $matches);
        if (!empty($matches)) {
            // Elimina puntos y comas, y convierte a entero
            return (int)str_replace(['.', ','], '', $matches[0]);
        }
        return 0;
    }

    /**
     * Extraer número del string
     */
    private function extractNumber($string)
    {
        preg_match('/\d+/', $string, $matches);
        return !empty($matches) ? (int) $matches[0] : 0;
    }

    /**
     * Mapear tipo de vivienda a ID de Fotocasa
     */
    private function mapTipoVivienda($tipo)
    {
        $mapping = [
            'Piso' => 1,
            'Casa o chalet' => 2,
            'Casa adosada' => 2,
            'Dúplex' => 1,
            'Ático' => 1,
            'Estudio' => 1,
            'Loft' => 1,
        ];

        return $mapping[$tipo] ?? 1; // Flat por defecto
    }

    /**
     * Mapear building subtype
     */
    private function mapBuildingSubtype($tipo)
    {
        $mapping = [
            'Piso' => 9, // Flat
            'Casa o chalet' => 13, // House
            'Casa adosada' => 17, // Terraced house
            'Dúplex' => 3, // Duplex
            'Ático' => 5, // Penthouse
            'Estudio' => 6, // Studio
            'Loft' => 7, // Loft
        ];

        return $mapping[$tipo] ?? 9; // Flat por defecto
    }

    /**
     * Mapear planta
     */
    private function mapFloor($planta)
    {
        if (empty($planta)) return null;

        $mapping = [
            'Bajo' => 3, // Ground floor
            'Entresuelo' => 4, // Mezzanine
            '1' => 6, // First
            '2' => 7, // Second
            '3' => 8, // Third
            '4' => 9, // Fourth
            '5' => 10, // Fifth
            '6' => 11, // Sixth
            '7' => 12, // Seventh
            '8' => 13, // Eighth
            '9' => 14, // Ninth
            '10' => 15, // Tenth
            'Ático' => 22, // Penthouse
        ];

        return $mapping[$planta] ?? null;
    }

    /**
     * Mapear características booleanas
     */
    private function mapBooleanFeatures($caracteristicas)
    {
        return [
            'furnished' => strtolower($caracteristicas['amueblado'] ?? '') === 'sí',
            'has_elevator' => strtolower($caracteristicas['ascensor'] ?? '') === 'sí',
            'has_terrace' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'terraza') !== false,
            'has_parking' => !empty($caracteristicas['parking']),
            'has_storage_room' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'trastero') !== false,
            'has_private_garden' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'jardín') !== false,
            'has_private_pool' => strpos(strtolower($caracteristicas['metros'] ?? ''), 'piscina') !== false,
        ];
    }

    /**
     * Mapear estado de conservación
     */
    private function mapConservationStatus($estado)
    {
        $mapping = [
            'Muy bien' => 'excelente',
            'Bien' => 'bueno',
            'Regular' => 'regular',
            'Necesita reforma' => 'necesita reforma',
        ];

        return $mapping[$estado] ?? 'bueno';
    }

    /**
     * Mapear certificación energética
     */
    private function mapEnergyCertification($caracteristicas)
    {
        $scaleMap = [
            'A' => 1,
            'B' => 2,
            'C' => 3,
            'D' => 4,
            'E' => 5,
            'F' => 6,
            'G' => 7,
            'NC' => 0
        ];
        $consumo = $caracteristicas['consumo_energia'] ?? '';
        $emisiones = $caracteristicas['emisiones'] ?? '';
        $scale = 'G'; // Por defecto
        $consumption_scale = 7;
        $emissions_scale = 7;

        if (preg_match('/([A-G])/', $consumo, $m)) {
            $scale = $m[1];
            $consumption_scale = $scaleMap[$scale];
        }
        if (preg_match('/([A-G])/', $emisiones, $m)) {
            $emissions_scale = $scaleMap[$m[1]];
        }

        return [
            'scale' => $scale,
            'consumption_scale' => $consumption_scale,
            'emissions_scale' => $emissions_scale,
            'consumption_value' => 999,
            'emissions_value' => 999,
        ];
    }

    /**
     * Obtener coordenadas desde dirección
     */
    private function getCoordinatesFromAddress($address)
    {
        // Coordenadas por defecto de Algeciras (más precisas)
        $defaultCoords = [
            'lat' => 36.1408,
            'lng' => -5.4565
        ];

        if (empty($address)) {
            return $defaultCoords;
        }

        // Coordenadas específicas de barrios de Algeciras
        $barrioCoords = [
            'rinconcillo' => ['lat' => 36.1456, 'lng' => -5.4567],
            'casco antiguo' => ['lat' => 36.1408, 'lng' => -5.4565],
            'san garcía' => ['lat' => 36.1389, 'lng' => -5.4589],
            'los pinos' => ['lat' => 36.1423, 'lng' => -5.4543],
            'reconquista' => ['lat' => 36.1412, 'lng' => -5.4578],
            'el rosario' => ['lat' => 36.1434, 'lng' => -5.4556],
        ];

        // Primero intentar con coordenadas específicas de barrios
        $addressLower = strtolower($address);
        foreach ($barrioCoords as $barrio => $coords) {
            if (strpos($addressLower, $barrio) !== false) {
                return $coords;
            }
        }

        try {
            // Si no se encuentra el barrio, intentar geocodificación con Google Maps
            $response = Http::withOptions([
                'verify' => false,
                'timeout' => 10,
            ])->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address . ', Algeciras, Cádiz, Spain',
                'key' => env('GOOGLE_MAPS_API_KEY')
            ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'OK' && !empty($data['results'])) {
                    $location = $data['results'][0]['geometry']['location'];

                    // Validar que las coordenadas estén en España
                    $lat = $location['lat'];
                    $lng = $location['lng'];

                    if ($lat >= 35.0 && $lat <= 44.0 && $lng >= -10.0 && $lng <= 5.0) {
                        return [
                            'lat' => $lat,
                            'lng' => $lng
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            Log::warning('Error getting coordinates from Google Maps', [
                'address' => $address,
                'error' => $e->getMessage()
            ]);
        }

        // Si todo falla, usar coordenadas por defecto de Algeciras
        return $defaultCoords;
    }

    /**
     * Parsea un array de JSON de propiedad al formato del modelo Inmuebles
     */
    public function parseJsonToInmuebleArray($prop)
    {
        // Aquí debes mapear todos los campos relevantes del JSON al modelo
        // Ajusta los nombres de los campos según tu estructura
        return [
            'referencia' => $prop['referencia'] ?? null,
            'titulo' => $prop['titulo'] ?? null,
            'descripcion' => $prop['descripcion'] ?? null,
            'direccion' => $prop['direccion'] ?? null,
            'localidad' => $prop['localidad'] ?? null,
            'provincia' => $prop['provincia'] ?? null,
            'cp' => $prop['cp'] ?? null,
            'precio' => isset($prop['precio']) ? $this->extractPrice($prop['precio']) : 0,
            'superficie' => $prop['superficie'] ?? null,
            'habitaciones' => $prop['habitaciones'] ?? null,
            'banos' => $prop['banos'] ?? null,
            'tipo_vivienda_id' => $prop['tipo_vivienda_id'] ?? null,
            'estado' => $prop['estado'] ?? null,
            'galeria' => isset($prop['galeria']) ? json_encode($prop['galeria']) : null,
            // Agrega aquí el resto de campos necesarios
        ];
    }

    public function documentos(Inmuebles $inmueble)
    {
        return view('inmuebles.documentos', compact('inmueble'));
    }

    public function contratos(Inmuebles $inmueble)
    {
        return view('inmuebles.contratos', compact('inmueble'));
    }


    public function caracteristicas(Inmuebles $inmueble)
    {
        return view('inmuebles.caracteristicas', compact('inmueble'));
    }

    public function destroy(Inmuebles $inmueble)
    {
        try {
            $inmueble->delete();
            return redirect()->route('inmuebles.index')->with('success', 'Inmueble eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al eliminar el inmueble: ' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        try {
            $query = Inmuebles::query()->with(['tipoVivienda', 'vendedor']);

            // Ubicación
            if ($request->filled('ubicacion')) {
                $query->where('ubicacion', 'like', '%' . $request->ubicacion . '%');
            }

            // Valor de referencia
            if ($request->filled('valorMin')) {
                $query->where('valor_referencia', '>=', $request->valorMin);
            }
            if ($request->filled('valorMax')) {
                $query->where('valor_referencia', '<=', $request->valorMax);
            }

            // M²
            if ($request->filled('m2Min')) {
                $query->where('m2', '>=', $request->m2Min);
            }
            if ($request->filled('m2Max')) {
                $query->where('m2', '<=', $request->m2Max);
            }

            // Habitaciones
            if ($request->filled('habitaciones')) {
                $query->whereIn('habitaciones', $request->habitaciones);
            }

            // Baños
            if ($request->filled('banos')) {
                $query->whereIn('banos', $request->banos);
            }

            // Estado
            if ($request->filled('estado')) {
                $query->whereIn('estado', $request->estado);
            }

            // Disponibilidad
            if ($request->filled('disponibilidad')) {
                $query->whereIn('disponibilidad', $request->disponibilidad);
            }

            // Tipo de vivienda
            if ($request->filled('tipo-vivienda')) {
                $query->whereIn('tipo_vivienda_id', $request->get('tipo-vivienda'));
            }

            // Características (buscar en campos booleanos)
            if ($request->filled('caracteristicas')) {
                foreach ($request->caracteristicas as $caracteristica) {
                    switch ($caracteristica) {
                        case 'furnished':
                            $query->where('furnished', true);
                            break;
                        case 'has_elevator':
                            $query->where('has_elevator', true);
                            break;
                        case 'has_terrace':
                            $query->where('has_terrace', true);
                            break;
                        case 'has_balcony':
                            $query->where('has_balcony', true);
                            break;
                        case 'has_parking':
                            $query->where('has_parking', true);
                            break;
                        case 'has_air_conditioning':
                            $query->where('has_air_conditioning', true);
                            break;
                        case 'has_heating':
                            $query->where('has_heating', true);
                            break;
                        case 'has_security_door':
                            $query->where('has_security_door', true);
                            break;
                        case 'has_equipped_kitchen':
                            $query->where('has_equipped_kitchen', true);
                            break;
                        case 'has_wardrobe':
                            $query->where('has_wardrobe', true);
                            break;
                        case 'has_storage_room':
                            $query->where('has_storage_room', true);
                            break;
                        case 'pets_allowed':
                            $query->where('pets_allowed', true);
                            break;
                        case 'has_private_garden':
                            $query->where('has_private_garden', true);
                            break;
                        case 'has_yard':
                            $query->where('has_yard', true);
                            break;
                        case 'has_community_pool':
                            $query->where('has_community_pool', true);
                            break;
                        case 'has_private_pool':
                            $query->where('has_private_pool', true);
                            break;
                        case 'has_jacuzzi':
                            $query->where('has_jacuzzi', true);
                            break;
                        case 'has_sauna':
                            $query->where('has_sauna', true);
                            break;
                        case 'has_gym':
                            $query->where('has_gym', true);
                            break;
                        case 'has_home_automation':
                            $query->where('has_home_automation', true);
                            break;
                        case 'has_home_appliances':
                            $query->where('has_home_appliances', true);
                            break;
                        case 'has_oven':
                            $query->where('has_oven', true);
                            break;
                        case 'has_washing_machine':
                            $query->where('has_washing_machine', true);
                            break;
                        case 'has_fridge':
                            $query->where('has_fridge', true);
                            break;
                        case 'has_tv':
                            $query->where('has_tv', true);
                            break;
                    }
                }
            }

            $inmuebles = $query->get();

            return response()->json([
                'success' => true,
                'inmuebles' => $inmuebles
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error en la búsqueda: ' . $e->getMessage()
            ], 500);
        }
    }
}
