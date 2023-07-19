<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card mb-3">
            <h5 class="card-header">
                Añadir cita a la agenda
            </h5>
            <div class="card-body">
                <div class="mb-3 row d-flex align-items-center">
                    <label class="col-sm-2 col-form-label">Tipo de cita</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_tarea" id="tipo_tarea_1"
                                wire:model="tipo_tarea" value="opcion_1">
                            <label class="form-check-label" for="tipo_tarea_1">
                                Cita con cliente
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_tarea" id="tipo_tarea_2"
                                wire:model="tipo_tarea" value="opcion_2">
                            <label class="form-check-label" for="tipo_tarea_2">
                                Personalizado
                            </label>
                        </div>
                        @error('tipo_tarea')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($tipo_tarea == 'opcion_1')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="cliente" class="col-sm-3 col-form-label"><strong>Cliente:</strong></label>
                        <div x-data="" x-init="$('#select2-cliente-create').select2();
                        $('#select2-cliente-create').on('change', function(e) {
                            var data = $('#select2-cliente-create').select2('val');
                            @this.set('cliente_id', data);
                        });">
                            <div class="col" wire:ignore>
                                <select class="form-control" id="select2-cliente-create">
                                    <option value="">-- Elige un cliente --</option>
                                    @foreach ($clientes as $cliente)
                                        <option value={{ $cliente->id }}>{{ $cliente->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="inmueble" class="col-sm-3 col-form-label"><strong>Inmueble :</strong></label>
                        <div x-data="" x-init="$('#select2-inmueble-create').select2();
                        $('#select2-inmueble-create').on('change', function(e) {
                            var data = $('#select2-inmueble-create').select2('val');
                            @this.set('inmueble_id', data);
                        });">
                            <div class="col" wire:ignore>
                                <select class="form-control" id="select2-inmueble-create">
                                    <option value="">-- Elige un inmueble --</option>
                                    @foreach ($inmuebles as $inmueble)
                                        <option value={{ $inmueble->id }}>{{ $inmueble->titulo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @elseif($tipo_tarea == 'opcion_2')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre_completo" class="col-sm-2 col-form-label">Título de la cita</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="titulo" class="form-control" name="titulo" id="titulo"
                                placeholder="Título para mostrar en la agenda">
                            @error('titulo')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre_completo" class="col-sm-2 col-form-label">Descripción</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="descripcion" class="form-control" name="descripcion"
                                id="descripcion" placeholder="Descripción completa sobre la cita">
                            @error('descripcion')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif


                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_inicio" class="col-sm-2 col-form-label">Fecha de inicio</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" wire:model="fecha_inicio" class="form-control" name="fecha_inicio"
                            id="fecha_inicio"
                            placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                        @error('fecha_inicio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_fin" class="col-sm-2 col-form-label">Fecha de finalización</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" wire:model="fecha_fin" class="form-control" name="fecha_fin"
                            id="fecha_fin" placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                        @error('fecha_fin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="inmobiliaria" class="col-sm-3 col-form-label">¿Esta entrada en la agenda pertenece a
                        ambas
                        inmobiliarias?</label>
                    <div class="col">
                        <input type="checkbox" wire:model="inmobiliaria" name="inmobiliaria" id="inmobiliaria">
                        @error('inmobiliaria')
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
