@extends('layouts.app')

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('content')
@section('encabezado', 'Hojas de Visita')
@section('subtitulo', 'Gestión de formularios de visita')

<div class="container-fluid px-4 py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold">
                        <i class="fas fa-file-signature text-success me-2"></i>
                        Hojas de Visita
                    </h4>
                    <p class="text-muted mb-0">Total: {{ $hojasVisita->total() }} registros</p>
                </div>
                <a href="{{ route('inmuebles.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>Cliente</th>
                            <th>Inmueble</th>
                            <th>Fecha</th>
                            <th style="width: 200px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hojasVisita as $hoja)
                            <tr>
                                <td class="fw-bold">#{{ $hoja->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-success text-white me-2">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $hoja->cliente->nombre_completo ?? 'N/A' }}</div>
                                            @if($hoja->cliente)
                                                <small class="text-muted">{{ $hoja->cliente->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary text-white me-2">
                                            <i class="fas fa-home"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $hoja->inmueble->titulo ?? 'N/A' }}</div>
                                            @if($hoja->inmueble)
                                                <small class="text-muted">{{ $hoja->inmueble->ubicacion }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <i class="fas fa-calendar text-muted me-1"></i>
                                    {{ \Carbon\Carbon::parse($hoja->fecha)->format('d/m/Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($hoja->created_at)->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @if($hoja->ruta)
                                            <a href="{{ route('hojas-visita.download', $hoja) }}" 
                                               class="btn btn-sm btn-success" 
                                               title="Descargar PDF">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        @if($hoja->firma)
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#firmaModal{{ $hoja->id }}"
                                                    title="Ver firma">
                                                <i class="fas fa-signature"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('hojas-visita.show', $hoja) }}" 
                                           class="btn btn-sm btn-primary"
                                           title="Ver detalles">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete({{ $hoja->id }})"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">No hay hojas de visita registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($hojasVisita->hasPages())
            <div class="card-footer bg-white py-3">
                {{ $hojasVisita->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modales para firmas -->
@foreach($hojasVisita as $hoja)
    @if($hoja->firma)
        <div class="modal fade" id="firmaModal{{ $hoja->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-signature me-2"></i>Firma del Cliente
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="{{ asset($hoja->firma) }}" 
                             alt="Firma" 
                             class="img-fluid rounded shadow-sm border"
                             style="max-height: 500px;">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<style>
    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }
</style>

<script>
    function confirmDelete(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta hoja de visita?')) {
            const form = document.getElementById('deleteForm');
            form.action = '/admin/hojas-visita/' + id;
            form.submit();
        }
    }
</script>
@endsection

