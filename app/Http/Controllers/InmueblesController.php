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

    return view('inmuebles.index', compact(
        'inmuebles',
        'tiposVivienda',
        'caracteristicas'
    ));
}
public function show($id)
{
    $inmueble = Inmuebles::with('tipoVivienda')->findOrFail($id);

    // Decodificar otras_caracteristicas de forma segura
    $caracteristicas_ids = json_decode($inmueble->otras_caracteristicas ?? '[]', true) ?? [];

    $caracteristicas = Caracteristicas::whereIn('id', $caracteristicas_ids)->get();

    return view('inmuebles.show', compact('inmueble', 'caracteristicas'));
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
        'ubicacion' => 'nullable|string',
        'cod_postal' => 'nullable|string',
        'referencia_catastral' => 'nullable|string',
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
        'tipo_vivienda_id' => $request->tipo_vivienda_id,
        'ubicacion' => $request->ubicacion,
        'cod_postal' => $request->cod_postal,
        'referencia_catastral' => $request->referencia_catastral,
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
    ]);

    return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
}
}
