<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use Illuminate\Http\Request;
use App\Models\Clientes;
use App\Models\FacturaItem;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class FacturaController extends Controller
{
    public function index(Request $request)
    {
        $query = Factura::with('cliente');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('cliente', function ($q) use ($search) {
                $q->where('nombre_completo', 'like', "%$search%");
            })->orWhere('numero_factura', 'like', "%$search%");
        }

        if ($request->filled('inmobiliaria')) {
            if ($request->inmobiliaria === 'sayco') {
                $query->where('inmobiliaria', true);
            } elseif ($request->inmobiliaria === 'sancer') {
                $query->where('inmobiliaria', false);
            }
        }

        $facturas = $query->orderBy('fecha', 'desc')->paginate(15);

        return view('facturacion.index', compact('facturas'));
    }

public function create()
    {
        $clientes = Clientes::all();
        return view('facturacion.create', compact('clientes'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'condiciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            // Generar número de factura
            $count = Factura::whereYear('fecha', now()->year)
                ->whereMonth('fecha', now()->month)
                ->count() + 1;
            $numero_factura = now()->format('Y/m') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);

            $subtotal = 0;
            $iva_total = 0;
            $iva_por_seccion = [];

            $factura = Factura::create([
                'cliente_id' => $request->cliente_id,
                'numero_factura' => $numero_factura,
                'fecha' => $request->fecha,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'subtotal' => 0, // provisional
                'iva_total' => 0,
                'iva_por_seccion' => null,
                'total' => 0,
                'condiciones' => $request->condiciones,
                'inmobiliaria' => session('inmobiliaria') === 'sayco',
            ]);

            foreach ($request->items as $item) {
                $importe = floatval($item['importe']);
                $impuesto = floatval($item['iva']);
                $iva = round(($importe * $impuesto) / 100, 2);
                $total_linea = $importe + $iva;

                FacturaItem::create([
                    'factura_id'      => $factura->id,
                    'descripcion'     => $item['descripcion'],
                    'importe'         => $importe,
                    'iva_tipo'        => $impuesto,
                    'iva_cantidad'    => $iva,
                    'total_linea'     => $total_linea,
                    'total_con_iva'   => $total_linea, // mismo que total_linea si no hay descuentos
                ]);

                $subtotal += $importe;
                $iva_total += $iva;
                $iva_por_seccion[$impuesto] = ($iva_por_seccion[$impuesto] ?? 0) + $iva;
            }

            $factura->update([
                'subtotal' => $subtotal,
                'iva_total' => $iva_total,
                'iva_por_seccion' => json_encode($iva_por_seccion),
                'total' => $subtotal + $iva_total,
            ]);

            DB::commit();

            return redirect()->route('facturacion.index')->with('success', 'Factura creada correctamente.');
        } catch (\Exception $e) {

            DB::rollBack();
            dd($e->getMessage());
            return redirect()->back()->with('error', 'Error al crear la factura: ' . $e->getMessage());
        }
    }

    public function show(Factura $factura)
    {
        $factura->load('cliente', 'items');

        return view('facturacion.show', compact('factura'));
    }


    public function edit(Factura $factura)
    {
        $clientes = Clientes::all();
        //$factura = $facturacion;
        $items = $factura->items;
        // dd($factura->id);
        return view('facturacion.edit', compact('factura', 'clientes', 'items'));
    }


    public function update(Request $request, Factura $factura)
    {
        $factura = $facturacion;

        $validator = Validator::make($request->all(), [
            'cliente_id' => 'required|exists:clientes,id',
            'fecha' => 'required|date',
            'fecha_vencimiento' => 'nullable|date|after_or_equal:fecha',
            'condiciones' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();

        try {
            $factura = $facturacion;
            $subtotal = 0;
            $iva_total = 0;
            $iva_por_seccion = [];

            // Eliminar ítems anteriores
            $factura->items()->delete();

            foreach ($request->items as $item) {
                $importe = floatval($item['importe']);
                $impuesto = floatval($item['iva']);
                $iva = round(($importe * $impuesto) / 100, 2);
                $total_linea = $importe + $iva;

                FacturaItem::create([
                    'factura_id' => $factura->id,
                    'descripcion' => $item['descripcion'],
                    'importe' => $importe,
                    'iva_tipo' => $impuesto,
                    'iva_cantidad' => $iva,
                    'total_linea' => $total_linea,
                    'total_con_iva' => $total_linea,
                ]);

                $subtotal += $importe;
                $iva_total += $iva;
                $iva_por_seccion[$impuesto] = ($iva_por_seccion[$impuesto] ?? 0) + $iva;
            }

            $factura->update([
                'cliente_id' => $request->cliente_id,
                'fecha' => $request->fecha,
                'fecha_vencimiento' => $request->fecha_vencimiento,
                'condiciones' => $request->condiciones,
                'inmobiliaria' => session('inmobiliaria') === 'sayco',
                'subtotal' => $subtotal,
                'iva_total' => $iva_total,
                'iva_por_seccion' => json_encode($iva_por_seccion),
                'total' => $subtotal + $iva_total,
            ]);

            DB::commit();

            return redirect()->route('facturacion.index')->with('success', 'Factura actualizada correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error al actualizar la factura: ' . $e->getMessage());
        }
    }

    public function destroy(Factura $factura)
    {
        $factura->delete();

        return redirect()->route('facturacion.index')->with('success', 'Factura eliminada correctamente.');
    }

    public function descargarPDF(Factura $factura)
{
    $factura->load('cliente', 'items');

    $datos = [
        'numero_factura' => $factura->numero_factura,
        'fecha' => $factura->fecha,
        'fecha_vencimiento' => $factura->fecha_vencimiento,
        'condiciones' => $factura->condiciones,
        'cliente_nombre' => $factura->cliente->nombre_completo,
        'cliente_dni' => $factura->cliente->dni,
        'cliente_direccion' => $factura->cliente->direccion ?? '',
        'cliente_telefono' => $factura->cliente->telefono ?? '',
        'subtotal' => number_format($factura->subtotal, 2),
        'total' => number_format($factura->total, 2),
        'articulos' => [],
    ];

    foreach ($factura->items as $item) {
        $datos['articulos'][] = [
            'descripcion' => $item->descripcion,
            'importe' => number_format($item->importe, 2),
            'impuesto' => $item->iva_tipo,
        ];
    }

    $pdf = Pdf::loadView('facturacion.generar', ['factura' => $datos]);

    return $pdf->download("Factura_{$factura->numero_factura}.pdf");
}
}
