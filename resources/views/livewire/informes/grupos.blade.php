<div>
    @mobile
        <div class="card">
            <h5 class="card-header">Tipos de producto</h5>
            <div class="card-body">
                @foreach ($tipos_producto as $tipo)
                    <input type="checkbox" value="{{ $tipo->id }}" wire:model="tiposSeleccionados">
                    {{ $tipo->tipo_producto }}<br>
                @endforeach
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Categorías</h5>
            <div class="card-body">
                @foreach ($productos_categoria as $categoria)
                    <input type="checkbox" value="{{ $categoria->id }}" wire:model="categoriasSeleccionadas">
                    {{ $categoria->nombre }}<br>
                @endforeach
            </div>
        </div>
    @elsemobile
        <div class="row justify-content-center">
            <div class="col" style="max-width:35rem; max-height:50vh">
                <div class="card">
                    <h5 class="card-header">Tipos de producto</h5>
                    <div class="card-body">
                        @foreach ($tipos_producto as $tipo)
                            <div class="mb-1">
                                <input type="checkbox" value="{{ $tipo->id }}" wire:model="tiposSeleccionados"
                                    @if (in_array($tipo->id, $tiposSeleccionados)) checked @endif>
                                {{ $tipo->tipo_producto }}
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <input type="checkbox" wire:change='seleccionarTipoTodos' wire:model="tipoTodos">
                        <label for="tipoTodos">Seleccionar todo</label>
                    </div>
                </div>
            </div>

            <div class="col" style="max-width:35rem;">
                <div class="card">
                    <h5 class="card-header">Categorías</h5>
                    <div class="card-body" style="max-height:50vh; overflow-y: scroll;">
                        @foreach ($productos_categoria as $categoria)
                            <div class="mb-1">
                                <input type="checkbox" value="{{ $categoria->id }}" wire:model="categoriasSeleccionadas">
                                {{ $tipos_producto->where('id', $categoria->tipo_producto)->first()->tipo_producto }} -
                                {{ $categoria->nombre }}
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <input type="checkbox" wire:change='seleccionarCatTodos' wire:model="catTodos" id="selCatTodos">
                        <label for="selCatTodos">Seleccionar todo</label>
                    </div>
                </div>
            </div>

            <div class="col" style="max-width:25rem;">
                <div class="card">
                    <h5 class="card-header">Grupos</h5>
                    <div class="card-body" style="max-height:50vh; overflow-y: scroll;">
                        <div class="mb-1">
                            <input type="radio" id="0" wire:model="grupo" value="0">
                            <label for="0">Sin grupo/Grupo nuevo</label>
                        </div>
                        @foreach ($grupos as $grupo)
                            <div class="mb-1">
                                <input type="radio" id="{{ $grupo->id }}" wire:model="grupo"
                                    value="{{ $grupo->id }}">
                                <label for="{{ $grupo->id }}">Grupo {{ $grupo->id }}</label>
                            </div>
                        @endforeach
                    </div>
                    <div class="card-footer">
                        <button type="button" wire:click="addGrupo" class="btn btn-primary"> Añadir
                            grupo </button>
                    </div>
                </div>
            </div>
        </div>
    @endmobile

</div>
