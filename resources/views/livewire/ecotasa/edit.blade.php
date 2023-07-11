<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Revisar ecotasas
        </h5>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                <div class="mb-3 row d-flex align-items-center">
                    <label for="nombre" class="col-sm-2 col-form-label">Nombre</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" value="{{$ecotasa->nombre}}">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="valor" class="col-sm-2 col-form-label">Valor de la ecotasa</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="valor" class="form-control" name="valor" id="valor" value="{{$ecotasa->valor}}" step="0.01">
                        @error('valor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="peso_min" class="col-sm-2 col-form-label">Peso mínimo</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="peso_min" class="form-control" name="peso_min" id="peso_min" value="{{$ecotasa->peso_min}}">
                        @error('peso_min')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="peso_max" class="col-sm-2 col-form-label">Peso máximo</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="peso_max" class="form-control" name="peso_max" id="peso_max" value="{{$ecotasa->peso_max}}">
                        @error('peso_max')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="diametro_mayor_1400" class="col-sm-2 col-form-label">¿Es el diámetro mayor a 1400
                        mm?</label>
                    <div class="col-sm-10">
                        <input type="checkbox" wire:model="diametro_mayor_1400" name="diametro_mayor_1400" @if($ecotasa->diametro_mayor_1400 == 1) checked @endif
                            id="diametro_mayor_1400">
                        @error('diametro_mayor_1400')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


                <div class="mb-3 row d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-info">Guardar</button>
                </div>

            </form>
        </div>
    </div>
</div>
