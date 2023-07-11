<div id="contenedorProductos">
    <div class="card mb-3">
        <h5 class="card-header">Buscador</h5>
        <div class="card-body">
            <h5>Selecciona la categoría principal</h5>
            <div class="col-sm-10" wire:ignore.self>
                <select name="tipo_producto" id="tipo_producto" wire:model="tipo_producto" wire:change="select_producto"
                    class="form-control">
                    <option selected value="">Todos los productos</option>
                    @foreach ($tipos_producto as $tipos)
                        <option value="{{ $tipos->id }}">{{ $tipos->tipo_producto }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-10" wire:ignore.self>
                <h5> Buscador por código de artículo </h5>
                <input type="text" wire:model="busqueda_articulo" class="form-control" name="busqueda_articulo"
                    wire:change="select_producto" id="busqueda_articulo"
                    placeholder="Código de artículo (Ej; FLKN055516D)">
            </div>



            <div class="col-sm-10" wire:ignore.self>
                <h5> Buscador por descripción </h5>
                <input type="text" wire:model="busqueda_descripcion" class="form-control" name="busqueda_descripcion"
                    wire:change="select_producto" id="busqueda_descripcion"
                    placeholder="Descripción (Ej; 205/55/16 TL ZIEX ZE310 ECORUN 91V)">
            </div>

            @if ($tipo_producto != 2 && $tipo_producto != '')
                <div class="col-sm-10" wire:ignore.self>
                    <h5> Buscador por categoría </h5>
                    <select name="busqueda_categoria" id="busqueda_categoria" wire:model="busqueda_categoria"
                        class="form-control" wire:change="select_producto">
                        @foreach ($categorias as $categoria)
                            <option value="{{ $categoria->id }}">{{ $categoria->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            @elseif($tipo_producto == 2)
            @endif

            <div class="col-sm-10" wire:ignore.self>
                <h5> Selección de almacén (para existencias) </h5>
                <select name="busqueda_almacen" id="busqueda_almacen" wire:model="busqueda_almacen" class="form-control"
                    wire:change="select_producto">
                    @foreach ($listAlmacenes as $almacene => $nombreA)
                        <option value="{{ $almacene }}">{{ $nombreA->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <br>
        </div>
    </div>

    @if ($tipo_producto == 2)
        <div class="card mb-3">
            <h5 class="card-header">Buscador de neumáticos</h5>
            <div class="card-body">
                <div class="row">
                    <div class="col-4">
                        <label for="busqueda_ancho" class="col-sm-2 col-form-label">Ancho</label>
                        <input type="number" wire:model="busqueda_ancho" class="form-control" name="busqueda_ancho"
                            wire:change="select_producto" id="busqueda_ancho" placeholder="Ancho">
                    </div>
                    <div class="col-4">
                        <label for="busqueda_serie" class="col-sm-2 col-form-label">Serie</label>
                        <input type="number" wire:model="busqueda_serie" class="form-control" name="busqueda_serie"
                            wire:change="select_producto" id="busqueda_serie" placeholder="Serie">
                    </div>
                    <div class="col-4">
                        <label for="busqueda_llanta" class="col-sm-2 col-form-label">Llanta</label>
                        <input type="number" wire:model="busqueda_llanta" class="form-control" name="busqueda_llanta"
                            wire:change="select_producto" id="busqueda_llanta" placeholder="Llanta">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-4">
                        <label for="busqueda_ic" class="col-sm-2 col-form-label">I.C</label>
                        <input type="number" wire:model="indice_carga" class="form-control" name="busqueda_ic"
                            id="busqueda_ic" placeholder="I.C.">
                    </div>
                    <div class="col-4">
                        <label for="busqueda_cv" class="col-sm-2 col-form-label">C.V</label>
                        <input type="number" wire:model="codigo_velocidad" class="form-control" name="busqueda_cv"
                            id="busqueda_cv" placeholder="C.V.">
                    </div>
                    <div class="col-4">
                        <label for="ecotasa" class="col-sm-2 col-form-label">Ecotasa</label>
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
                        <label for="busqueda_res_rod" class="col-sm-2 col-form-label">Resistencia rodaduras</label>
                        <select name="busqueda_res_rod" id="busqueda_res_rod" wire:model="busqueda_res_rod"
                            class="form-control">
                            <option selected value="">--</option>
                            @for ($i = 65; $i <= 71; $i++)
                                <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="busqueda_ag_moj" class="col-sm-2 col-form-label">Agarre en mojado</label>
                        <select name="busqueda_ag_moj" id="busqueda_ag_moj" wire:model="busqueda_ag_moj"
                            class="form-control">
                            <option selected value="">--</option>
                            @for ($i = 65; $i <= 71; $i++)
                                <option value="{{ chr($i) }}">{{ chr($i) }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-4">
                        <label for="busqueda_em_ruido" class="col-sm-2 col-form-label">Emisión de ruido</label>
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
    @endif

    <div class="card text-bg-light mb-3">
        <h5 class="card-header">Resultados</h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableCliente').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($productos->count() > 0)
                <table class="table responsive" id="tableCliente">
                    <thead>
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Existencias</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productos as $producto)
                            <tr>
                                <th scope="row">{{ $producto->cod_producto }}</th>
                                <td>{{ $producto->descripcion }}</td>
                                <td>
                                    @if ($almacenes->where('cod_producto', $producto->cod_producto)->first() != null)
                                        {{ $almacenes->where('cod_producto', $producto->cod_producto)->first()->existencias }}
                                    @else
                                        No mueve existencias
                                    @endif

                                </td>
                                <td><button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $producto->id }});">Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3>No existen productos de este tipo.</h3>
            @endif
        </div>
    </div>
</div>
