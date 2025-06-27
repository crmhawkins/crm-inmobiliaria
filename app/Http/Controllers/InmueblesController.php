<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\Clientes;
use App\Models\Caracteristicas;
use Illuminate\Support\Str;


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
    $caracteristicas = Caracteristicas::whereIn('id', json_decode($inmueble->otras_caracteristicas ?? '[]'))->get();
    return view('inmuebles.show', compact('inmueble', 'caracteristicas'));
}
public function create()
{
    $tiposVivienda = TipoVivienda::all();
    $caracteristicas = Caracteristicas::all();

    return view('inmuebles.create', compact('tiposVivienda', 'caracteristicas'));
}
public function store(Request $request)
{
    $request->validate([
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'm2' => 'nullable|numeric',
        'valor_referencia' => 'nullable|numeric',
        'habitaciones' => 'nullable|integer',
        'banos' => 'nullable|integer',
        'tipo_vivienda_id' => 'required|exists:tipo_viviendas,id',
        'ubicacion' => 'nullable|string',
        'galeria' => 'nullable|array',
        'galeria.*' => 'nullable|url',
        'otras_caracteristicas' => 'nullable|array',
    ]);

    $inmueble = Inmuebles::create([
        'titulo' => $request->titulo,
        'descripcion' => $request->descripcion,
        'm2' => $request->m2,
        'valor_referencia' => $request->valor_referencia,
        'habitaciones' => $request->habitaciones,
        'banos' => $request->banos,
        'tipo_vivienda_id' => $request->tipo_vivienda_id,
        'ubicacion' => $request->ubicacion,
        'galeria' => json_encode($request->galeria),
        'otras_caracteristicas' => json_encode($request->otras_caracteristicas),
        'inmobiliaria' => session('inmobiliaria'),
    ]);

    return redirect()->route('inmuebles.index')->with('success', 'Inmueble creado correctamente.');
}
}
