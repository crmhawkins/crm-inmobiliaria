@extends('layouts.app')

@section('encabezado', 'Facturas')
@section('subtitulo', 'Detalle de factura')

@section('content')
<div class="container">

    <a href="{{ route('facturacion.index') }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left"></i> Volver
    </a>

    <div class="card">
        <div class="card-body">

            <h5>Cliente</h5>
            <p><strong>{{ $factura->cliente->nombre_completo }}</strong></p>

            <div class="row">
                <div class="col-md-4"><strong>Fecha:</strong> {{ $factura->fecha }}</div>
                <div class="col-md-4"><strong>Vencimiento:</strong> {{ $factura->fecha_vencimiento ?? '-' }}</div>
                <div class="col-md-4"><strong>Nº Factura:</strong> {{ $factura->numero_factura }}</div>
            </div>

            <hr>

            <h5>Líneas de factura</h5>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Base (€)</th>
                        <th>IVA (%)</th>
                        <th>IVA (€)</th>
                        <th>Total línea (€)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factura->items as $item)
                        <tr>
                            <td>{{ $item->descripcion }}</td>
                            <td>{{ number_format($item->importe, 2) }}</td>
                            <td>{{ $item->iva_tipo }}%</td>
                            <td>{{ number_format($item->iva_cantidad, 2) }}</td>
                            <td>{{ number_format($item->total_con_iva, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <table class="table">
                        <tr><th>Subtotal:</th><td>{{ number_format($factura->subtotal, 2) }} €</td></tr>
                        <tr><th>Total IVA:</th><td>{{ number_format($factura->iva_total, 2) }} €</td></tr>
                        <tr><th><strong>Total:</strong></th><td><strong>{{ number_format($factura->total, 2) }} €</strong></td></tr>
                    </table>
                </div>
            </div>

            @if($factura->condiciones)
                <div class="mt-4">
                    <h6>Condiciones</h6>
                    <p>{{ $factura->condiciones }}</p>
                </div>
            @endif

            <div class="mt-4">
                <strong>Inmobiliaria:</strong>
                {{ $factura->inmobiliaria ? 'Sayco' : 'Sancer' }}
            </div>

        </div>
    </div>

</div>
@endsection
