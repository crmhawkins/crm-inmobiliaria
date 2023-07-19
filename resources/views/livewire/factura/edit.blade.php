<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Revisar clientes
        </h5>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Modificar datos del cliente
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre_completo" class="col-sm-2 col-form-label">Nombre</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="nombre_completo" class="form-control" name="nombre_completo"
                                    id="nombre_completo"
                                    placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                                @error('nombre_completo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="dni" class="col-sm-2 col-form-label">DNI / NIF</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="dni" class="form-control" name="dni" id="dni"
                                    placeholder="Documento de identidad del cliente (ej; 12345678A)">
                                @error('dni')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="email" class="col-sm-2 col-form-label">Correo electrónico</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="email" class="form-control" name="email" id="email"
                                    placeholder="Correo electrónico del cliente (ej; 12345678A)">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="mb-3 row d-flex align-items-center" style="overflow-y: scroll;">
                            <label for="email" class="col-sm-2 col-form-label">Intereses</label>
                            <div class="col-sm-10">
                                @foreach ($intereses_select as $interes)
                                    <div class="mb-1">
                                        <input type="checkbox" value="{{ $interes->id }}"
                                            wire:model="intereses">
                                        {{ $interes->nombre }}
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center" style="overflow-y: scroll;">
                            <label for="email" class="col-sm-2 col-form-label">Inmuebles de interés</label>
                            <div class="col-sm-10">
                                @foreach ($inmuebles_select as $inmueble)
                                    <div class="mb-1">
                                        <input type="checkbox" value="{{ $inmueble->id }}"
                                            wire:model="intereses_inmuebles">
                                        {{ $inmueble->titulo }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>
