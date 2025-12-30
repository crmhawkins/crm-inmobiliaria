<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\Clientes;
use App\Models\Caracteristicas;
use App\Events\InmuebleCreated;
use App\Services\Idealista\IdealistaPropertiesService;
use App\Services\Idealista\IdealistaPropertyCreator;
use App\Services\Idealista\IdealistaContactsService;
use App\Services\Idealista\IdealistaVideosService;
use App\Services\Idealista\IdealistaVirtualToursService;
use App\Services\Idealista\IdealistaCustomerService;
use App\Services\Fotocasa\FotocasaClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

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

    public function idealistaRecent()
    {
        // Obtener los últimos 3 inmuebles subidos a Idealista
        $inmuebles = Inmuebles::whereNotNull('idealista_property_id')
            ->whereNotNull('idealista_synced_at')
            ->with(['tipoVivienda', 'vendedor'])
            ->orderBy('idealista_synced_at', 'desc')
            ->limit(3)
            ->get();

        $idealistaService = app(IdealistaPropertiesService::class);
        $inmueblesData = [];

        foreach ($inmuebles as $inmueble) {
            $idealistaData = null;
            $idealistaImages = [];

            try {
                // Obtener datos de Idealista
                $idealistaData = $idealistaService->find($inmueble->idealista_property_id);

                // Obtener imágenes de Idealista
                try {
                    $imagesResponse = $idealistaService->listImages($inmueble->idealista_property_id);
                    if (is_array($imagesResponse)) {
                        if (isset($imagesResponse['images'])) {
                            $idealistaImages = $imagesResponse['images'];
                        } elseif (isset($imagesResponse['data'])) {
                            $idealistaImages = $imagesResponse['data'];
                        } elseif (is_array($imagesResponse) && !empty($imagesResponse)) {
                            $idealistaImages = $imagesResponse;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('No se pudieron obtener imágenes de Idealista', [
                        'property_id' => $inmueble->idealista_property_id,
                        'error' => $e->getMessage()
                    ]);
                }
            } catch (\Exception $e) {
                Log::warning('No se pudieron obtener datos de Idealista', [
                    'property_id' => $inmueble->idealista_property_id,
                    'error' => $e->getMessage()
                ]);
            }

            $inmueblesData[] = [
                'inmueble' => $inmueble,
                'idealistaData' => $idealistaData,
                'idealistaImages' => $idealistaImages,
            ];
        }

        return view('inmuebles.idealista-recent', compact('inmueblesData'));
    }

    public function idealistaPreview($id)
    {
        $inmueble = Inmuebles::with(['tipoVivienda', 'vendedor'])->findOrFail($id);

        // Si el inmueble tiene un idealista_property_id, obtener datos de la API de Idealista
        $idealistaData = null;
        $idealistaImages = [];

        if ($inmueble->idealista_property_id) {
            try {
                $idealistaService = app(\App\Services\Idealista\IdealistaPropertiesService::class);
                $idealistaData = $idealistaService->find($inmueble->idealista_property_id);

                // Obtener imágenes de Idealista
                try {
                    $imagesResponse = $idealistaService->listImages($inmueble->idealista_property_id);
                    if (is_array($imagesResponse)) {
                        // La respuesta puede venir en diferentes formatos
                        if (isset($imagesResponse['images'])) {
                            $idealistaImages = $imagesResponse['images'];
                        } elseif (isset($imagesResponse['data'])) {
                            $idealistaImages = $imagesResponse['data'];
                        } elseif (is_array($imagesResponse) && !empty($imagesResponse)) {
                            $idealistaImages = $imagesResponse;
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning('No se pudieron obtener imágenes de Idealista', [
                        'property_id' => $inmueble->idealista_property_id,
                        'error' => $e->getMessage()
                    ]);
                }
            } catch (\Exception $e) {
                Log::error('Error obteniendo datos de Idealista', [
                    'property_id' => $inmueble->idealista_property_id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Decodificar otras_caracteristicas de forma segura
        $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];
        $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

        return view('inmuebles.idealista-preview', compact('inmueble', 'caracteristicas', 'idealistaData', 'idealistaImages'));
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

    public function edit(Inmuebles $inmueble)
    {
        $caracteristicas = Caracteristicas::all();
        $tipos_vivienda = TipoVivienda::all();

        // Incluir vendedores tradicionales (inmobiliaria = 1) Y contactos de Idealista
        $vendedores = Clientes::where(function($query) {
            $query->where('inmobiliaria', 1)
                  ->orWhereNotNull('idealista_contact_id');
        })->get();

        return view('inmuebles.edit', compact('inmueble', 'caracteristicas', 'tipos_vivienda', 'vendedores'));
    }

    public function update(Request $request, Inmuebles $inmueble)
    {
        $validated = $request->validate([
            'titulo' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
            'm2' => 'nullable|numeric|min:0',
            'm2_construidos' => 'nullable|numeric|min:0',
            'valor_referencia' => 'nullable|numeric|min:0',
            'habitaciones' => 'nullable|integer|min:0',
            'banos' => 'nullable|integer|min:0',
            'tipo_vivienda_id' => 'required|integer',
            'building_subtype_id' => 'nullable|integer',
            'transaction_type_id' => 'required|integer|in:1,3',
            'ubicacion' => 'nullable|string',
            'cod_postal' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'vendedor_id' => 'nullable|integer|exists:clientes,id',
        ]);

        $inmueble->update($validated);

        // Actualizar en Idealista si está sincronizado
        if ($inmueble->idealista_property_id) {
            try {
                $this->updateIdealistaProperty($request, $inmueble->id);
                Log::info('Inmueble actualizado en Idealista desde update', [
                    'inmueble_id' => $inmueble->id
                ]);
            } catch (\Exception $e) {
                Log::error('Error actualizando en Idealista desde update', [
                    'inmueble_id' => $inmueble->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Actualizar en Fotocasa si está publicado
        try {
            $this->sendToFotocasa($inmueble);
            Log::info('Inmueble actualizado en Fotocasa desde update', [
                'inmueble_id' => $inmueble->id
            ]);
        } catch (\Exception $e) {
            Log::error('Error actualizando en Fotocasa desde update', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);
        }

        return redirect()->route('inmuebles.admin-show', $inmueble)
            ->with('success', 'Inmueble actualizado correctamente');
    }

    public function create()
    {
        $caracteristicas = Caracteristicas::all();
        // Incluir vendedores tradicionales (inmobiliaria = 1) Y contactos de Idealista
        $vendedores = Clientes::where(function($query) {
            $query->where('inmobiliaria', 1)
                  ->orWhereNotNull('idealista_contact_id');
        })->orderBy('nombre_completo')->get();

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
            'cod_postal' => 'required|string|max:10',
            'referencia_catastral' => 'nullable|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
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
            'transaction_type_id' => 'required|integer|in:1,3',
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
            'm2' => $request->filled('m2') && $request->m2 > 0 ? (float) $request->m2 : null,
            'm2_construidos' => $request->filled('m2_construidos') && $request->m2_construidos > 0 ? (float) $request->m2_construidos : null,
            'valor_referencia' => $request->filled('valor_referencia') && $request->valor_referencia > 0 ? (float) $request->valor_referencia : null,
            'habitaciones' => $request->filled('habitaciones') && $request->habitaciones > 0 ? (int) $request->habitaciones : null,
            'banos' => $request->filled('banos') && $request->banos > 0 ? (int) $request->banos : null,
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
            'vendedor_id' => $request->vendedor_id,
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

        // Variables para rastrear errores de sincronización
        $idealistaError = null;
        $fotocasaError = null;

        // Crear en Idealista (solo si tiene los campos mínimos requeridos)
        try {
            // Validar que tenga los campos mínimos antes de intentar crear en Idealista
            if (!$inmueble->cod_postal) {
                $errorMessage = 'Inmueble sin código postal. Se requiere código postal para sincronizar con Idealista.';
                $idealistaError = $errorMessage;
                $inmueble->update([
                    'idealista_sync_error' => $errorMessage,
                    'idealista_last_sync_error_at' => now(),
                ]);
                Log::warning('Inmueble sin código postal, omitiendo creación en Idealista', [
                    'inmueble_id' => $inmueble->id,
                ]);
            } elseif (!$inmueble->m2 && !$inmueble->m2_construidos) {
                $errorMessage = 'Inmueble sin área especificada. Se requiere m2 o m2_construidos para sincronizar con Idealista.';
                $idealistaError = $errorMessage;
                $inmueble->update([
                    'idealista_sync_error' => $errorMessage,
                    'idealista_last_sync_error_at' => now(),
                ]);
                Log::warning('Inmueble sin área especificada, omitiendo creación en Idealista', [
                    'inmueble_id' => $inmueble->id,
                ]);
            } else {
                $idealistaCreator = app(IdealistaPropertyCreator::class);
                $idealistaService = app(IdealistaPropertiesService::class);

                // Cargar el vendedor si existe para obtener el contactId
                if ($inmueble->vendedor_id) {
                    $inmueble->load('vendedor');
                }

                // Convertir el inmueble al formato de Idealista
                $idealistaPayload = $idealistaCreator->toIdealistaFormat($inmueble);

                // Log del payload para debugging
                Log::debug('Payload enviado a Idealista', [
                    'inmueble_id' => $inmueble->id,
                    'payload' => $idealistaPayload,
                ]);

                // Crear la propiedad en Idealista
                $idealistaResponse = $idealistaService->create($idealistaPayload);

                // Actualizar el inmueble con los datos de Idealista
                $updateData = [
                    'idealista_property_id' => $idealistaResponse['propertyId'] ?? null,
                    'idealista_code' => $idealistaResponse['code'] ?? null,
                    'idealista_payload' => json_encode($idealistaResponse),
                    'idealista_synced_at' => now(),
                    'idealista_sync_error' => null,
                    'idealista_last_sync_error_at' => null,
                ];

                $inmueble->update($updateData);

                // Subir imágenes a Idealista si hay
                $images = $idealistaCreator->prepareImages($inmueble);
                if (!empty($images) && $inmueble->idealista_property_id) {
                    try {
                        $idealistaService->replaceImages(
                            $inmueble->idealista_property_id,
                            ['images' => $images]
                        );
                    } catch (\Exception $e) {
                        Log::warning('Error subiendo imágenes a Idealista', [
                            'inmueble_id' => $inmueble->id,
                            'idealista_property_id' => $inmueble->idealista_property_id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                Log::info('Inmueble creado en Idealista', [
                    'inmueble_id' => $inmueble->id,
                    'idealista_property_id' => $inmueble->idealista_property_id,
                ]);
            }
        } catch (\Illuminate\Http\Client\RequestException $e) {
            // Capturar respuesta completa de la API
            $response = $e->response;
            $errorBody = $response ? $response->body() : 'No response body';
            $errorJson = $response ? $response->json() : null;
            $statusCode = $response ? $response->status() : null;

            $errorMessage = "Error HTTP {$statusCode}: " . $e->getMessage();
            if ($errorJson && isset($errorJson['message'])) {
                $errorMessage .= "\n" . $errorJson['message'];
            }
            if ($errorJson && isset($errorJson['errors'])) {
                $errorMessage .= "\nErrores: " . json_encode($errorJson['errors'], JSON_UNESCAPED_UNICODE);
            }

            $idealistaError = $errorMessage;

            $inmueble->update([
                'idealista_sync_error' => $errorMessage,
                'idealista_last_sync_error_at' => now(),
            ]);

            Log::error('Error creando inmueble en Idealista (HTTP)', [
                'inmueble_id' => $inmueble->id,
                'status_code' => $statusCode,
                'error_message' => $e->getMessage(),
                'error_body' => $errorBody,
                'error_json' => $errorJson,
            ]);
            // No fallar la creación del inmueble si Idealista falla
        } catch (\Exception $e) {
            $errorMessage = get_class($e) . ": " . $e->getMessage();
            $idealistaError = $errorMessage;

            $inmueble->update([
                'idealista_sync_error' => $errorMessage,
                'idealista_last_sync_error_at' => now(),
            ]);

            Log::error('Error creando inmueble en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            // No fallar la creación del inmueble si Idealista falla
        }

        // Enviar a Fotocasa
        $fotocasaResponse = $this->sendToFotocasa($inmueble);
        //dd($fotocasaResponse);
        // Si hay error en Fotocasa, logearlo pero continuar
        if ($fotocasaResponse->getStatusCode() !== 200) {
            $responseContent = $fotocasaResponse->getContent();
            $responseData = json_decode($responseContent, true);

            $errorMessage = "Error HTTP {$fotocasaResponse->getStatusCode()}: " . ($responseData['message'] ?? 'Error desconocido');
            if (isset($responseData['errors'])) {
                $errorMessage .= "\nErrores: " . json_encode($responseData['errors'], JSON_UNESCAPED_UNICODE);
            }

            $fotocasaError = $errorMessage;

            $inmueble->update([
                'fotocasa_sync_error' => $errorMessage,
                'fotocasa_last_sync_error_at' => now(),
            ]);

            Log::warning('Error sending to Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'status' => $fotocasaResponse->getStatusCode(),
                'response' => $responseContent
            ]);
        } else {
            // Limpiar errores si la sincronización fue exitosa
            $inmueble->update([
                'fotocasa_sync_error' => null,
                'fotocasa_last_sync_error_at' => null,
            ]);
        }

        // Preparar mensaje de éxito con advertencias si hay errores
        $message = 'Inmueble creado correctamente.';
        if ($idealistaError || $fotocasaError) {
            $message .= ' Sin embargo, hubo problemas al sincronizar:';
            if ($idealistaError) {
                $message .= ' Idealista falló.';
            }
            if ($fotocasaError) {
                $message .= ' Fotocasa falló.';
            }
            $message .= ' Puedes ver los detalles y reintentar desde la página de detalle del inmueble.';
        }

        return redirect()->route('inmuebles.admin-show', $inmueble)
            ->with('success', $message)
            ->with('idealista_error', $idealistaError)
            ->with('fotocasa_error', $fotocasaError);
    }

    /**
     * Construye el payload para Fotocasa a partir de un inmueble
     * Método público para uso en servicios externos
     */
    public function buildFotocasaPayload(Inmuebles $inmueble): array
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

        return $payload;
    }

    public function sendToFotocasa(Inmuebles $inmueble)
    {
        // Construir el payload
        $payload = $this->buildFotocasaPayload($inmueble);

        // Log del payload antes de enviar (para debugging)
        Log::info('Payload enviado a Fotocasa', [
            'inmueble_id' => $inmueble->id,
            'titulo' => $inmueble->titulo,
            'property_documents_count' => count($payload['PropertyDocument']),
            'property_documents' => $payload['PropertyDocument']
        ]);

        // Usar el servicio centralizado de Fotocasa
        try {
            $fotocasaClient = new FotocasaClient();
            $response = $fotocasaClient->createOrUpdateProperty($payload);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en petición a Fotocasa API', [
                'error' => $e->getMessage(),
                'inmueble_id' => $inmueble->id,
            ]);

            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'success' => false,
                'status' => $statusCode,
                'message' => 'Error de conexión con Fotocasa API: ' . $e->getMessage()
            ], $statusCode);
        }
    }

    /**
     * Reintentar sincronización con Idealista
     */
    public function retrySyncToIdealista(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        try {
            // Validar que tenga los campos mínimos
            if (!$inmueble->cod_postal) {
                $errorMessage = 'Inmueble sin código postal. Se requiere código postal para sincronizar con Idealista.';
                $inmueble->update([
                    'idealista_sync_error' => $errorMessage,
                    'idealista_last_sync_error_at' => now(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            if (!$inmueble->m2 && !$inmueble->m2_construidos) {
                $errorMessage = 'Inmueble sin área especificada. Se requiere m2 o m2_construidos para sincronizar con Idealista.';
                $inmueble->update([
                    'idealista_sync_error' => $errorMessage,
                    'idealista_last_sync_error_at' => now(),
                ]);
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 400);
            }

            $idealistaCreator = app(IdealistaPropertyCreator::class);
            $idealistaService = app(IdealistaPropertiesService::class);

            // Cargar el vendedor si existe para obtener el contactId
            if ($inmueble->vendedor_id) {
                $inmueble->load('vendedor');
            }

            // Convertir el inmueble al formato de Idealista
            $idealistaPayload = $idealistaCreator->toIdealistaFormat($inmueble);

            // Crear la propiedad en Idealista
            $idealistaResponse = $idealistaService->create($idealistaPayload);

            // Actualizar el inmueble con los datos de Idealista
            $updateData = [
                'idealista_property_id' => $idealistaResponse['propertyId'] ?? null,
                'idealista_code' => $idealistaResponse['code'] ?? null,
                'idealista_payload' => json_encode($idealistaResponse),
                'idealista_synced_at' => now(),
                'idealista_sync_error' => null,
                'idealista_last_sync_error_at' => null,
            ];

            $inmueble->update($updateData);

            // Subir imágenes a Idealista si hay
            $images = $idealistaCreator->prepareImages($inmueble);
            if (!empty($images) && $inmueble->idealista_property_id) {
                try {
                    $idealistaService->replaceImages(
                        $inmueble->idealista_property_id,
                        ['images' => $images]
                    );
                } catch (\Exception $e) {
                    Log::warning('Error subiendo imágenes a Idealista', [
                        'inmueble_id' => $inmueble->id,
                        'idealista_property_id' => $inmueble->idealista_property_id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            Log::info('Inmueble re-sincronizado con Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Inmueble sincronizado correctamente con Idealista',
                'data' => [
                    'idealista_property_id' => $inmueble->idealista_property_id,
                    'idealista_code' => $inmueble->idealista_code,
                ]
            ]);

        } catch (\Illuminate\Http\Client\RequestException $e) {
            $response = $e->response;
            $errorBody = $response ? $response->body() : 'No response body';
            $errorJson = $response ? $response->json() : null;
            $statusCode = $response ? $response->status() : null;

            $errorMessage = "Error HTTP {$statusCode}: " . $e->getMessage();
            if ($errorJson && isset($errorJson['message'])) {
                $errorMessage .= "\n" . $errorJson['message'];
            }
            if ($errorJson && isset($errorJson['errors'])) {
                $errorMessage .= "\nErrores: " . json_encode($errorJson['errors'], JSON_UNESCAPED_UNICODE);
            }

            $inmueble->update([
                'idealista_sync_error' => $errorMessage,
                'idealista_last_sync_error_at' => now(),
            ]);

            Log::error('Error re-sincronizando inmueble con Idealista (HTTP)', [
                'inmueble_id' => $inmueble->id,
                'status_code' => $statusCode,
                'error_message' => $e->getMessage(),
                'error_body' => $errorBody,
                'error_json' => $errorJson,
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], $statusCode ?: 500);

        } catch (\Exception $e) {
            $errorMessage = get_class($e) . ": " . $e->getMessage();

            $inmueble->update([
                'idealista_sync_error' => $errorMessage,
                'idealista_last_sync_error_at' => now(),
            ]);

            Log::error('Error re-sincronizando inmueble con Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 500);
        }
    }

    /**
     * Reintentar sincronización con Fotocasa
     */
    public function retrySyncToFotocasa(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        try {
            $fotocasaResponse = $this->sendToFotocasa($inmueble);

            if ($fotocasaResponse->getStatusCode() !== 200) {
                $responseContent = $fotocasaResponse->getContent();
                $responseData = json_decode($responseContent, true);

                $errorMessage = "Error HTTP {$fotocasaResponse->getStatusCode()}: " . ($responseData['message'] ?? 'Error desconocido');
                if (isset($responseData['errors'])) {
                    $errorMessage .= "\nErrores: " . json_encode($responseData['errors'], JSON_UNESCAPED_UNICODE);
                }

                $inmueble->update([
                    'fotocasa_sync_error' => $errorMessage,
                    'fotocasa_last_sync_error_at' => now(),
                ]);

                Log::warning('Error re-sincronizando con Fotocasa', [
                    'inmueble_id' => $inmueble->id,
                    'status' => $fotocasaResponse->getStatusCode(),
                    'response' => $responseContent
                ]);

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $fotocasaResponse->getStatusCode());
            } else {
                // Limpiar errores si la sincronización fue exitosa
                $inmueble->update([
                    'fotocasa_sync_error' => null,
                    'fotocasa_last_sync_error_at' => null,
                ]);

                Log::info('Inmueble re-sincronizado con Fotocasa', [
                    'inmueble_id' => $inmueble->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Inmueble sincronizado correctamente con Fotocasa'
                ]);
            }
        } catch (\Exception $e) {
            $errorMessage = get_class($e) . ": " . $e->getMessage();

            $inmueble->update([
                'fotocasa_sync_error' => $errorMessage,
                'fotocasa_last_sync_error_at' => now(),
            ]);

            Log::error('Error re-sincronizando inmueble con Fotocasa', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage(),
                'error_class' => get_class($e),
            ]);

            return response()->json([
                'success' => false,
                'message' => $errorMessage
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

        // Usar el servicio centralizado de Fotocasa
        try {
            $fotocasaClient = new FotocasaClient();
            $response = $fotocasaClient->createOrUpdateProperty($payload);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);
        } catch (\Exception $e) {
            // Log del error para debugging
            Log::error('Error en petición a Fotocasa API', [
                'error' => $e->getMessage(),
                'inmueble_id' => $inmueble->id,
                'original_id' => $originalId,
            ]);

            $statusCode = $e->getCode() ?: 500;
            return response()->json([
                'success' => false,
                'status' => $statusCode,
                'message' => 'Error de conexión con Fotocasa API: ' . $e->getMessage()
            ], $statusCode);
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
        // Cargar los documentos del inmueble
        $documentos = \App\Models\DocInmueble::where('inmueble_id', $inmueble->id)->get();

        // Pasar los documentos a la vista
        return view('inmuebles.documentos', compact('inmueble', 'documentos'));
    }

    public function contratos(Inmuebles $inmueble)
    {
        // Cargar los contratos del inmueble
        $contratos = \App\Models\ContratoArras::where('inmueble_id', $inmueble->id)->get();

        // Pasar los contratos a la vista
        return view('inmuebles.contratos', compact('inmueble', 'contratos'));
    }


    public function caracteristicas(Inmuebles $inmueble)
    {
        return view('inmuebles.caracteristicas', compact('inmueble'));
    }

    public function visitas(Inmuebles $inmueble)
    {
        // Obtener las visitas relacionadas con este inmueble desde la tabla eventos
        // Las visitas pueden estar identificadas por tipo_tarea = 'visita' o similar
        $visitas = \App\Models\Evento::where('inmueble_id', $inmueble->id)
            ->where(function($query) {
                $query->where('tipo_tarea', 'visita')
                      ->orWhere('tipo_tarea', 'Visita')
                      ->orWhere('titulo', 'like', '%visita%')
                      ->orWhere('titulo', 'like', '%Visita%');
            })
            ->with(['cliente', 'inmueble'])
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        return view('inmuebles.visitas.index', compact('inmueble', 'visitas'));
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

    /**
     * Proxy para búsquedas de Nominatim (evita problemas de CORS)
     */
    public function searchNominatim(Request $request): JsonResponse
    {
        $query = $request->input('q');
        $limit = $request->input('limit', 5);
        $countrycodes = $request->input('countrycodes', 'es');
        $addressdetails = $request->input('addressdetails', 1);

        if (!$query) {
            return response()->json([
                'error' => 'El parámetro "q" es requerido'
            ], 400);
        }

        try {
            $url = 'https://nominatim.openstreetmap.org/search';
            $response = Http::withHeaders([
                'User-Agent' => 'CRM-Inmobiliaria/1.0',
                'Accept' => 'application/json',
            ])->withoutVerifying()->timeout(10)->get($url, [
                'format' => 'json',
                'q' => $query,
                'countrycodes' => $countrycodes,
                'limit' => $limit,
                'addressdetails' => $addressdetails,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Error en la respuesta de Nominatim',
                'status' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error en proxy de Nominatim', [
                'query' => $query,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error al buscar la dirección: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Endpoint público para servir imágenes
     * Necesario para que Idealista pueda descargar las imágenes desde URLs públicas
     *
     * URL de ejemplo: https://tudominio.com/storage/images/photos/1/imagen.jpg
     */
    public function servePublicImage(Request $request, string $path)
    {
        // Validar que la ruta no contenga caracteres peligrosos (path traversal)
        if (strpos($path, '..') !== false || strpos($path, '//') !== false) {
            abort(403, 'Ruta no permitida');
        }

        // Construir la ruta completa del archivo
        $filePath = storage_path('app/public/' . $path);

        // Normalizar la ruta para evitar path traversal
        $filePath = realpath($filePath);
        $publicPath = realpath(storage_path('app/public'));

        // Verificar que el archivo está dentro del directorio público
        if (!$filePath || strpos($filePath, $publicPath) !== 0) {
            abort(403, 'Acceso no permitido');
        }

        // Verificar que el archivo existe
        if (!file_exists($filePath) || !is_file($filePath)) {
            abort(404, 'Imagen no encontrada');
        }

        // Obtener el tipo MIME
        $mimeType = mime_content_type($filePath);
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/jpg'];

        if (!in_array($mimeType, $allowedMimes)) {
            abort(403, 'Tipo de archivo no permitido');
        }

        // Leer y devolver el archivo con headers apropiados
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Cache-Control' => 'public, max-age=31536000', // Cache por 1 año
            'Content-Disposition' => 'inline; filename="' . basename($filePath) . '"',
        ]);
    }

    /**
     * Proxy para geocodificación inversa de Nominatim
     */

    public function reverseNominatim(Request $request): JsonResponse
    {
        $lat = $request->input('lat');
        $lon = $request->input('lon');
        $zoom = $request->input('zoom', 18);
        $addressdetails = $request->input('addressdetails', 1);

        if (!$lat || !$lon) {
            return response()->json([
                'error' => 'Los parámetros "lat" y "lon" son requeridos'
            ], 400);
        }

        try {
            $url = 'https://nominatim.openstreetmap.org/reverse';
            $response = Http::withHeaders([
                'User-Agent' => 'CRM-Inmobiliaria/1.0',
                'Accept' => 'application/json',
            ])->withoutVerifying()->timeout(10)->get($url, [
                'format' => 'json',
                'lat' => $lat,
                'lon' => $lon,
                'zoom' => $zoom,
                'addressdetails' => $addressdetails,
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'error' => 'Error en la respuesta de Nominatim',
                'status' => $response->status()
            ], $response->status());

        } catch (\Exception $e) {
            Log::error('Error en proxy reverso de Nominatim', [
                'lat' => $lat,
                'lon' => $lon,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'error' => 'Error en geocodificación inversa: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * ============================================
     * MÉTODOS PARA GESTIÓN COMPLETA DE IDEALISTA
     * ============================================
     */

    /**
     * Actualiza una propiedad en Idealista
     */
    public function updateIdealistaProperty(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        try {
            $propertyCreator = app(IdealistaPropertyCreator::class);
            $payload = $propertyCreator->toIdealistaFormat($inmueble);

            $propertiesService = app(IdealistaPropertiesService::class);
            $response = $propertiesService->update($inmueble->idealista_property_id, $payload);

            // Actualizar el payload guardado en el CRM
            $inmueble->update([
                'idealista_payload' => json_encode($payload),
                'idealista_synced_at' => now(),
            ]);

            Log::info('Propiedad actualizada en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Propiedad actualizada en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error actualizando propiedad en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la propiedad en Idealista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desactiva una propiedad en Idealista
     */
    public function deactivateIdealistaProperty($id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        try {
            $propertiesService = app(IdealistaPropertiesService::class);
            $response = $propertiesService->deactivate($inmueble->idealista_property_id);

            Log::info('Propiedad desactivada en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Propiedad desactivada en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error desactivando propiedad en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar la propiedad en Idealista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Reactiva una propiedad en Idealista
     */
    public function reactivateIdealistaProperty($id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        try {
            $propertiesService = app(IdealistaPropertiesService::class);
            $response = $propertiesService->reactivate($inmueble->idealista_property_id);

            Log::info('Propiedad reactivada en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Propiedad reactivada en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error reactivando propiedad en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al reactivar la propiedad en Idealista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clona una propiedad en Idealista para otra operación (venta/alquiler)
     */
    public function cloneIdealistaProperty(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        $request->validate([
            'operation' => 'required|in:sale,rent'
        ]);

        try {
            $propertiesService = app(IdealistaPropertiesService::class);
            $payload = [
                'operation' => $request->operation
            ];

            $response = $propertiesService->cloneProperty($inmueble->idealista_property_id, $payload);

            Log::info('Propiedad clonada en Idealista', [
                'inmueble_id' => $inmueble->id,
                'idealista_property_id' => $inmueble->idealista_property_id,
                'operation' => $request->operation,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Propiedad clonada en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error clonando propiedad en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al clonar la propiedad en Idealista: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista todos los contactos de Idealista
     */
    public function listIdealistaContacts(Request $request)
    {
        try {
            $contactsService = app(IdealistaContactsService::class);
            $page = $request->input('page', 1);
            $size = $request->input('size', 50);

            $response = $contactsService->list($page, $size);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error listando contactos de Idealista', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al listar contactos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un contacto en Idealista
     */
    public function createIdealistaContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'nullable|string',
        ]);

        try {
            $contactsService = app(IdealistaContactsService::class);
            $payload = $request->only(['name', 'email', 'phone']);

            $response = $contactsService->create($payload);

            // Si el contacto se creó correctamente, actualizar el cliente en el CRM
            if (isset($response['contactId'])) {
                $cliente = Clientes::where('email', $request->email)->first();
                if ($cliente) {
                    $cliente->update([
                        'idealista_contact_id' => $response['contactId']
                    ]);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Contacto creado en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando contacto en Idealista', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear contacto: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista todos los videos de una propiedad en Idealista
     */
    public function listIdealistaVideos($id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        try {
            $videosService = app(IdealistaVideosService::class);
            $response = $videosService->list($inmueble->idealista_property_id);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error listando videos de Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al listar videos: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un video para una propiedad en Idealista
     */
    public function createIdealistaVideo(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        $request->validate([
            'url' => 'required|url',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        try {
            $videosService = app(IdealistaVideosService::class);
            $payload = $request->only(['url', 'title', 'description']);

            $response = $videosService->create($inmueble->idealista_property_id, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Video creado en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando video en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Elimina un video de una propiedad en Idealista
     */
    public function deleteIdealistaVideo(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        $request->validate([
            'video_id' => 'required|integer',
        ]);

        try {
            $videosService = app(IdealistaVideosService::class);
            $response = $videosService->delete($inmueble->idealista_property_id, $request->video_id);

            return response()->json([
                'success' => true,
                'message' => 'Video eliminado de Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error eliminando video de Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Desactiva un tour virtual en Idealista
     */
    public function deactivateIdealistaVirtualTour(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        $request->validate([
            'type' => 'required|in:3d,virtual'
        ]);

        try {
            $virtualToursService = app(IdealistaVirtualToursService::class);
            $payload = $request->only(['type', 'url', 'provider']);

            $response = $virtualToursService->deactivate($inmueble->idealista_property_id, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Tour virtual desactivado correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error desactivando tour virtual en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al desactivar tour virtual: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista los tours virtuales de una propiedad en Idealista
     */
    public function listIdealistaVirtualTours($id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        try {
            $virtualToursService = app(IdealistaVirtualToursService::class);
            $response = $virtualToursService->find($inmueble->idealista_property_id);

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error listando tours virtuales de Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al listar tours virtuales: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crea un tour virtual para una propiedad en Idealista
     */
    public function createIdealistaVirtualTour(Request $request, $id)
    {
        $inmueble = Inmuebles::findOrFail($id);

        if (!$inmueble->idealista_property_id) {
            return response()->json([
                'success' => false,
                'message' => 'Este inmueble no está sincronizado con Idealista'
            ], 400);
        }

        $request->validate([
            'url' => 'required|url',
            'type' => 'required|in:3d,virtual',
            'provider' => 'nullable|string',
        ]);

        try {
            $virtualToursService = app(IdealistaVirtualToursService::class);
            $payload = $request->only(['url', 'type', 'provider']);

            $response = $virtualToursService->create($inmueble->idealista_property_id, $payload);

            return response()->json([
                'success' => true,
                'message' => 'Tour virtual creado en Idealista correctamente',
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error creando tour virtual en Idealista', [
                'inmueble_id' => $inmueble->id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al crear tour virtual: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtiene información de publicación del cliente en Idealista
     */
    public function getIdealistaPublicationInfo()
    {
        try {
            $customerService = app(IdealistaCustomerService::class);
            $response = $customerService->getPublicationInfo();

            return response()->json([
                'success' => true,
                'data' => $response
            ]);

        } catch (\Exception $e) {
            Log::error('Error obteniendo información de publicación de Idealista', [
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Lista todas las propiedades de Idealista (con filtros)
     */
    public function listIdealistaProperties(Request $request)
    {
        try {
            $state = $request->input('state'); // active, inactive, pending
            $forceSync = $request->input('force_sync', false); // Para forzar sincronización completa

            // Obtener propiedades de la BD local
            $query = Inmuebles::whereNotNull('idealista_property_id');

            if ($state) {
                // Mapear estado de Idealista a campo local si existe
                // Por ahora filtramos por sincronización reciente
            }

            $localProperties = $query->get();
            $syncedPropertyIds = $localProperties->pluck('idealista_property_id')->toArray();

            // Si hay propiedades sin sincronizar o se fuerza la sincronización, actualizar desde API
            // Pero respetando rate limits: solo sincronizar máximo 10 propiedades por petición
            $propertiesService = app(IdealistaPropertiesService::class);
            $propertiesToSync = [];
            $syncedCount = 0;
            $maxSyncPerRequest = 10; // Límite para respetar rate limits

            if ($forceSync || count($syncedPropertyIds) === 0) {
                // Obtener lista desde API (máximo 50)
                $response = $propertiesService->list(1, 50, $state);

                // Extraer propiedades de la respuesta
                $apiProperties = [];
                if (isset($response['properties']) && is_array($response['properties'])) {
                    $apiProperties = $response['properties'];
                } elseif (isset($response['data']['properties']) && is_array($response['data']['properties'])) {
                    $apiProperties = $response['data']['properties'];
                } elseif (isset($response['data']['data']['properties']) && is_array($response['data']['data']['properties'])) {
                    $apiProperties = $response['data']['data']['properties'];
                }

                // Identificar propiedades que no están en BD local o necesitan actualización
                foreach ($apiProperties as $apiProperty) {
                    $propertyId = $apiProperty['propertyId'] ?? null;
                    if (!$propertyId) continue;

                    $localProperty = Inmuebles::where('idealista_property_id', $propertyId)->first();

                    // Si no existe o necesita actualización (más de 24 horas sin sincronizar)
                    if (!$localProperty ||
                        !$localProperty->idealista_synced_at ||
                        $localProperty->idealista_synced_at->lt(now()->subHours(24))) {

                        if ($syncedCount < $maxSyncPerRequest) {
                            $propertiesToSync[] = $apiProperty;
                            $syncedCount++;
                        }
                    }
                }

                // Sincronizar propiedades (una por una para respetar rate limits)
                foreach ($propertiesToSync as $apiProperty) {
                    try {
                        $this->syncPropertyFromIdealista($apiProperty);
                        // Pequeña pausa para respetar rate limits
                        usleep(200000); // 0.2 segundos entre peticiones
                    } catch (\Exception $e) {
                        Log::warning('Error sincronizando propiedad de Idealista', [
                            'property_id' => $apiProperty['propertyId'] ?? null,
                            'error' => $e->getMessage()
                        ]);
                        // Continuar con la siguiente propiedad
                    }
                }
            }

            // Recargar propiedades de BD local después de la sincronización
            $localProperties = $query->get();

            // Convertir a formato similar al de la API para la vista
            $properties = $localProperties->map(function ($property) {
                $payload = $property->idealista_payload ? json_decode($property->idealista_payload, true) : [];

                return [
                    'propertyId' => $property->idealista_property_id,
                    'reference' => $property->idealista_code ?? $property->titulo,
                    'type' => $this->mapBuildingTypeToIdealista($property->tipo_vivienda_id),
                    'state' => $payload['state'] ?? 'unknown',
                    'address' => [
                        'streetName' => $this->extractStreetName($property->ubicacion),
                        'streetNumber' => $this->extractStreetNumber($property->ubicacion),
                        'town' => $this->extractTown($property->ubicacion),
                        'postalCode' => $property->cod_postal ?? '',
                        'latitude' => $property->latitude ?? 0,
                        'longitude' => $property->longitude ?? 0,
                    ],
                    'operation' => [
                        'type' => $property->transaction_type_id == 3 ? 'rent' : 'sale',
                        'price' => $property->valor_referencia ?? 0,
                    ],
                    'features' => [
                        'rooms' => $property->habitaciones ?? 0,
                        'bathroomNumber' => $property->banos ?? 0,
                        'areaConstructed' => $property->m2_construidos ?? $property->m2 ?? 0,
                    ],
                    'descriptions' => $payload['descriptions'] ?? [],
                    'contactId' => $payload['contactId'] ?? null,
                    '_local_id' => $property->id, // ID local para enlaces
                ];
            })->toArray();

            $totalProperties = count($properties);
            $activeProperties = count(array_filter($properties, fn($p) => ($p['state'] ?? '') === 'active'));
            $inactiveProperties = count(array_filter($properties, fn($p) => ($p['state'] ?? '') === 'inactive'));

            // Si es una petición AJAX, devolver JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'properties' => $properties,
                        'synced' => $syncedCount,
                        'total' => $totalProperties
                    ]
                ]);
            }

            return view('inmuebles.idealista-list', compact('properties', 'totalProperties', 'activeProperties', 'inactiveProperties', 'state', 'syncedCount'));

        } catch (\Exception $e) {
            Log::error('Error listando propiedades de Idealista', [
                'error' => $e->getMessage()
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error al listar propiedades: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->route('inmuebles.idealista')
                ->with('error', 'Error al listar propiedades: ' . $e->getMessage());
        }
    }

    /**
     * Sincronizar una propiedad desde Idealista a BD local
     */
    private function syncPropertyFromIdealista(array $apiProperty): void
    {
        $propertyId = $apiProperty['propertyId'] ?? null;
        if (!$propertyId) return;

        $localProperty = Inmuebles::where('idealista_property_id', $propertyId)->first();

        $data = [
            'idealista_property_id' => $propertyId,
            'idealista_code' => $apiProperty['reference'] ?? null,
            'idealista_payload' => json_encode($apiProperty),
            'idealista_synced_at' => now(),
        ];

        // Actualizar campos básicos si están disponibles
        if (isset($apiProperty['address'])) {
            $address = $apiProperty['address'];
            $data['ubicacion'] = trim(($address['streetName'] ?? '') . ' ' . ($address['streetNumber'] ?? '') . ', ' . ($address['town'] ?? ''));
            $data['cod_postal'] = $address['postalCode'] ?? null;
            $data['latitude'] = $address['latitude'] ?? null;
            $data['longitude'] = $address['longitude'] ?? null;
        }

        if (isset($apiProperty['operation'])) {
            $operation = $apiProperty['operation'];
            $data['transaction_type_id'] = ($operation['type'] ?? '') === 'rent' ? 3 : 1;
            $data['valor_referencia'] = $operation['price'] ?? null;
        }

        if (isset($apiProperty['features'])) {
            $features = $apiProperty['features'];
            $data['habitaciones'] = $features['rooms'] ?? null;
            $data['banos'] = $features['bathroomNumber'] ?? null;
            $data['m2_construidos'] = $features['areaConstructed'] ?? null;
            if (!isset($data['m2_construidos'])) {
                $data['m2'] = $features['areaConstructed'] ?? null;
            }
        }

        if ($localProperty) {
            $localProperty->update($data);
        } else {
            // Crear nueva propiedad si no existe
            $data['titulo'] = $apiProperty['reference'] ?? 'Propiedad Idealista ' . $propertyId;
            $data['descripcion'] = '';
            if (isset($apiProperty['descriptions']) && is_array($apiProperty['descriptions'])) {
                $esDescription = collect($apiProperty['descriptions'])->firstWhere('language', 'es');
                $data['descripcion'] = $esDescription['text'] ?? '';
            }
            $data['tipo_vivienda_id'] = $this->mapIdealistaTypeToBuildingType($apiProperty['type'] ?? 'flat');
            $data['estado'] = 'disponible';
            $data['disponibilidad'] = 'disponible';

            Inmuebles::create($data);
        }
    }

    /**
     * Mapear tipo de Idealista a building_type_id
     */
    private function mapIdealistaTypeToBuildingType(string $type): int
    {
        $mapping = [
            'flat' => 1,
            'house' => 2,
            'studio' => 1,
            'penthouse' => 1,
            'duplex' => 1,
        ];
        return $mapping[strtolower($type)] ?? 1;
    }

    /**
     * Mapear building_type_id a tipo de Idealista
     */
    private function mapBuildingTypeToIdealista(?int $typeId): string
    {
        $mapping = [
            1 => 'flat',
            2 => 'house',
        ];
        return $mapping[$typeId] ?? 'flat';
    }

    /**
     * Extraer nombre de calle de ubicación
     */
    private function extractStreetName(?string $ubicacion): string
    {
        if (!$ubicacion) return '';
        $parts = explode(',', $ubicacion);
        $streetPart = trim($parts[0] ?? '');
        // Remover número si está al final
        $streetPart = preg_replace('/\s+\d+$/', '', $streetPart);
        return $streetPart;
    }

    /**
     * Extraer número de calle de ubicación
     */
    private function extractStreetNumber(?string $ubicacion): string
    {
        if (!$ubicacion) return '';
        preg_match('/\b(\d+)\b/', $ubicacion, $matches);
        return $matches[1] ?? '';
    }

    /**
     * Extraer ciudad de ubicación
     */
    private function extractTown(?string $ubicacion): string
    {
        if (!$ubicacion) return '';
        $parts = explode(',', $ubicacion);
        return trim($parts[count($parts) - 1] ?? '');
    }
}
