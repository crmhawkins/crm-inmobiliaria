{{-- @extends('layouts.app')


@section('head')
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.bootstrap5.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.css">


    @vite(['resources/sass/app.scss'])
    <style>
        div.dataTables_wrapper div.dataTables_filter label {
            display: flex;
        }

        div.dataTables_wrapper div.dataTables_filter input {
            width: 70%;
            margin-left: 10px;
            margin-top: -5px;
        }
    </style>
@endsection
@section('content')
@section('encabezado', 'Inmuebles')
@section('subtitulo', 'Consulta y creación de inmuebles')

@livewire('inmuebles.tabs-component')
@endsection --}}
@extends('layouts.app')

@section('encabezado', 'Inmuebles')
@section('subtitulo', 'Consulta de inmuebles')

@section('content')
    <div class="container-fluid">


        <div class="row">
            <!-- FILTROS -->
            <div class="col-12 col-md-3 mb-4 mb-md-0">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Filtros de búsqueda</h5>
                            <button type="button" class="btn btn-sm btn-outline-secondary d-md-none" onclick="toggleFilters()">
                                <i class="bi bi-chevron-down" id="filter-toggle-icon"></i>
                            </button>
                        </div>
                        <div id="filters-container">
                        <div class="mb-3">
                            <label>Ubicación</label>
                            <input type="text" class="form-control" id="filter-ubicacion" placeholder="Madrid, Barcelona...">
                        </div>

                        <div class="mb-3">
                            <label>Valor de referencia (€)</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control" id="filter-valor-min" placeholder="Mín">
                                <input type="number" class="form-control" id="filter-valor-max" placeholder="Máx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Superficie (m²)</label>
                            <div class="d-flex gap-2">
                                <input type="number" class="form-control" id="filter-m2-min" placeholder="Mín">
                                <input type="number" class="form-control" id="filter-m2-max" placeholder="Máx">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Habitaciones</label><br>
                            @for ($i = 0; $i <= 4; $i++)
                                <div class="form-check">
                                    <input class="form-check-input filter-habitaciones" type="checkbox"
                                        value="{{ $i }}">
                                    <label class="form-check-label">{{ $i == 4 ? '4 o más' : $i }}</label>
                                </div>
                            @endfor
                        </div>

                        <div class="mb-3">
                            <label>Baños</label><br>
                            @for ($i = 1; $i <= 3; $i++)
                                <div class="form-check">
                                    <input class="form-check-input filter-banos" type="checkbox"
                                        value="{{ $i }}">
                                    <label class="form-check-label">{{ $i == 3 ? '3 o más' : $i }}</label>
                                </div>
                            @endfor
                        </div>

                        <div class="mb-3">
                            <label>Estado</label><br>
                            @foreach (['Obra nueva', 'Buen estado', 'A reformar'] as $estado)
                                <div class="form-check">
                                    <input class="form-check-input filter-estado" type="checkbox"
                                        value="{{ $estado }}">
                                    <label class="form-check-label">{{ $estado }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label>Disponibilidad</label><br>
                            @foreach (['Alquiler', 'Venta'] as $disponibilidad)
                                <div class="form-check">
                                    <input class="form-check-input filter-disponibilidad" type="checkbox"
                                        value="{{ $disponibilidad }}">
                                    <label class="form-check-label">{{ $disponibilidad }}</label>
                                </div>
                            @endforeach
                        </div>

                        <div class="mb-3">
                            <label>Tipo de vivienda</label><br>
                            @foreach ($tiposVivienda as $tipo)
                                <div class="form-check">
                                    <input class="form-check-input filter-tipo-vivienda" type="checkbox"
                                        value="{{ $tipo->id }}">
                                    <label class="form-check-label">{{ $tipo->nombre }}</label>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-secondary w-100" onclick="clearFilters()">
                            <i class="bi bi-arrow-clockwise me-1"></i> Limpiar filtros
                        </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RESULTADOS -->
            <div class="col-12 col-md-9">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="mb-0">Resultados</h5>
                        <small class="text-muted" id="results-counter">{{ count($inmuebles) }} inmuebles
                            encontrados</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('inmuebles.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-circle me-1"></i> Nuevo Inmueble
                        </a>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="exportFilteredResults()">
                            <i class="bi bi-download me-1"></i> Exportar
                        </button>
                    </div>
                </div>

                <div class="row" id="inmuebles-container">
                    @forelse($inmuebles as $inmueble)
                        @php $galeria = json_decode($inmueble->galeria, true); @endphp
                        <div class="col-12 col-md-6 col-lg-4 mb-3 inmueble-card"
                            data-ubicacion="{{ strtolower($inmueble->ubicacion) }}"
                            data-valor="{{ $inmueble->valor_referencia }}" data-m2="{{ $inmueble->m2 }}"
                            data-habitaciones="{{ $inmueble->habitaciones }}" data-banos="{{ $inmueble->banos }}"
                            data-estado="{{ strtolower($inmueble->estado) }}"
                            data-disponibilidad="{{ strtolower($inmueble->disponibilidad) }}"
                            data-tipo-vivienda="{{ $inmueble->tipoVivienda->id ?? '' }}"
                            data-caracteristicas="{{ $inmueble->caracteristicas ? $inmueble->caracteristicas->pluck('id')->implode(',') : '' }}"
                            data-descripcion="{{ strtolower($inmueble->descripcion) }}">
                            <div class="card h-100 shadow-sm">
                                <!-- Imagen -->
                                <div class="position-relative">
                                    <img src="{{ $galeria[1] ?? asset('images/default.jpg') }}" class="card-img-top"
                                        style="height: 200px; object-fit: cover;" alt="Foto inmueble">
                                    <div class="position-absolute top-0 end-0 m-2">
                                        <span class="badge bg-light text-muted">Novedad</span>
                                    </div>
                                </div>

                                <!-- Contenido -->
                                <div class="card-body d-flex flex-column">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="card-title mb-0 fw-bold">
                                            {{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</h6>
                                    </div>

                                    <h6 class="text-muted small mb-2">
                                        {{ $inmueble->tipoVivienda->nombre ?? 'Vivienda' }} en
                                        {{ $inmueble->ubicacion }}
                                    </h6>

                                    <div class="mb-3 d-flex flex-wrap gap-2 small">
                                        <span class="badge bg-secondary"><i
                                                class="bi bi-door-open me-1"></i>{{ $inmueble->habitaciones }}
                                            habs.</span>
                                        <span class="badge bg-secondary"><i
                                                class="bi bi-badge-wc me-1"></i>{{ $inmueble->banos }} baños</span>
                                        <span class="badge bg-secondary"><i
                                                class="bi bi-aspect-ratio me-1"></i>{{ $inmueble->m2 }} m²</span>
                                        @if ($inmueble->has_terrace)
                                            <span class="badge bg-info"><i
                                                    class="bi bi-house-door me-1"></i>Terraza</span>
                                        @endif
                                        @if ($inmueble->has_balcony)
                                            <span class="badge bg-info"><i
                                                    class="bi bi-columns-gap me-1"></i>Balcón</span>
                                        @endif
                                    </div>

                                    <p class="card-text text-muted small mb-3 flex-grow-1">
                                        {{ Str::limit($inmueble->descripcion, 100) }}</p>

                                    <div class="d-flex gap-2 mt-auto">
                                        <a href="#" class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bi bi-envelope me-1"></i>Contactar
                                        </a>
                                        <a href="{{ route('inmuebles.show', $inmueble->id) }}"
                                            class="btn btn-primary btn-sm flex-fill">Ver más</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                                <h5 class="text-muted mt-3">No se encontraron inmuebles</h5>
                                <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <div class="d-flex justify-content-center" id="pagination-container">
                    {{ $inmuebles->links() }}
                </div>
            </div>
        </div>
    </div>

    <script>
        // Variables globales
        let allInmuebles = [];
        let filteredInmuebles = [];

        // Inicializar cuando el DOM esté listo
        document.addEventListener('DOMContentLoaded', function() {
            initializeFiltering();
            setupEventListeners();
        });

        function initializeFiltering() {
            // Obtener todos los inmuebles
            const cards = document.querySelectorAll('.inmueble-card');
            allInmuebles = Array.from(cards);
            filteredInmuebles = [...allInmuebles];

            updateCounter();
            showAllInmuebles();
        }

        function setupEventListeners() {
            // Campos de texto
            ['filter-ubicacion', 'filter-valor-min', 'filter-valor-max', 'filter-m2-min', 'filter-m2-max'].forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('input', debounce(filterInmuebles, 300));
                }
            });

            // Checkboxes
            ['filter-habitaciones', 'filter-banos', 'filter-estado', 'filter-disponibilidad', 'filter-tipo-vivienda',
                'filter-caracteristicas'
            ].forEach(className => {
                document.querySelectorAll('.' + className).forEach(checkbox => {
                    checkbox.addEventListener('change', filterInmuebles);
                });
            });
        }

        function filterInmuebles() {
            const filters = getFilters();
            const hasFilters = Object.keys(filters).length > 0;

            if (!hasFilters) {
                // Sin filtros: mostrar todos los inmuebles de la página actual
                showAllInmuebles();
                return;
            }

            // Buscar en toda la BD via AJAX
            searchInDatabase(filters);
        }

        function searchInDatabase(filters) {
            // Mostrar loading
            const container = document.getElementById('inmuebles-container');
            const counter = document.getElementById('results-counter');

            container.innerHTML = `
                <div class="col-12">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Buscando...</span>
                        </div>
                        <p class="mt-3 text-muted">Buscando inmuebles...</p>
                    </div>
                </div>
            `;

            counter.textContent = 'Buscando...';

            // Ocultar paginación durante búsqueda
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.style.display = 'none';

            // Hacer petición AJAX
            fetch('{{ route('inmuebles.search') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(filters)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displaySearchResults(data.inmuebles);
                    } else {
                        showErrorMessage('Error en la búsqueda: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorMessage('Error de conexión. Intenta de nuevo.');
                });
        }

        function displaySearchResults(inmuebles) {
            const container = document.getElementById('inmuebles-container');
            const counter = document.getElementById('results-counter');
            const pagination = document.getElementById('pagination-container');

            if (inmuebles.length === 0) {
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                            <h5 class="text-muted mt-3">No se encontraron inmuebles</h5>
                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </div>
                `;
                counter.textContent = '0 inmuebles encontrados';
            } else {
                // Generar HTML para los inmuebles encontrados
                let html = '';
                inmuebles.forEach(inmueble => {
                    html += generateInmuebleCard(inmueble);
                });
                container.innerHTML = html;
                counter.textContent = `${inmuebles.length} inmuebles encontrados`;
            }

            // Ocultar paginación para resultados de búsqueda
            if (pagination) pagination.style.display = 'none';
        }

        function generateInmuebleCard(inmueble) {
            const galeria = inmueble.galeria ? JSON.parse(inmueble.galeria) : {};
            const imagen = galeria[1] || '{{ asset('images/default.jpg') }}';

            return `
                <div class="col-12 col-md-6 col-lg-4 mb-3 inmueble-card">
                    <div class="card h-100 shadow-sm">
                        <!-- Imagen -->
                        <div class="position-relative">
                            <img src="${imagen}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="Foto inmueble">
                            <div class="position-absolute top-0 end-0 m-2">
                                <span class="badge bg-light text-muted">Novedad</span>
                            </div>
                        </div>

                        <!-- Contenido -->
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0 fw-bold">
                                    ${new Intl.NumberFormat('es-ES').format(inmueble.valor_referencia)} €</h6>
                            </div>

                            <h6 class="text-muted small mb-2">
                                ${inmueble.tipo_vivienda?.nombre || 'Vivienda'} en ${inmueble.ubicacion}
                            </h6>

                            <div class="mb-3 d-flex flex-wrap gap-2 small">
                                <span class="badge bg-secondary"><i class="bi bi-door-open me-1"></i>${inmueble.habitaciones} habs.</span>
                                <span class="badge bg-secondary"><i class="bi bi-badge-wc me-1"></i>${inmueble.banos} baños</span>
                                <span class="badge bg-secondary"><i class="bi bi-aspect-ratio me-1"></i>${inmueble.m2} m²</span>
                                ${inmueble.has_terrace ? '<span class="badge bg-info"><i class="bi bi-house-door me-1"></i>Terraza</span>' : ''}
                                ${inmueble.has_balcony ? '<span class="badge bg-info"><i class="bi bi-columns-gap me-1"></i>Balcón</span>' : ''}
                                ${inmueble.furnished ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Amueblado</span>' : ''}
                                ${inmueble.has_elevator ? '<span class="badge bg-warning"><i class="bi bi-arrow-up-circle me-1"></i>Ascensor</span>' : ''}
                                ${inmueble.has_parking ? '<span class="badge bg-info"><i class="bi bi-car-front me-1"></i>Parking</span>' : ''}
                            </div>

                            <p class="card-text text-muted small mb-3 flex-grow-1">
                                ${inmueble.descripcion ? inmueble.descripcion.substring(0, 100) + '...' : 'Sin descripción'}
                            </p>

                            <div class="d-flex gap-2 mt-auto">
                                <a href="#" class="btn btn-outline-primary btn-sm flex-fill">
                                    <i class="bi bi-envelope me-1"></i>Contactar
                                </a>
                                <a href="/admin/inmuebles/admin-show/${inmueble.id}" class="btn btn-primary btn-sm flex-fill">Ver más</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }

        function showErrorMessage(message) {
            const container = document.getElementById('inmuebles-container');
            const counter = document.getElementById('results-counter');

            container.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        ${message}
                    </div>
                </div>
            `;
            counter.textContent = 'Error en la búsqueda';
        }

        function getFilters() {
            const filters = {};

            // Ubicación
            const ubicacion = document.getElementById('filter-ubicacion').value.trim().toLowerCase();
            if (ubicacion) filters.ubicacion = ubicacion;

            // Valor
            const valorMin = document.getElementById('filter-valor-min').value;
            const valorMax = document.getElementById('filter-valor-max').value;
            if (valorMin) filters.valorMin = parseInt(valorMin);
            if (valorMax) filters.valorMax = parseInt(valorMax);

            // M²
            const m2Min = document.getElementById('filter-m2-min').value;
            const m2Max = document.getElementById('filter-m2-max').value;
            if (m2Min) filters.m2Min = parseInt(m2Min);
            if (m2Max) filters.m2Max = parseInt(m2Max);

            // Checkboxes
            ['habitaciones', 'banos', 'estado', 'disponibilidad', 'tipo-vivienda', 'caracteristicas'].forEach(type => {
                const checked = document.querySelectorAll(`.filter-${type}:checked`);
                if (checked.length > 0) {
                    filters[type] = Array.from(checked).map(cb => cb.value);
                }
            });

            return filters;
        }

        function matchesFilters(inmueble, filters) {
            // Ubicación
            if (filters.ubicacion) {
                const ubicacion = inmueble.dataset.ubicacion || '';
                if (!ubicacion.includes(filters.ubicacion)) return false;
            }

            // Valor
            if (filters.valorMin || filters.valorMax) {
                const valor = parseInt(inmueble.dataset.valor) || 0;
                if (filters.valorMin && valor < filters.valorMin) return false;
                if (filters.valorMax && valor > filters.valorMax) return false;
            }

            // M²
            if (filters.m2Min || filters.m2Max) {
                const m2 = parseInt(inmueble.dataset.m2) || 0;
                if (filters.m2Min && m2 < filters.m2Min) return false;
                if (filters.m2Max && m2 > filters.m2Max) return false;
            }

            // Habitaciones
            if (filters.habitaciones && filters.habitaciones.length > 0) {
                const habitaciones = parseInt(inmueble.dataset.habitaciones) || 0;
                if (!filters.habitaciones.includes(habitaciones.toString())) return false;
            }

            // Baños
            if (filters.banos && filters.banos.length > 0) {
                const banos = parseInt(inmueble.dataset.banos) || 0;
                if (!filters.banos.includes(banos.toString())) return false;
            }

            // Estado
            if (filters.estado && filters.estado.length > 0) {
                const estado = inmueble.dataset.estado || '';
                if (!filters.estado.some(e => estado.includes(e.toLowerCase()))) return false;
            }

            // Disponibilidad
            if (filters.disponibilidad && filters.disponibilidad.length > 0) {
                const disponibilidad = inmueble.dataset.disponibilidad || '';
                if (!filters.disponibilidad.some(d => disponibilidad.includes(d.toLowerCase()))) return false;
            }

            // Tipo de vivienda
            if (filters['tipo-vivienda'] && filters['tipo-vivienda'].length > 0) {
                const tipoVivienda = inmueble.dataset.tipoVivienda || '';
                if (!filters['tipo-vivienda'].includes(tipoVivienda)) return false;
            }

            // Características
            if (filters.caracteristicas && filters.caracteristicas.length > 0) {
                const caracteristicas = inmueble.dataset.caracteristicas || '';
                const inmuebleCaracteristicas = caracteristicas.split(',').filter(c => c);
                const hasMatch = filters.caracteristicas.some(c => inmuebleCaracteristicas.includes(c));
                if (!hasMatch) return false;
            }

            return true;
        }

        function showAllInmuebles() {
            // Mostrar todos los inmuebles
            allInmuebles.forEach(inmueble => {
                inmueble.style.display = 'block';
            });

            // Mostrar paginación
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.style.display = 'block';

            // Restaurar contador
            const counter = document.getElementById('results-counter');
            if (counter) counter.textContent = `${allInmuebles.length} inmuebles encontrados`;
        }

        function showFilteredResults() {
            // Ocultar todos primero
            allInmuebles.forEach(inmueble => {
                inmueble.style.display = 'none';
            });

            // Mostrar solo los filtrados
            filteredInmuebles.forEach(inmueble => {
                inmueble.style.display = 'block';
            });

            // Ocultar paginación
            const pagination = document.getElementById('pagination-container');
            if (pagination) pagination.style.display = 'none';

            // Mostrar mensaje si no hay resultados
            if (filteredInmuebles.length === 0) {
                const container = document.getElementById('inmuebles-container');
                container.innerHTML = `
                    <div class="col-12">
                        <div class="text-center py-5">
                            <i class="bi bi-search text-muted" style="font-size:3rem;"></i>
                            <h5 class="text-muted mt-3">No se encontraron inmuebles</h5>
                            <p class="text-muted">Intenta ajustar los filtros de búsqueda</p>
                        </div>
                    </div>
                `;
            }
        }

        function updateCounter() {
            const counter = document.getElementById('results-counter');
            if (!counter) return;

            const total = allInmuebles.length;
            const filtered = filteredInmuebles.length;

            if (filtered === total) {
                counter.textContent = `${total} inmuebles encontrados`;
            } else {
                counter.textContent = `${filtered} de ${total} inmuebles encontrados`;
            }
        }

        function clearFilters() {
            // Limpiar campos de texto
            ['filter-ubicacion', 'filter-valor-min', 'filter-valor-max', 'filter-m2-min', 'filter-m2-max'].forEach(id => {
                const element = document.getElementById(id);
                if (element) element.value = '';
            });

            // Desmarcar checkboxes
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.checked = false;
            });

            // Restaurar vista
            filteredInmuebles = [...allInmuebles];
            showAllInmuebles();
        }

        function exportFilteredResults() {
            // Implementar exportación de resultados filtrados
            alert('Función de exportación en desarrollo');
        }

        function debounce(func, wait) {
            let timeout;
            return function(...args) {
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(this, args), wait);
            };
        }

        // Toggle filters en móvil
        function toggleFilters() {
            const container = document.getElementById('filters-container');
            const icon = document.getElementById('filter-toggle-icon');
            if (container && icon) {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    icon.classList.remove('bi-chevron-down');
                    icon.classList.add('bi-chevron-up');
                } else {
                    container.style.display = 'none';
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            }
        }

        // Ocultar filtros en móvil por defecto
        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth < 768) {
                const container = document.getElementById('filters-container');
                if (container) {
                    container.style.display = 'none';
                }
            }
        });

        // Manejar resize
        window.addEventListener('resize', function() {
            const container = document.getElementById('filters-container');
            const icon = document.getElementById('filter-toggle-icon');
            if (window.innerWidth >= 768) {
                if (container) container.style.display = 'block';
            } else {
                if (container && container.style.display === 'none') {
                    if (icon) {
                        icon.classList.remove('bi-chevron-up');
                        icon.classList.add('bi-chevron-down');
                    }
                }
            }
        });
    </script>

    <style>
        @media (max-width: 767px) {
            .inmueble-card {
                margin-bottom: 15px;
            }

            .card {
                margin-bottom: 15px;
            }

            .btn-sm {
                min-height: 44px;
                padding: 10px 16px;
            }

            #filters-container {
                display: none;
            }
        }

        @media (hover: none) and (pointer: coarse) {
            .btn, button, a.btn {
                min-height: 44px;
                padding: 12px 20px;
            }

            input, select, textarea {
                font-size: 16px;
                min-height: 44px;
            }
        }
    </style>
@endsection
