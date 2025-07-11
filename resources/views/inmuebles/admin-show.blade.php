@extends('layouts.app')

@section('encabezado', 'Detalle del inmueble - Admin')
@section('subtitulo', $inmueble->titulo)

@section('content')
    @php
        $galeria = json_decode($inmueble->galeria ?? '[]');
    @endphp
    @php $imagenes = array_values((array) $galeria); @endphp

    <div class="container my-4">
        <!-- Admin Actions Bar -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Volver al listado
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Editar
                                </a>
                                <form action="{{ route('inmuebles.destroy', $inmueble) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este inmueble? Esta acción no se puede deshacer.')">
                                        <i class="fas fa-trash me-2"></i>Eliminar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Galería principal -->
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Galería de imágenes</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            @if (isset($imagenes[0]))
                                <img src="{{ $imagenes[0] }}" class="img-fluid rounded w-100"
                                    style="max-height: 450px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="height: 300px;">
                                    <span class="text-muted">Sin imagen principal</span>
                                </div>
                            @endif
                        </div>
                        @if(count($imagenes) > 1)
                            <div class="d-flex flex-wrap gap-2">
                                @foreach (array_slice($imagenes, 1) as $img)
                                    <img src="{{ $img }}" class="rounded" width="120" height="90"
                                        style="object-fit: cover;">
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Información del inmueble -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Información del inmueble</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3 class="text-primary">{{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</h3>
                                <p class="text-muted h5 mb-3">
                                    {{ $inmueble->tipoVivienda->nombre ?? 'Inmueble' }} en {{ $inmueble->ubicacion }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-wrap gap-4 fs-5">
                                    @if($inmueble->habitaciones)
                                        <div><i class="bi bi-door-open"></i> {{ $inmueble->habitaciones }} habs.</div>
                                    @endif
                                    @if($inmueble->banos)
                                        <div><i class="bi bi-badge-wc"></i> {{ $inmueble->banos }} baños</div>
                                    @endif
                                    @if($inmueble->m2)
                                        <div><i class="bi bi-aspect-ratio"></i> {{ $inmueble->m2 }} m²</div>
                                    @endif
                                    @if($inmueble->has_terrace)
                                        <div><i class="bi bi-tree"></i> Terraza</div>
                                    @endif
                                    @if($inmueble->has_balcony)
                                        <div><i class="bi bi-columns-gap"></i> Balcón</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($inmueble->descripcion)
                            <div class="mt-4">
                                <h6>Descripción</h6>
                                <p class="fs-6">{{ $inmueble->descripcion }}</p>
                            </div>
                        @endif

                        @if ($caracteristicas->count())
                            <div class="mt-4">
                                <h6>Características adicionales</h6>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    @foreach ($caracteristicas as $car)
                                        <span class="badge bg-light text-dark border">{{ $car->nombre }}</span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Panel lateral con detalles -->
            <div class="col-md-4">
                <!-- Detalles técnicos -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Detalles técnicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <table class="table table-sm">
                                    <tbody>
                                        @if($inmueble->referencia_catastral)
                                            <tr>
                                                <td><strong>Ref. Catastral:</strong></td>
                                                <td>{{ $inmueble->referencia_catastral }}</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->m2_construidos)
                                            <tr>
                                                <td><strong>M² construidos:</strong></td>
                                                <td>{{ $inmueble->m2_construidos }} m²</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->cod_postal)
                                            <tr>
                                                <td><strong>Código postal:</strong></td>
                                                <td>{{ $inmueble->cod_postal }}</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->estado)
                                            <tr>
                                                <td><strong>Estado:</strong></td>
                                                <td>{{ $inmueble->estado }}</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->disponibilidad)
                                            <tr>
                                                <td><strong>Disponibilidad:</strong></td>
                                                <td>{{ $inmueble->disponibilidad }}</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->cert_energetico)
                                            <tr>
                                                <td><strong>Cert. energético:</strong></td>
                                                <td>{{ $inmueble->cert_energetico_elegido ?? 'Sí' }}</td>
                                            </tr>
                                        @endif
                                        @if($inmueble->year_built)
                                            <tr>
                                                <td><strong>Año construcción:</strong></td>
                                                <td>{{ $inmueble->year_built }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Información del vendedor -->
                @if($inmueble->vendedor)
                <div class="card mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">Información del vendedor</h6>
                    </div>
                    <div class="card-body">
                        <h6>{{ $inmueble->vendedor->nombre_completo }}</h6>
                        <p class="mb-1"><small class="text-muted">DNI: {{ $inmueble->vendedor->dni }}</small></p>
                        <p class="mb-1"><small class="text-muted">Ubicación: {{ $inmueble->vendedor->ubicacion }}</small></p>
                        <p class="mb-1"><small class="text-muted">Teléfono: {{ $inmueble->vendedor->telefono }}</small></p>
                        <p class="mb-0"><small class="text-muted">Email: {{ $inmueble->vendedor->email }}</small></p>
                    </div>
                </div>
                @endif

                <!-- Acciones rápidas -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Acciones rápidas</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <a href="{{ route('inmuebles.documentos', $inmueble) }}" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-file-alt me-2"></i>Documentos
                            </a>
                            <a href="{{ route('inmuebles.contratos', $inmueble) }}" class="btn btn-outline-success btn-sm">
                                <i class="fas fa-file-contract me-2"></i>Contratos
                            </a>
                            <a href="{{ route('inmuebles.visitas', $inmueble) }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-calendar-check me-2"></i>Visitas
                            </a>
                            <a href="{{ route('inmuebles.caracteristicas', $inmueble) }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-tags me-2"></i>Características
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
