@extends('layouts.app')

@section('encabezado', 'Facturas')
@section('subtitulo', 'Crear nueva factura')

@section('content')
<div class="container">
    <a href="{{ route('facturacion.index') }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left"></i> Volver
    </a>

    <form action="{{ route('facturacion.store') }}" method="POST" id="form-factura">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Se han producido los siguientes errores:</strong>
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
                <select name="cliente_id" id="cliente_id" class="form-select @error('cliente_id') is-invalid @enderror" required>
                    <option value="">-- Seleccione --</option>
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                            {{ $cliente->nombre_completo }}
                        </option>
                    @endforeach
                </select>
                @error('cliente_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="fecha" class="form-label">Fecha</label>
                <input type="date" name="fecha" id="fecha" value="{{ old('fecha') }}" class="form-control @error('fecha') is-invalid @enderror" required>
                @error('fecha')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label for="fecha_vencimiento" class="form-label">Vencimiento</label>
                <input type="date" name="fecha_vencimiento" id="fecha_vencimiento" value="{{ old('fecha_vencimiento') }}" class="form-control">
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
                @if(old('items'))
                    @foreach(old('items') as $i => $item)
                        <tr>
                            <td><input type="text" name="items[{{ $i }}][descripcion]" value="{{ $item['descripcion'] }}" class="form-control" required></td>
                            <td><input type="number" step="0.01" name="items[{{ $i }}][importe]" value="{{ $item['importe'] }}" class="form-control importe" required></td>
                            <td><input type="number" name="items[{{ $i }}][iva]" value="{{ $item['iva'] }}" class="form-control iva" required></td>
                            <td><input type="text" class="form-control iva_eur" readonly></td>
                            <td><input type="text" class="form-control total_linea" readonly></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td><input type="text" name="items[0][descripcion]" class="form-control" required></td>
                        <td><input type="number" step="0.01" name="items[0][importe]" class="form-control importe" required></td>
                        <td><input type="number" name="items[0][iva]" class="form-control iva" required></td>
                        <td><input type="text" class="form-control iva_eur" readonly></td>
                        <td><input type="text" class="form-control total_linea" readonly></td>
                        <td class="text-center">
                            <button type="button" class="btn btn-danger btn-sm" onclick="eliminarFila(this)">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <button type="button" class="btn btn-outline-primary mb-3" onclick="agregarFila()">
            <i class="fa fa-plus"></i> Añadir línea
        </button>

        <div class="row">
            <div class="col-md-4 offset-md-8">
                <table class="table">
                    <tr>
                        <th>Subtotal:</th>
                        <td><span id="subtotal">0.00</span> €</td>
                    </tr>
                    <tr>
                        <th>Total IVA:</th>
                        <td><span id="iva_total">0.00</span> €</td>
                    </tr>
                    <tr>
                        <th><strong>Total:</strong></th>
                        <td><strong><span id="total_factura">0.00</span> €</strong></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="mb-3">
            <label for="condiciones" class="form-label">Condiciones</label>
            <textarea name="condiciones" id="condiciones" class="form-control" rows="3">{{ old('condiciones') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="inmobiliaria" class="form-label">Inmobiliaria</label>
            <select name="inmobiliaria" id="inmobiliaria" class="form-select">
                <option value="">-- Seleccione --</option>
                <option value="1" {{ old('inmobiliaria') === '1' ? 'selected' : '' }}>Sayco</option>
                <option value="0" {{ old('inmobiliaria') === '0' ? 'selected' : '' }}>Sancer</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i> Guardar Factura
        </button>
    </form>
</div>
@endsection

@section('scripts')
<script>
    let index = {{ old('items') ? count(old('items')) : 1 }};

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
            </td>
        `;
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

    document.addEventListener('DOMContentLoaded', calcularTotales);

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
</script>
@endsection
