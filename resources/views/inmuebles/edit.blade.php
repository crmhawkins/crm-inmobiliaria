@extends('layouts.app')

@section('encabezado', 'Editar inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
<div class="container-fluid">
    <form action="{{ route('inmuebles.update', $inmueble) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <!-- Información Básica -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-info-circle me-2 text-primary"></i>Información Básica
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Título -->
                    <div class="col-md-6">
                        <label for="titulo" class="form-label fw-semibold">Título <span class="text-danger">*</span></label>
                        <input type="text" name="titulo" id="titulo"
                            value="{{ old('titulo', $inmueble->titulo) }}"
                            class="form-control form-control-lg @error('titulo') is-invalid @enderror"
                            placeholder="Ej: Piso en venta en San García">
                        @error('titulo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo de Vivienda -->
                    <div class="col-md-6">
                        <label for="tipo_vivienda_id" class="form-label fw-semibold">Tipo de Vivienda <span class="text-danger">*</span></label>
                        <select name="tipo_vivienda_id" id="tipo_vivienda_id"
                            class="form-select form-select-lg @error('tipo_vivienda_id') is-invalid @enderror">
                            <option value="">Seleccione un tipo</option>
                            @foreach(\App\Models\TipoVivienda::all() as $tipo)
                                <option value="{{ $tipo->id }}" {{ old('tipo_vivienda_id', $inmueble->tipo_vivienda_id) == $tipo->id ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                        @error('tipo_vivienda_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="col-12">
                        <label for="descripcion" class="form-label fw-semibold">Descripción</label>
                        <textarea name="descripcion" id="descripcion" rows="6"
                            class="form-control @error('descripcion') is-invalid @enderror"
                            placeholder="Describe las características principales del inmueble...">{{ old('descripcion', $inmueble->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Describe detalladamente el inmueble para atraer a posibles compradores o inquilinos.</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Características -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-home me-2 text-primary"></i>Características
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Superficie -->
                    <div class="col-md-4">
                        <label for="m2" class="form-label fw-semibold">Superficie (m²) <span class="text-danger">*</span></label>
                        <input type="number" name="m2" id="m2"
                            value="{{ old('m2', $inmueble->m2) }}"
                            class="form-control form-control-lg @error('m2') is-invalid @enderror"
                            placeholder="90" min="0" step="0.01">
                        @error('m2')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Superficie Construida -->
                    <div class="col-md-4">
                        <label for="m2_construidos" class="form-label fw-semibold">Superficie Construida (m²)</label>
                        <input type="number" name="m2_construidos" id="m2_construidos"
                            value="{{ old('m2_construidos', $inmueble->m2_construidos) }}"
                            class="form-control form-control-lg @error('m2_construidos') is-invalid @enderror"
                            placeholder="90" min="0" step="0.01">
                        @error('m2_construidos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tipo de operación -->
                    <div class="col-md-4">
                        <label for="transaction_type_id" class="form-label fw-semibold">Tipo de operación <span class="text-danger">*</span></label>
                        <select name="transaction_type_id" id="transaction_type_id" required
                            class="form-select form-select-lg @error('transaction_type_id') is-invalid @enderror">
                            <option value="1" {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '1' ? 'selected' : '' }}>Venta</option>
                            <option value="3" {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '3' ? 'selected' : '' }}>Alquiler</option>
                        </select>
                        @error('transaction_type_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Precio -->
                    <div class="col-md-4">
                        <label for="valor_referencia" id="precio-label" class="form-label fw-semibold">
                            Precio de {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '3' ? 'alquiler mensual (€/mes)' : 'venta (€)' }} <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <span class="input-group-text">€</span>
                            <input type="number" name="valor_referencia" id="valor_referencia"
                                value="{{ old('valor_referencia', $inmueble->valor_referencia) }}"
                                class="form-control @error('valor_referencia') is-invalid @enderror"
                                placeholder="168000" min="0" step="0.01">
                            @error('valor_referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Habitaciones -->
                    <div class="col-md-4">
                        <label for="habitaciones" class="form-label fw-semibold">Habitaciones <span class="text-danger">*</span></label>
                        <input type="number" name="habitaciones" id="habitaciones"
                            value="{{ old('habitaciones', $inmueble->habitaciones) }}"
                            class="form-control form-control-lg @error('habitaciones') is-invalid @enderror"
                            placeholder="3" min="0">
                        @error('habitaciones')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Baños -->
                    <div class="col-md-4">
                        <label for="banos" class="form-label fw-semibold">Baños <span class="text-danger">*</span></label>
                        <input type="number" name="banos" id="banos"
                            value="{{ old('banos', $inmueble->banos) }}"
                            class="form-control form-control-lg @error('banos') is-invalid @enderror"
                            placeholder="2" min="0">
                        @error('banos')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="col-md-4">
                        <label for="estado" class="form-label fw-semibold">Estado <span class="text-danger">*</span></label>
                        <select name="estado" id="estado"
                            class="form-select form-select-lg @error('estado') is-invalid @enderror">
                            <option value="Disponible" {{ old('estado', $inmueble->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Reservado" {{ old('estado', $inmueble->estado) == 'Reservado' ? 'selected' : '' }}>Reservado</option>
                            <option value="Vendido" {{ old('estado', $inmueble->estado) == 'Vendido' ? 'selected' : '' }}>Vendido</option>
                        </select>
                        @error('estado')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Ubicación -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white border-bottom">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-map-marker-alt me-2 text-primary"></i>Ubicación
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Dirección -->
                    <div class="col-md-6">
                        <label for="ubicacion" class="form-label fw-semibold">Dirección <span class="text-danger">*</span></label>
                        <input type="text" name="ubicacion" id="ubicacion"
                            value="{{ old('ubicacion', $inmueble->ubicacion) }}"
                            class="form-control form-control-lg @error('ubicacion') is-invalid @enderror"
                            placeholder="Ej: Piso San García">
                        @error('ubicacion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Código Postal -->
                    <div class="col-md-3">
                        <label for="cod_postal" class="form-label fw-semibold">Código Postal</label>
                        <input type="text" name="cod_postal" id="cod_postal"
                            value="{{ old('cod_postal', $inmueble->cod_postal) }}"
                            class="form-control form-control-lg @error('cod_postal') is-invalid @enderror"
                            placeholder="11200" maxlength="5">
                        @error('cod_postal')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Referencia Catastral -->
                    <div class="col-md-3">
                        <label for="referencia_catastral" class="form-label fw-semibold">Referencia Catastral</label>
                        <input type="text" name="referencia_catastral" id="referencia_catastral"
                            value="{{ old('referencia_catastral', $inmueble->referencia_catastral) }}"
                            class="form-control form-control-lg @error('referencia_catastral') is-invalid @enderror"
                            placeholder="Opcional">
                        @error('referencia_catastral')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('inmuebles.admin-show', $inmueble) }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </a>
                    <div class="d-flex gap-2">
                        <a href="{{ route('inmuebles.admin-show', $inmueble) }}" class="btn btn-outline-info btn-lg">
                            <i class="fas fa-eye me-2"></i>Ver Detalles
                        </a>
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-save me-2"></i>Guardar Cambios
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
    .form-label {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .form-control-lg,
    .form-select-lg {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        transition: all 0.3s ease;
    }

    .form-control-lg:focus,
    .form-select-lg:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }

    .card {
        border: none;
        border-radius: 12px;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        padding: 1.25rem 1.5rem;
    }

    .card-body {
        padding: 1.5rem;
    }

    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    .input-group-text {
        background-color: #f8f9fa;
        border-color: #dee2e6;
        color: #6c757d;
        font-weight: 500;
    }

    @media (max-width: 768px) {
        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 1rem;
        }

        .d-flex.gap-2 {
            width: 100%;
            flex-direction: column;
        }

        .btn-lg {
            width: 100%;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cambiar etiqueta del precio según tipo de operación
        const transactionTypeSelect = document.getElementById('transaction_type_id');
        const precioLabel = document.getElementById('precio-label');

        function updatePrecioLabel() {
            const transactionType = transactionTypeSelect.value;
            if (transactionType === '3') {
                precioLabel.innerHTML = 'Precio de alquiler mensual (€/mes) <span class="text-danger">*</span>';
            } else {
                precioLabel.innerHTML = 'Precio de venta (€) <span class="text-danger">*</span>';
            }
        }

        transactionTypeSelect.addEventListener('change', updatePrecioLabel);
        // Ejecutar al cargar la página
        updatePrecioLabel();
    });
</script>
@endsection
