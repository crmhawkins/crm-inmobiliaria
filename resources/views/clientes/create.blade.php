@extends('layouts.app')

@section('encabezado', 'Clientes')
@section('subtitulo', 'Nuevo cliente')

@section('content')
<div class="container mt-4">
    <a href="{{ route('clientes.index') }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left me-1"></i> Volver al listado
    </a>

    <form method="POST" action="{{ route('clientes.store') }}" id="cliente-form">
        @csrf

        <div class="card mb-4">
            <div class="card-header">Datos del cliente</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label for="nombre_completo" class="form-label">Nombre completo</label>
                    <input type="text" name="nombre_completo" id="nombre_completo" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="dni" class="form-label">DNI</label>
                    <input type="text" name="dni" id="dni" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="telefono" class="form-label">Teléfono</label>
                    <input type="text" name="telefono" id="telefono" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" name="inmobiliaria" id="inmobiliaria">
                        <label class="form-check-label" for="inmobiliaria">
                            Cliente pertenece a ambas inmobiliarias
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Intereses del cliente</div>
            <div class="card-body row g-3">
                <div class="col-md-6">
                    <label for="ubicacion" class="form-label">Ubicación</label>
                    <input type="text" class="form-control" name="ubicacion" id="ubicacion">
                </div>
                <div class="col-md-3">
                    <label for="habitaciones_min" class="form-label">Habitaciones (min)</label>
                    <input type="number" class="form-control" name="habitaciones_min" id="habitaciones_min">
                </div>
                <div class="col-md-3">
                    <label for="habitaciones_max" class="form-label">Habitaciones (max)</label>
                    <input type="number" class="form-control" name="habitaciones_max" id="habitaciones_max">
                </div>
                <div class="col-md-3">
                    <label for="banos_min" class="form-label">Baños (min)</label>
                    <input type="number" class="form-control" name="banos_min" id="banos_min">
                </div>
                <div class="col-md-3">
                    <label for="banos_max" class="form-label">Baños (max)</label>
                    <input type="number" class="form-control" name="banos_max" id="banos_max">
                </div>
                <div class="col-md-3">
                    <label for="m2_min" class="form-label">M2 (min)</label>
                    <input type="number" class="form-control" name="m2_min" id="m2_min">
                </div>
                <div class="col-md-3">
                    <label for="m2_max" class="form-label">M2 (max)</label>
                    <input type="number" class="form-control" name="m2_max" id="m2_max">
                </div>
                <div class="col-md-6">
                    <label for="estado" class="form-label">Estado</label>
                    <select name="estado" id="estado" class="form-select">
                        <option value="">-- Estado del inmueble --</option>
                        <option value="Obra nueva">Obra nueva</option>
                        <option value="Buen estado">Buen estado</option>
                        <option value="A reformar">A reformar</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="disponibilidad" class="form-label">Disponibilidad</label>
                    <select name="disponibilidad" id="disponibilidad" class="form-select">
                        <option value="">-- Disponibilidad --</option>
                        <option value="Alquiler">Alquiler</option>
                        <option value="Venta">Venta</option>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Otras características</label>
                    <div class="form-check form-check-inline">
                        @foreach($caracteristicas as $caracteristica)
                            <input class="form-check-input" type="checkbox" name="otras_caracteristicas[]" value="{{ $caracteristica->id }}" id="carac_{{ $caracteristica->id }}">
                            <label class="form-check-label me-3" for="carac_{{ $caracteristica->id }}">{{ $caracteristica->nombre }}</label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-4">
            <div class="card-header">Inmuebles relacionados con los intereses</div>
            <div class="card-body">
                <div id="inmuebles-container" class="row g-3"></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Guardar cliente</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function filtrarInmuebles() {
        const data = $('#cliente-form').serialize();
        $.ajax({
            url: '{{ route('clientes.filtrarInmuebles') }}',
            method: 'POST',
            data: data,
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            success: function(response) {
                $('#inmuebles-container').html(response);
            }
        });
    }

    $('#cliente-form input, #cliente-form select').on('change', function() {
        filtrarInmuebles();
    });

    $(document).ready(() => filtrarInmuebles());
</script>
@endpush
