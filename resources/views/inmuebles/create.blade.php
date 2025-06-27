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
                                <select name="tipo_vivienda_id" class="form-control" required>
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
                                <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" class="form-control">
                                @error('ubicacion')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label><strong>Código Postal</strong></label>
                                <input type="text" name="cod_postal" value="{{ old('cod_postal') }}"
                                    class="form-control">
                                @error('cod_postal')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
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
@endsection
