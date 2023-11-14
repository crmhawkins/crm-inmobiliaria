<div class="container-fluid" wire:key="index">
    <style>
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
    </style>

    <div class="row justify-content-center">
        <div class="col-2">
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Filtros de búsqueda</h5>
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
                    <div class="card mb-3" style="width: 60rem;">
                        <div class="row g-0">
                            <div class="col-md-4">
                                <img src="{{ json_decode($inmueble->galeria, true)[1] }}" width="100%" height="auto"
                                    class="rounded-start" alt="...">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body">
                                    <h4 class="card-title">{{ $inmueble->titulo }}</h4>
                                    <h5 class="card-title">{{ $inmueble->valor_referencia }}€</h5>
                                    <h6 class="card-title">{{ $inmueble->habitaciones }} hab. &nbsp;
                                        {{ $inmueble->m2 }}m<sup>2</sup></h6>
                                    <p class="card-text">{{ $inmueble->descripcion }}</p>
                                    <h2 class="accordion-header row" id="heading{{ $inmueble->id }}">
                                        <div class="col-6">
                                            <button class="accordion-button collapsed" type="button"
                                                data-bs-toggle="collapse" data-bs-target="#collapse{{ $inmueble->id }}"
                                                aria-expanded="false" aria-controls="collapse{{ $inmueble->id }}"
                                                onclick="Livewire.emit('seleccionarProducto2', {{ $inmueble->id }});">
                                                <h4>Ver detalles</h4>
                                            </button>
                                        </div>
                                        <div class="col-6">
                                            <button type="button"
                                                @if (
                                                    (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="accordion-button collapsed text-end"
                                                            onclick="Livewire.emit('seleccionarProducto', {{ $inmueble->id }});"
                                                            @else class="accordion-button collapsed text-end" disabled @endif>
                                                <h4>Editar</h4>
                                            </button>
                                        </div>
                                    </h2>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <br>
            @else
                No hay inmuebles que cumplan este criterio.
            @endif
        </div>
    </div>
</div>
