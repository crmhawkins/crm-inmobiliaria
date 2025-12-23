@extends('layouts.app')

@section('encabezado', 'Visitas del Inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><i class="fas fa-calendar-check me-2"></i>Visitas - {{ $inmueble->titulo }}</h3>
                    <p class="text-muted">Gestiona las visitas programadas para este inmueble</p>
                </div>
                <div>
                    <a href="{{ route('agenda.index') }}?inmueble_id={{ $inmueble->id }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Nueva Visita
                    </a>
                    <a href="{{ route('inmuebles.admin-show', $inmueble) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver al Inmueble
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if($visitas->count() > 0)
        <div class="row">
            @foreach($visitas as $visita)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-header bg-info text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-calendar me-2"></i>
                                {{ $visita->titulo ?? 'Visita sin título' }}
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($visita->cliente)
                                <p class="mb-2">
                                    <strong><i class="fas fa-user me-2"></i>Cliente:</strong>
                                    {{ $visita->cliente->nombre_completo }}
                                </p>
                            @endif
                            
                            @if($visita->fecha_inicio)
                                <p class="mb-2">
                                    <strong><i class="fas fa-clock me-2"></i>Fecha:</strong>
                                    {{ \Carbon\Carbon::parse($visita->fecha_inicio)->format('d/m/Y H:i') }}
                                </p>
                            @endif

                            @if($visita->fecha_fin)
                                <p class="mb-2">
                                    <strong><i class="fas fa-clock me-2"></i>Hasta:</strong>
                                    {{ \Carbon\Carbon::parse($visita->fecha_fin)->format('d/m/Y H:i') }}
                                </p>
                            @endif

                            @if($visita->descripcion)
                                <p class="mb-2">
                                    <strong><i class="fas fa-align-left me-2"></i>Descripción:</strong>
                                    {{ Str::limit($visita->descripcion, 100) }}
                                </p>
                            @endif

                            @if($visita->tipo_tarea)
                                <p class="mb-0">
                                    <span class="badge bg-secondary">{{ $visita->tipo_tarea }}</span>
                                </p>
                            @endif
                        </div>
                        <div class="card-footer bg-light">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('agenda.index') }}?evento_id={{ $visita->id }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i>Ver
                                </a>
                                <a href="{{ route('agenda.index') }}?evento_id={{ $visita->id }}&edit=1" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit me-1"></i>Editar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3"></i>
                    <h5>No hay visitas programadas</h5>
                    <p class="mb-0">Aún no se han programado visitas para este inmueble.</p>
                    <a href="{{ route('agenda.index') }}?inmueble_id={{ $inmueble->id }}" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-2"></i>Programar Primera Visita
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

