@extends('layouts.app')

@section('encabezado', 'Editar Cliente')
@section('subtitulo', 'Modifica los datos del cliente')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left me-1"></i> Volver al listado
        </a>

        <form method="POST" action="{{ route('clientes.update', $cliente) }}">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-header">Datos del cliente</div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="nombre_completo" class="form-label">Nombre completo</label>
                        <input type="text" name="nombre_completo" id="nombre_completo" class="form-control"
                            value="{{ old('nombre_completo', $cliente->nombre_completo) }}">
                        @error('nombre_completo')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="dni" class="form-label">DNI</label>
                        <input type="text" name="dni" id="dni" class="form-control"
                            value="{{ old('dni', $cliente->dni) }}">
                        @error('dni')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control"
                            value="{{ old('telefono', $cliente->telefono) }}">
                        @error('telefono')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Correo electrónico</label>
                        <input type="email" name="email" id="email" class="form-control"
                            value="{{ old('email', $cliente->email) }}">
                        @error('email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" name="direccion" id="direccion" class="form-control"
                            value="{{ old('direccion', $cliente->direccion) }}" placeholder="Calle Mayor, 123, Madrid">
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="inmobiliaria" id="inmobiliaria"
                            {{ is_null($cliente->inmobiliaria) ? 'checked' : '' }}>
                        <label class="form-check-label" for="inmobiliaria">
                            Pertenece a ambas inmobiliarias
                        </label>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">Intereses del cliente</div>
                <div class="card-body">
                    @php $intereses = json_decode($cliente->intereses, true); @endphp
                    <div class="mb-3">
                        <label for="ubicacion" class="form-label">Ubicación</label>
                        <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                            value="{{ old('ubicacion', $intereses['ubicacion'] ?? '') }}">
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="habitaciones_min" class="form-label">Habitaciones mín.</label>
                            <input type="number" name="habitaciones_min" class="form-control"
                                value="{{ old('habitaciones_min', $intereses['habitaciones_min'] ?? '') }}">
                        </div>
                        <div class="col">
                            <label for="habitaciones_max" class="form-label">Habitaciones máx.</label>
                            <input type="number" name="habitaciones_max" class="form-control"
                                value="{{ old('habitaciones_max', $intereses['habitaciones_max'] ?? '') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="banos_min" class="form-label">Baños mín.</label>
                            <input type="number" name="banos_min" class="form-control"
                                value="{{ old('banos_min', $intereses['banos_min'] ?? '') }}">
                        </div>
                        <div class="col">
                            <label for="banos_max" class="form-label">Baños máx.</label>
                            <input type="number" name="banos_max" class="form-control"
                                value="{{ old('banos_max', $intereses['banos_max'] ?? '') }}">
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col">
                            <label for="m2_min" class="form-label">M2 mín.</label>
                            <input type="number" name="m2_min" class="form-control"
                                value="{{ old('m2_min', $intereses['m2_min'] ?? '') }}">
                        </div>
                        <div class="col">
                            <label for="m2_max" class="form-label">M2 máx.</label>
                            <input type="number" name="m2_max" class="form-control"
                                value="{{ old('m2_max', $intereses['m2_max'] ?? '') }}">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select name="estado" class="form-select">
                            <option value="">-- Selecciona --</option>
                            <option value="Obra nueva"
                                {{ old('estado', $intereses['estado'] ?? '') === 'Obra nueva' ? 'selected' : '' }}>Obra
                                nueva</option>
                            <option value="Buen estado"
                                {{ old('estado', $intereses['estado'] ?? '') === 'Buen estado' ? 'selected' : '' }}>Buen
                                estado</option>
                            <option value="A reformar"
                                {{ old('estado', $intereses['estado'] ?? '') === 'A reformar' ? 'selected' : '' }}>A
                                reformar</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <label for="disponibilidad" class="form-label">Disponibilidad</label>
                        <select name="disponibilidad" class="form-select">
                            <option value="">-- Selecciona --</option>
                            <option value="Alquiler"
                                {{ old('disponibilidad', $intereses['disponibilidad'] ?? '') === 'Alquiler' ? 'selected' : '' }}>
                                Alquiler</option>
                            <option value="Venta"
                                {{ old('disponibilidad', $intereses['disponibilidad'] ?? '') === 'Venta' ? 'selected' : '' }}>
                                Venta</option>
                        </select>
                    </div>
                    <div class="mt-3">
                        <label class="form-label">Otras características</label>
                        <div class="border rounded p-3" style="max-height: 150px; overflow-y: auto;">
                            @php $seleccionadas = json_decode($intereses['otras_caracteristicas'] ?? '[]', true); @endphp
                            @foreach ($caracteristicas as $caracteristica)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="otras_caracteristicasArray[]"
                                        value="{{ $caracteristica->id }}" id="carac{{ $caracteristica->id }}"
                                        {{ in_array($caracteristica->id, $seleccionadas ?? []) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="carac{{ $caracteristica->id }}">
                                        {{ $caracteristica->nombre }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Actualizar Cliente</button>
        </form>
    </div>
@endsection
