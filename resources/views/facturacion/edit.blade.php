@extends('layouts.app')

@section('encabezado', 'Facturas')
@section('subtitulo', 'Editar factura')

@section('content')
<div class="container">
    <a href="{{ route('facturacion.index') }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left"></i> Volver
    </a>
{{-- Debug temporal --}}
{{-- <pre>{{ json_encode($factura->id) }}</pre> --}}

<form action="{{ route('facturacion.update', $factura) }}" method="POST">
        @csrf
        @method('PUT')

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Errores:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="cliente_id" class="form-label">Cliente</label>
                <select name="cliente_id" id="cliente_id" class="form-select" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id', $factura->cliente_id) == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" class="form-control" value="{{ old('fecha', $factura->fecha) }}" required>
            </div>
            <div class="col-md-3">
                <label for="fecha_vencimiento" class="form-label">Vencimiento</label>
                <input type="date" name="fecha_vencimiento" class="form-control" value="{{ old('fecha_vencimiento', $factura->fecha_vencimiento) }}">
            </div>
        </div>

        <h5 class="mt-4">Líneas de factura</h5>
        <table class="table table-bordered" id="tabla-items">
            <thead class="table-light">
                <tr>
                    <th>Descripción</th>
                    <th>Base (€)</th>
                    <th>IVA (%)</th>
                    <th>IVA (€)</th>
                    <th>Total línea (€)</th>
                    <th class="text-center">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $i => $item)
                <tr>
                    <td><input type="text" name="items[{{ $i }}][descripcion]" class="form-control" value="{{ $item->descripcion }}" required></td>
                    <td><input type="number" step="0.01" name="items[{{ $i }}][importe]" class="form-control importe" value="{{ $item->importe }}" required></td>
                    <td><input type="number" name="items[{{ $i }}][iva]" class="form-control iva" value="{{ $item->iva_tipo }}" required></td>
                    <td><input type="text" class="form-control iva_eur" value="{{ number_format($item->iva_cantidad, 2) }}" readonly></td>
                    <td><input type="text" class="form-control total_linea" value="{{ number_format($item->total_con_iva, 2) }}" readonly></td>
                    <td class="text-center">
                        <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <button type="button" class="btn btn-outline-primary mb-3" onclick="agregarFila()">
            <i class="fa fa-plus"></i> Añadir línea
        </button>

        <div class="row">
            <div class="col-md-4 offset-md-8">
                <table class="table">
                    <tr><th>Subtotal:</th><td><span id="subtotal">0.00</span> €</td></tr>
                    <tr><th>Total IVA:</th><td><span id="iva_total">0.00</span> €</td></tr>
                    <tr><th><strong>Total:</strong></th><td><strong><span id="total_factura">0.00</span> €</strong></td></tr>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <label for="condiciones" class="form-label">Condiciones</label>
            <textarea name="condiciones" class="form-control" rows="3">{{ old('condiciones', $factura->condiciones) }}</textarea>
        </div>

        <div class="mb-3">
            <label for="inmobiliaria" class="form-label">Inmobiliaria</label>
            <select name="inmobiliaria" id="inmobiliaria" class="form-select">
                <option value="">-- Seleccione --</option>
                <option value="1" {{ $factura->inmobiliaria ? 'selected' : '' }}>Sayco</option>
                <option value="0" {{ !$factura->inmobiliaria ? 'selected' : '' }}>Sancer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Actualizar Factura
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let index = {{ count($items) }};

    function agregarFila() {
        const tbody = document.querySelector('#tabla-items tbody');
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td><input type="text" name="items[${index}][descripcion]" class="form-control" required></td>
            <td><input type="number" step="0.01" name="items[${index}][importe]" class="form-control importe" required></td>
            <td><input type="number" name="items[${index}][iva]" class="form-control iva" required></td>
            <td><input type="text" class="form-control iva_eur" readonly></td>
            <td><input type="text" class="form-control total_linea" readonly></td>
            <td class="text-center">
                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                    <i class="fa fa-trash"></i>
                </button>
            </td>`;
        tbody.appendChild(tr);
        index++;
    }

    function eliminarFila(btn) {
        btn.closest('tr').remove();
        calcularTotales();
    }

    document.addEventListener('input', function (e) {
        if (e.target.classList.contains('importe') || e.target.classList.contains('iva')) {
            calcularTotales();
        }
    });

    function calcularTotales() {
        let subtotal = 0, totalIva = 0;
        document.querySelectorAll('#tabla-items tbody tr').forEach(row => {
            const base = parseFloat(row.querySelector('.importe')?.value || 0);
            const ivaPct = parseFloat(row.querySelector('.iva')?.value || 0);
            const ivaEur = (base * ivaPct) / 100;
            const totalLinea = base + ivaEur;

            row.querySelector('.iva_eur').value = ivaEur.toFixed(2);
            row.querySelector('.total_linea').value = totalLinea.toFixed(2);

            subtotal += base;
            totalIva += ivaEur;
        });

        document.getElementById('subtotal').innerText = subtotal.toFixed(2);
        document.getElementById('iva_total').innerText = totalIva.toFixed(2);
        document.getElementById('total_factura').innerText = (subtotal + totalIva).toFixed(2);
    }

    // Inicializar totales al cargar
    window.addEventListener('load', calcularTotales);
</script>
@endsection
