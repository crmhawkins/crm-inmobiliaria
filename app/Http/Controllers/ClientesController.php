<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Inmuebles;
use App\Models\Clientes;
use App\Models\Caracteristicas;


class ClientesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $query = Clientes::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%$search%")
                ->orWhere('dni', 'like', "%$search%")
                ->orWhere('telefono', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($request->filled('inmobiliaria')) {
            $query->where('inmobiliaria', $request->inmobiliaria === 'sayco' ? 1 : 0);
        }

        $sortField = $request->get('sort', 'nombre_completo');
        $sortDirection = $request->get('direction', 'asc');

        $clientes = $query->orderBy($sortField, $sortDirection)->paginate(15);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        $caracteristicas = Caracteristicas::all();
        return view('clientes.create', compact('caracteristicas'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required',
            'dni' => 'required',
            'telefono' => 'required',
            'email' => 'required|email',
        ], [
            'nombre_completo.required' => 'El nombre es obligatorio.',
            'dni.required' => 'El DNI del cliente es obligatorio.',
            'telefono.required' => 'El teléfono del cliente es obligatorio.',
            'email.required' => 'El correo del cliente es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $intereses = [
            'disponibilidad' => $request->disponibilidad,
            'estado' => $request->estado,
            'habitaciones_min' => $request->habitaciones_min,
            'habitaciones_max' => $request->habitaciones_max,
            'banos_min' => $request->banos_min,
            'banos_max' => $request->banos_max,
            'm2_min' => $request->m2_min,
            'm2_max' => $request->m2_max,
            'ubicacion' => $request->ubicacion,
            'otras_caracteristicas' => json_encode($request->input('otras_caracteristicasArray', [])),
        ];

        $inmobiliaria = $request->has('inmobiliaria') ? null : (session('inmobiliaria') === 'sayco');

        Clientes::create([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'intereses' => json_encode($intereses),
            'inmobiliaria' => $inmobiliaria,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente registrado correctamente.');
    }
     public function edit(Clientes $cliente)
    {
        $caracteristicas = Caracteristicas::all();
        return view('clientes.edit', compact('cliente', 'caracteristicas'));
    }

    public function update(Request $request, Clientes $cliente)
    {
        $validator = Validator::make($request->all(), [
            'nombre_completo' => 'required',
            'dni' => 'required',
            'telefono' => 'required',
            'email' => 'required|email',
        ], [
            'nombre_completo.required' => 'El nombre es obligatorio.',
            'dni.required' => 'El DNI del cliente es obligatorio.',
            'telefono.required' => 'El teléfono del cliente es obligatorio.',
            'email.required' => 'El correo del cliente es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $intereses = [
            'disponibilidad' => $request->disponibilidad,
            'estado' => $request->estado,
            'habitaciones_min' => $request->habitaciones_min,
            'habitaciones_max' => $request->habitaciones_max,
            'banos_min' => $request->banos_min,
            'banos_max' => $request->banos_max,
            'm2_min' => $request->m2_min,
            'm2_max' => $request->m2_max,
            'ubicacion' => $request->ubicacion,
            'otras_caracteristicas' => json_encode($request->input('otras_caracteristicasArray', [])),
        ];

        $inmobiliaria = $request->has('inmobiliaria') ? null : (session('inmobiliaria') === 'sayco');

        $cliente->update([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'intereses' => json_encode($intereses),
            'inmobiliaria' => $inmobiliaria,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

   public function show(Clientes $cliente)
    {
        $intereses = json_decode($cliente->intereses, true);

        $otras_caracteristicas_nombres = [];
        if (!empty($intereses['otras_caracteristicas'])) {
            $ids = json_decode($intereses['otras_caracteristicas'], true);
            $otras_caracteristicas_nombres = Caracteristicas::whereIn('id', $ids)->pluck('nombre')->toArray();
        }

        return view('clientes.show', compact('cliente', 'intereses', 'otras_caracteristicas_nombres'));
    }

    public function destroy(Clientes $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado correctamente.');
    }

    public function filtrarInmuebles(Request $request)
    {
        $query = Inmuebles::query();

        if ($request->disponibilidad) {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        if ($request->estado) {
            $query->where('estado', $request->estado);
        }

        if ($request->habitaciones_min) {
            $query->where('habitaciones', '>=', $request->habitaciones_min);
        }

        if ($request->habitaciones_max) {
            $query->where('habitaciones', '<=', $request->habitaciones_max);
        }

        if ($request->banos_min) {
            $query->where('banos', '>=', $request->banos_min);
        }

        if ($request->banos_max) {
            $query->where('banos', '<=', $request->banos_max);
        }

        if ($request->m2_min) {
            $query->where('m2', '>=', $request->m2_min);
        }

        if ($request->m2_max) {
            $query->where('m2', '<=', $request->m2_max);
        }

        if ($request->ubicacion) {
            $query->where('ubicacion', 'LIKE', "%{$request->ubicacion}%");
        }

        if (session('inmobiliaria') === 'sayco') {
            $query->where(function ($q) {
                $q->where('inmobiliaria', true)->orWhereNull('inmobiliaria');
            });
        } else {
            $query->where(function ($q) {
                $q->where('inmobiliaria', false)->orWhereNull('inmobiliaria');
            });
        }

        if ($request->filled('otras_caracteristicasArray')) {
            foreach ($request->otras_caracteristicasArray as $caracteristica) {
                $query->whereJsonContains('otras_caracteristicas', (string) $caracteristica);
            }
        }

        $inmuebles = $query->limit(6)->get();

        return response()->json($inmuebles);
    }
}

