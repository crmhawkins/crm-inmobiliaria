<?php

namespace App\Http\Controllers;

use App\Models\HojaVisita;
use App\Models\Inmuebles;
use Illuminate\Http\Request;

class VisitasController extends Controller
{
    public function create(Request $request)
    {
        $inmueble = Inmuebles::findOrFail($request->inmueble);
        return view('inmuebles.visitas.create', compact('inmueble'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id',
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'fecha_visita' => 'required|date',
            'hora_visita' => 'required',
            'estado' => 'required|in:pendiente,realizada,cancelada',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $visita = HojaVisita::create($request->all());

        return redirect()->route('visitas.show', $visita)
            ->with('success', 'Visita creada correctamente.');
    }

    public function show(HojaVisita $visita)
    {
        return view('inmuebles.visitas.show', compact('visita'));
    }

    public function edit(HojaVisita $visita)
    {
        return view('inmuebles.visitas.edit', compact('visita'));
    }

    public function update(Request $request, HojaVisita $visita)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'vendedor_id' => 'required|exists:users,id',
            'fecha_visita' => 'required|date',
            'hora_visita' => 'required',
            'estado' => 'required|in:pendiente,realizada,cancelada',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $visita->update($request->all());

        return redirect()->route('visitas.show', $visita)
            ->with('success', 'Visita actualizada correctamente.');
    }

    public function destroy(HojaVisita $visita)
    {
        $visita->delete();

        return back()->with('success', 'Visita eliminada correctamente.');
    }

    public function download(HojaVisita $visita)
    {
        $rutaArchivo = public_path($visita->ruta);
        
        if (!file_exists($rutaArchivo)) {
            \Log::error("PDF no encontrado para hoja de visita {$visita->id}: {$rutaArchivo}");
            return back()->with('error', 'El archivo PDF no existe en: ' . $visita->ruta);
        }

        return response()->download($rutaArchivo);
    }

    public function index()
    {
        $hojasVisita = HojaVisita::with(['cliente', 'inmueble', 'evento'])
            ->orderBy('fecha', 'desc')
            ->get();

        return view('hojas-visita.index', compact('hojasVisita'));
    }
}
