<div class="container-fluid">
    <style>
        .swiper {
            width: 600px;
            height: 300px;
        }
    </style>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <div class="row justify-content-center">
            @mobile
            <div class="col">
                <div class="accordion" id="accordionFiltro" style="width: 100%;">
                @elsemobile
                <div class="col-2">
                    <div class="accordion" id="accordionFiltro" style="width: 20rem;">
                    @endmobile
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingFiltro">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#collapseFiltro" aria-expanded="false" aria-controls="collapseFiltro">
                                <h5> Filtros de búsqueda </h5>
                            </button>
                        </h2>
                        <div id="collapseFiltro" class="accordion-collapse collapse" data-bs-parent="#accordionFiltro"
                            aria-labelledby="headingFiltro" data-bs-parent="#accordionFiltro">
                            <div class="accordion-body">
                                <div class="card mb-3" style="max-height: 40rem; overflow-y:scroll;">
                                    <div class="card-body">
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="titulo" class="col-sm-12 col-form-label">
                                                <strong>Título</strong></label>
                                            <div class="col-sm-12">
                                                <input type="text" wire:model.lazy="titulo" class="form-control"
                                                    name="titulo" id="titulo" placeholder="Título del inmueble">
                                                @error('titulo')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="ubicacion" class="col-sm-12 col-form-label">
                                                <strong>Descripción</strong></label>
                                            <div class="col-sm-12">
                                                <input type="text" wire:model.lazy="descripcion" class="form-control"
                                                    name="descripcion" id="descripcion"
                                                    placeholder="Descripción del inmueble">
                                                @error('descripcion')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="ubicacion" class="col-sm-12 col-form-label">
                                                <strong>Ubicación</strong></label>
                                            <div class="col-sm-12">
                                                <input type="text" wire:model.lazy="ubicacion" class="form-control"
                                                    name="ubicacion" id="ubicacion"
                                                    placeholder="Ubicación del inmueble">
                                                @error('ubicacion')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="habitaciones" class="col-sm-12 col-form-label">
                                                <strong>Habitaciones</strong></label>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="habitaciones_min"
                                                    class="form-control" name="habitaciones_min" id="habitaciones_min"
                                                    placeholder="Mínimo">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="habitaciones_max"
                                                    class="form-control" name="habitaciones_max" id="habitaciones_max"
                                                    placeholder="Máximo">
                                                @error('habitaciones')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="banos" class="col-sm-12 col-form-label">
                                                <strong>Baños</strong></label>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="banos_min" class="form-control"
                                                    name="banos_min" id="banos_min" placeholder="Mínimo">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="banos_max" class="form-control"
                                                    name="banos_max" id="banos_max" placeholder="Máximo">
                                            </div>
                                            @error('banos')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="m2" class="col-sm-12 col-form-label">
                                                <strong>Metros cuadrados</strong></label>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="m2_min" class="form-control"
                                                    name="m2_min" id="m2_min" placeholder="Mínimo">
                                            </div>
                                            <div class="col-sm-6">
                                                <input type="number" wire:model.lazy="m2_max" class="form-control"
                                                    name="m2_max" id="m2_max" placeholder="Máximo">
                                                @error('habitaciones')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="estado" class="col-sm-12 col-form-label">
                                                <strong>Estado</strong></label>
                                            <div x-data="" x-init="$('#select2-estado-create').select2();
                                            $('#select2-estado-create').on('change', function(e) {
                                                var data = $('#select2-estado-create').select2('val');
                                                @this.set('estado', data);
                                                console.log(data);
                                            });">
                                                <div class="col" wire:ignore>
                                                    <select class="form-control" id="select2-estado-create">
                                                        <option value="">-- Estado del inmueble --</option>
                                                        <option value="Obra nueva">Obra nueva</option>
                                                        <option value="Buen estado">Buen estado</option>
                                                        <option value="A reformar">A reformar</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="disponibilidad" class="col-sm-12 col-form-label">
                                                <strong>Disponibilidad</strong></label>
                                            <div x-data="" x-init="$('#select2-disponibilidad-create').select2();
                                            $('#select2-disponibilidad-create').on('change', function(e) {
                                                var data = $('#select2-disponibilidad-create').select2('val');
                                                @this.set('disponibilidad', data);
                                                console.log(data);
                                            });">
                                                <div class="col" wire:ignore>
                                                    <select class="form-control" id="select2-disponibilidad-create">
                                                        <option value="">-- Disponibilidad del inmueble --
                                                        </option>
                                                        <option value="Alquiler">Inmueble en alquiler</option>
                                                        <option value="Venta">Inmueble en venta</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-3 row d-flex align-items-center">
                                            <label for="otras_caracteristicasArray" class="col-sm-10 col-form-label">
                                                <strong>Otras
                                                    características</strong></label>
                                            <div class="col-sm-11 border rounded-2"
                                                style="overflow-y:scroll; height:5rem; margin-left:11px;">
                                                @foreach ($caracteristicas as $caracteristica)
                                                    <div class="mb-1">
                                                        <input type="checkbox" value="{{ $caracteristica->id }}"
                                                            wire:model.lazy="otras_caracteristicasArray"
                                                            @if (in_array($caracteristica->id, $otras_caracteristicasArray)) checked @endif>
                                                        {{ $caracteristica->nombre }}
                                                    </div>
                                                @endforeach
                                                @error('otras_caracteristicas')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @mobile
            </div>
            <div class="row justify-content-center">
            @elsemobile
                <div class="col-1"> &nbsp; </div>
            @endmobile
            <div class="col">
                @mobile
                    <div class="card mb-3" style="width: 100%;">
                    @elsemobile
                        <div class="card mb-3" style="width: 60rem;">
                        @endmobile
                        <h5 class="card-header">
                            Lista de inmuebles
                        </h5>
                        <div class="accordion" id="accordionInmuebles">
                            @foreach ($inmuebles as $inmueble)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading{{ $inmueble->id }}">
                                        <button class="accordion-button collapsed" type="button"
                                            data-bs-toggle="collapse" data-bs-target="#collapse{{ $inmueble->id }}"
                                            aria-expanded="false" aria-controls="collapse{{ $inmueble->id }}"
                                            @if ($acordeon_activo != $inmueble->id) wire:click="setActiveInmueble({{ $inmueble->id }})" @else @endif>
                                            <h4>{{ $inmueble->titulo }}</h4>
                                        </button>
                                    </h2>
                                    <div id="collapse{{ $inmueble->id }}" class="accordion-collapse collapse"
                                        data-bs-parent="#accordionInmuebles"
                                        aria-labelledby="heading{{ $inmueble->id }}">
                                        <div class="accordion-body">
                                            <div class="accordion" id="accordionSub{{ $inmueble->id }}">
                                                @if ($acordeon_activo == $inmueble->id)
                                                    <div class="accordion-item"
                                                        wire:key="inmueble-{{ $inmueble->id }}">
                                                        <h2 class="accordion-header"
                                                            id="subHeading1{{ $inmueble->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse1{{ $inmueble->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse1{{ $inmueble->id }}">
                                                                Visualizar información
                                                            </button>
                                                        </h2>
                                                        <div id="subCollapse1{{ $inmueble->id }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionSub{{ $inmueble->id }}"
                                                            aria-labelledby="subHeading1{{ $inmueble->id }}"
                                                            x-data="" x-init="const swiper = new Swiper('.swiper', {
                                                                // Optional parameters
                                                                direction: 'vertical',
                                                                loop: true,

                                                                // If we need pagination
                                                                pagination: {
                                                                    el: '.swiper-pagination',
                                                                },

                                                                // Navigation arrows
                                                                navigation: {
                                                                    nextEl: '.swiper-button-next',
                                                                    prevEl: '.swiper-button-prev',
                                                                },

                                                                // And if we need scrollbar
                                                                scrollbar: {
                                                                    el: '.swiper-scrollbar',
                                                                },
                                                            });">
                                                            <div class="accordion-body">
                                                                <div class="swiper">
                                                                    <!-- Additional required wrapper -->
                                                                    <div class="swiper-wrapper">
                                                                        @foreach (json_decode($inmueble->galeria, true) as $imagen)
                                                                            <div class="swiper-slide"><img
                                                                                    class="card-img-top"
                                                                                    src="{{ $imagen }}"
                                                                                    alt="Foto del inmueble">
                                                                            </div>
                                                                        @endforeach
                                                                    </div>
                                                                    <!-- If we need pagination -->
                                                                    <div class="swiper-pagination"></div>

                                                                    <!-- If we need navigation buttons -->
                                                                    <div class="swiper-button-prev"></div>
                                                                    <div class="swiper-button-next"></div>

                                                                    <!-- If we need scrollbar -->
                                                                    <div class="swiper-scrollbar"></div>
                                                                </div>

                                                                <div class="card-body">
                                                                    <h4 class="card-title">{{ $inmueble->titulo }}
                                                                    </h4>
                                                                    <p class="card-text">{{ $inmueble->descripcion }}
                                                                    </p>
                                                                </div>
                                                                <hr />
                                                                <ul class="list-group list-group-flush border">
                                                                    <li class="list-group-item">{{ $inmueble->m2 }} m2
                                                                    </li>
                                                                    <li class="list-group-item">
                                                                        {{ $inmueble->habitaciones }}
                                                                        habitaciones
                                                                    </li>
                                                                    <li class="list-group-item">{{ $inmueble->banos }}
                                                                        baños</li>

                                                                    @foreach (json_decode($inmueble->otras_caracteristicas, true) as $caracteristica)
                                                                        <li class="list-group-item">
                                                                            {{ $caracteristicas->where('id', $caracteristica)->first()->nombre }}
                                                                        </li>
                                                                    @endforeach
                                                                </ul>
                                                                <hr />
                                                                <button type="button"
                                                                    @if (
                                                                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                                            (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-primary boton-producto"
                                                        onclick="Livewire.emit('seleccionarProducto', {{ $inmueble->id }});"
                                                        @else                                         class="btn btn-secondary boton-producto" disabled @endif>Ver/Editar</button>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="subHeading2{{ $inmueble->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse2{{ $inmueble->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse2{{ $inmueble->id }}">
                                                                Seleccionar y enviar imágenes
                                                            </button>
                                                        </h2>
                                                        <div id="subCollapse2{{ $inmueble->id }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionSub{{ $inmueble->id }}"
                                                            aria-labelledby="subHeading2{{ $inmueble->id }}">
                                                            <div class="accordion-body">
                                                                <div class="row">
                                                                    <h5> Imágenes del inmueble </h5>
                                                                    @foreach (json_decode($inmueble->galeria, true) as $imagen)
                                                                        <div class="col-4">
                                                                            <div class="card">
                                                                                <img src="{{ $imagen }}"
                                                                                    class="card-img-top"
                                                                                    alt="Imagen del inmueble">
                                                                                <div class="card-body">
                                                                                    <input type="checkbox"
                                                                                        class="form-check-input"
                                                                                        id="check-{{ $loop->index }}">
                                                                                    <label class="form-check-label"
                                                                                        for="check-{{ $loop->index }}">Seleccionar
                                                                                        imagen</label>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endforeach
                                                                </div>
                                                                <hr />

                                                                <h5>Seleccionar cliente</h5>
                                                                <div class="mb-3 row d-flex align-items-center">
                                                                    <div x-data=""
                                                                        x-init="$('#select2-cliente-{{ $inmueble->id }}').select2();
                                                                        $('#select2-cliente-{{ $inmueble->id }}').on('change', function(e) {
                                                                            var data = $('#select2-cliente-{{ $inmueble->id }}').select2('val');
                                                                            @this.set('cliente_correo', data);
                                                                            console.log(data);
                                                                        });" wire:ignore>
                                                                        <select class="form-control"
                                                                            id="select2-cliente-{{ $inmueble->id }}">
                                                                            @foreach ($clientes as $cliente)
                                                                                <option value="{{ $cliente->id }}">
                                                                                    {{ $cliente->nombre_completo }}
                                                                                </option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <hr />

                                                                <button class="btn btn-primary">Enviar
                                                                    imágenes</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="subHeading3{{ $inmueble->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse3{{ $inmueble->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse3{{ $inmueble->id }}">
                                                                Hojas de visita
                                                            </button>
                                                        </h2>
                                                        <div id="subCollapse3{{ $inmueble->id }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionSub{{ $inmueble->id }}"
                                                            aria-labelledby="subHeading3{{ $inmueble->id }}">
                                                            <div class="accordion-body">
                                                                <ul class="nav nav-tabs" id="myTab"
                                                                    role="tablist">
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link active" id="home-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#home" type="button"
                                                                            role="tab" aria-controls="home"
                                                                            aria-selected="true">Crear
                                                                            nueva hoja de visita</button>
                                                                    </li>
                                                                    <li class="nav-item" role="presentation">
                                                                        <button class="nav-link" id="profile-tab"
                                                                            data-bs-toggle="tab"
                                                                            data-bs-target="#profile" type="button"
                                                                            role="tab" aria-controls="profile"
                                                                            aria-selected="true">Ver
                                                                            hojas ya creadas</button>
                                                                    </li>
                                                                </ul>
                                                                <div class="tab-content" id="myTabContent">
                                                                    <div class="tab-pane fade show active"
                                                                        id="home" role="tabpanel"
                                                                        aria-labelledby="home-tab">
                                                                        @livewire('inmuebles.visita-create', ['inmueble_id' => $inmueble->id], key('inmueble-' . $inmueble->id))

                                                                    </div>
                                                                    <div class="tab-pane fade" id="profile"
                                                                        role="tabpanel" aria-labelledby="profile-tab">
                                                                        @livewire('inmuebles.visita-index', ['inmueble_id' => $inmueble->id], key('inmueble-' . $inmueble->id))
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="subHeading4{{ $inmueble->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse4{{ $inmueble->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse4{{ $inmueble->id }}">
                                                                Documentos
                                                            </button>
                                                        </h2>
                                                        <div id="subCollapse4{{ $inmueble->id }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionSub{{ $inmueble->id }}"
                                                            aria-labelledby="subHeading4{{ $inmueble->id }}">
                                                            <div class="accordion-body">
                                                                @livewire('inmuebles.documentos-create', ['inmueble_id' => $inmueble->id], key($inmueble->id))
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="accordion-item">
                                                        <h2 class="accordion-header"
                                                            id="subHeading5{{ $inmueble->id }}">
                                                            <button class="accordion-button collapsed" type="button"
                                                                data-bs-toggle="collapse"
                                                                data-bs-target="#subCollapse5{{ $inmueble->id }}"
                                                                aria-expanded="false"
                                                                aria-controls="subCollapse5{{ $inmueble->id }}">
                                                                Contrato de arras
                                                            </button>
                                                        </h2>
                                                        <div id="subCollapse5{{ $inmueble->id }}"
                                                            class="accordion-collapse collapse"
                                                            data-bs-parent="#accordionSub{{ $inmueble->id }}"
                                                            aria-labelledby="subHeading5{{ $inmueble->id }}">
                                                            <div class="accordion-body">
                                                                @livewire('inmuebles.contrato-create', ['inmueble_id' => $inmueble->id], key($inmueble->id))
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <br>
                    {{ $inmuebles->links() }}
                </div>
            </div>
        </div>
