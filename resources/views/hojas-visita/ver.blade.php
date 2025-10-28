@extends('layouts.app')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('content')
@section('encabezado', 'Detalles de Hoja de Visita')
@section('subtitulo', 'Información completa')

<div class="container-fluid px-4 py-4">
    <div class="row">
        <div class="col-md-4 mb-4">
            <!-- Información del Cliente -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>CLIENTE
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $hojaVisita->cliente->nombre_completo ?? 'N/A' }}</h6>
                    <p class="mb-2"><i class="fas fa-id-card text-muted me-2"></i>{{ $hojaVisita->cliente->dni ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-envelope text-muted me-2"></i>{{ $hojaVisita->cliente->email ?? 'N/A' }}</p>
                    @if($hojaVisita->cliente->telefono)
                        <p class="mb-0"><i class="fas fa-phone text-muted me-2"></i>{{ $hojaVisita->cliente->telefono }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <!-- Información del Inmueble -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-home me-2"></i>INMUEBLE
                    </h5>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">{{ $hojaVisita->inmueble->titulo ?? 'N/A' }}</h6>
                    <p class="mb-2"><i class="fas fa-map-marker-alt text-muted me-2"></i>{{ $hojaVisita->inmueble->ubicacion ?? 'N/A' }}</p>
                    <p class="mb-2"><i class="fas fa-bed text-muted me-2"></i>{{ $hojaVisita->inmueble->habitaciones ?? 'N/A' }} habitaciones</p>
                    <p class="mb-2"><i class="fas fa-bath text-muted me-2"></i>{{ $hojaVisita->inmueble->banos ?? 'N/A' }} baños</p>
                    <p class="mb-0"><i class="fas fa-ruler-combined text-muted me-2"></i>{{ $hojaVisita->inmueble->m2 ?? 'N/A' }} m²</p>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <!-- Información de la Visita -->
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check me-2"></i>VISITA
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Fecha:</strong><br>{{ \Carbon\Carbon::parse($hojaVisita->fecha)->format('d/m/Y') }}</p>
                    <p class="mb-2"><strong>Creado:</strong><br>{{ $hojaVisita->created_at->format('d/m/Y H:i') }}</p>
                    @if($hojaVisita->evento)
                        <p class="mb-0"><strong>Evento:</strong><br>{{ $hojaVisita->evento->titulo }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Firma y Acciones -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-signature me-2"></i>FIRMA DEL CLIENTE
                    </h5>
                </div>
                <div class="card-body text-center">
                    @if($hojaVisita->firma && file_exists(public_path($hojaVisita->firma)))
                        <img src="{{ asset($hojaVisita->firma) }}" 
                             alt="Firma del cliente" 
                             class="img-fluid rounded border shadow-sm"
                             style="max-height: 400px;">
                    @else
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            No hay firma disponible
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Acciones</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($hojaVisita->ruta)
                            <a href="{{ route('hojas-visita.download', $hojaVisita) }}" class="btn btn-success btn-lg">
                                <i class="fas fa-download me-2"></i>Descargar PDF
                            </a>
                        @endif
                        
                        <button type="button" 
                                class="btn btn-danger" 
                                onclick="confirmDelete({{ $hojaVisita->id }})">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                        
                        <a href="{{ route('hojas-visita.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al listado
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" action="{{ route('hojas-visita.destroy', $hojaVisita) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script>
    function confirmDelete(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta hoja de visita?')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endsection

