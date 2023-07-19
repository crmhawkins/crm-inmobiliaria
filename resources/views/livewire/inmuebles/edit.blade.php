<div class="container mx-auto">
    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="row justify-content-center">
            <div class="col">
                <div class="card mb-3" style="max-width: 40rem">
                    <h5 class="card-header">
                        Datos básicos
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label"> <strong>Título</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="titulo" class="form-control" name="titulo"
                                    id="titulo" placeholder="Título">
                                @error('titulo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="dni" class="col-sm-3 col-form-label"> <strong>Descripción</strong></label>
                            <div class="col-sm-12">
                                <textarea wire:model="descripcion" rows=3 class="form-control" name="descripcion" id="descripcion"
                                    placeholder="Características del inmueble"></textarea>
                                @error('dni')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="referencia_catastral" class="col-sm-4 col-form-label"> <strong>Referencia
                                    catastral</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="referencia_catastral" class="form-control"
                                    name="referencia_catastral" id="referencia_catastral"
                                    placeholder="Referencia catastral del inmueble">
                                @error('referencia_catastral')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="valor_referencia" class="col-sm-4 col-form-label"> <strong>Valor de
                                    referencia</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="valor_referencia" class="form-control"
                                    name="valor_referencia" id="valor_referencia"
                                    placeholder="Valor de referencia del inmueble">
                                @error('valor_referencia')
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
                        Datos de inmueble
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="tipo_vivienda_id" class="col-sm-4 col-form-label"> <strong>Tipo de
                                    vivienda:</strong></label>
                            <div x-data="" x-init="$('#select2-tipo_vivienda_id-create').select2();
                            if (!$('#select2-tipo_vivienda_id-create').val()) {
                                $('#select2-tipo_vivienda_id-create').val(@this.get('tipo_vivienda_id')).trigger('change');
                            }
                            $('#select2-tipo_vivienda_id-create').on('change', function(e) {
                                var data = $('#select2-tipo_vivienda_id-create').select2('val');
                                @this.set('tipo_vivienda_id', data);
                            });">
                                <div class="col" wire:ignore>
                                    <select class="form-control" id="select2-tipo_vivienda_id-create">
                                        <option value="">-- Elige un tipo de vivienda --</option>
                                        @foreach ($tipos_vivienda as $tipo)
                                            <option value={{ $tipo->id }}>{{ $tipo->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="m2" class="col-sm-1 col-form-label">
                                <strong>M<sup>2</sup></strong></label>
                            <div class="col">
                                <input type="text" wire:model="m2" class="form-control" name="m2" id="m2"
                                    placeholder="Metros cuadrados del inmueble">
                                @error('m2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <label for="m2_construidos" class="col-sm-3 col-form-label"
                                style="margin-right: -30px;"><strong>M<sup>2</sup> construidos
                                </strong></label>
                            <div class="col">
                                <input type="text" wire:model="m2_construidos" class="form-control"
                                    name="m2_construidos" id="m2_construidos"
                                    placeholder="Metros cuadrados construidos del inmueble">
                                @error('m2_construidos')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row d-flex align-items-center">
                            <label for="cod_postal" class="col-sm-4 col-form-label"> <strong>¿Tiene certificado
                                    energético?</strong></label>
                            <div class="col">
                                <input type="checkbox" wire:model="cert_energetico" name="cert_energetico"
                                    id="cert_energetico">
                                @error('ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <label for="inmobiliaria" class="col-sm-4 col-form-label"> <strong>¿Este inmueble pertenece
                                    a
                                    ambas
                                    inmobiliarias?</strong></label>
                            <div class="col">
                                <input type="checkbox" wire:model="inmobiliaria" name="inmobiliaria"
                                    id="inmobiliaria">
                                @error('inmobiliaria')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        @if ($cert_energetico == 1)
                            <div class="mb-3 row d-flex align-items-center">
                                <label for="cert_energetico_elegido" class="col-sm-3 col-form-label">
                                    <strong><strong>Etiqueta
                                            del
                                            certificado energético:</strong></strong></label>
                                <div x-data="" x-init="$('#select2-cert_energetico_elegido-create').select2();
                                if (!$('#select2-cert_energetico_elegido-create').val()) {
                                    $('#select2-cert_energetico_elegido-create').val(@this.get('cert_energetico_elegido')).trigger('change');
                                }
                                $('#select2-cert_energetico_elegido-create').on('change', function(e) {
                                    var data = $('#select2-cert_energetico_elegido-create').select2('val');
                                    @this.set('cert_energetico_elegido', data);
                                });">
                                    <div class="col" wire:ignore>
                                        <select class="form-control" id="select2-cert_energetico_elegido-create">
                                            <option value="">-- Elige la etiqueta del certificado --</option>
                                            <option value="A">A</option>
                                            <option value="B">B</option>
                                            <option value="C">C</option>
                                            <option value="D">D</option>
                                            <option value="E">E</option>
                                            <option value="F">F</option>
                                            <option value="G">G</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        @endif
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="ubicacion" class="col-sm-3 col-form-label"> <strong>Ubicación</strong></label>
                            <div class="col-sm-12">
                                <input type="text" wire:model="ubicacion" class="form-control" name="ubicacion"
                                    id="ubicacion" placeholder="Ubicación del inmueble">
                                @error('ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="cod_postal" class="col-sm-3 col-form-label"> <strong>Código
                                    postal</strong></label>
                            <div class="col-sm-12">
                                <input type="number" wire:model="cod_postal" class="form-control" name="cod_postal"
                                    id="cod_postal" placeholder="Código postal del inmueble">
                                @error('ubicacion')
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
                <div class="card mb-3" style="max-width: 40rem">
                    <h5 class="card-header">
                        Características del inmueble
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="habitaciones" class="col-sm-3 col-form-label">
                                <strong>Habitaciones</strong></label>
                            <div class="col-sm-12">
                                <input type="number" wire:model="habitaciones" class="form-control"
                                    name="habitaciones" id="habitaciones" placeholder="Habitaciones del inmueble">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="banos" class="col-sm-3 col-form-label"> <strong>Baños</strong></label>
                            <div class="col-sm-12">
                                <input type="number" wire:model="banos" class="form-control" name="banos"
                                    id="banos" placeholder="Baños del inmueble">
                                @error('banos')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="estado" class="col-sm-3 col-form-label"> <strong>Estado</strong></label>
                            <div x-data="" x-init="$('#select2-estado-create').select2();
                            if (!$('#select2-estado-create').val()) {
                                $('#select2-estado-create').val(@this.get('estado')).trigger('change');
                            }
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
                            <label for="disponibilidad" class="col-sm-3 col-form-label">
                                <strong>Disponibilidad</strong></label>
                            <div x-data="" x-init="$('#select2-disponibilidad-create').select2();
                            if (!$('#select2-disponibilidad-create').val()) {
                                $('#select2-disponibilidad-create').val(@this.get('disponibilidad')).trigger('change');
                            }
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
                            <label for="otras_caracteristicasArray" class="col-sm-4 col-form-label"> <strong>Otras
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
            <div class="col">
                <div class="card mb-3" style="max-width: 40rem;">
                    <h5 class="card-header">
                        Vendedor asignado
                    </h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="vendedor" class="col-sm-3 col-form-label">
                                <strong>Cliente:</strong></label>
                            <div x-data="" x-init="$('#select2-vendedor-create').select2();
                            if (!$('#select2-vendedor-create').val()) {
                                $('#select2-vendedor-create').val(@this.get('vendedor_id')).trigger('change');
                            }
                            $('#select2-vendedor-create').on('change', function(e) {
                                var data = $('#select2-vendedor-create').select2('val');
                                @this.set('vendedor_id', data);
                                console.log(data);
                            });">
                                <div class="col" wire:ignore>
                                    <select class="form-control" id="select2-vendedor-create">
                                        <option value="">-- Elige un cliente --</option>
                                        @foreach ($vendedores as $vendedor)
                                            <option value={{ $vendedor->id }}>
                                                {{ $vendedor->nombre_completo }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label"> <strong>Nombre</strong></label>
                            <div class="col-sm-12">
                                <input type="text" disabled wire:model="vendedor_nombre" class="form-control"
                                    name="vendedor_nombre" id="vendedor_nombre" placeholder="Nombre">
                                @error('vendedor_nombre')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label"> <strong>DNI</strong></label>
                            <div class="col-sm-12">
                                <input type="text" disabled wire:model="vendedor_dni" class="form-control"
                                    name="vendedor_dni" id="vendedor_dni" placeholder="DNI">
                                @error('vendedor_dni')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label">
                                <strong>Ubicación</strong></label>
                            <div class="col-sm-12">
                                <input type="text" disabled wire:model="vendedor_ubicacion" class="form-control"
                                    name="vendedor_ubicacion" id="vendedor_ubicacion" placeholder="Ubicación">
                                @error('vendedor_ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label">
                                <strong>Teléfono</strong></label>
                            <div class="col-sm-12">
                                <input type="text" disabled wire:model="vendedor_telefono" class="form-control"
                                    name="vendedor_telefono" id="vendedor_telefono" placeholder="Teléfono">
                                @error('titulo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="titulo" class="col-sm-3 col-form-label"> <strong>Correo</strong></label>
                            <div class="col-sm-12">
                                <input type="text" disabled wire:model="vendedor_correo" class="form-control"
                                    name="vendedor_correo" id="vendedor_correo" placeholder="Correo">
                                @error('vendedor_correo')
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
                <div class="card mb-3" style="max-width: 40rem">
                    <h5 class="card-header">
                        Añadir imagen a galería
                    </h5>
                    <div class="card-body">

                        @if (!empty($ruta_imagenes))
                            <img src="{{ $ruta_imagenes }}" style="max-width: 50%; max-height: 50%"
                                class="self-center">
                        @endif

                        <div class="input-group">
                            <span class="input-group-btn">
                                <a id="lfm" data-input="thumbnail" data-preview="holder"
                                    class="btn btn-primary">
                                    <i class="fa fa-picture-o"></i> Seleccionar imagen
                                </a>
                            </span>
                            <input id="thumbnail" name="ruta_imagenes" wire:model="ruta_imagenes"
                                class="form-control" type="text">
                        </div>
                        <img id="holder" style="margin-top:15px;max-height:100px;margin-bottom:5px;">
                        @if (!empty($ruta_imagenes))
                            <button class="btn btn-primary" wire:click.prevent="addGaleria">Añadir a
                                galería</button>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col">
                <div class="card mb-3" style="max-width: 20rem">
                    <h5 class="card-header">
                        Galería
                    </h5>
                    <div class="card-body">
                        <div class="row">
                            @foreach ($galeriaArray as $imagenIndex => $imagen)
                                <div class="col-6 mb-5"><img src="{{ $imagen }}" width="100%"
                                        height="100%"> <button class="btn btn-sm btn-danger"
                                        wire:click.prevent="eliminarImagen('{{ $imagenIndex }}')">X</button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

        <script>
            $('#lfm').on('click', function() {
                var route_prefix = '/laravel-filemanager' || '';
                var type = $(this).data('type') || 'images';
                var target_input = document.getElementById('thumbnail');

                window.open(route_prefix + '?type=' + type || 'file', 'FileManager',
                    'width=900,height=600');
                window.SetUrl = function(items) {
                    var file_path = items.map(function(item) {
                        return item.url;
                    }).join(',');

                    // set the value of the desired input to image url
                    target_input.value = file_path;
                    target_input.dispatchEvent(new Event('input'));

                    // trigger change event
                    window.livewire.emit('fileSelected', file_path);
                };
                return false;
            });
        </script>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
