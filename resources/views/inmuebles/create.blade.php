@extends('layouts.app')

@section('content')
    <div class="container">
        <form action="{{ route('inmuebles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Datos básicos -->
            <div class="card mb-3">
                <h5 class="card-header">Datos básicos</h5>
                <div class="card-body">
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
                                    @foreach ($tipos_vivienda as $tipo)
                                        <option value="{{ $tipo->id }}"
                                            {{ old('tipo_vivienda_id') == $tipo->id ? 'selected' : '' }}>
                                            {{ $tipo->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('tipo_vivienda_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Subtipo de inmueble *</strong></label>
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
            <div class="card mb-3">
                <h5 class="card-header">Características físicas</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>M²</strong></label>
                                <input type="number" name="m2" value="{{ old('m2') }}" class="form-control"
                                    min="0">
                                @error('m2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label><strong>M² Construidos</strong></label>
                                <input type="number" name="m2_construidos" value="{{ old('m2_construidos') }}"
                                    class="form-control" min="0">
                                @error('m2_construidos')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
                                <label><strong>Valor de referencia (€)</strong></label>
                                <input type="number" name="valor_referencia" value="{{ old('valor_referencia') }}"
                                    class="form-control" min="0" step="0.01">
                                @error('valor_referencia')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
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
            <div class="card mb-3">
                <h5 class="card-header">Ubicación</h5>
                <div class="card-body">
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
                                <label><strong>Código Postal</strong></label>
                                <input type="text" name="cod_postal" id="postal-code-input"
                                    value="{{ old('cod_postal') }}" class="form-control" placeholder="Código postal">
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
            <div class="card mb-3">
                <h5 class="card-header">Estado y disponibilidad</h5>
                <div class="card-body">
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
            <div class="card mb-3">
                <h5 class="card-header">Certificación energética</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label><strong>¿Tiene certificado energético?</strong></label>
                                <select name="cert_energetico" class="form-control">
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
                        <div class="col-md-4">
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
                        <div class="col-md-4">
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
                    <div class="row">
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
            <div class="card mb-3">
                <h5 class="card-header">Características del inmueble</h5>
                <div class="card-body">
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
            <div class="card mb-3">
                <h5 class="card-header">Configuración para Fotocasa</h5>
                <div class="card-body">
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
                <div class="card-body">
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
            1: { // Piso -> Flat
                fotocasaType: 1,
                subtypes: [{
                        id: 2,
                        name: 'Triplex'
                    },
                    {
                        id: 3,
                        name: 'Duplex'
                    },
                    {
                        id: 5,
                        name: 'Penthouse'
                    },
                    {
                        id: 6,
                        name: 'Studio'
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
            2: { // Casa -> House
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
            3: { // Local -> Commercial store
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
                        name: 'Residencial mixto'
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
            4: { // Oficina -> Office
                fotocasaType: 4,
                subtypes: [{
                        id: 56,
                        name: 'Terreno residencial'
                    },
                    {
                        id: 60,
                        name: 'Terreno industrial'
                    },
                    {
                        id: 91,
                        name: 'Terreno rústico'
                    }
                ]
            },
            5: { // Edificio -> Building
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
                        name: 'Residencial mixto'
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
            6: { // Terreno -> Land
                fotocasaType: 6,
                subtypes: [{
                        id: 56,
                        name: 'Terreno residencial'
                    },
                    {
                        id: 60,
                        name: 'Terreno industrial'
                    },
                    {
                        id: 91,
                        name: 'Terreno rústico'
                    }
                ]
            },
            7: { // Nave -> Industrial building
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
            8: { // Garaje -> Garage
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
            9: { // Trastero -> Storage room
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
        document.getElementById('tipo_vivienda_id').addEventListener('change', updateSubtypes);

        // Inicializar subtipos si ya hay un valor seleccionado (en caso de error de validación)
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo_vivienda_id');
            if (tipoSelect.value) {
                updateSubtypes();

                // Restaurar el valor seleccionado anteriormente si existe
                const subtypeSelect = document.getElementById('building_subtype_id');
                const oldSubtypeValue = '{{ old('building_subtype_id') }}';
                if (oldSubtypeValue) {
                    subtypeSelect.value = oldSubtypeValue;
                }
            }
        });
    </script>

    <!-- Google Maps JavaScript -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places">
    </script>
    <script>
        let map;
        let marker;
        let geocoder;
        let autocomplete;

        function initMap() {
            // Coordenadas por defecto (Madrid)
            const defaultLocation = {
                lat: 40.4168,
                lng: -3.7038
            };

            // Crear el mapa
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: defaultLocation,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            // Crear el geocoder
            geocoder = new google.maps.Geocoder();

            // Crear el marcador
            marker = new google.maps.Marker({
                map: map,
                draggable: true,
                position: defaultLocation
            });

            // Autocompletado para el campo de dirección
            const addressInput = document.getElementById('address-input');
            autocomplete = new google.maps.places.Autocomplete(addressInput, {
                types: ['address'],
                componentRestrictions: {
                    country: 'ES'
                }
            });

            // Evento cuando se selecciona una dirección del autocompletado
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();

                if (place.geometry) {
                    const position = place.geometry.location;

                    // Actualizar el mapa
                    map.setCenter(position);
                    map.setZoom(16);
                    marker.setPosition(position);

                    // Actualizar coordenadas
                    updateCoordinates(position.lat(), position.lng());

                    // Extraer código postal si está disponible
                    if (place.address_components) {
                        for (let component of place.address_components) {
                            if (component.types.includes('postal_code')) {
                                document.getElementById('postal-code-input').value = component.long_name;
                                break;
                            }
                        }
                    }
                }
            });

            // Evento cuando se hace clic en el mapa
            map.addListener('click', function(event) {
                const position = event.latLng;
                marker.setPosition(position);
                updateCoordinates(position.lat(), position.lng());

                // Geocodificar las coordenadas para obtener la dirección
                geocoder.geocode({
                    location: position
                }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('address-input').value = results[0].formatted_address;

                        // Extraer código postal
                        for (let component of results[0].address_components) {
                            if (component.types.includes('postal_code')) {
                                document.getElementById('postal-code-input').value = component.long_name;
                                break;
                            }
                        }
                    }
                });
            });

            // Evento cuando se arrastra el marcador
            marker.addListener('dragend', function(event) {
                const position = event.latLng;
                updateCoordinates(position.lat(), position.lng());

                // Geocodificar las coordenadas para obtener la dirección
                geocoder.geocode({
                    location: position
                }, function(results, status) {
                    if (status === 'OK' && results[0]) {
                        document.getElementById('address-input').value = results[0].formatted_address;

                        // Extraer código postal
                        for (let component of results[0].address_components) {
                            if (component.types.includes('postal_code')) {
                                document.getElementById('postal-code-input').value = component.long_name;
                                break;
                            }
                        }
                    }
                });
            });

            // Botón para buscar dirección
            const searchButton = document.createElement('button');
            searchButton.textContent = 'Buscar';
            searchButton.className = 'btn btn-primary btn-sm';
            searchButton.style.marginTop = '10px';
            searchButton.onclick = searchAddress;

            document.getElementById('map').parentNode.appendChild(searchButton);
        }

        function updateCoordinates(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            document.getElementById('latitude-display').value = lat.toFixed(6);
            document.getElementById('longitude-display').value = lng.toFixed(6);
        }

        function searchAddress() {
            const address = document.getElementById('address-input').value;
            const postalCode = document.getElementById('postal-code-input').value;

            let searchQuery = address;
            if (postalCode) {
                searchQuery += ', ' + postalCode + ', Spain';
            }

            geocoder.geocode({
                address: searchQuery
            }, function(results, status) {
                if (status === 'OK' && results[0]) {
                    const position = results[0].geometry.location;

                    // Actualizar el mapa
                    map.setCenter(position);
                    map.setZoom(16);
                    marker.setPosition(position);

                    // Actualizar coordenadas
                    updateCoordinates(position.lat(), position.lng());

                    // Actualizar dirección formateada
                    document.getElementById('address-input').value = results[0].formatted_address;

                    // Extraer código postal
                    for (let component of results[0].address_components) {
                        if (component.types.includes('postal_code')) {
                            document.getElementById('postal-code-input').value = component.long_name;
                            break;
                        }
                    }
                } else {
                    alert('No se pudo encontrar la dirección. Por favor, verifica que la dirección sea correcta.');
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
                const position = {
                    lat: parseFloat(oldLat),
                    lng: parseFloat(oldLng)
                };
                map.setCenter(position);
                marker.setPosition(position);
                updateCoordinates(position.lat, position.lng);
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
        });
    </script>
@endsection
