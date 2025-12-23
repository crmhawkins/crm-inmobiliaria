@extends('layouts.app')

@section('encabezado', 'Gestión de Tours Virtuales de Idealista')
@section('subtitulo', 'Administra los tours virtuales 3D de tus propiedades')

@section('content')
<div class="container-fluid py-4">
    <style>
        .tour-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            margin-bottom: 20px;
            transition: transform 0.3s ease;
        }

        .tour-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.12);
        }

        .tour-preview {
            width: 100%;
            max-width: 300px;
            height: 200px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
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

        .provider-badge {
            display: inline-block;
            padding: 5px 12px;
            background: #FF6B35;
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 5px 5px 5px 0;
        }
    </style>

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3><i class="fas fa-cube me-2"></i>Gestión de Tours Virtuales</h3>
                    <p class="text-muted">Administra los tours virtuales 3D de tus propiedades en Idealista</p>
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

    <!-- Área de gestión de tours -->
    <div id="tours-management-area" style="display: none;">
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><i class="fas fa-vr-cardboard me-2"></i>Tours Virtuales de la Propiedad</h5>
                        <button class="btn btn-idealista" onclick="showAddTourModal()">
                            <i class="fas fa-plus me-2"></i>Añadir Tour Virtual
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="tours-list">
                            <p class="text-muted text-center">Cargando tours virtuales...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para añadir tour virtual -->
    <div class="modal fade" id="addTourModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Añadir Tour Virtual a Idealista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addTourForm">
                        <input type="hidden" id="tour-property-id" name="property_id">
                        <div class="mb-3">
                            <label class="form-label">URL del Tour Virtual *</label>
                            <input type="url" class="form-control" name="url" required
                                   placeholder="https://ejemplo.com/tour">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                URL oficial del proveedor del tour virtual
                            </small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Tour *</label>
                            <select class="form-control" name="type" required>
                                <option value="">Selecciona...</option>
                                <option value="3d">Tour 3D (Matterport, VistaPlayer3d)</option>
                                <option value="virtual">Tour Virtual (Otros formatos)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Proveedor</label>
                            <select class="form-control" name="provider">
                                <option value="">Selecciona un proveedor...</option>
                                <option value="Matterport">Matterport</option>
                                <option value="VistaPlayer3d">VistaPlayer3d</option>
                                <option value="Immoviewer">Immoviewer</option>
                                <option value="Spectando">Spectando</option>
                                <option value="Floorplanner">Floorplanner</option>
                                <option value="Realisti_co">Realisti_co</option>
                                <option value="Goldmark">Goldmark</option>
                                <option value="Floorfy">Floorfy</option>
                                <option value="Fastout">Fastout</option>
                                <option value="Panotour">Panotour</option>
                                <option value="Everpano">Everpano</option>
                                <option value="Toursvirtuales360">Toursvirtuales360</option>
                                <option value="KeepEyeOnBall">KeepEyeOnBall</option>
                                <option value="Inmovilla">Inmovilla</option>
                                <option value="Abitarepn">Abitarepn</option>
                                <option value="Pano2VR">Pano2VR</option>
                                <option value="Plushglobalmedia">Plushglobalmedia</option>
                                <option value="Vizor.io">Vizor.io</option>
                                <option value="Nodalview">Nodalview</option>
                                <option value="Gothru">Gothru</option>
                                <option value="Guru360">Guru360</option>
                                <option value="Creotour">Creotour</option>
                                <option value="Habiteo">Habiteo</option>
                                <option value="Vitrio">Vitrio</option>
                                <option value="Plug-in.studio">Plug-in.studio</option>
                                <option value="Ppgstudios">Ppgstudios</option>
                                <option value="360forcurious">360forcurious</option>
                                <option value="Roundme">Roundme</option>
                                <option value="Virtualitour">Virtualitour</option>
                                <option value="Sircase">Sircase</option>
                                <option value="Divein.studio">Divein.studio</option>
                                <option value="Casagest24">Casagest24</option>
                                <option value="Spherical">Spherical</option>
                                <option value="Gizmo-3d">Gizmo-3d</option>
                                <option value="Kuula">Kuula</option>
                                <option value="Emporda360">Emporda360</option>
                                <option value="Vista360">Vista360</option>
                                <option value="Clicktours">Clicktours</option>
                                <option value="Espaciosvirtuales.es">Espaciosvirtuales.es</option>
                                <option value="Cloudpano">Cloudpano</option>
                                <option value="Bizionar">Bizionar</option>
                                <option value="Matterport360">Matterport360</option>
                            </select>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Selecciona el proveedor del tour virtual. Si no está en la lista, déjalo vacío.
                            </small>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Importante:</strong> Solo se permite un tour 3D y un tour virtual por propiedad.
                            Si añades uno nuevo, reemplazará al anterior.
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-idealista" onclick="addTour()">
                        <i class="fas fa-upload me-2"></i>Añadir Tour Virtual
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPropertyId = null;
    let currentIdealistaPropertyId = null;

    // Cargar propiedad desde URL si existe
    const urlParams = new URLSearchParams(window.location.search);
    const propertyId = urlParams.get('property');
    if (propertyId) {
        const option = document.querySelector(`#property-select option[value="${propertyId}"]`);
        if (option) {
            currentPropertyId = propertyId;
            currentIdealistaPropertyId = option.getAttribute('data-idealista-id');
            document.getElementById('property-select').value = propertyId;
            loadTours();
        }
    }

    document.getElementById('property-select').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (option.value) {
            currentPropertyId = option.value;
            currentIdealistaPropertyId = option.getAttribute('data-idealista-id');
            loadTours();
        }
    });

    document.getElementById('property-search').addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const resultsDiv = document.getElementById('property-results');

        if (query.length < 2) {
            resultsDiv.innerHTML = '';
            return;
        }

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
        loadTours();
    }

    function loadTours() {
        if (!currentPropertyId || !currentIdealistaPropertyId) {
            alert('Por favor selecciona una propiedad primero');
            return;
        }

        document.getElementById('tours-management-area').style.display = 'block';
        document.getElementById('tours-list').innerHTML = '<p class="text-muted text-center">Cargando tours virtuales...</p>';

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/virtual-tours`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTours(data.data);
            } else {
                document.getElementById('tours-list').innerHTML =
                    `<div class="alert alert-warning">${data.message || 'No se pudieron cargar los tours virtuales'}</div>`;
            }
        })
        .catch(error => {
            document.getElementById('tours-list').innerHTML =
                `<div class="alert alert-danger">Error al cargar tours: ${error.message}</div>`;
        });
    }

    function displayTours(data) {
        const toursList = document.getElementById('tours-list');

        if (!data || (!data.virtualTours && !data.tours) || (data.virtualTours && data.virtualTours.length === 0 && (!data.tours || data.tours.length === 0))) {
            toursList.innerHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta propiedad no tiene tours virtuales en Idealista. Añade el primero usando el botón "Añadir Tour Virtual".
                </div>
            `;
            return;
        }

        const tours = data.virtualTours || data.tours || [];
        let html = '<div class="row">';
        tours.forEach(tour => {
            const type = tour.type || tour.tourType || 'virtual';
            const provider = tour.provider || 'No especificado';
            const url = tour.url || '';
            html += `
                <div class="col-md-6 mb-3">
                    <div class="tour-card">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h6>
                                    <i class="fas fa-${type === '3d' ? 'cube' : 'vr-cardboard'} me-2"></i>
                                    Tour ${type === '3d' ? '3D' : 'Virtual'}
                                </h6>
                                <span class="provider-badge">${provider}</span>
                            </div>
                            <button class="btn btn-sm btn-outline-danger"
                                    onclick="deactivateTour('${type}', '${url.replace(/'/g, "\\'")}')">
                                <i class="fas fa-ban me-1"></i>Desactivar
                            </button>
                        </div>
                        <div class="tour-preview mb-3">
                            <i class="fas fa-cube"></i>
                        </div>
                        ${url ? `<p class="small text-muted mb-2"><strong>URL:</strong> <a href="${url}" target="_blank">${url}</a></p>` : ''}
                    </div>
                </div>
            `;
        });
        html += '</div>';
        toursList.innerHTML = html;
    }

    function showAddTourModal() {
        if (!currentPropertyId) {
            alert('Por favor selecciona una propiedad primero');
            return;
        }

        document.getElementById('tour-property-id').value = currentPropertyId;
        const modal = new bootstrap.Modal(document.getElementById('addTourModal'));
        modal.show();
    }

    function addTour() {
        const form = document.getElementById('addTourForm');
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/virtual-tours`, {
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
                alert('Tour virtual añadido correctamente a Idealista');
                bootstrap.Modal.getInstance(document.getElementById('addTourModal')).hide();
                form.reset();
                loadTours();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            alert('Error al añadir tour virtual: ' + error.message);
        });
    }

    function deactivateTour(type, url) {
        if (!confirm('¿Estás seguro de que quieres desactivar este tour virtual?')) {
            return;
        }

        if (!url) {
            url = prompt('Introduce la URL del tour virtual a desactivar:');
            if (!url) {
                return;
            }
        }

        const data = {
            type: type,
            url: url
        };

        fetch(`/admin/inmuebles/${currentPropertyId}/idealista/virtual-tours/deactivate`, {
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
                alert('Tour virtual desactivado correctamente');
                loadTours();
            } else {
                alert('Error: ' + result.message);
            }
        })
        .catch(error => {
            alert('Error al desactivar tour: ' + error.message);
        });
    }
</script>
@endsection

