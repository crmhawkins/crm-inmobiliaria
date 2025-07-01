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
        <!-- Botón de importación -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Importar viviendas desde JSON</h5>
                        <p class="card-text">Importa las viviendas del archivo JSON y envíalas automáticamente a la API de
                            Fotocasa.</p>
                        <button type="button" class="btn btn-success" onclick="importProperties()">
                            <i class="bi bi-upload"></i> Importar viviendas
                        </button>
                        <div id="import-status" class="mt-3" style="display: none;">
                            <div class="alert alert-info">
                                <div class="d-flex align-items-center">
                                    <div class="spinner-border spinner-border-sm me-2" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                    <span id="import-message">Importando viviendas...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('inmuebles.index') }}">
            <div class="row">
                <!-- FILTROS -->
                <div class="col-md-3">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5>Filtros de búsqueda</h5>

                            <div class="mb-3">
                                <label>Ubicación</label>
                                <input type="text" class="form-control" name="ubicacion"
                                    value="{{ request('ubicacion') }}">
                            </div>

                            <div class="mb-3">
                                <label>Valor de referencia (€)</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control" name="valor_min" placeholder="Mín"
                                        value="{{ request('valor_min') }}">
                                    <input type="number" class="form-control" name="valor_max" placeholder="Máx"
                                        value="{{ request('valor_max') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Superficie (m²)</label>
                                <div class="d-flex gap-2">
                                    <input type="number" class="form-control" name="m2_min" placeholder="Mín"
                                        value="{{ request('m2_min') }}">
                                    <input type="number" class="form-control" name="m2_max" placeholder="Máx"
                                        value="{{ request('m2_max') }}">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label>Habitaciones</label><br>
                                @for ($i = 0; $i <= 4; $i++)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="habitaciones[]"
                                            value="{{ $i }}"
                                            {{ in_array($i, request('habitaciones', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $i == 4 ? '4 o más' : $i }}</label>
                                    </div>
                                @endfor
                            </div>

                            <div class="mb-3">
                                <label>Baños</label><br>
                                @for ($i = 1; $i <= 3; $i++)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="banos[]"
                                            value="{{ $i }}"
                                            {{ in_array($i, request('banos', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $i == 3 ? '3 o más' : $i }}</label>
                                    </div>
                                @endfor
                            </div>

                            <div class="mb-3">
                                <label>Estado</label><br>
                                @foreach (['Obra nueva', 'Buen estado', 'A reformar'] as $estado)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="estado[]"
                                            value="{{ $estado }}"
                                            {{ in_array($estado, request('estado', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $estado }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label>Disponibilidad</label><br>
                                @foreach (['Alquiler', 'Venta'] as $disponibilidad)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="disponibilidad[]"
                                            value="{{ $disponibilidad }}"
                                            {{ in_array($disponibilidad, request('disponibilidad', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $disponibilidad }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label>Tipo de vivienda</label><br>
                                @foreach ($tiposVivienda as $tipo)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="tipo_vivienda[]"
                                            value="{{ $tipo->id }}"
                                            {{ in_array($tipo->id, request('tipo_vivienda', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $tipo->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-3">
                                <label>Características</label><br>
                                @foreach ($caracteristicas as $car)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="caracteristicas[]"
                                            value="{{ $car->id }}"
                                            {{ in_array($car->id, request('caracteristicas', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label">{{ $car->nombre }}</label>
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Aplicar filtros</button>
                        </div>
                    </div>
                </div>

                <!-- RESULTADOS -->
                <div class="col-md-9">
                    <div class="row">
                        @forelse($inmuebles as $inmueble)
                            @php $galeria = json_decode($inmueble->galeria, true); @endphp
                            <div class="card mb-3 shadow-sm">
                                <div class="row g-0 align-items-center">
                                    <!-- Galería de imágenes -->
                                    <div class="col-md-4">
                                        <img src="{{ $galeria[1] ?? asset('images/default.jpg') }}"
                                            class="img w-100 rounded-start" alt="Foto inmueble">
                                    </div>

                                    <!-- Info -->
                                    <div class="col-md-8">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between">
                                                <h4 class="card-title mb-1">
                                                    {{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</h4>
                                                <span class="badge bg-light text-muted">Novedad</span>
                                            </div>
                                            <h6 class="text-muted mb-3">
                                                {{ $inmueble->tipoVivienda->nombre ?? 'Vivienda' }} en
                                                {{ $inmueble->ubicacion }}
                                            </h6>

                                            <div class="mb-2 d-flex flex-wrap gap-4">
                                                <span><i class="bi bi-door-open"></i> {{ $inmueble->habitaciones }}
                                                    habs.</span>
                                                <span><i class="bi bi-badge-wc"></i> {{ $inmueble->banos }} baños</span>
                                                <span><i class="bi bi-aspect-ratio"></i> {{ $inmueble->m2 }} m²</span>
                                                @if ($inmueble->has_terrace)
                                                    <span><i class="bi bi-house-door"></i> Terraza</span>
                                                @endif
                                                @if ($inmueble->has_balcony)
                                                    <span><i class="bi bi-columns-gap"></i> Balcón</span>
                                                @endif
                                                @if ($inmueble->otras_caracteristicas)
                                                    <span class="badge bg-light text-dark">
                                                        +{{ count(json_decode($inmueble->otras_caracteristicas, true) ?? []) }}
                                                        extras
                                                    </span>
                                                @endif
                                            </div>

                                            <p class="card-text text-muted small mb-3">
                                                {{ Str::limit($inmueble->descripcion, 180) }}</p>

                                            <div class="d-flex gap-2">
                                                <a href="#" class="btn btn-outline-primary btn-sm"><i
                                                        class="bi bi-envelope"></i> Contactar</a>
                                                <a href="#" class="btn btn-outline-secondary btn-sm"><i
                                                        class="bi bi-telephone"></i> Llamar</a>
                                                <a href="{{ route('inmuebles.show', $inmueble->id) }}"
                                                    class="btn btn-primary btn-sm ms-auto">Ver más</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>No se encontraron inmuebles.</p>
                        @endforelse

                    </div>

                    <div class="d-flex justify-content-center">
                        {{ $inmuebles->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function importProperties() {
            const importBtn = document.querySelector('button[onclick="importProperties()"]');
            const importStatus = document.getElementById('import-status');
            const importMessage = document.getElementById('import-message');

            // Deshabilitar botón y mostrar estado
            importBtn.disabled = true;
            importStatus.style.display = 'block';
            importMessage.textContent = 'Importando viviendas...';

            // Realizar petición AJAX
            fetch('{{ route('inmuebles.import-json') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        importStatus.innerHTML = `
                <div class="alert alert-success">
                    <h6>Importación completada</h6>
                    <p><strong>${data.imported}</strong> viviendas importadas correctamente.</p>
                    ${data.errors.length > 0 ? `<p><strong>${data.errors.length}</strong> errores encontrados.</p>` : ''}
                    <button type="button" class="btn btn-sm btn-outline-success" onclick="showImportDetails()">Ver detalles</button>
                </div>
            `;

                        // Guardar resultados para mostrar detalles
                        window.importResults = data;

                        // Recargar la página después de 3 segundos
                        setTimeout(() => {
                            window.location.reload();
                        }, 3000);
                    } else {
                        importStatus.innerHTML = `
                <div class="alert alert-danger">
                    <h6>Error en la importación</h6>
                    <p>${data.error}</p>
                </div>
            `;
                    }
                })
                .catch(error => {
                    importStatus.innerHTML = `
            <div class="alert alert-danger">
                <h6>Error de conexión</h6>
                <p>${error.message}</p>
            </div>
        `;
                })
                .finally(() => {
                    importBtn.disabled = false;
                });
        }

        function showImportDetails() {
            if (!window.importResults) return;

            const results = window.importResults;
            let detailsHtml = '<div class="mt-3"><h6>Detalles de la importación:</h6>';

            // Mostrar viviendas importadas
            if (results.results && results.results.length > 0) {
                detailsHtml += '<h6 class="text-success">Viviendas importadas:</h6><ul>';
                results.results.forEach(result => {
                    detailsHtml +=
                        `<li>${result.titulo} (ID: ${result.inmueble_id}) - Fotocasa: ${result.fotocasa_status}</li>`;
                });
                detailsHtml += '</ul>';
            }

            // Mostrar errores
            if (results.errors && results.errors.length > 0) {
                detailsHtml += '<h6 class="text-danger">Errores:</h6><ul>';
                results.errors.forEach(error => {
                    detailsHtml += `<li>${error.titulo}: ${error.error}</li>`;
                });
                detailsHtml += '</ul>';
            }

            detailsHtml += '</div>';

            document.getElementById('import-status').innerHTML += detailsHtml;
        }
    </script>
@endsection
