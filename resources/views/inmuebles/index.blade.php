{{-- @extends('layouts.app')


@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.css">


    @vite(['resources/sass/app.scss'])
    <style>
        div.dataTables_wrapper div.dataTables_filter label {
            display: flex;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            width: 70%;
            margin-left: 10px;
            margin-top: -5px;
        }
    </style>
@endsection
@section('content')
@section('encabezado', 'Inmuebles')
@section('subtitulo', 'Consulta y creación de inmuebles')

@livewire('inmuebles.tabs-component')
@endsection --}}
@extends('layouts.app')

@section('encabezado', 'Inmuebles')
@section('subtitulo', 'Consulta de inmuebles')

@section('content')
<div class="container-fluid">
    <form method="GET" action="{{ route('inmuebles.index') }}">
        <div class="row">
            <!-- FILTROS -->
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5>Filtros de búsqueda</h5>

                        <div class="mb-3">
                            <label>Ubicación</label>
                            <input type="text" class="form-control" name="ubicacion" value="{{ request('ubicacion') }}">
                        </div>

                        <div class="mb-3">
                            <label>Valor de referencia (€)</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control" name="valor_min" placeholder="Mín" value="{{ request('valor_min') }}">
                                <input type="number" class="form-control" name="valor_max" placeholder="Máx" value="{{ request('valor_max') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Superficie (m²)</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control" name="m2_min" placeholder="Mín" value="{{ request('m2_min') }}">
                                <input type="number" class="form-control" name="m2_max" placeholder="Máx" value="{{ request('m2_max') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Habitaciones</label><br>
                            @for($i = 0; $i <= 4; $i++)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="habitaciones[]" value="{{ $i }}" {{ in_array($i, request('habitaciones', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $i == 4 ? '4 o más' : $i }}</label>
                                </div>
                            @endfor
                        </div>

                        <div class="mb-3">
                            <label>Baños</label><br>
                            @for($i = 1; $i <= 3; $i++)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="banos[]" value="{{ $i }}" {{ in_array($i, request('banos', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $i == 3 ? '3 o más' : $i }}</label>
                                </div>
                            @endfor
                        </div>

                        <div class="mb-3">
                            <label>Estado</label><br>
                            @foreach (['Obra nueva', 'Buen estado', 'A reformar'] as $estado)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="estado[]" value="{{ $estado }}" {{ in_array($estado, request('estado', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $estado }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label>Disponibilidad</label><br>
                            @foreach (['Alquiler', 'Venta'] as $disponibilidad)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="disponibilidad[]" value="{{ $disponibilidad }}" {{ in_array($disponibilidad, request('disponibilidad', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $disponibilidad }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label>Tipo de vivienda</label><br>
                            @foreach($tiposVivienda as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="tipo_vivienda[]" value="{{ $tipo->id }}" {{ in_array($tipo->id, request('tipo_vivienda', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $tipo->nombre }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label>Características</label><br>
                            @foreach($caracteristicas as $car)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="caracteristicas[]" value="{{ $car->id }}" {{ in_array($car->id, request('caracteristicas', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label">{{ $car->nombre }}</label>
                                </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Aplicar filtros</button>
                    </div>
                </div>
            </div>

            <!-- RESULTADOS -->
            <div class="col-md-9">
                <div class="row">
                    @forelse($inmuebles as $inmueble)
    @php $galeria = json_decode($inmueble->galeria, true); @endphp
    <div class="card mb-3 shadow-sm">
        <div class="row g-0 align-items-center">
            <!-- Galería de imágenes -->
            <div class="col-md-4">
                <img src="{{ $galeria[1] ?? asset('images/default.jpg') }}" class="img w-100 rounded-start" alt="Foto inmueble">
            </div>

            <!-- Info -->
            <div class="col-md-8">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <h4 class="card-title mb-1">{{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</h4>
                        <span class="badge bg-light text-muted">Novedad</span>
                    </div>
                    <h6 class="text-muted mb-3">
                        {{ $inmueble->tipoVivienda->nombre ?? 'Vivienda' }} en {{ $inmueble->ubicacion }}
                    </h6>

                    <div class="mb-2 d-flex flex-wrap gap-4">
                        <span><i class="bi bi-door-open"></i> {{ $inmueble->habitaciones }} habs.</span>
                        <span><i class="bi bi-badge-wc"></i> {{ $inmueble->banos }} baños</span>
                        <span><i class="bi bi-aspect-ratio"></i> {{ $inmueble->m2 }} m²</span>
                        @if($inmueble->has_terrace)<span><i class="bi bi-house-door"></i> Terraza</span>@endif
                        @if($inmueble->has_balcony)<span><i class="bi bi-columns-gap"></i> Balcón</span>@endif
                        @if($inmueble->otras_caracteristicas)
                            <span class="badge bg-light text-dark">
                                +{{ count(json_decode($inmueble->otras_caracteristicas, true) ?? []) }} extras
                            </span>
                        @endif
                    </div>

                    <p class="card-text text-muted small mb-3">{{ Str::limit($inmueble->descripcion, 180) }}</p>

                    <div class="d-flex gap-2">
                        <a href="#" class="btn btn-outline-primary btn-sm"><i class="bi bi-envelope"></i> Contactar</a>
                        <a href="#" class="btn btn-outline-secondary btn-sm"><i class="bi bi-telephone"></i> Llamar</a>
                        <a href="{{ route('inmuebles.show', $inmueble->id) }}" class="btn btn-primary btn-sm ms-auto">Ver más</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    <p>No se encontraron inmuebles.</p>
@endforelse

                </div>

                <div class="d-flex justify-content-center">
                    {{ $inmuebles->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
