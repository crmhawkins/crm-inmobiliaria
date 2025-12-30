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

        <!-- Mensajes flash y errores de sincronización -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Panel de errores de sincronización -->
        @if($inmueble->idealista_sync_error || $inmueble->fotocasa_sync_error || session('idealista_error') || session('fotocasa_error'))
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card border-danger">
                        <div class="card-header bg-danger text-white">
                            <h6 class="mb-0">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Errores de Sincronización
                            </h6>
                        </div>
                        <div class="card-body">
                            @if($inmueble->idealista_sync_error || session('idealista_error'))
                                <div class="alert alert-warning mb-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-building me-2"></i>Error al sincronizar con Idealista
                                            </h6>
                                            <p class="mb-2 small">
                                                <pre class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;">{{ $inmueble->idealista_sync_error ?? session('idealista_error') }}</pre>
                                            </p>
                                            @if($inmueble->idealista_last_sync_error_at)
                                                <small class="text-muted">
                                                    Último error: {{ $inmueble->idealista_last_sync_error_at->format('d/m/Y H:i:s') }}
                                                </small>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-warning ms-3" onclick="retrySyncIdealista({{ $inmueble->id }})">
                                            <i class="fas fa-redo me-1"></i>Reintentar
                                        </button>
                                    </div>
                                </div>
                            @endif

                            @if($inmueble->fotocasa_sync_error || session('fotocasa_error'))
                                <div class="alert alert-warning mb-0">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="alert-heading">
                                                <i class="fas fa-home me-2"></i>Error al sincronizar con Fotocasa
                                            </h6>
                                            <p class="mb-2 small">
                                                <pre class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;">{{ $inmueble->fotocasa_sync_error ?? session('fotocasa_error') }}</pre>
                                            </p>
                                            @if($inmueble->fotocasa_last_sync_error_at)
                                                <small class="text-muted">
                                                    Último error: {{ $inmueble->fotocasa_last_sync_error_at->format('d/m/Y H:i:s') }}
                                                </small>
                                            @endif
                                        </div>
                                        <button type="button" class="btn btn-sm btn-warning ms-3" onclick="retrySyncFotocasa({{ $inmueble->id }})">
                                            <i class="fas fa-redo me-1"></i>Reintentar
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                                <img src="{{ $imagenes[0] }}" id="imagenPrincipal" class="img-fluid rounded w-100"
                                    style="max-height: 450px; object-fit: cover; cursor: pointer;"
                                    onclick="mostrarImagen('{{ $imagenes[0] }}')">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="height: 300px;">
                                    <span class="text-muted">Sin imagen principal</span>
                                </div>
                            @endif
                        </div>
                        @if(count($imagenes) > 1)
                            @php
                                $imagenesRestantes = array_slice($imagenes, 1);
                                $imagenesVisibles = array_slice($imagenesRestantes, 0, 3);
                                $imagenesOcultas = array_slice($imagenesRestantes, 3);
                            @endphp
                            <div class="row g-2">
                                @foreach ($imagenesVisibles as $img)
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <img src="{{ $img }}" class="img-fluid rounded w-100"
                                            style="height: 120px; object-fit: cover; cursor: pointer;"
                                            onclick="mostrarImagen('{{ $img }}')">
                                    </div>
                                @endforeach

                                @if(count($imagenesOcultas) > 0)
                                    <div class="col-6 col-sm-4 col-md-3">
                                        <div class="position-relative rounded overflow-hidden"
                                             style="height: 120px; cursor: pointer;"
                                             onclick="abrirModalCarrusel()">
                                            <div class="position-relative h-100" style="display: flex; flex-direction: column;">
                                                @foreach(array_slice($imagenesOcultas, 0, 3) as $index => $img)
                                                    <div class="position-absolute w-100 h-100" style="top: {{ $index * 5 }}px; left: {{ $index * 5 }}px; z-index: {{ 10 - $index }};">
                                                        <img src="{{ $img }}" class="w-100 h-100"
                                                             style="object-fit: cover; filter: blur(2px); opacity: {{ 1 - ($index * 0.2) }};">
                                                    </div>
                                                @endforeach
                                                <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 20;">
                                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                         style="width: 50px; height: 50px; font-size: 24px; font-weight: bold;">
                                                        +{{ count($imagenesOcultas) }}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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

                <!-- Gestión de Idealista -->
                @if($inmueble->idealista_property_id)
                <div class="card mb-4 border-warning">
                    <div class="card-header bg-warning text-dark">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Gestión Idealista
                            <span class="badge bg-success ms-2">Sincronizado</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            <strong>ID Idealista:</strong> {{ $inmueble->idealista_property_id }}<br>
                            @if($inmueble->idealista_synced_at)
                                <strong>Sincronizado:</strong> {{ $inmueble->idealista_synced_at->format('d/m/Y H:i') }}
                            @endif
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('inmuebles.idealista-preview', $inmueble) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-2"></i>Ver en Idealista
                            </a>
                            <button class="btn btn-sm btn-outline-success" onclick="updateIdealistaProperty({{ $inmueble->id }})">
                                <i class="fas fa-sync me-2"></i>Actualizar en Idealista
                            </button>
                            <button class="btn btn-sm btn-outline-warning" onclick="deactivateIdealistaProperty({{ $inmueble->id }})">
                                <i class="fas fa-pause me-2"></i>Desactivar
                            </button>
                            <button class="btn btn-sm btn-outline-info" onclick="reactivateIdealistaProperty({{ $inmueble->id }})">
                                <i class="fas fa-play me-2"></i>Reactivar
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="showCloneModal({{ $inmueble->id }})">
                                <i class="fas fa-copy me-2"></i>Clonar (Venta/Alquiler)
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="manageIdealistaVideos({{ $inmueble->id }})">
                                <i class="fas fa-video me-2"></i>Gestionar Videos
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="manageIdealistaVirtualTours({{ $inmueble->id }})">
                                <i class="fas fa-cube me-2"></i>Tours Virtuales
                            </button>
                        </div>
                    </div>
                </div>
                @elseif($inmueble->idealista_sync_error)
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Gestión Idealista
                            <span class="badge bg-danger ms-2">Error de sincronización</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-3">
                            <h6 class="alert-heading">Error al sincronizar con Idealista</h6>
                            <p class="mb-2 small">
                                <pre class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;">{{ $inmueble->idealista_sync_error }}</pre>
                            </p>
                            @if($inmueble->idealista_last_sync_error_at)
                                <small class="text-muted">
                                    Último error: {{ $inmueble->idealista_last_sync_error_at->format('d/m/Y H:i:s') }}
                                </small>
                            @endif
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-sm btn-warning" onclick="retrySyncIdealista({{ $inmueble->id }})">
                                <i class="fas fa-redo me-2"></i>Reintentar sincronización
                            </button>
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar inmueble
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="card mb-4 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-building me-2"></i>
                            Gestión Idealista
                            <span class="badge bg-warning ms-2">No sincronizado</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Este inmueble aún no está sincronizado con Idealista. Se sincronizará automáticamente al actualizar si cumple los requisitos.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar para sincronizar
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Gestión de Fotocasa -->
                @if($inmueble->external_id)
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            Gestión Fotocasa
                            <span class="badge bg-success ms-2">Sincronizado</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            <strong>ID Externo:</strong> {{ $inmueble->external_id }}
                        </p>
                    </div>
                </div>
                @elseif($inmueble->fotocasa_sync_error)
                <div class="card mb-4 border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            Gestión Fotocasa
                            <span class="badge bg-danger ms-2">Error de sincronización</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-danger mb-3">
                            <h6 class="alert-heading">Error al sincronizar con Fotocasa</h6>
                            <p class="mb-2 small">
                                <pre class="mb-0" style="white-space: pre-wrap; font-size: 0.875rem;">{{ $inmueble->fotocasa_sync_error }}</pre>
                            </p>
                            @if($inmueble->fotocasa_last_sync_error_at)
                                <small class="text-muted">
                                    Último error: {{ $inmueble->fotocasa_last_sync_error_at->format('d/m/Y H:i:s') }}
                                </small>
                            @endif
                        </div>
                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-sm btn-warning" onclick="retrySyncFotocasa({{ $inmueble->id }})">
                                <i class="fas fa-redo me-2"></i>Reintentar sincronización
                            </button>
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar inmueble
                            </a>
                        </div>
                    </div>
                </div>
                @else
                <div class="card mb-4 border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-home me-2"></i>
                            Gestión Fotocasa
                            <span class="badge bg-warning ms-2">No sincronizado</span>
                        </h6>
                    </div>
                    <div class="card-body">
                        <p class="small text-muted mb-3">
                            Este inmueble aún no está sincronizado con Fotocasa. Se sincronizará automáticamente al actualizar si cumple los requisitos.
                        </p>
                        <div class="d-grid gap-2">
                            <a href="{{ route('inmuebles.edit', $inmueble) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-edit me-2"></i>Editar para sincronizar
                            </a>
                        </div>
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

    <!-- Modal para clonar propiedad -->
    <div class="modal fade" id="cloneModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Clonar Propiedad en Idealista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Selecciona la operación para la que quieres clonar esta propiedad:</p>
                    <form id="cloneForm">
                        <div class="mb-3">
                            <label class="form-label">Operación *</label>
                            <select class="form-control" name="operation" required>
                                <option value="">Selecciona...</option>
                                <option value="sale">Venta</option>
                                <option value="rent">Alquiler</option>
                            </select>
                            <small class="text-muted">La propiedad se clonará para la operación seleccionada. Esto solo consume un slot en Idealista.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="cloneProperty()">Clonar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPropertyId = null;

        function updateIdealistaProperty(id) {
            if (!confirm('¿Actualizar esta propiedad en Idealista?')) return;

            fetch(`/admin/inmuebles/${id}/idealista/update`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Propiedad actualizada correctamente en Idealista');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al actualizar: ' + error.message);
            });
        }

        function deactivateIdealistaProperty(id) {
            if (!confirm('¿Desactivar esta propiedad en Idealista?')) return;

            fetch(`/admin/inmuebles/${id}/idealista/deactivate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Propiedad desactivada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al desactivar: ' + error.message);
            });
        }

        function reactivateIdealistaProperty(id) {
            if (!confirm('¿Reactivar esta propiedad en Idealista?')) return;

            fetch(`/admin/inmuebles/${id}/idealista/reactivate`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Propiedad reactivada correctamente');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al reactivar: ' + error.message);
            });
        }

        function showCloneModal(id) {
            currentPropertyId = id;
            const modal = new bootstrap.Modal(document.getElementById('cloneModal'));
            modal.show();
        }

        function cloneProperty() {
            const form = document.getElementById('cloneForm');
            const formData = new FormData(form);
            const operation = formData.get('operation');

            if (!operation) {
                alert('Por favor selecciona una operación');
                return;
            }

            fetch(`/admin/inmuebles/${currentPropertyId}/idealista/clone`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ operation })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Propiedad clonada correctamente en Idealista');
                    bootstrap.Modal.getInstance(document.getElementById('cloneModal')).hide();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al clonar: ' + error.message);
            });
        }

        function manageIdealistaVideos(id) {
            window.location.href = `{{ route('inmuebles.idealista-videos') }}?property=${id}`;
        }

        function manageIdealistaVirtualTours(id) {
            window.location.href = `{{ route('inmuebles.idealista-virtual-tours') }}?property=${id}`;
        }

        function retrySyncIdealista(id) {
            if (!confirm('¿Reintentar la sincronización con Idealista?')) return;

            const button = document.querySelector(`button[onclick="retrySyncIdealista(${id})"]`);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reintentando...';

            fetch(`{{ url('/admin/inmuebles') }}/${id}/retry-sync/idealista`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Inmueble sincronizado correctamente con Idealista');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                alert('Error al reintentar sincronización: ' + error.message);
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        function retrySyncFotocasa(id) {
            if (!confirm('¿Reintentar la sincronización con Fotocasa?')) return;

            const button = document.querySelector(`button[onclick*="retrySyncFotocasa(${id})"]`);
            const originalText = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Reintentando...';

            fetch(`{{ url('/admin/inmuebles') }}/${id}/retry-sync/fotocasa`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Inmueble sincronizado correctamente con Fotocasa');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    button.disabled = false;
                    button.innerHTML = originalText;
                }
            })
            .catch(error => {
                alert('Error al reintentar sincronización: ' + error.message);
                button.disabled = false;
                button.innerHTML = originalText;
            });
        }

        function mostrarImagen(url) {
            document.getElementById('imagenPrincipal').src = url;
            document.getElementById('imagenModalImg').src = url;
            const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
            modal.show();
        }

        function abrirModalCarrusel() {
            const modal = new bootstrap.Modal(document.getElementById('carruselModal'));
            modal.show();
        }
    </script>

    <!-- Modal para ver imagen en grande -->
    <div class="modal fade" id="imagenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center">
                    <img id="imagenModalImg" src="" class="img-fluid rounded" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal carrusel con todas las imágenes -->
    <div class="modal fade" id="carruselModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white">Galería completa ({{ count($imagenes) }} imágenes)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="carouselImagenes" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($imagenes as $index => $img)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $img }}" class="d-block w-100" style="max-height: 70vh; object-fit: contain;">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImagenes" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImagenes" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <span class="text-white" id="contadorImagen">1 / {{ count($imagenes) }}</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Hacer clic en imagen principal también abre el modal
        document.addEventListener('DOMContentLoaded', function() {
            const imgPrincipal = document.getElementById('imagenPrincipal');
            if (imgPrincipal) {
                imgPrincipal.addEventListener('click', function() {
                    mostrarImagen(this.src);
                });
            }

            // Actualizar contador del carrusel
            const carousel = document.getElementById('carouselImagenes');
            if (carousel) {
                carousel.addEventListener('slid.bs.carousel', function (e) {
                    const activeIndex = e.to;
                    const total = {{ count($imagenes) }};
                    document.getElementById('contadorImagen').textContent = (activeIndex + 1) + ' / ' + total;
                });
            }
        });
    </script>
@endsection
