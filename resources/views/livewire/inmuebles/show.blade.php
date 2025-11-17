<div class="container mx-auto">
    <style>
        .property-show-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .property-show-header {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 25px 30px;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .property-show-header i {
            font-size: 1.6rem;
        }
        .info-box {
            background: var(--corporate-green-lightest);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border-left: 4px solid var(--corporate-green);
        }
        .info-box h3 {
            color: var(--corporate-green-dark);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .info-box p {
            margin: 0;
            line-height: 1.8;
        }
        .info-box b {
            color: var(--corporate-green-dark);
            font-weight: 600;
        }
        .gallery-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .gallery-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
        }
    </style>
    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="row d-flex justify-content-center g-4">
            <div class="col-lg-6">
                <div class="property-show-section">
                    <div class="property-show-header">
                        <i class="fas fa-info-circle"></i>
                        Datos del inmueble
                    </div>
                    <div class="card-body p-4">
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
                        <div class="col-12">
                            @if ($publicado == true)
                            <button class="btn btn-outline-primary" wire:click="apiPut()">Modificar</button>
                            <button class="btn btn-outline-danger" wire:click="apiDelete()">Eliminar pulbicación</button>
                            @else
                            <button class="btn btn-outline-primary" wire:click="apiPost()">Publicar</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="property-show-section">
                    <div class="property-show-header">
                        <i class="fas fa-images"></i>
                        Seleccionar y enviar imágenes
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <h5 class="mb-3"><i class="fas fa-images me-2"></i>Imágenes del inmueble</h5>
                            @foreach ($galeriaArray as $key => $imagen)
                                <div class="col-md-4">
                                    <div class="gallery-card">
                                        <img src="{{ $imagen }}" class="card-img-top" alt="Imagen del inmueble" style="height: 200px; object-fit: cover;">
                                        <div class="card-body p-3">
                                            @if (in_array($key, $imagenes_correo))
                                                <button type="button" class="btn btn-danger w-100"
                                                    id="check-{{ $key }}"
                                                    wire:click.prevent="deleteImagen({{ $key }})">
                                                    <i class="fas fa-times me-2"></i>Deseleccionar
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-success w-100"
                                                    id="check-{{ $key }}"
                                                    wire:click.prevent="addImagen({{ $key }})">
                                                    <i class="fas fa-check me-2"></i>Seleccionar imagen
                                                </button>
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
        <div class="row d-flex justify-content-center g-4 mt-2">
            <div class="col-lg-4">
                <div class="property-show-section">
                    <div class="property-show-header">
                        <i class="fas fa-file-alt"></i>
                        Documentos
                    </div>
                    <div class="card-body">
                        @livewire('inmuebles.documentos-create', ['inmueble_id' => $identificador], key(time() . $identificador))
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="property-show-section">
                    <div class="property-show-header">
                        <i class="fas fa-file-contract"></i>
                        Contrato de arras
                    </div>
                    <div class="card-body">
                        @livewire('inmuebles.contrato-create', ['inmueble_id' => $identificador], key(time() . $identificador))
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
