@extends('layouts.app')

@section('head')
    <style>
        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            margin-bottom: 24px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }
        .form-section:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.12);
        }
        .form-section-header {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            color: white;
            padding: 20px 24px;
            font-weight: 600;
            font-size: 1.1rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .form-section-header i {
            font-size: 1.2rem;
        }
        .form-section-body {
            padding: 24px;
        }
        .form-label {
            font-weight: 600;
            color: #374151;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }
        .form-control, .form-select {
            border: 2px solid #e5e7eb;
            border-radius: 8px;
            padding: 10px 14px;
            transition: all 0.2s ease;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6b8e6b;
            box-shadow: 0 0 0 3px rgba(107, 142, 107, 0.1);
            outline: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            border: none;
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(107, 142, 107, 0.4);
        }
        .btn-secondary {
            border-radius: 8px;
            padding: 12px 24px;
            font-weight: 600;
        }
        .energy-cert-field {
            transition: opacity 0.3s ease;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="mb-4">
            <h2 class="mb-2" style="color: #1f2937; font-weight: 700;">Crear nuevo inmueble</h2>
            <p class="text-muted">Completa el formulario para añadir un nuevo inmueble al sistema</p>
        </div>
        <form action="{{ route('inmuebles.store') }}" method="POST" enctype="multipart/form-data" id="inmueble-form">
            @csrf

            <!-- Datos básicos -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-info-circle"></i>
                    Datos básicos
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Título *</strong></label>
                                <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-control"
                                    required>
                                @error('titulo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Tipo de vivienda *</strong></label>
                                <select name="tipo_vivienda_id" id="tipo_vivienda_id" class="form-control" required>
                                    <option value="">-- Elige --</option>
                                    <option value="1" {{ old('tipo_vivienda_id') == '1' ? 'selected' : '' }}>Piso</option>
                                    <option value="2" {{ old('tipo_vivienda_id') == '2' ? 'selected' : '' }}>Casa</option>
                                    <option value="3" {{ old('tipo_vivienda_id') == '3' ? 'selected' : '' }}>Local comercial</option>
                                    <option value="4" {{ old('tipo_vivienda_id') == '4' ? 'selected' : '' }}>Oficina</option>
                                    <option value="5" {{ old('tipo_vivienda_id') == '5' ? 'selected' : '' }}>Edificio</option>
                                    <option value="6" {{ old('tipo_vivienda_id') == '6' ? 'selected' : '' }}>Terreno</option>
                                    <option value="7" {{ old('tipo_vivienda_id') == '7' ? 'selected' : '' }}>Nave industrial</option>
                                    <option value="8" {{ old('tipo_vivienda_id') == '8' ? 'selected' : '' }}>Garaje</option>
                                    <option value="12" {{ old('tipo_vivienda_id') == '12' ? 'selected' : '' }}>Trastero</option>
                                </select>
                                @error('tipo_vivienda_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Subtipo de inmueble *</label>
                                <select name="building_subtype_id" id="building_subtype_id" class="form-control" required>
                                    <option value="">-- Primero selecciona el tipo --</option>
                                </select>
                                @error('building_subtype_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label><strong>Descripción</strong></label>
                        <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label><strong>Foto de la casa</strong></label>
                        <input type="file" name="imagen_principal" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</small>
                        @error('imagen_principal')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Galería de imágenes -->
                    <div class="mb-3">
                        <label><strong>Galería de imágenes</strong></label>
                        <input type="file" name="galeria[]" id="galeria-input" class="form-control" accept="image/*"
                            multiple>
                        <small class="form-text text-muted">Puedes seleccionar múltiples imágenes. Formatos permitidos: JPG,
                            PNG, GIF. Tamaño máximo: 5MB por imagen</small>
                        @error('galeria')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Preview de la galería -->
                    <div id="galeria-preview" class="row mt-3" style="display: none;">
                        <div class="col-12">
                            <h6>Vista previa de las imágenes:</h6>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Características físicas -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-ruler-combined"></i>
                    Características físicas
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>M²</strong></label>
                                <input type="number" name="m2" id="m2" value="{{ old('m2') }}" class="form-control"
                                    min="0" step="0.01">
                                @error('m2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <span class="text-danger" id="m2-error" style="display: none; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>M² Construidos</strong></label>
                                <input type="number" name="m2_construidos" id="m2_construidos" value="{{ old('m2_construidos') }}"
                                    class="form-control" min="0" step="0.01">
                                @error('m2_construidos')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <span class="text-danger" id="m2_construidos-error" style="display: none; font-size: 0.875rem;"></span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Habitaciones</strong></label>
                                <input type="number" name="habitaciones" value="{{ old('habitaciones') }}"
                                    class="form-control" min="0">
                                @error('habitaciones')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Baños</strong></label>
                                <input type="number" name="banos" value="{{ old('banos') }}" class="form-control"
                                    min="0">
                                @error('banos')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Tipo de operación *</strong></label>
                                <select name="transaction_type_id" id="transaction_type_id" class="form-control" required>
                                    <option value="">-- Selecciona --</option>
                                    <option value="1" {{ old('transaction_type_id', 1) == '1' ? 'selected' : '' }}>Venta</option>
                                    <option value="3" {{ old('transaction_type_id') == '3' ? 'selected' : '' }}>Alquiler</option>
                                </select>
                                @error('transaction_type_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label id="precio-label"><strong>Precio de venta (€)</strong></label>
                                <input type="number" name="valor_referencia" id="valor_referencia" value="{{ old('valor_referencia') }}"
                                    class="form-control" min="0" step="0.01">
                                @error('valor_referencia')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Año de construcción</strong></label>
                                <input type="number" name="year_built" value="{{ old('year_built') }}"
                                    class="form-control" min="1800" max="{{ date('Y') }}">
                                @error('year_built')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ubicación -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-map-marker-alt"></i>
                    Ubicación
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Ubicación</strong></label>
                                <input type="text" name="ubicacion" id="address-input"
                                    value="{{ old('ubicacion') }}" class="form-control"
                                    placeholder="Introduce la dirección">
                                @error('ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Código Postal *</strong></label>
                                <input type="text" name="cod_postal" id="postal-code-input"
                                    value="{{ old('cod_postal') }}" class="form-control" placeholder="Código postal" required>
                                @error('cod_postal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Mapa de Google Maps -->
                    <div class="mb-3">
                        <label><strong>Selecciona la ubicación exacta en el mapa</strong></label>
                        <div id="map"
                            style="height: 400px; width: 100%; border: 1px solid #ccc; border-radius: 4px;"></div>
                        <small class="form-text text-muted">Haz clic en el mapa para marcar la ubicación exacta del
                            inmueble</small>
                        @error('coordinates')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Campos ocultos para las coordenadas -->
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Latitud</strong></label>
                                <input type="text" id="latitude-display" class="form-control" readonly
                                    placeholder="Selecciona en el mapa">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Longitud</strong></label>
                                <input type="text" id="longitude-display" class="form-control" readonly
                                    placeholder="Selecciona en el mapa">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label><strong>Referencia catastral</strong></label>
                        <input type="text" name="referencia_catastral" value="{{ old('referencia_catastral') }}"
                            class="form-control">
                        @error('referencia_catastral')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Estado y disponibilidad -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-clipboard-check"></i>
                    Estado y disponibilidad
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Estado</strong></label>
                                <select name="estado" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="Obra nueva" {{ old('estado') == 'Obra nueva' ? 'selected' : '' }}>Obra
                                        nueva</option>
                                    <option value="Buen estado" {{ old('estado') == 'Buen estado' ? 'selected' : '' }}>
                                        Buen estado</option>
                                    <option value="A reformar" {{ old('estado') == 'A reformar' ? 'selected' : '' }}>A
                                        reformar</option>
                                </select>
                                @error('estado')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Disponibilidad</strong></label>
                                <select name="disponibilidad" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="Alquiler" {{ old('disponibilidad') == 'Alquiler' ? 'selected' : '' }}>
                                        Alquiler</option>
                                    <option value="Venta" {{ old('disponibilidad') == 'Venta' ? 'selected' : '' }}>Venta
                                    </option>
                                </select>
                                @error('disponibilidad')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Estado de conservación</strong></label>
                                <select name="conservation_status" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="Excelente"
                                        {{ old('conservation_status') == 'Excelente' ? 'selected' : '' }}>Excelente
                                    </option>
                                    <option value="Muy bueno"
                                        {{ old('conservation_status') == 'Muy bueno' ? 'selected' : '' }}>Muy bueno
                                    </option>
                                    <option value="Bueno" {{ old('conservation_status') == 'Bueno' ? 'selected' : '' }}>
                                        Bueno</option>
                                    <option value="Regular"
                                        {{ old('conservation_status') == 'Regular' ? 'selected' : '' }}>Regular</option>
                                    <option value="Necesita reforma"
                                        {{ old('conservation_status') == 'Necesita reforma' ? 'selected' : '' }}>Necesita
                                        reforma</option>
                                </select>
                                @error('conservation_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Vendedor</strong></label>
                                <select name="vendedor_id" class="form-control">
                                    <option value="">-- Elige --</option>
                                    @foreach ($vendedores as $vendedor)
                                        <option value="{{ $vendedor->id }}"
                                            {{ old('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                            {{ $vendedor->nombre_completo }}
                                            @if($vendedor->idealista_contact_id)
                                                (Idealista #{{ $vendedor->idealista_contact_id }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('vendedor_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificación energética -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-leaf"></i>
                    Certificación energética
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>¿Tiene certificado energético?</strong></label>
                                <select name="cert_energetico" id="cert_energetico" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="1" {{ old('cert_energetico') == '1' ? 'selected' : '' }}>Sí
                                    </option>
                                    <option value="0" {{ old('cert_energetico') == '0' ? 'selected' : '' }}>No
                                    </option>
                                </select>
                                @error('cert_energetico')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 energy-cert-field" style="display: none;">
                            <div class="mb-3">
                                <label><strong>Calificación energética</strong></label>
                                <select name="cert_energetico_elegido" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="A" {{ old('cert_energetico_elegido') == 'A' ? 'selected' : '' }}>A
                                    </option>
                                    <option value="B" {{ old('cert_energetico_elegido') == 'B' ? 'selected' : '' }}>B
                                    </option>
                                    <option value="C" {{ old('cert_energetico_elegido') == 'C' ? 'selected' : '' }}>C
                                    </option>
                                    <option value="D" {{ old('cert_energetico_elegido') == 'D' ? 'selected' : '' }}>D
                                    </option>
                                    <option value="E" {{ old('cert_energetico_elegido') == 'E' ? 'selected' : '' }}>E
                                    </option>
                                    <option value="F" {{ old('cert_energetico_elegido') == 'F' ? 'selected' : '' }}>F
                                    </option>
                                    <option value="G" {{ old('cert_energetico_elegido') == 'G' ? 'selected' : '' }}>G
                                    </option>
                                </select>
                                @error('cert_energetico_elegido')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-4 energy-cert-field" style="display: none;">
                            <div class="mb-3">
                                <label><strong>Estado del certificado</strong></label>
                                <select name="energy_certificate_status" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="Vigente"
                                        {{ old('energy_certificate_status') == 'Vigente' ? 'selected' : '' }}>Vigente
                                    </option>
                                    <option value="En trámite"
                                        {{ old('energy_certificate_status') == 'En trámite' ? 'selected' : '' }}>En trámite
                                    </option>
                                    <option value="Caducado"
                                        {{ old('energy_certificate_status') == 'Caducado' ? 'selected' : '' }}>Caducado
                                    </option>
                                </select>
                                @error('energy_certificate_status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Nuevos campos para Fotocasa -->
                    <div class="row energy-cert-field" style="display: none;">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Escala de consumo energético</strong></label>
                                <select name="consumption_efficiency_scale" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="A"
                                        {{ old('consumption_efficiency_scale') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B"
                                        {{ old('consumption_efficiency_scale') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C"
                                        {{ old('consumption_efficiency_scale') == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D"
                                        {{ old('consumption_efficiency_scale') == 'D' ? 'selected' : '' }}>D</option>
                                    <option value="E"
                                        {{ old('consumption_efficiency_scale') == 'E' ? 'selected' : '' }}>E</option>
                                    <option value="F"
                                        {{ old('consumption_efficiency_scale') == 'F' ? 'selected' : '' }}>F</option>
                                    <option value="G"
                                        {{ old('consumption_efficiency_scale') == 'G' ? 'selected' : '' }}>G</option>
                                </select>
                                @error('consumption_efficiency_scale')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Escala de emisiones CO₂</strong></label>
                                <select name="emissions_efficiency_scale" class="form-control">
                                    <option value="">-- Elige --</option>
                                    <option value="A"
                                        {{ old('emissions_efficiency_scale') == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B"
                                        {{ old('emissions_efficiency_scale') == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="C"
                                        {{ old('emissions_efficiency_scale') == 'C' ? 'selected' : '' }}>C</option>
                                    <option value="D"
                                        {{ old('emissions_efficiency_scale') == 'D' ? 'selected' : '' }}>D</option>
                                    <option value="E"
                                        {{ old('emissions_efficiency_scale') == 'E' ? 'selected' : '' }}>E</option>
                                    <option value="F"
                                        {{ old('emissions_efficiency_scale') == 'F' ? 'selected' : '' }}>F</option>
                                    <option value="G"
                                        {{ old('emissions_efficiency_scale') == 'G' ? 'selected' : '' }}>G</option>
                                </select>
                                @error('emissions_efficiency_scale')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Valor de consumo (kWh/m²)</strong></label>
                                <input type="number" name="consumption_efficiency_value"
                                    value="{{ old('consumption_efficiency_value') }}" class="form-control"
                                    min="0" step="0.1">
                                @error('consumption_efficiency_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>Valor de emisiones (kg CO₂/m²)</strong></label>
                                <input type="number" name="emissions_efficiency_value"
                                    value="{{ old('emissions_efficiency_value') }}" class="form-control" min="0"
                                    step="0.1">
                                @error('emissions_efficiency_value')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Características del inmueble -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-list-check"></i>
                    Características del inmueble
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="furnished" value="1"
                                    {{ old('furnished') ? 'checked' : '' }}>
                                <label class="form-check-label">Amueblado</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_elevator" value="1"
                                    {{ old('has_elevator') ? 'checked' : '' }}>
                                <label class="form-check-label">Ascensor</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_terrace" value="1"
                                    {{ old('has_terrace') ? 'checked' : '' }}>
                                <label class="form-check-label">Terraza</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_balcony" value="1"
                                    {{ old('has_balcony') ? 'checked' : '' }}>
                                <label class="form-check-label">Balcón</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_parking" value="1"
                                    {{ old('has_parking') ? 'checked' : '' }}>
                                <label class="form-check-label">Parking</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_air_conditioning"
                                    value="1" {{ old('has_air_conditioning') ? 'checked' : '' }}>
                                <label class="form-check-label">Aire acondicionado</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_heating" value="1"
                                    {{ old('has_heating') ? 'checked' : '' }}>
                                <label class="form-check-label">Calefacción</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_security_door" value="1"
                                    {{ old('has_security_door') ? 'checked' : '' }}>
                                <label class="form-check-label">Puerta de seguridad</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_equipped_kitchen"
                                    value="1" {{ old('has_equipped_kitchen') ? 'checked' : '' }}>
                                <label class="form-check-label">Cocina equipada</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_wardrobe" value="1"
                                    {{ old('has_wardrobe') ? 'checked' : '' }}>
                                <label class="form-check-label">Armarios empotrados</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_storage_room" value="1"
                                    {{ old('has_storage_room') ? 'checked' : '' }}>
                                <label class="form-check-label">Trastero</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="pets_allowed" value="1"
                                    {{ old('pets_allowed') ? 'checked' : '' }}>
                                <label class="form-check-label">Mascotas permitidas</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_private_garden" value="1"
                                    {{ old('has_private_garden') ? 'checked' : '' }}>
                                <label class="form-check-label">Jardín privado</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_yard" value="1"
                                    {{ old('has_yard') ? 'checked' : '' }}>
                                <label class="form-check-label">Patio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_smoke_outlet" value="1"
                                    {{ old('has_smoke_outlet') ? 'checked' : '' }}>
                                <label class="form-check-label">Salida de humos</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_community_pool" value="1"
                                    {{ old('has_community_pool') ? 'checked' : '' }}>
                                <label class="form-check-label">Piscina comunitaria</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_private_pool" value="1"
                                    {{ old('has_private_pool') ? 'checked' : '' }}>
                                <label class="form-check-label">Piscina privada</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_loading_area" value="1"
                                    {{ old('has_loading_area') ? 'checked' : '' }}>
                                <label class="form-check-label">Zona de carga</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_24h_access" value="1"
                                    {{ old('has_24h_access') ? 'checked' : '' }}>
                                <label class="form-check-label">Acceso 24h</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_internal_transport"
                                    value="1" {{ old('has_internal_transport') ? 'checked' : '' }}>
                                <label class="form-check-label">Transporte interno</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_alarm" value="1"
                                    {{ old('has_alarm') ? 'checked' : '' }}>
                                <label class="form-check-label">Alarma</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_access_code" value="1"
                                    {{ old('has_access_code') ? 'checked' : '' }}>
                                <label class="form-check-label">Código de acceso</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_free_parking" value="1"
                                    {{ old('has_free_parking') ? 'checked' : '' }}>
                                <label class="form-check-label">Parking gratuito</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_laundry" value="1"
                                    {{ old('has_laundry') ? 'checked' : '' }}>
                                <label class="form-check-label">Lavandería</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_community_area" value="1"
                                    {{ old('has_community_area') ? 'checked' : '' }}>
                                <label class="form-check-label">Zona comunitaria</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_office_kitchen" value="1"
                                    {{ old('has_office_kitchen') ? 'checked' : '' }}>
                                <label class="form-check-label">Cocina de oficina</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_jacuzzi" value="1"
                                    {{ old('has_jacuzzi') ? 'checked' : '' }}>
                                <label class="form-check-label">Jacuzzi</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_sauna" value="1"
                                    {{ old('has_sauna') ? 'checked' : '' }}>
                                <label class="form-check-label">Sauna</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_tennis_court" value="1"
                                    {{ old('has_tennis_court') ? 'checked' : '' }}>
                                <label class="form-check-label">Pista de tenis</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_gym" value="1"
                                    {{ old('has_gym') ? 'checked' : '' }}>
                                <label class="form-check-label">Gimnasio</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_sports_area" value="1"
                                    {{ old('has_sports_area') ? 'checked' : '' }}>
                                <label class="form-check-label">Zona deportiva</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_children_area" value="1"
                                    {{ old('has_children_area') ? 'checked' : '' }}>
                                <label class="form-check-label">Zona infantil</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_home_automation"
                                    value="1" {{ old('has_home_automation') ? 'checked' : '' }}>
                                <label class="form-check-label">Domótica</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_internet" value="1"
                                    {{ old('has_internet') ? 'checked' : '' }}>
                                <label class="form-check-label">Internet</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_suite_bathroom" value="1"
                                    {{ old('has_suite_bathroom') ? 'checked' : '' }}>
                                <label class="form-check-label">Baño suite</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_home_appliances"
                                    value="1" {{ old('has_home_appliances') ? 'checked' : '' }}>
                                <label class="form-check-label">Electrodomésticos</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_oven" value="1"
                                    {{ old('has_oven') ? 'checked' : '' }}>
                                <label class="form-check-label">Horno</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_washing_machine"
                                    value="1" {{ old('has_washing_machine') ? 'checked' : '' }}>
                                <label class="form-check-label">Lavadora</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_microwave" value="1"
                                    {{ old('has_microwave') ? 'checked' : '' }}>
                                <label class="form-check-label">Microondas</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_fridge" value="1"
                                    {{ old('has_fridge') ? 'checked' : '' }}>
                                <label class="form-check-label">Frigorífico</label>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_tv" value="1"
                                    {{ old('has_tv') ? 'checked' : '' }}>
                                <label class="form-check-label">TV</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_parquet" value="1"
                                    {{ old('has_parquet') ? 'checked' : '' }}>
                                <label class="form-check-label">Parquet</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="has_stoneware" value="1"
                                    {{ old('has_stoneware') ? 'checked' : '' }}>
                                <label class="form-check-label">Gres</label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="nearby_public_transport"
                                    value="1" {{ old('nearby_public_transport') ? 'checked' : '' }}>
                                <label class="form-check-label">Transporte público cercano</label>
                            </div>
                        </div>
                    </div>

                    <!-- Superficie de terraza -->
                    <div class="row" id="terrace-surface-row" style="display: none;">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Superficie de terraza (m²)</strong></label>
                                <input type="number" name="terrace_surface" value="{{ old('terrace_surface') }}"
                                    class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>

                    <!-- Superficie del terreno -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Superficie del terreno (m²)</strong></label>
                                <input type="number" name="land_area" value="{{ old('land_area') }}"
                                    class="form-control" min="0" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuración de Fotocasa -->
            <div class="form-section">
                <h5 class="form-section-header">
                    <i class="fas fa-cog"></i>
                    Configuración para Fotocasa
                </h5>
                <div class="form-section-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Tipo de transacción</strong></label>
                                <select name="transaction_type_id" class="form-control">
                                    <option value="1" {{ old('transaction_type_id') == '1' ? 'selected' : '' }}>
                                        Venta</option>
                                    <option value="3" {{ old('transaction_type_id') == '3' ? 'selected' : '' }}>
                                        Alquiler</option>
                                    <option value="4" {{ old('transaction_type_id') == '4' ? 'selected' : '' }}>
                                        Traspaso</option>
                                    <option value="7" {{ old('transaction_type_id') == '7' ? 'selected' : '' }}>
                                        Compartir</option>
                                    <option value="9" {{ old('transaction_type_id') == '9' ? 'selected' : '' }}>
                                        Alquiler con opción de compra</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Modo de visibilidad</strong></label>
                                <select name="visibility_mode_id" class="form-control">
                                    <option value="1" {{ old('visibility_mode_id') == '1' ? 'selected' : '' }}>
                                        Exacta</option>
                                    <option value="2" {{ old('visibility_mode_id') == '2' ? 'selected' : '' }}>Calle
                                    </option>
                                    <option value="3" {{ old('visibility_mode_id') == '3' ? 'selected' : '' }}>Zona
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Planta</strong></label>
                                <select name="floor_id" class="form-control">
                                    <option value="">-- Selecciona --</option>
                                    <option value="1" {{ old('floor_id') == '1' ? 'selected' : '' }}>Sótano</option>
                                    <option value="3" {{ old('floor_id') == '3' ? 'selected' : '' }}>Planta baja
                                    </option>
                                    <option value="4" {{ old('floor_id') == '4' ? 'selected' : '' }}>Entresuelo
                                    </option>
                                    <option value="6" {{ old('floor_id') == '6' ? 'selected' : '' }}>Primera
                                    </option>
                                    <option value="7" {{ old('floor_id') == '7' ? 'selected' : '' }}>Segunda
                                    </option>
                                    <option value="8" {{ old('floor_id') == '8' ? 'selected' : '' }}>Tercera
                                    </option>
                                    <option value="9" {{ old('floor_id') == '9' ? 'selected' : '' }}>Cuarta</option>
                                    <option value="10" {{ old('floor_id') == '10' ? 'selected' : '' }}>Quinta
                                    </option>
                                    <option value="11" {{ old('floor_id') == '11' ? 'selected' : '' }}>Sexta</option>
                                    <option value="12" {{ old('floor_id') == '12' ? 'selected' : '' }}>Séptima
                                    </option>
                                    <option value="13" {{ old('floor_id') == '13' ? 'selected' : '' }}>Octava
                                    </option>
                                    <option value="14" {{ old('floor_id') == '14' ? 'selected' : '' }}>Novena
                                    </option>
                                    <option value="15" {{ old('floor_id') == '15' ? 'selected' : '' }}>Décima
                                    </option>
                                    <option value="16" {{ old('floor_id') == '16' ? 'selected' : '' }}>Décima en
                                        adelante</option>
                                    <option value="22" {{ old('floor_id') == '22' ? 'selected' : '' }}>Ático</option>
                                    <option value="31" {{ old('floor_id') == '31' ? 'selected' : '' }}>Otro</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Orientación</strong></label>
                                <select name="orientation_id" class="form-control">
                                    <option value="">-- Selecciona --</option>
                                    <option value="1" {{ old('orientation_id') == '1' ? 'selected' : '' }}>Noreste
                                    </option>
                                    <option value="2" {{ old('orientation_id') == '2' ? 'selected' : '' }}>Oeste
                                    </option>
                                    <option value="3" {{ old('orientation_id') == '3' ? 'selected' : '' }}>Norte
                                    </option>
                                    <option value="4" {{ old('orientation_id') == '4' ? 'selected' : '' }}>Suroeste
                                    </option>
                                    <option value="5" {{ old('orientation_id') == '5' ? 'selected' : '' }}>Este
                                    </option>
                                    <option value="6" {{ old('orientation_id') == '6' ? 'selected' : '' }}>Sureste
                                    </option>
                                    <option value="7" {{ old('orientation_id') == '7' ? 'selected' : '' }}>Noroeste
                                    </option>
                                    <option value="8" {{ old('orientation_id') == '8' ? 'selected' : '' }}>Sur
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Tipo de calefacción</strong></label>
                                <select name="heating_type_id" class="form-control">
                                    <option value="">-- Selecciona --</option>
                                    <option value="1" {{ old('heating_type_id') == '1' ? 'selected' : '' }}>Gas
                                        natural</option>
                                    <option value="2" {{ old('heating_type_id') == '2' ? 'selected' : '' }}>
                                        Eléctrica</option>
                                    <option value="3" {{ old('heating_type_id') == '3' ? 'selected' : '' }}>Gasóleo
                                    </option>
                                    <option value="4" {{ old('heating_type_id') == '4' ? 'selected' : '' }}>Butano
                                    </option>
                                    <option value="5" {{ old('heating_type_id') == '5' ? 'selected' : '' }}>Propano
                                    </option>
                                    <option value="6" {{ old('heating_type_id') == '6' ? 'selected' : '' }}>Solar
                                    </option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>Tipo de agua caliente</strong></label>
                                <select name="hot_water_type_id" class="form-control">
                                    <option value="">-- Selecciona --</option>
                                    <option value="1" {{ old('hot_water_type_id') == '1' ? 'selected' : '' }}>Gas
                                        natural</option>
                                    <option value="2" {{ old('hot_water_type_id') == '2' ? 'selected' : '' }}>
                                        Eléctrica</option>
                                    <option value="3" {{ old('hot_water_type_id') == '3' ? 'selected' : '' }}>
                                        Gasóleo</option>
                                    <option value="4" {{ old('hot_water_type_id') == '4' ? 'selected' : '' }}>Butano
                                    </option>
                                    <option value="5" {{ old('hot_water_type_id') == '5' ? 'selected' : '' }}>
                                        Propano</option>
                                    <option value="6" {{ old('hot_water_type_id') == '6' ? 'selected' : '' }}>Solar
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" name="mostrar_precio" value="1"
                                    {{ old('mostrar_precio') ? 'checked' : '' }}>
                                <label class="form-check-label">Mostrar precio en publicaciones</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="card mb-3">
                <div class="form-section-body">
                    <div class="text-end">
                        <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">Guardar inmueble</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        // Mapeo de tipos locales a Fotocasa y sus subtipos correspondientes
        const buildingTypeMapping = {
            1: { // Piso
                fotocasaType: 1,
                subtypes: [{
                        id: 2,
                        name: 'Tríplex'
                    },
                    {
                        id: 3,
                        name: 'Dúplex'
                    },
                    {
                        id: 5,
                        name: 'Ático'
                    },
                    {
                        id: 6,
                        name: 'Estudio'
                    },
                    {
                        id: 7,
                        name: 'Loft'
                    },
                    {
                        id: 9,
                        name: 'Piso'
                    },
                    {
                        id: 10,
                        name: 'Apartamento'
                    },
                    {
                        id: 11,
                        name: 'Bajo'
                    }
                ]
            },
            2: { // Casa
                fotocasaType: 2,
                subtypes: [{
                        id: 13,
                        name: 'Casa'
                    },
                    {
                        id: 17,
                        name: 'Casa adosada'
                    },
                    {
                        id: 19,
                        name: 'Casa pareada'
                    },
                    {
                        id: 20,
                        name: 'Chalet'
                    },
                    {
                        id: 24,
                        name: 'Casa rústica'
                    },
                    {
                        id: 27,
                        name: 'Bungalow'
                    }
                ]
            },
            3: { // Local comercial
                fotocasaType: 3,
                subtypes: [{
                        id: 48,
                        name: 'Residencial'
                    },
                    {
                        id: 49,
                        name: 'Otros'
                    },
                    {
                        id: 50,
                        name: 'Mixto residencial'
                    },
                    {
                        id: 51,
                        name: 'Oficinas'
                    },
                    {
                        id: 72,
                        name: 'Hotel'
                    }
                ]
            },
            4: { // Oficina
                fotocasaType: 4,
                subtypes: [{
                        id: 56,
                        name: 'Suelo residencial'
                    },
                    {
                        id: 60,
                        name: 'Suelo industrial'
                    },
                    {
                        id: 91,
                        name: 'Suelo rústico'
                    }
                ]
            },
            5: { // Edificio
                fotocasaType: 5,
                subtypes: [{
                        id: 48,
                        name: 'Residencial'
                    },
                    {
                        id: 49,
                        name: 'Otros'
                    },
                    {
                        id: 50,
                        name: 'Mixto residencial'
                    },
                    {
                        id: 51,
                        name: 'Oficinas'
                    },
                    {
                        id: 72,
                        name: 'Hotel'
                    }
                ]
            },
            6: { // Terreno
                fotocasaType: 6,
                subtypes: [{
                        id: 56,
                        name: 'Suelo residencial'
                    },
                    {
                        id: 60,
                        name: 'Suelo industrial'
                    },
                    {
                        id: 91,
                        name: 'Suelo rústico'
                    }
                ]
            },
            7: { // Nave industrial
                fotocasaType: 7,
                subtypes: [{
                        id: 62,
                        name: 'Moto'
                    },
                    {
                        id: 63,
                        name: 'Doble'
                    }
                ]
            },
            8: { // Garaje
                fotocasaType: 8,
                subtypes: [{
                        id: 68,
                        name: 'Moto'
                    },
                    {
                        id: 69,
                        name: 'Doble'
                    },
                    {
                        id: 70,
                        name: 'Individual'
                    }
                ]
            },
            12: { // Trastero
                fotocasaType: 12,
                subtypes: [{
                    id: 90,
                    name: 'Suelos'
                }]
            }
        };

        // Función para actualizar los subtipos disponibles
        function updateSubtypes() {
            const tipoSelect = document.getElementById('tipo_vivienda_id');
            const subtypeSelect = document.getElementById('building_subtype_id');
            const selectedTipoId = parseInt(tipoSelect.value);

            // Limpiar opciones actuales
            subtypeSelect.innerHTML = '<option value="">-- Elige subtipo --</option>';

            if (selectedTipoId && buildingTypeMapping[selectedTipoId]) {
                const subtypes = buildingTypeMapping[selectedTipoId].subtypes;
                subtypes.forEach(subtype => {
                    const option = document.createElement('option');
                    option.value = subtype.id;
                    option.textContent = subtype.name;
                    subtypeSelect.appendChild(option);
                });
            }
        }

        // Event listener para el cambio de tipo de vivienda
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo_vivienda_id');

            if (tipoSelect) {
                tipoSelect.addEventListener('change', updateSubtypes);

                // Inicializar subtipos si ya hay un valor seleccionado
                if (tipoSelect.value) {
                    updateSubtypes();

                    // Restaurar el valor seleccionado anteriormente si existe
                    const subtypeSelect = document.getElementById('building_subtype_id');
                    const oldSubtypeValue = '{{ old('building_subtype_id') }}';
                    if (oldSubtypeValue) {
                        subtypeSelect.value = oldSubtypeValue;
                    }
                }
            }

            // Lógica para mostrar/ocultar campos de certificado energético
            const certEnergeticoSelect = document.getElementById('cert_energetico');
            const energyCertFields = document.querySelectorAll('.energy-cert-field');

            function toggleEnergyCertFields() {
                const hasCert = certEnergeticoSelect && certEnergeticoSelect.value === '1';
                energyCertFields.forEach(field => {
                    field.style.display = hasCert ? '' : 'none';
                });
            }

            // Ejecutar al cargar la página
            if (certEnergeticoSelect) {
                toggleEnergyCertFields();
                // Ejecutar cuando cambie el select
                certEnergeticoSelect.addEventListener('change', toggleEnergyCertFields);
            }
        });
    </script>

    <!-- Leaflet Maps -->
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
    <!-- Leaflet JavaScript -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
    <script>
        // Variables globales para el mapa
        let map;
        let marker;
        let addressInput;
        let autocompleteTimeout;

        function initMap() {
            // Coordenadas por defecto (Madrid)
            const defaultLat = 40.4168;
            const defaultLng = -3.7038;

            // Inicializar el mapa
            map = L.map('map').setView([defaultLat, defaultLng], 13);

            // Añadir capa de tiles de OpenStreetMap
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);

            // Crear icono personalizado para el marcador
            const customIcon = L.divIcon({
                className: 'custom-marker',
                html: '<div style="width: 30px; height: 30px; border-radius: 50%; background: #007bff; border: 3px solid white; box-shadow: 0 2px 8px rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center;"><div style="width: 10px; height: 10px; border-radius: 50%; background: white;"></div></div>',
                iconSize: [30, 30],
                iconAnchor: [15, 15]
            });

            // Crear el marcador
            marker = L.marker([defaultLat, defaultLng], {
                draggable: true,
                icon: customIcon
            }).addTo(map);

            // Autocompletado para el campo de dirección usando Nominatim
            addressInput = document.getElementById('address-input');
            let autocompleteResults = [];
            let selectedIndex = -1;

            addressInput.addEventListener('input', function() {
                clearTimeout(autocompleteTimeout);
                const query = addressInput.value.trim();

                if (query.length < 2) {
                    removeAutocomplete();
                    return;
                }

                autocompleteTimeout = setTimeout(() => {
                    // Búsqueda más flexible: incluye zonas, barriadas, barrios, etc.
                    // Aumentamos el límite para mostrar más opciones
                    fetch(`{{ route('nominatim.search') }}?q=${encodeURIComponent(query)}&countrycodes=es&limit=10&addressdetails=1`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.error) {
                                console.error('Error en autocompletado:', data.error);
                                return;
                            }
                            // Filtrar y ordenar resultados para priorizar zonas y lugares relevantes
                            const filteredData = data.filter(item => {
                                const type = item.type || '';
                                const classType = item.class || '';
                                // Incluir: lugares, zonas, barrios, barriadas, calles, etc.
                                return ['place', 'boundary', 'highway', 'amenity', 'landuse'].includes(classType) ||
                                       type.includes('residential') ||
                                       type.includes('neighbourhood') ||
                                       type.includes('suburb') ||
                                       type.includes('quarter') ||
                                       item.display_name.toLowerCase().includes('barriada') ||
                                       item.display_name.toLowerCase().includes('barrio') ||
                                       item.display_name.toLowerCase().includes('zona');
                            });

                            // Si no hay resultados filtrados, usar todos
                            autocompleteResults = filteredData.length > 0 ? filteredData : data;
                            showAutocomplete(autocompleteResults);
                        })
                        .catch(error => console.error('Error en autocompletado:', error));
                }, 300);
            });

            function showAutocomplete(results) {
                removeAutocomplete();
                if (results.length === 0) return;

                const list = document.createElement('ul');
                list.id = 'autocomplete-list';
                list.className = 'list-group';
                list.style.position = 'absolute';
                list.style.zIndex = '1000';
                list.style.width = addressInput.offsetWidth + 'px';
                list.style.maxHeight = '300px';
                list.style.overflowY = 'auto';
                list.style.marginTop = '2px';
                list.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
                list.style.borderRadius = '4px';

                results.slice(0, 10).forEach((result, index) => {
                    const item = document.createElement('li');
                    item.className = 'list-group-item';
                    item.style.cursor = 'pointer';
                    item.style.padding = '10px 15px';
                    item.style.borderBottom = '1px solid #e9ecef';

                    // Mostrar nombre y tipo de lugar
                    const displayName = result.display_name;
                    const type = result.type || result.class || '';
                    const address = result.address || {};

                    // Construir texto descriptivo
                    let itemText = displayName;
                    const zone = address.neighbourhood || address.suburb || address.quarter;
                    if (zone) {
                        itemText = `${zone} - ${displayName}`;
                    }

                    // Agregar badge con tipo si es relevante
                    const typeLabel = getTypeLabel(result);
                    if (typeLabel) {
                        item.innerHTML = `
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong>${displayName}</strong>
                                    ${zone ? `<br><small style="color: #6c757d;">${zone}</small>` : ''}
                                </div>
                                <span class="badge bg-secondary" style="font-size: 0.7rem;">${typeLabel}</span>
                            </div>
                        `;
                    } else {
                        item.textContent = itemText;
                    }

                    item.addEventListener('click', () => selectAddress(result));
                    item.addEventListener('mouseenter', () => {
                        item.style.backgroundColor = '#f8f9fa';
                    });
                    item.addEventListener('mouseleave', () => {
                        item.style.backgroundColor = '';
                    });
                    list.appendChild(item);
                });

                addressInput.parentNode.style.position = 'relative';
                addressInput.parentNode.appendChild(list);
            }

            function getTypeLabel(result) {
                const type = result.type || '';
                const classType = result.class || '';
                const address = result.address || {};

                if (address.neighbourhood || address.suburb || address.quarter) {
                    return 'Zona';
                }
                if (classType === 'place' && (type.includes('neighbourhood') || type.includes('suburb'))) {
                    return 'Barrio';
                }
                if (type.includes('residential')) {
                    return 'Residencial';
                }
                if (classType === 'highway') {
                    return 'Calle';
                }
                if (classType === 'amenity') {
                    return 'Lugar';
                }
                return '';
            }

            function removeAutocomplete() {
                const list = document.getElementById('autocomplete-list');
                if (list) list.remove();
            }

            function selectAddress(result) {
                const lat = parseFloat(result.lat);
                const lng = parseFloat(result.lon);

                map.setView([lat, lng], 16);
                marker.setLatLng([lat, lng]);

                updateCoordinates(lat, lng);

                addressInput.value = result.display_name;

                // Extraer código postal
                if (result.address && result.address.postcode) {
                    document.getElementById('postal-code-input').value = result.address.postcode;
                }

                removeAutocomplete();
            }

            // Cerrar autocompletado al hacer clic fuera
            document.addEventListener('click', function(e) {
                if (!addressInput.contains(e.target) && !document.getElementById('autocomplete-list')?.contains(e.target)) {
                    removeAutocomplete();
                }
            });

            // Evento cuando se hace clic en el mapa
            map.on('click', function(e) {
                const lat = e.latlng.lat;
                const lng = e.latlng.lng;
                marker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
                reverseGeocode(lat, lng);
            });

            // Evento cuando se arrastra el marcador
            marker.on('dragend', function(e) {
                const lat = e.target.getLatLng().lat;
                const lng = e.target.getLatLng().lng;
                updateCoordinates(lat, lng);
                reverseGeocode(lat, lng);
            });

            // Función para geocodificación inversa (coordenadas -> dirección)
            function reverseGeocode(lat, lng) {
                fetch(`{{ route('nominatim.reverse') }}?lat=${lat}&lon=${lng}&addressdetails=1&zoom=18`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error('Error en geocodificación inversa:', data.error);
                            return;
                        }
                        if (data.display_name) {
                            addressInput.value = data.display_name;
                        }
                        if (data.address && data.address.postcode) {
                            document.getElementById('postal-code-input').value = data.address.postcode;
                        }
                    })
                    .catch(error => console.error('Error en geocodificación inversa:', error));
            }

            // Botón para buscar dirección
            const searchButton = document.createElement('button');
            searchButton.textContent = 'Buscar';
            searchButton.className = 'btn btn-primary btn-sm';
            searchButton.style.marginTop = '10px';
            searchButton.type = 'button'; // Evitar que sea submit
            searchButton.setAttribute('onclick', 'searchAddress()');

            const mapContainer = document.getElementById('map').parentNode;
            mapContainer.appendChild(searchButton);
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('latitude-display').value = lat.toFixed(6);
            document.getElementById('longitude-display').value = lng.toFixed(6);
        }

        function searchAddress() {
            // Verificar que el mapa esté inicializado
            if (!map || !marker) {
                alert('El mapa no está inicializado. Por favor, espera a que se cargue completamente.');
                return;
            }

            const address = document.getElementById('address-input').value;
            const postalCode = document.getElementById('postal-code-input').value;

            if (!address || address.trim() === '') {
                alert('Por favor, introduce una dirección, zona o barriada para buscar.');
                return;
            }

            let searchQuery = address.trim();
            // Si no parece una dirección exacta (no tiene número), buscar como zona
            const hasNumber = /\d/.test(searchQuery);

            if (postalCode && postalCode.trim()) {
                searchQuery += ', ' + postalCode.trim();
            }

            // Agregar España al final para mejorar resultados
            if (!searchQuery.toLowerCase().includes('spain') && !searchQuery.toLowerCase().includes('españa')) {
                searchQuery += ', Spain';
            }

            // Mostrar indicador de carga
            const searchButton = document.querySelector('button[onclick="searchAddress()"]');
            const originalText = searchButton ? searchButton.textContent : '';
            if (searchButton) {
                searchButton.disabled = true;
                searchButton.textContent = 'Buscando...';
            }

            // Si es una búsqueda por zona (sin número), usar zoom más amplio y más resultados
            const limit = hasNumber ? 1 : 5;
            const zoom = hasNumber ? 16 : 14;

            fetch(`{{ route('nominatim.search') }}?q=${encodeURIComponent(searchQuery)}&countrycodes=es&limit=${limit}&addressdetails=1`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.length > 0) {
                        // Si hay múltiples resultados y es búsqueda por zona, usar el primero (más relevante)
                        const result = data[0];
                        const lat = parseFloat(result.lat);
                        const lng = parseFloat(result.lon);

                        if (isNaN(lat) || isNaN(lng)) {
                            throw new Error('Coordenadas inválidas recibidas');
                        }

                        // Zoom más amplio para zonas, más cercano para direcciones exactas
                        map.setView([lat, lng], zoom);
                        marker.setLatLng([lat, lng]);
                        updateCoordinates(lat, lng);

                        if (addressInput) {
                            // Si es una zona, mostrar el nombre completo; si es dirección, mantener el original
                            addressInput.value = hasNumber ? (result.display_name || address) : result.display_name;
                        }

                        if (result.address && result.address.postcode) {
                            const postalInput = document.getElementById('postal-code-input');
                            if (postalInput) {
                                postalInput.value = result.address.postcode;
                            }
                        }

                        // Si hay múltiples resultados y es búsqueda por zona, mostrar mensaje informativo
                        if (data.length > 1 && !hasNumber) {
                            console.log(`Se encontraron ${data.length} resultados. Se ha seleccionado el más relevante.`);
                        }
                    } else {
                        alert('No se pudo encontrar la dirección o zona. Por favor, verifica que el nombre sea correcto.');
                    }
                })
                .catch(error => {
                    console.error('Error en búsqueda:', error);
                    alert('Error al buscar la dirección: ' + error.message + '. Por favor, intenta de nuevo.');
                })
                .finally(() => {
                    // Restaurar botón
                    if (searchButton) {
                        searchButton.disabled = false;
                        searchButton.textContent = originalText;
                    }
                });
        }

        // Inicializar el mapa cuando se carga la página
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // Restaurar valores si existen (en caso de error de validación)
            const oldLat = '{{ old('latitude') }}';
            const oldLng = '{{ old('longitude') }}';

            if (oldLat && oldLng) {
                const lat = parseFloat(oldLat);
                const lng = parseFloat(oldLng);
                map.setView([lat, lng], 15);
                marker.setLatLng([lat, lng]);
                updateCoordinates(lat, lng);
            }

            // Mostrar/ocultar campo de superficie de terraza
            const terraceCheckbox = document.querySelector('input[name="has_terrace"]');
            const terraceSurfaceRow = document.getElementById('terrace-surface-row');

            function toggleTerraceSurface() {
                if (terraceCheckbox.checked) {
                    terraceSurfaceRow.style.display = 'block';
                } else {
                    terraceSurfaceRow.style.display = 'none';
                }
            }

            // Ejecutar al cargar la página
            toggleTerraceSurface();

            // Ejecutar cuando cambie el checkbox
            terraceCheckbox.addEventListener('change', toggleTerraceSurface);

            // Validación de áreas: m2_construidos debe ser mayor que m2
            const m2Input = document.getElementById('m2');
            const m2ConstruidosInput = document.getElementById('m2_construidos');
            const m2Error = document.getElementById('m2-error');
            const m2ConstruidosError = document.getElementById('m2_construidos-error');
            const form = document.getElementById('inmueble-form');

            function validateAreas() {
                const m2 = parseFloat(m2Input.value) || 0;
                const m2Construidos = parseFloat(m2ConstruidosInput.value) || 0;
                let isValid = true;

                // Limpiar errores previos
                m2Error.style.display = 'none';
                m2Error.textContent = '';
                m2ConstruidosError.style.display = 'none';
                m2ConstruidosError.textContent = '';

                // Solo validar si ambos campos tienen valores
                if (m2 > 0 && m2Construidos > 0) {
                    if (m2Construidos <= m2) {
                        isValid = false;
                        const errorMessage = 'Los m² construidos deben ser mayores que los m² útiles. Idealista requiere que el área construida sea mayor que el área útil.';
                        m2ConstruidosError.textContent = errorMessage;
                        m2ConstruidosError.style.display = 'block';
                        m2ConstruidosInput.classList.add('is-invalid');
                        m2Input.classList.add('is-invalid');
                    } else {
                        m2ConstruidosInput.classList.remove('is-invalid');
                        m2Input.classList.remove('is-invalid');
                    }
                } else {
                    // Si no hay ambos valores, quitar clases de error
                    m2ConstruidosInput.classList.remove('is-invalid');
                    m2Input.classList.remove('is-invalid');
                }

                return isValid;
            }

            // Validar cuando cambien los valores
            m2Input.addEventListener('input', validateAreas);
            m2Input.addEventListener('blur', validateAreas);
            m2ConstruidosInput.addEventListener('input', validateAreas);
            m2ConstruidosInput.addEventListener('blur', validateAreas);

            // Validación de código postal
            const postalCodeInput = document.getElementById('postal-code-input');

            function validatePostalCode() {
                const postalCode = postalCodeInput.value.trim();
                if (!postalCode || postalCode === '') {
                    postalCodeInput.classList.add('is-invalid');
                    return false;
                } else {
                    postalCodeInput.classList.remove('is-invalid');
                    return true;
                }
            }

            // Validar cuando cambie el valor
            postalCodeInput.addEventListener('blur', validatePostalCode);
            postalCodeInput.addEventListener('input', function() {
                if (this.value.trim()) {
                    this.classList.remove('is-invalid');
                }
            });

            // Validación de coordenadas (obligatorias para Idealista)
            const latitudeInput = document.querySelector('input[name="latitude"]');
            const longitudeInput = document.querySelector('input[name="longitude"]');

            function validateCoordinates() {
                const lat = parseFloat(latitudeInput?.value) || 0;
                const lng = parseFloat(longitudeInput?.value) || 0;

                if (!latitudeInput || !longitudeInput || !lat || !lng || lat === 0 || lng === 0) {
                    if (latitudeInput) latitudeInput.classList.add('is-invalid');
                    if (longitudeInput) longitudeInput.classList.add('is-invalid');
                    // Mostrar mensaje en el mapa
                    const mapContainer = document.getElementById('map');
                    if (mapContainer) {
                        const errorDiv = document.createElement('div');
                        errorDiv.id = 'map-error';
                        errorDiv.className = 'text-danger mt-2';
                        errorDiv.style.fontSize = '0.875rem';
                        errorDiv.textContent = 'Debes seleccionar una ubicación en el mapa haciendo clic en él.';
                        // Eliminar error previo si existe
                        const prevError = document.getElementById('map-error');
                        if (prevError) prevError.remove();
                        mapContainer.parentElement.appendChild(errorDiv);
                    }
                    return false;
                } else {
                    if (latitudeInput) latitudeInput.classList.remove('is-invalid');
                    if (longitudeInput) longitudeInput.classList.remove('is-invalid');
                    // Eliminar mensaje de error del mapa
                    const mapError = document.getElementById('map-error');
                    if (mapError) mapError.remove();
                    return true;
                }
            }

            // Validar antes de enviar el formulario
            form.addEventListener('submit', function(e) {
                let hasErrors = false;

                // Validar áreas
                if (!validateAreas()) {
                    hasErrors = true;
                }

                // Validar código postal
                if (!validatePostalCode()) {
                    hasErrors = true;
                }

                // Validar coordenadas
                if (!validateCoordinates()) {
                    hasErrors = true;
                }

                if (hasErrors) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Scroll al primer error
                    const firstError = document.querySelector('.is-invalid');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstError.focus();
                    } else {
                        // Si no hay campo con error, hacer scroll al mapa
                        const mapContainer = document.getElementById('map');
                        if (mapContainer) {
                            mapContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    }

                    // Mostrar alerta
                    let errorMessage = 'Por favor, corrige los siguientes errores antes de continuar:\n\n';
                    if (!validateAreas()) {
                        errorMessage += '- Los m² construidos deben ser mayores que los m² útiles.\n';
                    }
                    if (!validatePostalCode()) {
                        errorMessage += '- El código postal es obligatorio.\n';
                    }
                    if (!validateCoordinates()) {
                        errorMessage += '- Debes seleccionar una ubicación en el mapa haciendo clic en él.\n';
                    }
                    alert(errorMessage);
                    return false;
                }
            });

            // Manejo de la galería de imágenes
            const galeriaInput = document.getElementById('galeria-input');
            const galeriaPreview = document.getElementById('galeria-preview');

            galeriaInput.addEventListener('change', function(e) {
                const files = e.target.files;
                galeriaPreview.innerHTML =
                    '<div class="col-12"><h6>Vista previa de las imágenes:</h6></div>';

                if (files.length > 0) {
                    galeriaPreview.style.display = 'block';

                    Array.from(files).forEach((file, index) => {
                        if (file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                const previewDiv = document.createElement('div');
                                previewDiv.className = 'col-md-3 col-sm-4 col-6 mb-3';
                                previewDiv.innerHTML = `
                                    <div class="card">
                                        <img src="${e.target.result}" class="card-img-top" style="height: 150px; object-fit: cover;" alt="Preview ${index + 1}">
                                        <div class="card-body p-2">
                                            <small class="text-muted">${file.name}</small>
                                        </div>
                                    </div>
                                `;
                                galeriaPreview.appendChild(previewDiv);
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                } else {
                    galeriaPreview.style.display = 'none';
                }
            });

            // Cambiar etiqueta del precio según tipo de operación
            const transactionTypeSelect = document.getElementById('transaction_type_id');
            const precioLabel = document.getElementById('precio-label');

            function updatePrecioLabel() {
                const transactionType = transactionTypeSelect.value;
                if (transactionType === '3') {
                    precioLabel.innerHTML = '<strong>Precio de alquiler mensual (€/mes)</strong>';
                } else {
                    precioLabel.innerHTML = '<strong>Precio de venta (€)</strong>';
                }
            }

            transactionTypeSelect.addEventListener('change', updatePrecioLabel);
            // Ejecutar al cargar la página
            updatePrecioLabel();
        });
    </script>
@endsection
