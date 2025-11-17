<div class="container-fluid" wire:key="index">
    <style>
        .page-header-modern {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.2);
        }
        .page-header-modern h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .page-header-modern h5 i {
            font-size: 1.8rem;
        }
        .card-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .filters-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 20px;
            position: sticky;
            top: 20px;
        }
        .filters-card h5 {
            color: var(--corporate-green-dark);
            font-weight: 600;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--corporate-green-lightest);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filters-card h6 {
            color: var(--corporate-green-dark);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 10px;
            margin-top: 15px;
        }
        .filters-card label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9rem;
            cursor: pointer;
            padding: 5px 0;
        }
        .filters-card input[type="checkbox"] {
            margin-right: 8px;
            cursor: pointer;
        }
        .filters-card input[type="text"],
        .filters-card select {
            width: 100%;
            padding: 8px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .filters-card input[type="text"]:focus,
        .filters-card select:focus {
            border-color: var(--corporate-green);
            outline: none;
            box-shadow: 0 0 0 3px rgba(107, 142, 107, 0.1);
        }
        .property-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        .property-card img {
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        .property-card:hover img {
            transform: scale(1.05);
        }
        .property-card .card-body {
            padding: 25px;
        }
        .property-card h4 {
            color: var(--corporate-green-dark);
            font-weight: 600;
            margin-bottom: 10px;
        }
        .property-card h5 {
            color: var(--corporate-green);
            font-weight: 700;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        .property-card h6 {
            color: #666;
            font-weight: 500;
            margin-bottom: 15px;
        }
        .property-card .card-text {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .property-actions {
            display: flex;
            gap: 10px;
        }
        .property-actions .btn {
            flex: 1;
            padding: 10px 20px;
        }
        .swiper {
            width: 600px;
            height: 300px;
        }
        .accordion-button .collapsed {
            color: #a0a0a0;
        }
        .accordion-button {
            color: #333333;
        }
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        .no-results i {
            font-size: 4rem;
            color: var(--corporate-green-light);
            margin-bottom: 20px;
        }
    </style>

    <div class="row justify-content-center">
        <div class="col-2">
            <div class="filters-card">
                <h5>
                    <i class="fas fa-filter"></i>
                    Filtros de búsqueda
                </h5>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Ubicación </h6>
                        <input type="text" wire:model="ubicacion">
                    </div>
                    @if (is_array($opcionesPrecio) && !empty($opcionesPrecio))
                        <div class="mb-3 row d-flex align-items-center">
                            <h6> Valor de referencia </h6>

                            <div class="col-6">
                                <select wire:model="valor_min" class="w-100">
                                    <option value="1">Mínimo</option>
                                    @foreach ($opcionesPrecio as $opcion)
                                        <option value="{{ $opcion }}">{{ $opcion }} €</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select wire:model="valor_max" class="w-100">
                                    @foreach ($opcionesPrecio as $opcion)
                                        <option value="{{ $opcion }}">{{ $opcion }} €</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    @if (is_array($opcionesTamano) && !empty($opcionesTamano))
                        <div class="mb-3 row d-flex align-items-center">
                            <h6> Valor de referencia </h6>
                            <div class="col-6">
                                <select wire:model="m2_min" class="w-100">
                                    <option value="1">Mínimo</option>
                                    @foreach ($opcionesTamano as $opcion)
                                        <option value="{{ $opcion }}">{{ $opcion }} m<sup>2</sup></option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-6">
                                <select wire:model="m2_max" class="w-100">
                                    @foreach ($opcionesTamano as $opcion)
                                        <option value="{{ $opcion }}">{{ $opcion }} m<sup>2</sup></option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Habitaciones </h6>
                        <label>
                            <input type="checkbox" wire:model="habitacionesSeleccionadas" value=0>
                            0 habitaciones (estudios)
                        </label>
                        <label>
                            <input type="checkbox" wire:model="habitacionesSeleccionadas" value=1>
                            1
                        </label>
                        <label>
                            <input type="checkbox" wire:model="habitacionesSeleccionadas" value=2>
                            2
                        </label>
                        <label>
                            <input type="checkbox" wire:model="habitacionesSeleccionadas" value=3>
                            3
                        </label>
                        <label>
                            <input type="checkbox" wire:model="habitacionesSeleccionadas" value=4>
                            4 habitaciones o más
                        </label>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Baños </h6>
                        <label>
                            <input type="checkbox" wire:model="banosSeleccionados" value=1>
                            1
                        </label>
                        <label>
                            <input type="checkbox" wire:model="banosSeleccionados" value=2>
                            2
                        </label>
                        <label>
                            <input type="checkbox" wire:model="banosSeleccionados" value=3>
                            3 baños o más
                        </label>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Estado </h6>
                        <label>
                            <input type="checkbox" wire:model="estadoSeleccionados" value="Obra nueva">
                            Obra nueva
                        </label>
                        <label>
                            <input type="checkbox" wire:model="estadoSeleccionados" value="Buen estado">
                            Buen estado
                        </label>
                        <label>
                            <input type="checkbox" wire:model="estadoSeleccionados" value="A reformar">
                            A reformar
                        </label>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Disponibilidad </h6>
                        <label>
                            <input type="checkbox" wire:model="disponibilidad_seleccionados" value="Alquiler">
                            En alquiler
                        </label>
                        <label>
                            <input type="checkbox" wire:model="disponibilidad_seleccionados" value="Venta">
                            A la venta
                        </label>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Tipo de vivienda </h6>
                        @foreach ($tipos_vivienda as $tipo)
                            <label>
                                <input type="checkbox" value="{{ $tipo->id }}" wire:model.lazy="tipos_seleccionados"
                                    @if (in_array($tipo->id, $tipos_seleccionados)) checked @endif>
                                {{ $tipo->nombre }}
                            </label>
                        @endforeach
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <h6> Otras
                            características </h6>
                        @foreach ($caracteristicas as $caracteristica)
                            <label>
                                <input type="checkbox" value="{{ $caracteristica->id }}"
                                    wire:model="otras_caracteristicasArray"
                                    @if (in_array($caracteristica->id, $otras_caracteristicasArray)) checked @endif>
                                {{ $caracteristica->nombre }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-1"> &nbsp; </div>
        <div class="col">
            @if ($inmuebles->count() > 0)
                @foreach ($inmuebles as $inmueble)
                    <div class="card property-card">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="{{ json_decode($inmueble->galeria, true)[1] }}" width="100%" height="100%"
                                    style="object-fit: cover; min-height: 250px;" alt="{{ $inmueble->titulo }}">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $inmueble->titulo }}</h4>
                                    <h5 class="card-title">{{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</h5>
                                    <h6 class="card-title">
                                        <i class="fas fa-bed me-2"></i>{{ $inmueble->habitaciones }} hab. &nbsp;
                                        <i class="fas fa-ruler-combined me-2"></i>{{ $inmueble->m2 }}m<sup>2</sup>
                                    </h6>
                                    <p class="card-text">{{ Str::limit($inmueble->descripcion, 150) }}</p>
                                    <div class="property-actions">
                                        <button class="btn btn-outline-primary" type="button"
                                            onclick="Livewire.emit('seleccionarProducto2', {{ $inmueble->id }});">
                                            <i class="fas fa-eye me-1"></i>Ver detalles
                                        </button>
                                        <button type="button"
                                            @if (
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                    (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                    (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                    (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                                            class="btn btn-primary"
                                            onclick="Livewire.emit('seleccionarProducto', {{ $inmueble->id }});"
                                            @else
                                            class="btn btn-secondary" disabled
                                            @endif>
                                            <i class="fas fa-edit me-1"></i>Editar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-results">
                    <i class="fas fa-search"></i>
                    <h4>No hay inmuebles que cumplan este criterio</h4>
                    <p>Intenta ajustar los filtros de búsqueda</p>
                </div>
            @endif
        </div>
    </div>
</div>
