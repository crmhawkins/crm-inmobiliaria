<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\Clientes;
use App\Models\Caracteristicas;
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

    public function publicShow($id)
    {
        $inmueble = Inmuebles::with(['tipoVivienda', 'vendedor'])->findOrFail($id);

        // Decodificar otras_caracteristicas de forma segura
        $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];
        $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

        return view('inmuebles.public-show', compact('inmueble', 'caracteristicas'));
    }

    public function create()
    {
        $tipos_vivienda = TipoVivienda::all();
        $caracteristicas = Caracteristicas::all();
        $vendedores = Clientes::where('inmobiliaria', 1)->get();

        return view('inmuebles.create', compact('tipos_vivienda', 'caracteristicas', 'vendedores'));
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
            'tipo_vivienda_id' => 'required|exists:tipos_vivienda,id',
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
            'galeria.*' => 'nullable|url',
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

            // Crear la URL completa
            $url = asset('storage/' . $ruta);

            // Guardar en el formato JSON que usa el sistema
            $galeria = ['1' => $url];
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

        // Enviar a Fotocasa
        $fotocasaResponse = $this->sendToFotocasa($inmueble);

        // Si hay error en Fotocasa, logearlo pero continuar
        if (!$fotocasaResponse->getData()->success ?? false) {
            Log::warning('Error sending to Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'response' => $fotocasaResponse->getData()
            ]);
        }

        return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
    }

    public function sendToFotocasa(Inmuebles $inmueble)
    {
        // Función para mapear tipo_vivienda_id local a Fotocasa TypeId
        $mapToFotocasaType = function($localTipoId) {
            // Mapeo de tipos locales a Fotocasa
            // Esto debería basarse en los nombres de los tipos en tu base de datos
            $mapping = [
                1 => 1, // Piso -> Flat
                2 => 2, // Casa -> House
                3 => 3, // Local -> Commercial store
                4 => 4, // Oficina -> Office
                5 => 5, // Edificio -> Building
                6 => 6, // Terreno -> Land
                7 => 7, // Nave -> Industrial building
                8 => 8, // Garaje -> Garage
                9 => 12, // Trastero -> Storage room
            ];

            return $mapping[$localTipoId] ?? 1; // Por defecto Flat
        };

        // Obtener el TypeId de Fotocasa
        $fotocasaTypeId = $mapToFotocasaType($inmueble->tipo_vivienda_id);

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

            'cod_postal' => ['required','string'],
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
                    "ZipCode" => $safeString($inmueble->cod_postal),
                    "Street" => $safeString($inmueble->ubicacion),
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

            // Añadir imágenes si existen
            "PropertyDocument" => $this->getPropertyDocuments($inmueble)
        ];

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

    public function getPropertyDocuments(Inmuebles $inmueble)
    {
        $documents = [];

        // Procesar la galería de imágenes
        if ($inmueble->galeria) {
            $galeria = json_decode($inmueble->galeria, true);

            if (is_array($galeria)) {
                $sortingId = 1;

                foreach ($galeria as $key => $imageUrl) {
                    // Si la URL es relativa, convertirla a absoluta
                    if (!filter_var($imageUrl, FILTER_VALIDATE_URL) && strpos($imageUrl, 'http') !== 0) {
                        // Si es una ruta de storage, convertirla a URL pública
                        if (strpos($imageUrl, 'storage/') === 0) {
                            $imageUrl = url($imageUrl);
                        } else {
                            $imageUrl = url($imageUrl);
                        }
                    }

                    // Verificar que la URL sea válida y accesible
                    if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
                        // Verificar que la imagen sea accesible públicamente
                        try {
                            $response = Http::timeout(5)->head($imageUrl);
                            if ($response->successful()) {
                                $documents[] = [
                                    "TypeId" => 1, // Image
                                    "Url" => $imageUrl,
                                    "SortingId" => $sortingId
                                ];
                                $sortingId++;
                            } else {
                                Log::warning('Image not accessible', [
                                    'url' => $imageUrl,
                                    'status' => $response->status()
                                ]);
                            }
                        } catch (\Exception $e) {
                            Log::warning('Error checking image accessibility', [
                                'url' => $imageUrl,
                                'error' => $e->getMessage()
                            ]);
                        }
                    }
                }
            }
        }

        return $documents;
    }
}
