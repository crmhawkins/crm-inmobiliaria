<div class="container mx-auto" style="min-height: 100vh">
    <div class="card text-dark bg-light mb-3">
        <h5 class="card-header"> Añadir proveedor</h5>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">


                <div class="mb-3 row d-flex align-items-center">
                    <label for="dni" class="col-sm-2 col-form-label">DNI </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="dni" class="form-control" name="dni" id="dni"
                            placeholder="7515763200P">
                        @error('dni')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                            placeholder="Carlos">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="email" class="form-control" name="email" id="email"
                            placeholder="ejemplo@gmail.com">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="telefono" class="form-control" name="telefono" id="telefono"
                            placeholder="956812502">
                        @error('telefono')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="direccion" class="col-sm-2 col-form-label">Dirección </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="direccion" class="form-control" name="direccion"
                            id="direccion" placeholder="Calle Baldomero nº 12">
                        @error('direccion')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="observaciones" class="col-sm-2 col-form-label">Observaciones </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="observaciones" class="form-control" name="observaciones"
                            id="observaciones" placeholder="Pérez">
                        @error('observaciones')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
        </div>
    </div>
    <div class="mb-3 row d-flex align-items-center">
        <button type="submit" class="btn btn-primary">Añadir proveedor</button>
    </div>
    </form>
</div>
