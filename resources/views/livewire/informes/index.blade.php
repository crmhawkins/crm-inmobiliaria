<div id="contenedorProductos">
    @mobile
        <div class="row justify-content-center">
            <div class="col" style="max-width:36rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Selector de informe</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="categoria-informe" class="col-form-label">
                                <h5>Categoría de informe:</h5>
                            </label>
                            <div x-data="" x-init="$('#m-select2-categoria-informe-create').select2();
                            $('#m-select2-categoria-informe-create').on('change', function(e) {
                                var data = $('#m-select2-categoria-informe-create').select2('val');
                                @this.set('categoria_informe', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-categoria-informe-create').select2();
                                $('#m-select2-categoria-informe-create').on('change', function(e) {
                                    var data = $('#m-select2-categoria-informe-create').select2('val');
                                    @this.set('categoria_informe', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-categoria-informe-create">
                                        <option value=""> TODOS </option>
                                        @foreach ($categoriasInforme as $categoriaInf)
                                            <option value={{ $categoriaInf->id }}>{{ $categoriaInf->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="tipo-informe" class="col-form-label">
                                <h5>Tipo de informe:</h5>
                            </label>
                            <div x-data="" wire:key="select2-{{ $categoria_informe }}"
                                x-init="$('#m-select2-tipo-informe-create').select2();
                                $('#m-select2-tipo-informe-create').on('change', function(e) {
                                    var data = $('#m-select2-tipo-informe-create').select2('val');
                                    @this.set('tipo_informe', data);
                                });
                                livewire.on('refreshTomSelect', () => {
                                    $('#m-select2-tipo-informe-create').select2();
                                    $('#m-select2-tipo-informe-create').on('change', function(e) {
                                        var data = $('#m-select2-tipo-informe-create').select2('val');
                                        @this.set('tipo_informe', data);
                                    });
                                });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-tipo-informe-create">
                                        <option value=""> TODOS </option>
                                        @if ($categoria_informe != '')
                                            @foreach ($tiposInforme->where('categoria_id', $categoria_informe) as $tipoInf)
                                                <option value={{ $tipoInf->id }}>{{ $tipoInf->nombre }}</option>
                                            @endforeach
                                        @else
                                            @foreach ($tiposInforme as $tipoInf)
                                                <option value={{ $tipoInf->id }}>{{ $tipoInf->nombre }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" style="max-width:36rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de albarán</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="servicio" class="col-form-label">
                                <h5>Servicio:</h5>
                            </label>
                            <div x-data="" x-init="$('#m-select2-servicio-create').select2();
                            $('#m-select2-servicio-create').on('change', function(e) {
                                var data = $('#m-select2-servicio-create').select2('val');
                                @this.set('servicio', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-servicio-create').select2();
                                $('#m-select2-servicio-create').on('change', function(e) {
                                    var data = $('#m-select2-servicio-create').select2('val');
                                    @this.set('servicio', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-servicio-create">
                                        <option value=""> TODOS </option>
                                        @foreach ($listAlmacenes as $listalmacen)
                                            <option value={{ $listalmacen->nombre }}>{{ $listalmacen->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label for="cliente" class="col-form-label">
                                    <h5>Inicio periodo:</h5>
                                </label>
                                <div class="col-sm-10" wire:ignore>
                                    <input type="datetime-local" wire:model="fecha_inicio" class="form-control"
                                        name="fecha_inicio" id="fecha_inicio">
                                </div>
                            </div>
                            <div class="col-6">
                                <label for="cliente" class="col-form-label">
                                    <h5>Fin periodo:</h5>
                                </label>
                                <div class="col-sm-10" wire:ignore>
                                    <input type="datetime-local" wire:model="fecha_fin" class="form-control"
                                        name="fecha_fin" id="fecha_fin">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col" style="max-width:36rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de albarán (2)</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="cliente" class="col-form-label">
                                <h5>Cliente:</h5>
                            </label>
                            <div x-data="" x-init="$('#m-select2-cliente-create').select2();
                            $('#m-select2-cliente-create').on('change', function(e) {
                                var data = $('#m-select2-cliente-create').select2('val');
                                @this.set('cliente', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-cliente-create').select2();
                                $('#m-select2-cliente-create').on('change', function(e) {
                                    var data = $('#m-select2-cliente-create').select2('val');
                                    @this.set('cliente', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-cliente-create">
                                        <option value=""> TODOS </option>
                                        @foreach ($clientes as $cliente)
                                            <option value={{ $cliente->id }}>{{ $cliente->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="servicio" class="col-form-label">
                                <h5>Matrícula:</h5>
                            </label>
                            <div x-data="" wire:key="select2-{{ $cliente }}" x-init="$('#m-select2-matricula-create').select2();
                            $('#m-select2-matricula-create').on('change', function(e) {
                                var data = $('#m-select2-matricula-create').select2('val');
                                @this.set('matricula', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-matricula-create').select2();
                                $('#m-select2-matricula-create').on('change', function(e) {
                                    var data = $('#m-select2-matricula-create').select2('val');
                                    @this.set('matricula', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-matricula-create">
                                        <option value=""> TODOS </option>
                                        @if ($cliente != '')
                                            @foreach ($presupuestos as $presupuesto)
                                                @if ($presupuesto->cliente_id == $cliente)
                                                    <option value={{ $presupuesto->matricula }}>
                                                        {{ $presupuesto->matricula }}</option>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach ($presupuestos as $presupuesto)
                                                <option value={{ $presupuesto->matricula }}>{{ $presupuesto->matricula }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col" style="max-width:36rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de artículo (1)</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;"
                        style="height:14.5rem !important; overflow-y: scroll;">
                        <label for="art_busc" class="col-form-label">
                            <h5>Elegir método de búsqueda:</h5>
                        </label> <br>
                        <input wire:model="art_busc" type="radio" value="0"> Código<br>
                        <input wire:model="art_busc" type="radio" value="1"> Descripción<br>
                        @if ($art_busc == 0)
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="art_codigo" class="col-form-label">
                                    <h5>Código:</h5>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model="art_codigo" class="form-control" name="art_codigo"
                                        id="art_codigo">
                                </div>

                            </div>
                        @else
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="art_desc" class="col-form-label">
                                    <h5>Descripción:</h5>
                                </label>
                                <div class="col-sm-10">
                                    <input type="text" wire:model="art_desc" class="form-control" name="art_desc"
                                        id="art_desc">
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col" style="max-width:36rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de artículo (2)</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="proveedor" class="col-form-label">
                                <h5>Proveedor:</h5>
                            </label>
                            <div x-data="" x-init="$('#m-select2-proveedor-create').select2();
                            $('#m-select2-proveedor-create').on('change', function(e) {
                                var data = $('#m-select2-proveedor-create').select2('val');
                                @this.set('proveedor', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-proveedor-create').select2();
                                $('#m-select2-proveedor-create').on('change', function(e) {
                                    var data = $('#m-select2-proveedor-create').select2('val');
                                    @this.set('proveedor', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-cliente-create">
                                        <option value=""> TODOS </option>
                                        @foreach ($proveedores as $proveedor)
                                            <option value={{ $proveedor->id }}>{{ $proveedor->nombre }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="fabricante" class="col-form-label">
                                <h5>Fabricante:</h5>
                            </label>
                            <div x-data="" x-init="$('#m-select2-fabricante-create').select2();
                            $('#m-select2-fabricante-create').on('change', function(e) {
                                var data = $('#m-select2-fabricante-create').select2('val');
                                @this.set('fabricante', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#m-select2-fabricante-create').select2();
                                $('#m-select2-fabricante-create').on('change', function(e) {
                                    var data = $('#m-select2-fabricante-create').select2('val');
                                    @this.set('fabricante', data);
                                });
                            });">
                                <div class="col-sm-10" wire:ignore>
                                    <select class="form-control" id="m-select2-fabricante-create">
                                        <option value=""> TODOS </option>
                                        @foreach ($fabricantes as $fabricante)
                                            <option value={{ $fabricante->id }}> {{ $fabricante->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Selección de varios fabricantes</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    @foreach ($fabricantes as $fabricante)
                        <input type="checkbox" value="{{ $fabricante->id }}" wire:model="fabricantesSeleccionados">
                        {{ $fabricante->nombre }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        @if (in_array(2, $tiposSeleccionados))
            <div class="col" style="max-width:48rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de neumáticos</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="row">
                            <div class="col-4">
                                <label for="busqueda_ancho" class="col-form-label">Ancho</label>
                                <input type="number" wire:model="busqueda_ancho" class="form-control"
                                    name="busqueda_ancho" wire:change="select_producto" id="busqueda_ancho"
                                    placeholder="Ancho">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_serie" class="col-form-label">Serie</label>
                                <input type="number" wire:model="busqueda_serie" class="form-control"
                                    name="busqueda_serie" wire:change="select_producto" id="busqueda_serie"
                                    placeholder="Serie">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_llanta" class="col-form-label">Llanta</label>
                                <input type="number" wire:model="busqueda_llanta" class="form-control"
                                    name="busqueda_llanta" wire:change="select_producto" id="busqueda_llanta"
                                    placeholder="Llanta">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <label for="busqueda_ic" class="col-form-label">I.C</label>
                                <input type="number" wire:model="indice_carga" class="form-control" name="busqueda_ic"
                                    id="busqueda_ic" placeholder="I.C.">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_cv" class="col-form-label">C.V</label>
                                <input type="number" wire:model="codigo_velocidad" class="form-control"
                                    name="busqueda_cv" id="busqueda_cv" placeholder="C.V.">
                            </div>
                            <div class="col-4">
                                <label for="ecotasa" class="col-form-label">Ecotasa</label>
                                <select wire:model="ecotasa" class="form-control" id="ecotasa" name="ecotasa"
                                    wire:change="precio_costo">
                                    <option selected value="">-- Selecciona una opción --</option>
                                    @foreach ($tasas as $tasa)
                                        <option value="{{ $tasa->id }}">{{ $tasa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <h5> Etiquetado europeo </h5>
                            <div class="col-4">
                                <label for="busqueda_res_rod" class="col-form-label">Resistencia
                                    rodaduras</label>
                                <select name="busqueda_res_rod" id="busqueda_res_rod" wire:model="busqueda_res_rod"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 65; $i <= 71; $i++)
                                        <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="busqueda_ag_moj" class="col-form-label">Agarre en
                                    mojado</label>
                                <select name="busqueda_ag_moj" id="busqueda_ag_moj" wire:model="busqueda_ag_moj"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 65; $i <= 71; $i++)
                                        <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="busqueda_em_ruido" class="col-form-label">Emisión de
                                    ruido</label>
                                <select name="busqueda_em_ruido" id="busqueda_em_ruido" wire:model="busqueda_em_ruido"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 67; $i <= 74; $i++)
                                        <option value="{{ $i }}">
                                            {{ $i . ' dB' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@elsemobile
    <div class="row justify-content-center">
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Selector de informe</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="categoria-informe" class="col-form-label">
                            <h5>Categoría de informe:</h5>
                        </label>
                        <div x-data="" x-init="$('#select2-categoria-informe-create').select2();
                        $('#select2-categoria-informe-create').on('change', function(e) {
                            var data = $('#select2-categoria-informe-create').select2('val');
                            @this.set('categoria_informe', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-categoria-informe-create').select2();
                            $('#select2-categoria-informe-create').on('change', function(e) {
                                var data = $('#select2-categoria-informe-create').select2('val');
                                @this.set('categoria_informe', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-categoria-informe-create" required>
                                    <option value=""> TODOS </option>
                                    @foreach ($categoriasInforme as $categoriaInf)
                                        <option value={{ $categoriaInf->id }}>{{ $categoriaInf->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="tipo-informe" class="col-form-label">
                            <h5>Tipo de informe:</h5>
                        </label>
                        <div x-data="" wire:key="select-2-{{ $categoria_informe }}"
                            x-init="$('#select2-tipo-informe-create').select2();
                            $('#select2-tipo-informe-create').on('change', function(e) {
                                var data = $('#select2-tipo-informe-create').select2('val');
                                @this.set('tipo_informe', data);
                            });
                            livewire.on('refreshTomSelect', () => {
                                $('#select2-tipo-informe-create').select2();
                                $('#select2-tipo-informe-create').on('change', function(e) {
                                    var data = $('#select2-tipo-informe-create').select2('val');
                                    @this.set('tipo_informe', data);
                                });
                            });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-tipo-informe-create" required>
                                    <option value=""> TODOS </option>
                                    @if ($categoria_informe != '')
                                        @foreach ($tiposInforme->where('categoria_id', $categoria_informe) as $tipoInf)
                                            <option value={{ $tipoInf->id }}>{{ $tipoInf->nombre }}</option>
                                        @endforeach
                                    @else
                                        @foreach ($tiposInforme as $tipoInf)
                                            <option value={{ $tipoInf->id }}>{{ $tipoInf->nombre }}</option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Filtros de albarán</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="servicio" class="col-form-label">
                            <h5>Servicio:</h5>
                        </label>
                        <div x-data="" x-init="$('#select2-servicio-create').select2();
                        $('#select2-servicio-create').on('change', function(e) {
                            var data = $('#select2-servicio-create').select2('val');
                            @this.set('servicio', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-servicio-create').select2();
                            $('#select2-servicio-create').on('change', function(e) {
                                var data = $('#select2-servicio-create').select2('val');
                                @this.set('servicio', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-servicio-create">
                                    <option value=""> TODOS </option>
                                    @foreach ($listAlmacenes as $listalmacen)
                                        <option value={{ $listalmacen->nombre }}>{{ $listalmacen->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="fecha_inicio" class="col-form-label">
                                <h5>Inicio periodo:</h5>
                            </label>
                            <div class="col-sm-10" wire:ignore>
                                <input type="datetime-local" wire:model="fecha_inicio" class="form-control"
                                    name="fecha_inicio" id="fecha_inicio" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label for="fecha_fin" class="col-form-label">
                                <h5>Fin periodo:</h5>
                            </label>
                            <div class="col-sm-10" wire:ignore>
                                <input type="datetime-local" wire:model="fecha_fin" class="form-control"
                                    name="fecha_fin" id="fecha_fin" required>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Filtros de albarán (2)</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="cliente" class="col-form-label">
                            <h5>Cliente:</h5>
                        </label>
                        <div x-data="" x-init="$('#select2-cliente-create').select2();
                        $('#select2-cliente-create').on('change', function(e) {
                            var data = $('#select2-cliente-create').select2('val');
                            @this.set('cliente', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-cliente-create').select2();
                            $('#select2-cliente-create').on('change', function(e) {
                                var data = $('#select2-cliente-create').select2('val');
                                @this.set('cliente', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-cliente-create">
                                    <option value=""> TODOS </option>
                                    @foreach ($clientes as $client)
                                        <option value={{ $client->id }}>{{ $client->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="servicio" class="col-form-label">
                            <h5>Matrícula:</h5>
                        </label>
                        <div x-data="" wire:key="select-2-{{ $cliente }}" x-init="$('#select2-matricula-create').select2();
                        $('#select2-matricula-create').on('change', function(e) {
                            var data = $('#select2-matricula-create').select2('val');
                            @this.set('matricula', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-matricula-create').select2();
                            $('#select2-matricula-create').on('change', function(e) {
                                var data = $('#select2-matricula-create').select2('val');
                                @this.set('matricula', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-matricula-create">
                                    <option value=""> TODOS </option>
                                    @if ($cliente != '')
                                        @foreach ($presupuestos as $presupuesto)
                                            @if ($presupuesto->cliente_id == $cliente)
                                                <option value={{ $presupuesto->matricula }}>
                                                    {{ $presupuesto->matricula }}</option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($presupuestos as $presupuesto)
                                            <option value={{ $presupuesto->matricula }}>{{ $presupuesto->matricula }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Filtros de artículo (1)</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;"
                    style="height:14.5rem !important; overflow-y: scroll;">
                    <label for="art_busc" class="col-form-label">
                        <h5>Elegir método de búsqueda:</h5>
                    </label> <br>
                    <input wire:model="art_busc" type="radio" value="0"> Código<br>
                    <input wire:model="art_busc" type="radio" value="1"> Descripción<br>
                    @if ($art_busc == 0)
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="art_codigo" class="col-form-label">
                                <h5>Código:</h5>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="art_codigo" class="form-control" name="art_codigo"
                                    id="art_codigo">
                            </div>

                        </div>
                    @else
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="art_desc" class="col-form-label">
                                <h5>Descripción:</h5>
                            </label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="art_desc" class="form-control" name="art_desc"
                                    id="art_desc">
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Filtros de artículo (2)</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="proveedor" class="col-form-label">
                            <h5>Proveedor:</h5>
                        </label>
                        <div x-data="" x-init="$('#select2-proveedor-create').select2();
                        $('#select2-proveedor-create').on('change', function(e) {
                            var data = $('#select2-proveedor-create').select2('val');
                            @this.set('proveedor', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-proveedor-create').select2();
                            $('#select2-proveedor-create').on('change', function(e) {
                                var data = $('#select2-proveedor-create').select2('val');
                                @this.set('proveedor', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-cliente-create">
                                    <option value=""> TODOS </option>
                                    @foreach ($proveedores as $proveedor)
                                        <option value={{ $proveedor->id }}>{{ $proveedor->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="fabricante" class="col-form-label">
                            <h5>Fabricante:</h5>
                        </label>
                        <div x-data="" x-init="$('#select2-fabricante-create').select2();
                        $('#select2-fabricante-create').on('change', function(e) {
                            var data = $('#select2-fabricante-create').select2('val');
                            @this.set('fabricante', data);
                        });
                        livewire.on('refreshTomSelect', () => {
                            $('#select2-fabricante-create').select2();
                            $('#select2-fabricante-create').on('change', function(e) {
                                var data = $('#select2-fabricante-create').select2('val');
                                @this.set('fabricante', data);
                            });
                        });">
                            <div class="col-sm-10" wire:ignore>
                                <select class="form-control" id="select2-fabricante-create">
                                    <option value=""> TODOS </option>
                                    @foreach ($fabricantes as $fabricante)
                                        <option value={{ $fabricante->id }}> {{ $fabricante->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="max-width:36rem;">
            <div class="card mb-3">
                <h5 class="card-header">Selección de varios fabricantes</h5>
                <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                    @foreach ($fabricantes as $fabricante)
                        <input type="checkbox" value="{{ $fabricante->id }}" wire:model="fabricantesSeleccionados">
                        {{ $fabricante->nombre }}<br>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row justify-content-center">
        @if (in_array(2, $tiposSeleccionados))
            <div class="col" style="max-width:48rem;">
                <div class="card mb-3">
                    <h5 class="card-header">Filtros de neumáticos</h5>
                    <div class="card-body" style="height:14.5rem !important; overflow-y: scroll;">
                        <div class="row">
                            <div class="col-4">
                                <label for="busqueda_ancho" class="col-form-label">Ancho</label>
                                <input type="number" wire:model="busqueda_ancho" class="form-control"
                                    name="busqueda_ancho" wire:change="select_producto" id="busqueda_ancho"
                                    placeholder="Ancho">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_serie" class="col-form-label">Serie</label>
                                <input type="number" wire:model="busqueda_serie" class="form-control"
                                    name="busqueda_serie" wire:change="select_producto" id="busqueda_serie"
                                    placeholder="Serie">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_llanta" class="col-form-label">Llanta</label>
                                <input type="number" wire:model="busqueda_llanta" class="form-control"
                                    name="busqueda_llanta" wire:change="select_producto" id="busqueda_llanta"
                                    placeholder="Llanta">
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-4">
                                <label for="busqueda_ic" class="col-form-label">I.C</label>
                                <input type="number" wire:model="indice_carga" class="form-control" name="busqueda_ic"
                                    id="busqueda_ic" placeholder="I.C.">
                            </div>
                            <div class="col-4">
                                <label for="busqueda_cv" class="col-form-label">C.V</label>
                                <input type="number" wire:model="codigo_velocidad" class="form-control"
                                    name="busqueda_cv" id="busqueda_cv" placeholder="C.V.">
                            </div>
                            <div class="col-4">
                                <label for="ecotasa" class="col-form-label">Ecotasa</label>
                                <select wire:model="ecotasa" class="form-control" id="ecotasa" name="ecotasa"
                                    wire:change="precio_costo">
                                    <option selected value="">-- Selecciona una opción --</option>
                                    @foreach ($tasas as $tasa)
                                        <option value="{{ $tasa->id }}">{{ $tasa->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <h5> Etiquetado europeo </h5>
                            <div class="col-4">
                                <label for="busqueda_res_rod" class="col-form-label">Resistencia
                                    rodaduras</label>
                                <select name="busqueda_res_rod" id="busqueda_res_rod" wire:model="busqueda_res_rod"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 65; $i <= 71; $i++)
                                        <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="busqueda_ag_moj" class="col-form-label">Agarre en
                                    mojado</label>
                                <select name="busqueda_ag_moj" id="busqueda_ag_moj" wire:model="busqueda_ag_moj"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 65; $i <= 71; $i++)
                                        <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-4">
                                <label for="busqueda_em_ruido" class="col-form-label">Emisión de
                                    ruido</label>
                                <select name="busqueda_em_ruido" id="busqueda_em_ruido" wire:model="busqueda_em_ruido"
                                    class="form-control">
                                    <option selected value="">--</option>
                                    @for ($i = 67; $i <= 74; $i++)
                                        <option value="{{ $i }}">
                                            {{ $i . ' dB' }}
                                        </option>
                                    @endfor
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="row justify-content-center">
        <div class="col-11">
            <div class="mb-3 row align-items-center">
                <button type="button" wire:click="generarInforme" class="btn btn-primary btn-xl">Generar
                    informe</button>
            </div>
        </div>
    </div>
@endmobile
</div>
