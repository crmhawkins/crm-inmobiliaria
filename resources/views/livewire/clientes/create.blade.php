<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        @mobile
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Añadir datos del cliente
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre_completo" class="col-sm-2 col-form-label">Nombre</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="nombre_completo" class="form-control"
                                    name="nombre_completo" id="nombre_completo"
                                    placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                                @error('nombre_completo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="dni" class="col-sm-2 col-form-label">DNI / NIF</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="dni" class="form-control" name="dni"
                                    id="dni" placeholder="Documento de identidad del cliente (ej; 12345678A)">
                                @error('dni')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="telefono" class="form-control" name="telefono"
                                    id="telefono" placeholder="Teléfono del cliente (ej; 123456789)">
                                @error('telefono')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="mb-3 row d-flex align-items-center">
                            <label for="email" class="col-sm-2 col-form-label">Correo electrónico</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="email" class="form-control" name="email"
                                    id="email" placeholder="Correo electrónico del cliente (ej; 12345678A)">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="inmobiliaria" class="col-sm-12 col-form-label">¿Este cliente pertenece a ambas
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
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Intereses
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="ubicacion" class="col-sm-12 col-form-label">
                                <strong>Ubicación</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="ubicacion" class="form-control" name="ubicacion"
                                    id="ubicacion" placeholder="Ubicación del inmueble">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="habitaciones" class="col-sm-12 col-form-label">
                                <strong>Habitaciones</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="habitaciones_min" class="form-control"
                                    name="habitaciones_min" id="habitaciones_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="habitaciones_max" class="form-control"
                                    name="habitaciones_max" id="habitaciones_max" placeholder="Máximo">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="banos" class="col-sm-12 col-form-label">
                                <strong>Baños</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="banos_min" class="form-control" name="banos_min"
                                    id="banos_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="banos_max" class="form-control" name="banos_max"
                                    id="banos_max" placeholder="Máximo">
                            </div>
                            @error('banos')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="m2" class="col-sm-12 col-form-label">
                                <strong>Metros cuadrados</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="m2_min" class="form-control" name="m2_min"
                                    id="m2_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="m2_max" class="form-control" name="m2_max"
                                    id="m2_max" placeholder="Máximo">
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
                                        <option value="">-- Disponibilidad del inmueble --</option>
                                        <option value="Alquiler">Inmueble en alquiler</option>
                                        <option value="Venta">Inmueble en venta</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="otras_caracteristicasArray" class="col-sm-4 col-form-label">
                                <strong>Otras
                                    características</strong></label>
                            <div class="col-sm-11 border rounded-2"
                                style="overflow-y:scroll; height:5rem; margin-left:11px;">
                                @foreach ($caracteristicas as $caracteristica)
                                    <div class="mb-1">
                                        <input type="checkbox" value="{{ $caracteristica->id }}"
                                            wire:model="otras_caracteristicasArray"
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
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Inmuebles en base a tus intereses
                    </h5>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @foreach ($intereses_inmuebles as $inmueble)
                                <div class="col-4 mb-4">
                                    <div class="card" style="width: 25rem;">
                                        <img class="card-img-top"
                                            src="{{ json_decode($inmueble->galeria, true)[1] }}"
                                            alt="Foto del inmueble">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $inmueble->titulo }}</h5>
                                            <p class="card-text">{{ $inmueble->descripcion }}</p>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">{{ $inmueble->m2 }} m2</li>
                                            <li class="list-group-item">{{ $inmueble->habitaciones }} habitaciones
                                            </li>
                                            <li class="list-group-item">{{ $inmueble->banos }} baños</li>

                                            @foreach (json_decode($inmueble->otras_caracteristicas, true) as $caracteristica)
                                                <li class="list-group-item">
                                                    {{ $caracteristicas->where('id', $caracteristica)->first()->nombre }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $intereses_inmuebles->links() }}

                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        @elsemobile
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3" style="max-width: 40rem">
                    <h5 class="card-header">
                        Añadir datos del cliente
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="nombre_completo" class="col-sm-2 col-form-label">Nombre</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="nombre_completo" class="form-control"
                                    name="nombre_completo" id="nombre_completo"
                                    placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                                @error('nombre_completo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="dni" class="col-sm-2 col-form-label">DNI / NIF</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="dni" class="form-control" name="dni"
                                    id="dni" placeholder="Documento de identidad del cliente (ej; 12345678A)">
                                @error('dni')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="telefono" class="form-control" name="telefono"
                                    id="telefono" placeholder="Teléfono del cliente (ej; 123456789)">
                                @error('telefono')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>


                        <div class="mb-3 row d-flex align-items-center">
                            <label for="email" class="col-sm-2 col-form-label">Correo electrónico</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="email" class="form-control" name="email"
                                    id="email" placeholder="Correo electrónico del cliente (ej; 12345678A)">
                                @error('email')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="inmobiliaria" class="col-sm-12 col-form-label">¿Este cliente pertenece a ambas
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
            </div>

            <div class="col">
                <div class="card mb-3" style="max-width: 40rem">
                    <h5 class="card-header">
                        Intereses
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="ubicacion" class="col-sm-12 col-form-label">
                                <strong>Ubicación</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="ubicacion" class="form-control" name="ubicacion"
                                    id="ubicacion" placeholder="Ubicación del inmueble">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="habitaciones" class="col-sm-12 col-form-label">
                                <strong>Habitaciones</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="habitaciones_min" class="form-control"
                                    name="habitaciones_min" id="habitaciones_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="habitaciones_max" class="form-control"
                                    name="habitaciones_max" id="habitaciones_max" placeholder="Máximo">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="banos" class="col-sm-12 col-form-label">
                                <strong>Baños</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="banos_min" class="form-control" name="banos_min"
                                    id="banos_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="banos_max" class="form-control" name="banos_max"
                                    id="banos_max" placeholder="Máximo">
                            </div>
                            @error('banos')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="m2" class="col-sm-12 col-form-label">
                                <strong>Metros cuadrados</strong></label>
                            <div class="col-sm-6">
                                <input type="number" wire:model="m2_min" class="form-control" name="m2_min"
                                    id="m2_min" placeholder="Mínimo">
                            </div>
                            <div class="col-sm-6">
                                <input type="number" wire:model="m2_max" class="form-control" name="m2_max"
                                    id="m2_max" placeholder="Máximo">
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
                                        <option value="">-- Disponibilidad del inmueble --</option>
                                        <option value="Alquiler">Inmueble en alquiler</option>
                                        <option value="Venta">Inmueble en venta</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="otras_caracteristicasArray" class="col-sm-4 col-form-label">
                                <strong>Otras
                                    características</strong></label>
                            <div class="col-sm-11 border rounded-2"
                                style="overflow-y:scroll; height:5rem; margin-left:11px;">
                                @foreach ($caracteristicas as $caracteristica)
                                    <div class="mb-1">
                                        <input type="checkbox" value="{{ $caracteristica->id }}"
                                            wire:model="otras_caracteristicasArray"
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
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Inmuebles en base a tus intereses
                    </h5>
                    <div class="card-body">
                        <div class="row justify-content-center">
                            @foreach ($intereses_inmuebles as $inmueble)
                                <div class="col-4 mb-4">
                                    <div class="card" style="width: 25rem;">
                                        <img class="card-img-top"
                                            src="{{ json_decode($inmueble->galeria, true)[1] }}"
                                            alt="Foto del inmueble">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $inmueble->titulo }}</h5>
                                            <p class="card-text">{{ $inmueble->descripcion }}</p>
                                        </div>
                                        <ul class="list-group list-group-flush">
                                            <li class="list-group-item">{{ $inmueble->m2 }} m2</li>
                                            <li class="list-group-item">{{ $inmueble->habitaciones }} habitaciones
                                            </li>
                                            <li class="list-group-item">{{ $inmueble->banos }} baños</li>

                                            @foreach (json_decode($inmueble->otras_caracteristicas, true) as $caracteristica)
                                                <li class="list-group-item">
                                                    {{ $caracteristicas->where('id', $caracteristica)->first()->nombre }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        {{ $intereses_inmuebles->links() }}

                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
        @endmobile
    </form>
</div>
