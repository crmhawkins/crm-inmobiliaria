@extends('layouts.app')

@section('encabezado', 'Editar inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
<div class="container mx-auto px-4 py-8">
    <form action="{{ route('inmuebles.update', $inmueble) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Información Básica -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Información Básica</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Título -->
                <div>
                    <label for="titulo" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                    <input type="text" name="titulo" id="titulo" value="{{ old('titulo', $inmueble->titulo) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('titulo')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de Vivienda -->
                <div>
                    <label for="tipo_vivienda_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Vivienda</label>
                    <select name="tipo_vivienda_id" id="tipo_vivienda_id"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        @foreach(\App\Models\TipoVivienda::all() as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_vivienda_id', $inmueble->tipo_vivienda_id) == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_vivienda_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Descripción -->
                <div class="md:col-span-2">
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                    <textarea name="descripcion" id="descripcion" rows="4"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">{{ old('descripcion', $inmueble->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Características -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Características</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Superficie -->
                <div>
                    <label for="m2" class="block text-sm font-medium text-gray-700 mb-2">Superficie (m²)</label>
                    <input type="number" name="m2" id="m2" value="{{ old('m2', $inmueble->m2) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('m2')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Superficie Construida -->
                <div>
                    <label for="m2_construidos" class="block text-sm font-medium text-gray-700 mb-2">Superficie Construida (m²)</label>
                    <input type="number" name="m2_construidos" id="m2_construidos" value="{{ old('m2_construidos', $inmueble->m2_construidos) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('m2_construidos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tipo de operación -->
                <div>
                    <label for="transaction_type_id" class="block text-sm font-medium text-gray-700 mb-2">Tipo de operación *</label>
                    <select name="transaction_type_id" id="transaction_type_id" required
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="1" {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '1' ? 'selected' : '' }}>Venta</option>
                        <option value="3" {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '3' ? 'selected' : '' }}>Alquiler</option>
                    </select>
                    @error('transaction_type_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Precio -->
                <div>
                    <label for="valor_referencia" id="precio-label" class="block text-sm font-medium text-gray-700 mb-2">
                        Precio de {{ old('transaction_type_id', $inmueble->transaction_type_id ?? 1) == '3' ? 'alquiler mensual (€/mes)' : 'venta (€)' }}
                    </label>
                    <input type="number" name="valor_referencia" id="valor_referencia" value="{{ old('valor_referencia', $inmueble->valor_referencia) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('valor_referencia')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Habitaciones -->
                <div>
                    <label for="habitaciones" class="block text-sm font-medium text-gray-700 mb-2">Habitaciones</label>
                    <input type="number" name="habitaciones" id="habitaciones" value="{{ old('habitaciones', $inmueble->habitaciones) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('habitaciones')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Baños -->
                <div>
                    <label for="banos" class="block text-sm font-medium text-gray-700 mb-2">Baños</label>
                    <input type="number" name="banos" id="banos" value="{{ old('banos', $inmueble->banos) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('banos')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Estado -->
                <div>
                    <label for="estado" class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                    <select name="estado" id="estado"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="Disponible" {{ old('estado', $inmueble->estado) == 'Disponible' ? 'selected' : '' }}>Disponible</option>
                        <option value="Reservado" {{ old('estado', $inmueble->estado) == 'Reservado' ? 'selected' : '' }}>Reservado</option>
                        <option value="Vendido" {{ old('estado', $inmueble->estado) == 'Vendido' ? 'selected' : '' }}>Vendido</option>
                    </select>
                    @error('estado')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Ubicación -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Ubicación</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Dirección -->
                <div>
                    <label for="ubicacion" class="block text-sm font-medium text-gray-700 mb-2">Dirección</label>
                    <input type="text" name="ubicacion" id="ubicacion" value="{{ old('ubicacion', $inmueble->ubicacion) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('ubicacion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Código Postal -->
                <div>
                    <label for="cod_postal" class="block text-sm font-medium text-gray-700 mb-2">Código Postal</label>
                    <input type="text" name="cod_postal" id="cod_postal" value="{{ old('cod_postal', $inmueble->cod_postal) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('cod_postal')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Referencia Catastral -->
                <div>
                    <label for="referencia_catastral" class="block text-sm font-medium text-gray-700 mb-2">Referencia Catastral</label>
                    <input type="text" name="referencia_catastral" id="referencia_catastral"
                        value="{{ old('referencia_catastral', $inmueble->referencia_catastral) }}"
                        class="w-full rounded-lg border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                    @error('referencia_catastral')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-4">
            <a href="{{ route('inmuebles.show', $inmueble) }}"
                class="px-6 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition">
                Cancelar
            </a>
            <button type="submit"
                class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Cambiar etiqueta del precio según tipo de operación
        const transactionTypeSelect = document.getElementById('transaction_type_id');
        const precioLabel = document.getElementById('precio-label');

        function updatePrecioLabel() {
            const transactionType = transactionTypeSelect.value;
            if (transactionType === '3') {
                precioLabel.innerHTML = 'Precio de alquiler mensual (€/mes)';
            } else {
                precioLabel.innerHTML = 'Precio de venta (€)';
            }
        }

        transactionTypeSelect.addEventListener('change', updatePrecioLabel);
        // Ejecutar al cargar la página
        updatePrecioLabel();
    });
</script>
@endsection
