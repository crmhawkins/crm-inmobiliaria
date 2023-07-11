<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card mb-3">
            <h5 class="card-header">
                Añadir datos de ecotasas
            </h5>
            <div class="card-body">
                <div class="mb-3 row d-flex align-items-center">
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre"
                            placeholder="Denominación de la ecotasa (N1, N2...)">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="valor" class="col-sm-2 col-form-label">Valor de la ecotasa</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="valor" class="form-control" name="valor" id="valor"
                            placeholder="Importe añadido de la ecotasa (1,19, 12,39...)" step="0.01">
                        @error('valor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="peso_min" class="col-sm-2 col-form-label">Peso mínimo</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="peso_min" class="form-control" name="peso_min" id="peso_min"
                            placeholder="Baremo mínimo de peso para la ecotasa (1,19, 12,39...)">
                        @error('peso_min')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="peso_max" class="col-sm-2 col-form-label">Peso máximo</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="peso_max" class="form-control" name="peso_max" id="peso_max"
                            placeholder="Baremo máximo de peso para la ecotasa (1,19, 12,39...)">
                        @error('peso_max')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="diametro_mayor_1400" class="col-sm-2 col-form-label">¿Es el diámetro mayor a 1400
                        mm?</label>
                    <div class="col-sm-10">
                        <input type="checkbox" wire:model="diametro_mayor_1400" name="diametro_mayor_1400"
                            id="diametro_mayor_1400">
                        @error('peso_max')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
