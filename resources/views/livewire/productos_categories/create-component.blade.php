<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Categorías de productos
        </h5>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                <div class="mb-3 row d-flex align-items-center">

                    <label for="tipo" class="col-sm-2 col-form-label">Tipo de producto de la categoría</label>
                    <div class="col-sm-10">

                        <select class="form-select" wire:model="tipo_producto" aria-label="Default select example"
                            id="tipo">
                            <option selected>-- SELECCIONA UN TIPO DE PRODUCTO --</option>
                            @foreach ($tipos_producto as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->tipo_producto }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre de la categoría</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" wire:model="nombre" nombre="nombre" id="nombre"
                            placeholder="Nombre de la categoría...">
                        @error('name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="col-sm-10">
                        <br>

                        <button type="submit" class="btn btn-outline-info">Guardar</button>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
