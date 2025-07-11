@extends('layouts.app')

@section('encabezado', 'Editar Contrato')
@section('subtitulo', $contrato->inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Admin Actions Bar -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Editar Contrato #{{ $contrato->id }}
                    </h1>
                    <a href="{{ route('contratos.show', $contrato) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                </div>
            </div>

            <!-- Formulario -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <form action="{{ route('contratos.update', $contrato) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <!-- Cliente -->
                        <div>
                            <label for="cliente_id" class="block text-sm font-medium text-gray-700">Cliente</label>
                            <select name="cliente_id" id="cliente_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar cliente</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ $contrato->cliente_id == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre_completo }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tipo de Contrato -->
                        <div>
                            <label for="tipo" class="block text-sm font-medium text-gray-700">Tipo de Contrato</label>
                            <select name="tipo" id="tipo" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">Seleccionar tipo</option>
                                <option value="arras" {{ $contrato->tipo == 'arras' ? 'selected' : '' }}>
                                    Contrato de Arras
                                </option>
                                <option value="alquiler" {{ $contrato->tipo == 'alquiler' ? 'selected' : '' }}>
                                    Contrato de Alquiler
                                </option>
                                <option value="compraventa" {{ $contrato->tipo == 'compraventa' ? 'selected' : '' }}>
                                    Contrato de Compraventa
                                </option>
                            </select>
                            @error('tipo')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Firma -->
                        <div>
                            <label for="fecha_firma" class="block text-sm font-medium text-gray-700">Fecha de Firma</label>
                            <input type="date" name="fecha_firma" id="fecha_firma"
                                value="{{ $contrato->fecha_firma ? $contrato->fecha_firma->format('Y-m-d') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('fecha_firma')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div>
                            <label for="precio" class="block text-sm font-medium text-gray-700">Precio</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">€</span>
                                </div>
                                <input type="number" name="precio" id="precio" required step="0.01" min="0"
                                    value="{{ $contrato->precio }}"
                                    class="pl-7 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            </div>
                            @error('precio')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Observaciones -->
                        <div>
                            <label for="observaciones" class="block text-sm font-medium text-gray-700">Observaciones</label>
                            <textarea name="observaciones" id="observaciones" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ $contrato->observaciones }}</textarea>
                            @error('observaciones')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Botones -->
                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('contratos.show', $contrato) }}"
                                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                                Cancelar
                            </a>
                            <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
