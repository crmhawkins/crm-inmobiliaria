@extends('layouts.app')

@section('encabezado', 'Gestión de Idealista')
@section('subtitulo', 'Administra todas las funcionalidades de Idealista desde el CRM')

@section('content')
<div class="container-fluid py-4">
    <style>
        .idealista-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        }

        .idealista-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .idealista-card-header {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
            color: white;
            padding: 20px;
            border-radius: 12px 12px 0 0;
            font-weight: 600;
        }

        .idealista-card-body {
            padding: 25px;
        }

        .idealista-btn {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            border: none;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .idealista-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.4);
            color: white;
        }

        .idealista-btn-secondary {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
        }

        .idealista-btn-secondary:hover {
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.4);
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #6b8e6b;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }
    </style>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" id="total-properties">-</div>
                <div class="stat-label">Propiedades en Idealista</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" id="active-properties">-</div>
                <div class="stat-label">Propiedades Activas</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" id="total-contacts">-</div>
                <div class="stat-label">Contactos</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-number" id="published-ads">-</div>
                <div class="stat-label">Anuncios Publicados</div>
            </div>
        </div>
    </div>

    <!-- Funcionalidades principales -->
    <div class="row">
        <!-- Gestión de Propiedades -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-home me-2"></i>
                    Gestión de Propiedades
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Administra tus propiedades en Idealista</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('inmuebles.idealista-list') }}" class="btn idealista-btn">
                            <i class="fas fa-list me-2"></i>
                            Listar Todas las Propiedades
                        </a>
                        <a href="{{ route('inmuebles.idealista-recent') }}" class="btn idealista-btn">
                            <i class="fas fa-clock me-2"></i>
                            Últimas Propiedades Subidas
                        </a>
                        <button class="btn idealista-btn" onclick="loadProperties('active')">
                            <i class="fas fa-check-circle me-2"></i>
                            Ver Propiedades Activas
                        </button>
                        <button class="btn idealista-btn" onclick="loadProperties('inactive')">
                            <i class="fas fa-times-circle me-2"></i>
                            Ver Propiedades Inactivas
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Contactos -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-users me-2"></i>
                    Gestión de Contactos
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Administra los contactos asociados a las propiedades</p>
                    <div class="d-grid gap-2">
                        <button class="btn idealista-btn" onclick="loadContacts()">
                            <i class="fas fa-list me-2"></i>
                            Listar Contactos
                        </button>
                        <button class="btn idealista-btn" onclick="showCreateContactModal()">
                            <i class="fas fa-plus me-2"></i>
                            Crear Nuevo Contacto
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Videos -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-video me-2"></i>
                    Gestión de Videos
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Añade y gestiona videos para tus propiedades</p>
                    <p class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Formatos: MP4, AVI, MOV, WMV, MPEG, FLV, 3GP (máx. 750MB)
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('inmuebles.idealista-videos') }}" class="btn idealista-btn">
                            <i class="fas fa-film me-2"></i>
                            Gestionar Videos de Propiedades
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gestión de Tours Virtuales -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-cube me-2"></i>
                    Tours Virtuales
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Añade tours virtuales 3D a tus propiedades</p>
                    <p class="small text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Proveedores: Matterport, VistaPlayer3d, Immoviewer, etc.
                    </p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('inmuebles.idealista-virtual-tours') }}" class="btn idealista-btn">
                            <i class="fas fa-vr-cardboard me-2"></i>
                            Gestionar Tours Virtuales
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información de Publicación -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-chart-bar me-2"></i>
                    Información de Publicación
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Consulta el estado de tu cuenta y límites de publicación</p>
                    <div class="d-grid gap-2">
                        <button class="btn idealista-btn-secondary" onclick="loadPublicationInfo()">
                            <i class="fas fa-info-circle me-2"></i>
                            Ver Información de Cuenta
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="col-md-6 mb-4">
            <div class="idealista-card">
                <div class="idealista-card-header">
                    <i class="fas fa-bolt me-2"></i>
                    Acciones Rápidas
                </div>
                <div class="idealista-card-body">
                    <p class="text-muted mb-3">Accesos directos a funcionalidades comunes</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('inmuebles.create') }}" class="btn idealista-btn">
                            <i class="fas fa-plus-circle me-2"></i>
                            Crear Nuevo Inmueble
                        </a>
                        <a href="{{ route('inmuebles.index') }}" class="btn idealista-btn">
                            <i class="fas fa-list me-2"></i>
                            Ver Todos los Inmuebles del CRM
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para crear contacto -->
    <div class="modal fade" id="createContactModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Crear Contacto en Idealista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="createContactForm">
                        <div class="mb-3">
                            <label class="form-label">Nombre *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Teléfono</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn idealista-btn" onclick="createContact()">Crear Contacto</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Área de resultados -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Resultados</h5>
                </div>
                <div class="card-body">
                    <div id="results-area">
                        <p class="text-muted text-center">Selecciona una opción para ver los resultados</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Cargar estadísticas al cargar la página
    document.addEventListener('DOMContentLoaded', function() {
        loadPublicationInfo();
    });

    function loadProperties(state = null) {
        const url = state
            ? `{{ route('inmuebles.idealista-list') }}?state=${state}`
            : '{{ route('inmuebles.idealista-list') }}';

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayProperties(data.data);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Error al cargar propiedades: ' + error.message);
            });
    }

    function loadContacts() {
        fetch('{{ route('inmuebles.idealista-contacts') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayContacts(data.data);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Error al cargar contactos: ' + error.message);
            });
    }

    function loadPublicationInfo() {
        fetch('{{ route('inmuebles.idealista-publication-info') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayPublicationInfo(data.data);
                } else {
                    showError(data.message);
                }
            })
            .catch(error => {
                showError('Error al cargar información: ' + error.message);
            });
    }

    function displayProperties(data) {
        const resultsArea = document.getElementById('results-area');
        let html = '<h6>Propiedades en Idealista</h6>';

        if (data.content && data.content.length > 0) {
            html += '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
            html += '<th>ID</th><th>Código</th><th>Referencia</th><th>Estado</th><th>Acciones</th>';
            html += '</tr></thead><tbody>';

            data.content.forEach(property => {
                html += `<tr>
                    <td>${property.propertyId || property.id || '-'}</td>
                    <td>${property.code || '-'}</td>
                    <td>${property.reference || '-'}</td>
                    <td><span class="badge bg-${property.state === 'active' ? 'success' : 'secondary'}">${property.state || 'N/A'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="viewProperty(${property.propertyId || property.id})">Ver</button>
                    </td>
                </tr>`;
            });

            html += '</tbody></table></div>';
        } else {
            html += '<p class="text-muted">No se encontraron propiedades</p>';
        }

        resultsArea.innerHTML = html;
    }

    function displayContacts(data) {
        const resultsArea = document.getElementById('results-area');
        let html = '<h6>Contactos de Idealista</h6>';

        if (data.content && data.content.length > 0) {
            html += '<div class="table-responsive"><table class="table table-striped"><thead><tr>';
            html += '<th>ID</th><th>Nombre</th><th>Email</th><th>Teléfono</th><th>Agente</th>';
            html += '</tr></thead><tbody>';

            data.content.forEach(contact => {
                html += `<tr>
                    <td>${contact.contactId || contact.id || '-'}</td>
                    <td>${contact.name || '-'}</td>
                    <td>${contact.email || '-'}</td>
                    <td>${contact.phone || '-'}</td>
                    <td>${contact.agent ? '<span class="badge bg-info">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                </tr>`;
            });

            html += '</tbody></table></div>';
        } else {
            html += '<p class="text-muted">No se encontraron contactos</p>';
        }

        resultsArea.innerHTML = html;
    }

    function displayPublicationInfo(data) {
        // Actualizar estadísticas
        if (data.publishedAds !== undefined) {
            document.getElementById('published-ads').textContent = data.publishedAds || 0;
        }
        if (data.totalAds !== undefined) {
            document.getElementById('total-properties').textContent = data.totalAds || 0;
        }

        const resultsArea = document.getElementById('results-area');
        let html = '<h6>Información de Publicación</h6>';
        html += '<div class="row">';
        html += `<div class="col-md-6"><strong>Cuenta Activa:</strong> ${data.accountActive ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>'}</div>`;
        html += `<div class="col-md-6"><strong>Anuncios Publicados:</strong> ${data.publishedAds || 0}</div>`;
        html += `<div class="col-md-6"><strong>Total de Anuncios:</strong> ${data.totalAds || 0}</div>`;
        html += `<div class="col-md-6"><strong>Anuncios Disponibles:</strong> ${(data.totalAds || 0) - (data.publishedAds || 0)}</div>`;
        html += '</div>';

        resultsArea.innerHTML = html;
    }

    function showCreateContactModal() {
        const modal = new bootstrap.Modal(document.getElementById('createContactModal'));
        modal.show();
    }

    function createContact() {
        const form = document.getElementById('createContactForm');
        const formData = new FormData(form);

        fetch('{{ route('inmuebles.idealista-contacts-create') }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Contacto creado correctamente');
                bootstrap.Modal.getInstance(document.getElementById('createContactModal')).hide();
                form.reset();
                loadContacts();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error al crear contacto: ' + error.message);
        });
    }


    function viewProperty(propertyId) {
        window.location.href = `/admin/inmuebles?idealista_id=${propertyId}`;
    }

    function showError(message) {
        const resultsArea = document.getElementById('results-area');
        resultsArea.innerHTML = `<div class="alert alert-danger">${message}</div>`;
    }
</script>
@endsection

