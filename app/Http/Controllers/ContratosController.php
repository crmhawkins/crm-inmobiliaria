<?php

namespace App\Http\Controllers;

use App\Models\ContratoArras;
use App\Models\Inmuebles;
use Illuminate\Http\Request;

class ContratosController extends Controller
{
    public function create(Request $request)
    {
        $inmueble = Inmuebles::findOrFail($request->inmueble);
        return view('inmuebles.contratos.create', compact('inmueble'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'inmueble_id' => 'required|exists:inmuebles,id',
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:arras,alquiler,compraventa',
            'fecha_firma' => 'nullable|date',
            'precio' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $contrato = ContratoArras::create($request->all());

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato creado correctamente.');
    }

    public function show(ContratoArras $contrato)
    {
        return view('inmuebles.contratos.show', compact('contrato'));
    }

    public function edit(ContratoArras $contrato)
    {
        return view('inmuebles.contratos.edit', compact('contrato'));
    }

    public function update(Request $request, ContratoArras $contrato)
    {
        $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'tipo' => 'required|in:arras,alquiler,compraventa',
            'fecha_firma' => 'nullable|date',
            'precio' => 'required|numeric|min:0',
            'observaciones' => 'nullable|string|max:1000'
        ]);

        $contrato->update($request->all());

        return redirect()->route('contratos.show', $contrato)
            ->with('success', 'Contrato actualizado correctamente.');
    }

    public function destroy(ContratoArras $contrato)
    {
        $contrato->delete();

        return back()->with('success', 'Contrato eliminado correctamente.');
    }
}
