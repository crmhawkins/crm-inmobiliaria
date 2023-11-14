<div class="container" style="max-width: max-content;">

    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="row d-flex justify-content-center">
            <div class="col-6">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Datos del inmueble
                    </h5>
                    <div class="card-body">
                        <div class="row d-flex align-items-start">
                            <div class="col-6">
                                <h3>Datos básicos</h3>
                                <p><b>Inmueble:</b> {{ $titulo }}<br>
                                    <b>Descripción:</b> {{ $descripcion }}<br>
                                    <b>Referencia catastral:</b> {{ $referencia_catastral }}<br>
                                    <b>Valor de referencia:</b> {{ $valor_referencia }}
                                </p>
                            </div>
                            <div class="col-6">
                                <h3>Datos de inmueble</h3>
                                <p><b>Tipo de vivienda:</b>
                                    {{ $tipos_vivienda->where('id', $tipo_vivienda_id)->first()->nombre }}<br>
                                    <b>M<sup>2</sup>:</b> {{ $m2 }}<br>
                                    <b>M<sup>2</sup> construidos:</b> {{ $m2_construidos }}<br>
                                    <b>¿Tiene certificado energético?</b> {{ $cert_energetico == true ? 'Sí' : 'No' }}
                                    <br>
                                    @if ($cert_energetico == 1)
                                        <b>Etiqueta del certificado energético:</b> {{ $cert_energetico_elegido }} <br>
                                    @endif
                                    <b>¿Este inmueble pertenece a ambas inmobiliarias?</b>
                                    {{ $inmobiliaria == true ? 'Sí' : 'No' }} <br>
                                    <b>Ubicación:</b> {{ $ubicacion }} <br>
                                    <b>Código postal:</b> {{ $cod_postal }} <br>

                                </p>
                            </div>
                            <div class="col-6">
                                <h3>Características de inmueble</h3>
                                <p><b>Habitaciones:</b> {{ $habitaciones }}<br>
                                    <b>Baños:</b> {{ $banos }}<br>
                                    <b>Estado:</b> {{ $estado }}<br>
                                    <b>Disponibilidad:</b> {{ $disponibilidad }} <br>
                                    <b>Otras características:</b>
                                    @foreach ($caracteristicas as $caracteristica)
                                        @if (in_array($caracteristica->id, $otras_caracteristicasArray))
                                            {{ $caracteristica->nombre }},
                                        @endif
                                    @endforeach <br>
                                </p>
                            </div>
                            <div class="col-6">
                                <h3>Características de inmueble</h3>
                                <p><b>Vendedor:</b>
                                    {{ $vendedores->where('id', $vendedor_id)->first()->nombre_completo }}<br>
                                    <b>DNI:</b> {{ $vendedores->where('id', $vendedor_id)->first()->dni }}<br>
                                    <b>Ubicación:</b>
                                    {{ $vendedores->where('id', $vendedor_id)->first()->ubicacion }}<br>
                                    <b>Teléfono:</b> {{ $vendedores->where('id', $vendedor_id)->first()->telefono }}
                                    <br>
                                    <b>Correo:</b> {{ $vendedores->where('id', $vendedor_id)->first()->email }} <br>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Seleccionar y enviar imágenes
                    </h5>
                    <div class="card-body">
                        <div class="row">
                            <h5> Imágenes del inmueble </h5>
                            @foreach ($galeriaArray as $key => $imagen)
                                <div class="col-4">
                                    <div class="card">
                                        <img src="{{ $imagen }}" class="card-img-top" alt="Imagen del inmueble">
                                        <div class="card-body">
                                            @if (in_array($key, $imagenes_correo))
                                                <button type="button" class="btn btn-dark text-white"
                                                    id="check-{{ $key }}"
                                                    wire:click.prevent="deleteImagen({{ $key }})">X</button>
                                            @else
                                                <button type="button" class="btn btn-dark text-white"
                                                    id="check-{{ $key }}"
                                                    wire:click.prevent="addImagen({{ $key }})">Seleccionar
                                                    imagen</button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <hr />

                        <h5>Seleccionar cliente</h5>
                        <div class="mb-3 row d-flex align-items-center">
                            <div x-data="" x-init="$('#select2-cliente-{{ $identificador }}').select2();
                            $('#select2-cliente-{{ $identificador }}').on('change', function(e) {
                                var data = $('#select2-cliente-{{ $identificador }}').select2('val');
                                @this.set('cliente_correo', data);
                                console.log(data);
                            });" wire:ignore>
                                <select class="form-control" id="select2-cliente-{{ $identificador }}">
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}">
                                            {{ $cliente->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <hr />

                        <button type="button" class="btn btn-primary"
                            wire:click.prevent="enviarCorreoImagenes({{ $identificador }})"
                            id="enviarCorreoImagenes-{{ $identificador }}"
                            wire:key="btn-correo-imgs-{{ $identificador }}">Enviar
                            imágenes</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="row d-flex justify-content-center">
            <div class="col-4">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Documentos
                    </h5>
                    <div class="card-body">
                        @livewire('inmuebles.documentos-create', ['inmueble_id' => $identificador], key(time() . $identificador))
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Contrato de arras
                    </h5>
                    <div class="card-body">
                        @livewire('inmuebles.contrato-create', ['inmueble_id' => $identificador], key(time() . $identificador))
                    </div>
                </div>
            </div>
            <div class="col-4">
                <div class="card mb-3">
                    <h5 class="card-header">
                        Hojas de visita
                    </h5>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="home-tab" data-bs-toggle="tab"
                                    data-bs-target="#home" type="button" role="tab" aria-controls="home"
                                    aria-selected="true">Crear
                                    nueva hoja de visita</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    type="button" role="tab" aria-controls="profile" aria-selected="true">Ver
                                    hojas ya creadas</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="home" role="tabpanel"
                                aria-labelledby="home-tab">
                                @livewire('inmuebles.visita-create', ['inmueble_id' => $identificador], key(time() . 'inmueble-' . $identificador))

                            </div>
                            <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                                @livewire('inmuebles.visita-index', ['inmueble_id' => $identificador], key(time() . 'inmueble-' . $identificador))
                            </div>
                        </div>
                    </div>
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
                </div>
            </div>
        </div>
    </form>
</div>
