@extends('layouts.app')

@section('encabezado', 'Gestión de Videos de Idealista')
@section('subtitulo', 'Administra los videos de tus propiedades en Idealista')

@section('content')
<div class="container-fluid py-4">
    <style>
        .video-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .video-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .video-preview {
            width: 100%;
            max-width: 300px;
            height: 200px;
            background: #f0f0f0;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
            font-size: 3rem;
        }

        .btn-idealista {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-idealista:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.4);
            color: white;
        }

        .btn-idealista-danger {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .btn-idealista-danger:hover {
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.4);
        }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><i class="fas fa-video me-2"></i>Gestión de Videos</h3>
                    <p class="text-muted">Administra los videos de tus propiedades en Idealista</p>
                </div>
                <div>
                    <a href="{{ route('inmuebles.idealista') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Volver a Idealista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Selector de propiedad -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-search me-2"></i>Seleccionar Propiedad</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <label class="form-label">Buscar propiedad por ID Idealista o título</label>
                            <input type="text" class="form-control" id="property-search"
                                   placeholder="Buscar propiedad...">
                            <div id="property-results" class="mt-2"></div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">O seleccionar de la lista</label>
                            <select class="form-control" id="property-select">
                                <option value="">Selecciona una propiedad...</option>
                                @foreach(\App\Models\Inmuebles::whereNotNull('idealista_property_id')->get() as $inmueble)
                                    <option value="{{ $inmueble->id }}" data-idealista-id="{{ $inmueble->idealista_property_id }}">
                                        {{ $inmueble->titulo }} (ID: {{ $inmueble->idealista_property_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de gestión de videos -->
    <div id="videos-management-area" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-film me-2"></i>Videos de la Propiedad</h5>
                        <button class="btn btn-idealista" onclick="showAddVideoModal()">
                            <i class="fas fa-plus me-2"></i>Añadir Video
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="videos-list">
                            <p class="text-muted text-center">Cargando videos...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para añadir video -->
    <div class="modal fade" id="addVideoModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Video a Idealista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addVideoForm">
                        <input type="hidden" id="video-property-id" name="property_id">
                        <div class="mb-3">
                            <label class="form-label">URL del Video *</label>
                            <input type="url" class="form-control" name="url" required
                                   placeholder="https://ejemplo.com/video.mp4">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Debe ser una URL directa que permita descargar el archivo.
                                Formatos: MP4, AVI, MOV, WMV, MPEG, FLV, 3GP (máx. 750MB)
                                <br>
                                <strong>NO se aceptan:</strong> YouTube, Vimeo u otras plataformas de streaming
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Título del Video</label>
                            <input type="text" class="form-control" name="title"
                                   placeholder="Ej: Tour completo de la propiedad">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="description" rows="3"
                                      placeholder="Descripción del video..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-idealista" onclick="addVideo()">
                        <i class="fas fa-upload me-2"></i>Subir Video
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPropertyId = null;
    let currentIdealistaPropertyId = null;

    document.getElementById('property-select').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            currentPropertyId = option.value;
            currentIdealistaPropertyId = option.getAttribute('data-idealista-id');
            loadVideos();
        }
    });

    document.getElementById('property-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const resultsDiv = document.getElementById('property-results');

        if (query.length < 2) {
            resultsDiv.innerHTML = '';
            return;
        }

        // Filtrar propiedades
        const options = document.querySelectorAll('#property-select option');
        let matches = [];

        options.forEach(option => {
            if (option.value && option.text.toLowerCase().includes(query)) {
                matches.push({
                    id: option.value,
                    idealistaId: option.getAttribute('data-idealista-id'),
                    text: option.text
                });
            }
        });

        if (matches.length > 0) {
            let html = '<div class="list-group">';
            matches.slice(0, 5).forEach(match => {
                html += `<a href="#" class="list-group-item list-group-item-action"
                           onclick="selectProperty(${match.id}, ${match.idealistaId}); return false;">
                    ${match.text}
                </a>`;
            });
            html += '</div>';
            resultsDiv.innerHTML = html;
        } else {
            resultsDiv.innerHTML = '<p class="text-muted">No se encontraron propiedades</p>';
        }
    });

    function selectProperty(propertyId, idealistaId) {
        currentPropertyId = propertyId;
        currentIdealistaPropertyId = idealistaId;
        document.getElementById('property-select').value = propertyId;
        document.getElementById('property-results').innerHTML = '';
        loadVideos();
    }

    function loadVideos() {
        if (!currentPropertyId || !currentIdealistaPropertyId) {
            alert('Por favor selecciona una propiedad primero');
            return;
        }

        document.getElementById('videos-management-area').style.display = 'block';
        document.getElementById('videos-list').innerHTML = '<p class="text-muted text-center">Cargando videos...</p>';

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/videos`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayVideos(data.data);
            } else {
                document.getElementById('videos-list').innerHTML =
                    `<div class="alert alert-warning">${data.message || 'No se pudieron cargar los videos'}</div>`;
            }
        })
        .catch(error => {
            document.getElementById('videos-list').innerHTML =
                `<div class="alert alert-danger">Error al cargar videos: ${error.message}</div>`;
        });
    }

    function displayVideos(data) {
        const videosList = document.getElementById('videos-list');

        if (!data || !data.videos || data.videos.length === 0) {
            videosList.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta propiedad no tiene videos en Idealista. Añade el primero usando el botón "Añadir Video".
                </div>
            `;
            return;
        }

        let html = '<div class="row">';
        data.videos.forEach(video => {
            html += `
                <div class="col-md-6 mb-3">
                    <div class="video-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6>${video.title || 'Video sin título'}</h6>
                                <p class="text-muted small mb-0">ID: ${video.videoId || video.id || 'N/A'}</p>
                            </div>
                            <button class="btn btn-sm btn-idealista-danger"
                                    onclick="deleteVideo(${video.videoId || video.id})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        <div class="video-preview mb-3">
                            <i class="fas fa-video"></i>
                        </div>
                        ${video.url ? `<p class="small text-muted mb-2"><strong>URL:</strong> ${video.url}</p>` : ''}
                        ${video.description ? `<p class="small">${video.description}</p>` : ''}
                    </div>
                </div>
            `;
        });
        html += '</div>';
        videosList.innerHTML = html;
    }

    function showAddVideoModal() {
        if (!currentPropertyId) {
            alert('Por favor selecciona una propiedad primero');
            return;
        }

        document.getElementById('video-property-id').value = currentPropertyId;
        const modal = new bootstrap.Modal(document.getElementById('addVideoModal'));
        modal.show();
    }

    function addVideo() {
        const form = document.getElementById('addVideoForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/videos`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Video añadido correctamente a Idealista');
                bootstrap.Modal.getInstance(document.getElementById('addVideoModal')).hide();
                form.reset();
                loadVideos();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            alert('Error al añadir video: ' + error.message);
        });
    }

    function deleteVideo(videoId) {
        if (!confirm('¿Estás seguro de que quieres eliminar este video de Idealista?')) {
            return;
        }

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/videos`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ video_id: videoId })
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                alert('Video eliminado correctamente');
                loadVideos();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            alert('Error al eliminar video: ' + error.message);
        });
    }
</script>
@endsection

