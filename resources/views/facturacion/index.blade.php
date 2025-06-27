@extends('layouts.app')

@section('encabezado', 'Facturas')
@section('subtitulo', 'Listado y búsqueda')

@section('content')
<div class="container mt-4">
    <form method="GET" action="{{ route('facturacion.index') }}" class="row g-2 mb-4 align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Buscar</label>
            <input type="text" name="search" id="search" class="form-control"
                placeholder="Cliente o Nº de factura" value="{{ request('search') }}">
        </div>

        <div class="col-md-4">
            <label for="inmobiliaria" class="form-label">Inmobiliaria</label>
            <select name="inmobiliaria" id="inmobiliaria" class="form-select">
                <option value="">-- Todas --</option>
                <option value="sayco" {{ request('inmobiliaria') === 'sayco' ? 'selected' : '' }}>Sayco</option>
                <option value="sancer" {{ request('inmobiliaria') === 'sancer' ? 'selected' : '' }}>Sancer</option>
            </select>
        </div>

        <div class="col-md-4 d-flex gap-2">
            <button class="btn btn-primary w-100" type="submit">Filtrar</button>
            @if(request()->has('search') || request()->has('inmobiliaria'))
                <a href="{{ route('facturacion.index') }}" class="btn btn-outline-secondary w-100">Quitar filtros</a>
            @endif
        </div>
    </form>

    <div class="mb-3">
        <a href="{{ route('facturacion.create') }}" class="btn btn-success">
            <i class="fa fa-plus"></i> Nueva factura
        </a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Cliente</th>
                    <th>Nº Factura</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Inmobiliaria</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($facturas as $factura)
                    <tr>
                        <td>{{ $factura->cliente->nombre_completo }}</td>
                        <td>{{ $factura->numero_factura }}</td>
                        <td>{{ \Carbon\Carbon::parse($factura->fecha)->format('d/m/Y') }}</td>
                        <td>{{ number_format($factura->total, 2) }} €</td>
                        <td>
                            @if ($factura->inmobiliaria === 1)
                                Sayco
                            @elseif ($factura->inmobiliaria === 0)
                                Sancer
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('facturacion.pdf', $factura->id) }}" class="btn btn-sm btn-outline-dark" target="_blank">
                                <i class="fa fa-file-pdf"></i> PDF
                            </a>
                            <a href="{{ route('facturacion.show', $factura) }}" class="btn btn-sm btn-info me-1" title="Ver">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('facturacion.edit', $factura) }}" class="btn btn-sm btn-warning me-1" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('facturacion.destroy', $factura) }}" class="d-inline-block" onsubmit="return confirm('¿Eliminar esta factura?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" title="Eliminar">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron facturas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $facturas->withQueryString()->links() }}
    </div>
</div>
@endsection
