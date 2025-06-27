@extends('layouts.app')

@section('encabezado', 'Clientes')
@section('subtitulo', 'Listado y búsqueda')

@section('content')
<div class="container mt-4">
    <div class="d-flex justify-content-end mb-3">
    <a href="{{ route('clientes.create') }}" class="btn btn-success">
        <i class="fa fa-plus"></i> Nuevo Cliente
    </a>
</div>

    <form method="GET" action="{{ route('clientes.index') }}" class="row g-2 mb-4 align-items-end">
        <div class="col-md-4">
            <label for="search" class="form-label">Buscar</label>
            <input type="text" name="search" id="search" class="form-control"
                placeholder="Nombre, DNI, teléfono o correo"
                value="{{ request('search') }}">
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
                <a href="{{ route('clientes.index') }}" class="btn btn-outline-secondary w-100">Quitar filtros</a>
            @endif
        </div>
    </form>


    @php
        $direction = request('direction') === 'asc' ? 'desc' : 'asc';
    @endphp

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light">
                <tr>
                    @foreach (['nombre_completo' => 'Nombre', 'dni' => 'DNI', 'telefono' => 'Teléfono', 'email' => 'Email'] as $field => $label)
                        <th>
                            <a href="{{ route('clientes.index', array_merge(request()->all(), ['sort' => $field, 'direction' => request('sort') === $field ? $direction : 'asc'])) }}">
                                {{ $label }}
                                @if (request('sort') === $field)
                                    <i class="fa fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }}"></i>
                                @endif
                            </a>
                        </th>
                    @endforeach
                    <th>Inmobiliaria</th>
                    <th class="text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($clientes as $cliente)
                    <tr>
                        <td>{{ $cliente->nombre_completo }}</td>
                        <td>{{ $cliente->dni }}</td>
                        <td>{{ $cliente->telefono }}</td>
                        <td>{{ $cliente->email }}</td>
                        <td>
                            @if ($cliente->inmobiliaria === 1)
                                Sayco
                            @elseif ($cliente->inmobiliaria === 0)
                                Sancer
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-center">
                            <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info me-1" title="Ver">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning me-1" title="Editar">
                                <i class="fa fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('clientes.destroy', $cliente) }}" class="d-inline-block" onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?')">
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
                        <td colspan="6" class="text-center">No se encontraron clientes.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $clientes->withQueryString()->links() }}
    </div>

</div>
@endsection
