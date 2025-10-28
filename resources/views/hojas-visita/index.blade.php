@extends('layouts.app')

@section('head')
    @vite(['resources/sass/app.scss'])
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.css">
@endsection

@section('content')
@section('encabezado', 'Hojas de Visita')
@section('subtitulo', 'Listado de visitas realizadas')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-file-signature text-success me-2"></i>Hojas de Visita
                </h1>
                <div class="flex gap-2">
                    <a href="{{ route('inmuebles.index') }}" 
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>
            <p class="text-gray-600 mt-2">
                Gestión de todas las hojas de visita registradas en el sistema
            </p>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table id="hojasVisitaTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Cliente</th>
                            <th>Inmueble</th>
                            <th>Fecha</th>
                            <th>Evento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hojasVisita as $hoja)
                            <tr>
                                <td>{{ $hoja->id }}</td>
                                <td>
                                    @if($hoja->cliente)
                                        <div class="flex items-center">
                                            <i class="fas fa-user text-success me-2"></i>
                                            <span class="font-semibold">{{ $hoja->cliente->nombre_completo }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if($hoja->inmueble)
                                        <div class="flex items-center">
                                            <i class="fas fa-home text-primary me-2"></i>
                                            <span class="font-semibold">{{ $hoja->inmueble->titulo }}</span>
                                        </div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-calendar text-success me-2"></i>
                                    {{ \Carbon\Carbon::parse($hoja->fecha)->format('d/m/Y') }}
                                </td>
                                <td>
                                    @if($hoja->evento)
                                        <span class="badge bg-info">
                                            <i class="fas fa-calendar-check me-1"></i>
                                            {{ $hoja->evento->titulo }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">Sin evento</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        @if($hoja->ruta)
                                            <a href="{{ route('visitas.download', $hoja) }}" 
                                                class="btn btn-sm btn-success" 
                                                title="Descargar PDF">
                                                <i class="fas fa-download me-1"></i>PDF
                                            </a>
                                        @endif
                                        @if($hoja->firma)
                                            <button type="button" 
                                                class="btn btn-sm btn-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#firmaModal{{ $hoja->id }}"
                                                title="Ver firma">
                                                <i class="fas fa-signature me-1"></i>Firma
                                            </button>
                                        @endif
                                        <a href="{{ route('visitas.show', $hoja) }}" 
                                            class="btn btn-sm btn-info" 
                                            title="Ver detalles">
                                            <i class="fas fa-eye me-1"></i>
                                        </a>
                                        <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $hoja->id }})"
                                            title="Eliminar">
                                            <i class="fas fa-trash me-1"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Modal para ver firma -->
                            @if($hoja->firma)
                                <div class="modal fade" id="firmaModal{{ $hoja->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header bg-success text-white">
                                                <h5 class="modal-title">
                                                    <i class="fas fa-signature me-2"></i>Firma del Cliente
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body text-center">
                                                @if(file_exists(public_path($hoja->firma)))
                                                    <img src="{{ asset($hoja->firma) }}" 
                                                         alt="Firma" 
                                                         class="img-fluid rounded border p-3"
                                                         style="max-height: 400px; background: #f8f9fa;">
                                                @else
                                                    <div class="alert alert-warning">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        El archivo de firma no se encuentra en: {{ $hoja->firma }}
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Cerrar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-8">
                                    <i class="fas fa-inbox text-gray-400 text-4xl mb-3 d-block"></i>
                                    <p class="text-gray-500">No hay hojas de visita registradas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para eliminar -->
<form id="deleteForm" action="" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap5.min.js"></script>
<script>
    $(document).ready(function() {
        $('#hojasVisitaTable').DataTable({
            responsive: true,
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.4/i18n/es-ES.json'
            },
            order: [[0, 'desc']],
            columnDefs: [
                { orderable: false, targets: -1 }
            ]
        });
    });

    function confirmDelete(id) {
        if (confirm('¿Estás seguro de que deseas eliminar esta hoja de visita?')) {
            const form = document.getElementById('deleteForm');
            form.action = '{{ url("admin/visitas") }}/' + id;
            form.submit();
        }
    }
</script>
@endsection

