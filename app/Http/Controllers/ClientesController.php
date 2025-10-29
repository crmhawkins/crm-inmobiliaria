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
            'nombre_completo' => 'required|string|max:255',
            'dni' => 'required|string|max:20',
            'telefono' => 'required|string|max:20',
            'email' => 'required|email|max:255',
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
            'caracteristicas' => $request->input('caracteristicas', []),
        ];

        $inmobiliaria = $request->has('inmobiliaria') ? 1 : 0;

        Clientes::create([
            'nombre_completo' => $request->nombre_completo,
            'dni' => $request->dni,
            'telefono' => $request->telefono,
            'email' => $request->email,
            'direccion' => $request->direccion,
            'intereses' => json_encode($intereses),
            'inmobiliaria' => 1,
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
            'direccion' => $request->direccion,
            'intereses' => json_encode($intereses),
            'inmobiliaria' => $inmobiliaria,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado correctamente.');
    }

   public function show(Clientes $cliente)
    {
        // Si ya es array (con el cast del modelo), usarlo directamente
        // Si es string JSON, decodificarlo
        $intereses = is_array($cliente->intereses) ? $cliente->intereses : (json_decode($cliente->intereses ?? '{}', true) ?? []);

        $otras_caracteristicas_nombres = [];
        if (!empty($intereses['otras_caracteristicas'])) {
            $caracteristicasRaw = $intereses['otras_caracteristicas'];
            $ids = is_array($caracteristicasRaw) ? $caracteristicasRaw : (json_decode($caracteristicasRaw, true) ?? []);
            if (!empty($ids)) {
                $otras_caracteristicas_nombres = Caracteristicas::whereIn('id', $ids)->pluck('nombre')->toArray();
            }
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
        \Log::info('Método filtrarInmuebles llamado');
        \Log::info('Datos recibidos:', $request->all());

        $query = Inmuebles::query();

        // Filtro básico por ubicación
        if ($request->filled('ubicacion')) {
            $query->where('ubicacion', 'LIKE', "%{$request->ubicacion}%");
        }

        // Filtro por disponibilidad
        if ($request->filled('disponibilidad')) {
            $query->where('disponibilidad', $request->disponibilidad);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        // Filtros de habitaciones
        if ($request->filled('habitaciones_min')) {
            $query->where('habitaciones', '>=', $request->habitaciones_min);
        }
        if ($request->filled('habitaciones_max')) {
            $query->where('habitaciones', '<=', $request->habitaciones_max);
        }

        // Filtros de baños
        if ($request->filled('banos_min')) {
            $query->where('banos', '>=', $request->banos_min);
        }
        if ($request->filled('banos_max')) {
            $query->where('banos', '<=', $request->banos_max);
        }

        // Filtros de metros cuadrados
        if ($request->filled('m2_min')) {
            $query->where('m2', '>=', $request->m2_min);
        }
        if ($request->filled('m2_max')) {
            $query->where('m2', '<=', $request->m2_max);
        }

        // Filtrar por inmobiliaria según la sesión
        if (session('inmobiliaria') === 'sayco') {
            $query->where(function ($q) {
                $q->where('inmobiliaria', true)->orWhereNull('inmobiliaria');
            });
        } else {
            $query->where(function ($q) {
                $q->where('inmobiliaria', false)->orWhereNull('inmobiliaria');
            });
        }

        $inmuebles = $query->limit(6)->get();

        \Log::info('Query SQL:', $query->toSql());
        \Log::info('Bindings:', $query->getBindings());
        \Log::info('Inmuebles encontrados:', $inmuebles->toArray());

        return response()->json($inmuebles);
    }
}

