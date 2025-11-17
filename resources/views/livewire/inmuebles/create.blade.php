<div class="container mx-auto">
    <style>
        .form-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 25px;
            transition: all 0.3s ease;
        }
        .form-section:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        .section-header {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 20px 30px;
            margin: 0;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .section-header i {
            font-size: 1.4rem;
        }
        .form-section .card-body {
            padding: 30px;
        }
        .form-label-modern {
            font-weight: 600;
            color: var(--corporate-green-dark);
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .form-control-modern {
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 16px;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }
        .form-control-modern:focus {
            border-color: var(--corporate-green);
            box-shadow: 0 0 0 4px rgba(107, 142, 107, 0.1);
            outline: none;
        }
        .btn-submit-modern {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 14px 32px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.3);
        }
        .btn-submit-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(107, 142, 107, 0.4);
        }
    </style>
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="row justify-content-center g-4">
            <div class="col-lg-6">
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-info-circle"></i>
                        Datos básicos
                    </div>
                    <div class="card-body">
                        <div class="mb-4">
                            <label for="titulo" class="form-label-modern">
                                <i class="fas fa-heading me-2"></i>Título
                            </label>
                            <input type="text" wire:model="titulo" class="form-control form-control-modern" name="titulo"
                                id="titulo" placeholder="Título del inmueble">
                            @error('titulo')
                                <div class="text-danger mt-2 small">
                                    <i class="fas fa-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                            @enderror
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
                                <input type="number" step="0.01" wire:model="valor_referencia" class="form-control"
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
            <div class="col-lg-6">
                <div class="form-section">
                    <div class="section-header">
                        <i class="fas fa-home"></i>
                        Datos de inmueble
                    </div>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="tipo_vivienda_id" class="col-sm-4 col-form-label"> <strong>Tipo de
                                    vivienda:</strong></label>
                            <div x-data="" x-init="$('#select2-tipo_vivienda_id-create').select2();
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
                            {{-- <label><strong>Extras y equipamiento</strong></label> --}}
                        <div class="row">
                            @foreach ([
                                'amueblado'=>'Amueblado','calefaccion'=>'Calefacción',
                                'jardin_privado'=>'Jardín','piscina_privada'=>'Piscina privada','piscina_comunitaria'=>'Piscina comunitaria',
                                'zona_comunitaria'=>'Zona comunitaria','garaje'=>'Garaje','ascensor'=>'Ascensor',
                                'trastero'=>'Trastero','balcon'=>'Balcón','terraza'=>'Terraza','lavadero'=>'Lavadero',
                                'internet'=>'Internet','parquet'=>'Parquet','electrodomesticos'=>'Electrodomésticos',
                                'aire_acondicionado'=>'Aire acondicionado','cocina_equipada'=>'Cocina','domotica'=>'Domótica','tv'=>'TV'
                            ] as $field=>$label)
                                <div class="col-6 mb-2"><div class="form-check">
                                    <input type="checkbox" wire:model="{{ $field }}" id="{{ $field }}" class="form-check-input">
                                    <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
                                </div></div>
                            @endforeach
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
                                <strong>Vendedor:</strong></label>
                            <div x-data="" x-init="$('#select2-vendedor-create').select2();
                            $('#select2-vendedor-create').on('change', function(e) {
                                var data = $('#select2-vendedor-create').select2('val');
                                @this.set('vendedor_id', data);
                                console.log(data);
                            });">
                                <div class="col" wire:ignore>
                                    <select class="form-control" id="select2-vendedor-create">
                                        <option value="">-- Elige un vendedor --</option>
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
                    <div class="card-body text-center">
                        @if (!empty($ruta_imagenes))
                            <img class="mb-2" src="{{ $ruta_imagenes }}" style="max-width: 50%; max-height: 50%">
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
                            <button class="btn btn-primary w-100 mt-3" wire:click.prevent="addGaleria">Añadir a
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


         <div class="row justify-content-center mt-4">
            <div class="col-12">
                <div class="card mb-3">
                    <h5 class="card-header">Datos API Fotocasa</h5>
                    <div class="card-body">
                        <div class="mb-3"><label><strong>ID externo</strong></label><input type="text" wire:model="external_id" class="form-control"></div>
                        <div class="mb-3"><label><strong>Referencia agencia</strong></label><input type="text" wire:model="agency_reference" class="form-control"></div>
                        <div class="mb-3"><label><strong>TypeId</strong></label><input type="number" wire:model="type_id" class="form-control"></div>
                        <div class="mb-3"><label><strong>SubTypeId</strong></label><input type="number" wire:model="subtype_id" class="form-control"></div>
                        <div class="mb-3"><label><strong>ContactTypeId</strong></label><input type="number" wire:model="contact_type_id" class="form-control"></div>

                        <label><strong>Dirección completa</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="text" wire:model="street" class="form-control" placeholder="Calle"></div>
                            <div class="col"><input type="text" wire:model="number" class="form-control" placeholder="Número"></div>
                            <div class="col"><input type="text" wire:model="zip_code" class="form-control" placeholder="CP"></div>
                        </div>

                        <label><strong>Planta / Visibilidad</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="number" wire:model="floor_id" class="form-control" placeholder="FloorId"></div>
                            <div class="col"><input type="number" wire:model="visibility_mode_id" class="form-control" placeholder="VisibilityId"></div>
                        </div>

                        <label><strong>Coordenadas</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="text" wire:model="x" class="form-control" placeholder="longitud"></div>
                            <div class="col"><input type="text" wire:model="y" class="form-control" placeholder="latitud"></div>
                        </div>

                        <hr>

                        <label><strong>Subtipo / Transacción</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="number" wire:model="subtipo_vivienda_id" class="form-control" placeholder="SubTypeId"></div>
                            <div class="col"><input type="number" wire:model="tipo_transaccion_id" class="form-control" placeholder="TransactionTypeId"></div>
                        </div>

                        <label><strong>Visibilidad / Planta / Orientación</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="number" wire:model="visibilidad_id" class="form-control" placeholder="VisibilityId"></div>
                            <div class="col"><input type="number" wire:model="planta_id" class="form-control" placeholder="FloorId"></div>
                            <div class="col"><input type="number" wire:model="orientacion_id" class="form-control" placeholder="OrientationId"></div>
                        </div>

                        <label><strong>Certificado energético (escala / valores / estado)</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="number" wire:model="cert_consumo_eficiencia_escala" class="form-control" placeholder="Escala consumo"></div>
                            <div class="col"><input type="number" wire:model="cert_emisiones_eficiencia_escala" class="form-control" placeholder="Escala emisiones"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col"><input type="number" step="0.01" wire:model="cert_consumo_valor" class="form-control" placeholder="Consumo valor"></div>
                            <div class="col"><input type="number" step="0.01" wire:model="cert_emisiones_valor" class="form-control" placeholder="Emisiones valor"></div>
                        </div>
                        <div class="mb-3"><input type="number" wire:model="cert_estado" class="form-control" placeholder="Estado certificado (1‑3)"></div>

                        <label><strong>Conservación / Año construcción</strong></label>
                        <div class="row mb-3">
                            <div class="col"><input type="number" wire:model="conservacion_estado_id" class="form-control" placeholder="Estado conservación"></div>
                            <div class="col"><input type="number" wire:model="ano_construccion" class="form-control" placeholder="Año construcción"></div>
                        </div>

                        <hr>



                        <div class="mb-3 mt-3"><label><strong>Superficie terraza (m²)</strong></label><input type="number" step="0.01" wire:model="superficie_terraza" class="form-control"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DATOS API FOTOCASA -->
<div class="row justify-content-center mt-4">
  <div class="col-12">
    <div class="card mb-3">
      <h5 class="card-header">Datos API Fotocasa</h5>
      <div class="card-body">
        <!-- TypeId -->
        <div class="mb-3">
          <label for="type_id"><strong>Tipo de propiedad</strong></label>
          <select wire:model="type_id" id="type_id" class="form-control">
            <option value="">-- Selecciona tipo --</option>
            <option value="1">Flat</option>
            <option value="2">House</option>
            <option value="3">Commercial store</option>
            <option value="4">Office</option>
            <option value="5">Building</option>
            <option value="6">Land</option>
            <option value="7">Industrial building</option>
            <option value="8">Garage</option>
            <option value="12">Storage room</option>
          </select>
          @error('type_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- SubTypeId -->
        <div class="mb-3">
          <label for="subtype_id"><strong>Subtipo de propiedad</strong></label>
          <select wire:model="subtype_id" id="subtype_id" class="form-control">
            <option value="">-- Selecciona subtipo --</option>
            <optgroup label="Flat">
              <option value="2">Triplex</option>
              <option value="3">Duplex</option>
              <option value="5">Penthouse</option>
              <option value="6">Studio</option>
              <option value="7">Loft</option>
              <option value="9">Flat</option>
              <option value="10">Apartment</option>
              <option value="11">Ground floor</option>
            </optgroup>
            <optgroup label="House">
              <option value="13">House</option>
              <option value="17">Terraced house</option>
              <option value="19">Paired house</option>
              <option value="20">Chalet</option>
              <option value="24">Rustic house</option>
              <option value="27">Bungalow</option>
            </optgroup>
            <!-- Puedes completar más grupos según tu necesidad -->
          </select>
          @error('subtype_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- TransactionTypeId -->
        <div class="mb-3">
          <label for="tipo_transaccion_id"><strong>Tipo de transacción</strong></label>
          <select wire:model="tipo_transaccion_id" id="tipo_transaccion_id" class="form-control">
            <option value="">-- Selecciona transacción --</option>
            <option value="1">Buy</option>
            <option value="3">Rent</option>
            <option value="4">Transfer</option>
            <option value="7">Share</option>
            <option value="9">Rent with buy option</option>
          </select>
          @error('tipo_transaccion_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- VisibilityMode -->
        <div class="mb-3">
          <label for="visibility_mode_id"><strong>Modo de visibilidad</strong></label>
          <select wire:model="visibility_mode_id" id="visibility_mode_id" class="form-control">
            <option value="">-- Selecciona modo --</option>
            <option value="1">Exact</option>
            <option value="2">Street</option>
            <option value="3">Zone</option>
          </select>
          @error('visibility_mode_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- FloorId -->
        <div class="mb-3">
          <label for="floor_id"><strong>Planta/Floor</strong></label>
          <select wire:model="floor_id" id="floor_id" class="form-control">
            <option value="">-- Selecciona planta --</option>
            <option value="1">Basement</option>
            <option value="3">Ground floor</option>
            <option value="4">Mezzanine</option>
            <option value="6">First</option>
            <!-- Agrega más pisos según el diccionario -->
          </select>
          @error('floor_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- Orientation -->
        <div class="mb-3">
          <label for="orientacion_id"><strong>Orientación</strong></label>
          <select wire:model="orientacion_id" id="orientacion_id" class="form-control">
            <option value="">-- Selecciona orientación --</option>
            <option value="1">North east</option>
            <option value="2">West</option>
            <option value="3">North</option>
            <option value="4">South west</option>
            <option value="5">East</option>
            <option value="6">South east</option>
            <option value="7">North west</option>
            <option value="8">South</option>
          </select>
          @error('orientacion_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- Conservación estado -->
        <div class="mb-3">
          <label for="conservacion_estado_id"><strong>Estado de conservación</strong></label>
          <select wire:model="conservacion_estado_id" id="conservacion_estado_id" class="form-control">
            <option value="">-- Selecciona estado --</option>
            <option value="1">Good</option>
            <option value="2">Pretty good</option>
            <option value="3">Almost new</option>
            <option value="4">Needs renovation</option>
            <option value="6">Renovated</option>
          </select>
          @error('conservacion_estado_id') <span class="text-danger">{{ $message }}</span>@enderror
        </div>

        <!-- Conservación de más selects según lo necesites... -->

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
        <div class="mb-5 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
