@extends('layouts.app')

@section('encabezado', 'Nuevo Cliente')
@section('subtitulo', 'Registrar nuevo cliente')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white d-flex align-items-center">
                        <a href="{{ route('clientes.index') }}" class="btn btn-outline-light btn-sm me-3">
                            <i class="bi bi-arrow-left"></i> Volver
                        </a>
                        <div>
                            <h4 class="mb-0">Nuevo Cliente</h4>
                            <small>Registrar nuevo cliente</small>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('clientes.store') }}" id="cliente-form">
                        @csrf
                        <div class="card-body">
                            <div class="row g-4 mb-4">
                                <div class="col-md-6">
                                    <label for="nombre_completo" class="form-label">Nombre completo *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                                        <input type="text" name="nombre_completo" id="nombre_completo"
                                            class="form-control" placeholder="Juan Pérez García" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="dni" class="form-label">DNI *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                        <input type="text" name="dni" id="dni" class="form-control"
                                            placeholder="12345678A" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="telefono" class="form-label">Teléfono *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                        <input type="text" name="telefono" id="telefono" class="form-control"
                                            placeholder="+34 600 123 456" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email *</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                        <input type="email" name="email" id="email" class="form-control"
                                            placeholder="juan.perez@email.com" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="direccion" class="form-label">Dirección</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-geo-alt"></i></span>
                                        <input type="text" name="direccion" id="direccion" class="form-control"
                                            placeholder="Calle Mayor, 123, Madrid">
                                    </div>
                                </div>
                            </div>
                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" name="inmobiliaria" id="inmobiliaria"
                                    value="1">
                                <label class="form-check-label" for="inmobiliaria">Cliente pertenece a ambas
                                    inmobiliarias</label>
                            </div>
                            <hr>
                            <h5 class="mb-3"><i class="bi bi-search me-2 text-primary"></i>Criterios de Búsqueda</h5>
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label for="ubicacion" class="form-label">Ubicación</label>
                                    <input type="text" name="ubicacion" id="ubicacion" class="form-control"
                                        placeholder="Madrid, Barcelona...">
                                </div>
                                <div class="col-md-2">
                                    <label for="habitaciones_min" class="form-label">Habitaciones (mín)</label>
                                    <input type="number" name="habitaciones_min" id="habitaciones_min" min="0"
                                        max="10" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="habitaciones_max" class="form-label">Habitaciones (máx)</label>
                                    <input type="number" name="habitaciones_max" id="habitaciones_max" min="0"
                                        max="10" class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="banos_min" class="form-label">Baños (mín)</label>
                                    <input type="number" name="banos_min" id="banos_min" min="0" max="10"
                                        class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="banos_max" class="form-label">Baños (máx)</label>
                                    <input type="number" name="banos_max" id="banos_max" min="0" max="10"
                                        class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="m2_min" class="form-label">M² (mín)</label>
                                    <input type="number" name="m2_min" id="m2_min" min="0"
                                        class="form-control">
                                </div>
                                <div class="col-md-2">
                                    <label for="m2_max" class="form-label">M² (máx)</label>
                                    <input type="number" name="m2_max" id="m2_max" min="0"
                                        class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="estado" class="form-label">Estado</label>
                                    <select name="estado" id="estado" class="form-select">
                                        <option value="">Seleccionar estado</option>
                                        <option value="Obra nueva">Obra nueva</option>
                                        <option value="Buen estado">Buen estado</option>
                                        <option value="A reformar">A reformar</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="disponibilidad" class="form-label">Disponibilidad</label>
                                    <select name="disponibilidad" id="disponibilidad" class="form-select">
                                        <option value="">Seleccionar disponibilidad</option>
                                        <option value="Alquiler">Alquiler</option>
                                        <option value="Venta">Venta</option>
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <h5 class="mb-3"><i class="bi bi-stars me-2 text-warning"></i>Características Deseadas</h5>
                            <div class="row g-2 mb-4">
                                @php
                                    $caracteristicas = [
                                        ['furnished', 'Amueblado'],
                                        ['has_elevator', 'Ascensor'],
                                        ['has_terrace', 'Terraza'],
                                        ['has_balcony', 'Balcón'],
                                        ['has_parking', 'Parking'],
                                        ['has_air_conditioning', 'Aire acondicionado'],
                                        ['has_heating', 'Calefacción'],
                                        ['has_security_door', 'Puerta de seguridad'],
                                        ['has_equipped_kitchen', 'Cocina equipada'],
                                        ['has_wardrobe', 'Armarios empotrados'],
                                        ['has_storage_room', 'Trastero'],
                                        ['pets_allowed', 'Mascotas permitidas'],
                                        ['has_private_garden', 'Jardín privado'],
                                        ['has_yard', 'Patio'],
                                        ['has_community_pool', 'Piscina comunitaria'],
                                        ['has_private_pool', 'Piscina privada'],
                                        ['has_jacuzzi', 'Jacuzzi'],
                                        ['has_sauna', 'Sauna'],
                                        ['has_gym', 'Gimnasio'],
                                        ['has_home_automation', 'Domótica'],
                                        ['has_home_appliances', 'Electrodomésticos'],
                                        ['has_oven', 'Horno'],
                                        ['has_washing_machine', 'Lavadora'],
                                        ['has_fridge', 'Frigorífico'],
                                        ['has_tv', 'TV'],
                                    ];
                                @endphp
                                @foreach ($caracteristicas as [$value, $label])
                                    <div class="col-6 col-md-4 col-lg-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="caracteristicas[]"
                                                value="{{ $value }}" id="carac_{{ $value }}">
                                            <label class="form-check-label"
                                                for="carac_{{ $value }}">{{ $label }}</label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0"><i class="bi bi-house-door me-2 text-info"></i>Inmuebles Relacionados
                                </h5>
                                <a href="{{ route('inmuebles.create') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i> Nuevo Inmueble
                                </a>
                            </div>
                            <div id="search-status" class="mb-3 text-secondary small d-flex align-items-center">
                                <span class="spinner-border spinner-border-sm me-2" role="status"></span> Buscando
                                inmuebles...
                            </div>
                            <div id="inmuebles-container" class="row g-4">
                                <!-- Cards de inmuebles se renderizan por JS -->
                            </div>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <button type="submit" class="btn btn-primary btn-lg px-5">
                                <i class="bi bi-save me-2"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Template para inmuebles -->
    <template id="inmueble-template">
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:180px;">
                    <i class="bi bi-house-door text-secondary" style="font-size:2.5rem;"></i>
                </div>
                <div class="card-body">
                    <h6 class="card-title mb-1" data-title></h6>
                    <div class="mb-2 text-muted small" data-location></div>
                    <div class="mb-2">
                        <span class="badge bg-primary me-1" data-price></span>
                        <span class="badge bg-secondary me-1" data-rooms></span>
                        <span class="badge bg-secondary me-1" data-bathrooms></span>
                        <span class="badge bg-secondary" data-m2></span>
                    </div>
                    <span class="badge bg-info text-dark mb-2" data-status></span>
                    <div>
                        <button class="btn btn-outline-primary btn-sm">Ver detalles</button>
                    </div>
                </div>
            </div>
        </div>
    </template>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Elementos del DOM
            const form = document.getElementById('cliente-form');
            const searchStatus = document.getElementById('search-status');
            const inmueblesContainer = document.getElementById('inmuebles-container');
            const template = document.getElementById('inmueble-template');

            // Campos de filtro
            const filterFields = [
                'ubicacion', 'habitaciones_min', 'habitaciones_max', 'banos_min', 'banos_max',
                'm2_min', 'm2_max', 'estado', 'disponibilidad'
            ];

            // Checkboxes de características
            const caracteristicasCheckboxes = document.querySelectorAll('input[name="caracteristicas[]"]');

            // Estado de búsqueda
            let searchTimeout;
            let isSearching = false;

            // Función para obtener los filtros actuales
            function getCurrentFilters() {
                const filters = {};

                // Mapear campos del formulario a los campos esperados por el backend
                const fieldMapping = {
                    'ubicacion': 'ubicacion',
                    'habitaciones_min': 'habitaciones_min',
                    'habitaciones_max': 'habitaciones_max',
                    'banos_min': 'banos_min',
                    'banos_max': 'banos_max',
                    'm2_min': 'm2_min',
                    'm2_max': 'm2_max',
                    'estado': 'estado',
                    'disponibilidad': 'disponibilidad'
                };

                // Campos de texto y números
                Object.keys(fieldMapping).forEach(field => {
                    const element = document.getElementById(field);
                    if (element && element.value.trim()) {
                        filters[fieldMapping[field]] = element.value.trim();
                    }
                });

                // Características seleccionadas
                const caracteristicas = [];
                caracteristicasCheckboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        caracteristicas.push(checkbox.value);
                    }
                });

                if (caracteristicas.length > 0) {
                    filters.caracteristicas = caracteristicas;
                }

                return filters;
            }

            // Función para mostrar estado de búsqueda
            function showSearchStatus(message, isSearching = false) {
                if (isSearching) {
                    searchStatus.innerHTML = `
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                ${message}
            `;
                    searchStatus.classList.remove('d-none');
                } else {
                    searchStatus.innerHTML = message;
                    searchStatus.classList.remove('d-none');
                }
            }

            // Función para ocultar estado de búsqueda
            function hideSearchStatus() {
                searchStatus.classList.add('d-none');
            }

            // Función para renderizar inmuebles
            function renderInmuebles(inmuebles) {
                inmueblesContainer.innerHTML = '';

                if (inmuebles.length === 0) {
                    inmueblesContainer.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                        <h5 class="text-muted mt-3">No se encontraron inmuebles</h5>
                        <p class="text-muted">Intenta ajustar los criterios de búsqueda</p>
                    </div>
                </div>
            `;
                    return;
                }

                inmuebles.forEach(inmueble => {
                    const clone = template.content.cloneNode(true);

                    // Llenar datos del inmueble
                    clone.querySelector('[data-title]').textContent = inmueble.titulo || 'Sin título';
                    clone.querySelector('[data-location]').textContent = inmueble.ubicacion ||
                        'Ubicación no especificada';
                    clone.querySelector('[data-price]').textContent =
                        `${inmueble.valor_referencia ? new Intl.NumberFormat('es-ES').format(inmueble.valor_referencia) + '€' : 'Precio no especificado'}`;
                    clone.querySelector('[data-rooms]').textContent = `${inmueble.habitaciones || 0} hab`;
                    clone.querySelector('[data-bathrooms]').textContent = `${inmueble.banos || 0} baños`;
                    clone.querySelector('[data-m2]').textContent = `${inmueble.m2 || 0} m²`;
                    clone.querySelector('[data-status]').textContent = inmueble.estado ||
                        'Estado no especificado';

                    // Cambiar color del badge de precio según disponibilidad
                    const priceBadge = clone.querySelector('[data-price]');
                    if (inmueble.disponibilidad === 'Venta') {
                        priceBadge.className = 'badge bg-success me-1';
                    } else if (inmueble.disponibilidad === 'Alquiler') {
                        priceBadge.className = 'badge bg-warning text-dark me-1';
                    }

                    inmueblesContainer.appendChild(clone);
                });
            }

            // Función para buscar inmuebles
            async function searchInmuebles() {
                if (isSearching) return;

                const filters = getCurrentFilters();
                console.log('Filtros enviados:', filters);

                // Si no hay filtros, mostrar mensaje inicial
                if (Object.keys(filters).length === 0) {
                    showSearchStatus('Ingresa criterios de búsqueda para ver inmuebles relacionados', false);
                    inmueblesContainer.innerHTML = '';
                    return;
                }

                // Mostrar contador de filtros activos
                const activeFilters = Object.keys(filters).length;
                showSearchStatus(`Buscando con ${activeFilters} filtro(s)...`, true);
                isSearching = true;

                try {
                    console.log('URL de la petición:', '{{ route('clientes.filtrarInmuebles') }}');
                    const response = await fetch('{{ route('clientes.filtrarInmuebles') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content')
                        },
                        body: JSON.stringify(filters)
                    });

                    console.log('Status de la respuesta:', response.status);
                    console.log('Headers de la respuesta:', response.headers);

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                        throw new Error(`Error en la búsqueda: ${response.status} ${response.statusText}`);
                    }

                    const data = await response.json();
                    console.log('Respuesta del servidor:', data);
                    renderInmuebles(data || []);

                    // Mostrar resultado de la búsqueda
                    if (data && data.length > 0) {
                        showSearchStatus(`Se encontraron ${data.length} inmueble(s)`, false);
                    } else {
                        showSearchStatus('No se encontraron inmuebles con estos criterios', false);
                    }

                } catch (error) {
                    console.error('Error buscando inmuebles:', error);
                    showSearchStatus('Error al buscar inmuebles. Intenta de nuevo.', false);
                    inmueblesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error al buscar inmuebles. Intenta de nuevo.
                    </div>
                </div>
            `;
                } finally {
                    isSearching = false;
                }
            }

            // Función para manejar cambios en filtros
            function handleFilterChange() {
                console.log('Evento de cambio detectado:', this.id || this.name);
                // Ejecutar búsqueda inmediatamente sin debounce
                searchInmuebles();
            }

            // Event listeners para campos de filtro
            filterFields.forEach(field => {
                const element = document.getElementById(field);
                if (element) {
                    console.log(`Añadiendo event listener para: ${field}`);
                    element.addEventListener('input', handleFilterChange);
                    element.addEventListener('change', handleFilterChange);
                } else {
                    console.warn(`Elemento no encontrado: ${field}`);
                }
            });

            // Event listeners para características
            caracteristicasCheckboxes.forEach(checkbox => {
                console.log(`Añadiendo event listener para checkbox: ${checkbox.value}`);
                checkbox.addEventListener('change', handleFilterChange);
            });

            // Debug: mostrar elementos encontrados
            console.log('Campos de filtro encontrados:', filterFields.filter(field => document.getElementById(
                field)));
            console.log('Checkboxes de características encontrados:', caracteristicasCheckboxes.length);

            // Función para limpiar filtros
            function clearFilters() {
                filterFields.forEach(field => {
                    const element = document.getElementById(field);
                    if (element) {
                        if (element.type === 'checkbox') {
                            element.checked = false;
                        } else {
                            element.value = '';
                        }
                    }
                });

                caracteristicasCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

                // Limpiar resultados
                inmueblesContainer.innerHTML = '';
                showSearchStatus('Ingresa criterios de búsqueda para ver inmuebles relacionados', false);
            }

            // Añadir botón para limpiar filtros
            const inmueblesSection = document.querySelector('h5');
            if (inmueblesSection && inmueblesSection.textContent.includes('Inmuebles Relacionados')) {
                const clearButton = document.createElement('button');
                clearButton.type = 'button';
                clearButton.className = 'btn btn-outline-secondary btn-sm ms-2';
                clearButton.innerHTML = '<i class="bi bi-x-circle me-1"></i>Limpiar filtros';
                clearButton.onclick = clearFilters;
                inmueblesSection.parentNode.insertBefore(clearButton, inmueblesSection.nextSibling);
            }

            // Búsqueda inicial
            setTimeout(() => {
                showSearchStatus('Ingresa criterios de búsqueda para ver inmuebles relacionados', false);

                // Test manual - añadir un botón de test
                const testButton = document.createElement('button');
                testButton.type = 'button';
                testButton.className = 'btn btn-warning btn-sm ms-2';
                testButton.innerHTML = '<i class="bi bi-bug me-1"></i>Test Búsqueda';
                testButton.onclick = () => {
                    console.log('Test manual de búsqueda');
                    document.getElementById('ubicacion').value = 'Madrid';
                    searchInmuebles();
                };

                const inmueblesSection = document.querySelector('h5');
                if (inmueblesSection && inmueblesSection.textContent.includes('Inmuebles Relacionados')) {
                    inmueblesSection.parentNode.insertBefore(testButton, inmueblesSection.nextSibling);
                }
            }, 100);
        });
    </script>
@endpush
