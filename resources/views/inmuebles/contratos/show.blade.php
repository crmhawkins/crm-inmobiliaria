@extends('layouts.app')

@section('encabezado', 'Detalle del Contrato')
@section('subtitulo', $contrato->inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Admin Actions Bar -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Contrato #{{ $contrato->id }}
                    </h1>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('inmuebles.contratos', $contrato->inmueble) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                        <a href="{{ route('contratos.edit', $contrato) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                        <form action="{{ route('contratos.destroy', $contrato) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar este contrato?')">
                                <i class="fas fa-trash mr-2"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Detalles del Contrato -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Información Principal -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6 flex items-center">
                            <i class="fas fa-file-contract text-blue-600 mr-3"></i>
                            Información Principal
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <span class="text-gray-600">Tipo de Contrato</span>
                                <p class="font-medium mt-1">
                                    @switch($contrato->tipo)
                                        @case('arras')
                                            Contrato de Arras
                                            @break
                                        @case('alquiler')
                                            Contrato de Alquiler
                                            @break
                                        @case('compraventa')
                                            Contrato de Compraventa
                                            @break
                                        @default
                                            {{ $contrato->tipo }}
                                    @endswitch
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600">Precio</span>
                                <p class="font-medium mt-1">{{ number_format($contrato->precio, 2, ',', '.') }} €</p>
                            </div>
                            <div>
                                <span class="text-gray-600">Fecha de Firma</span>
                                <p class="font-medium mt-1">
                                    {{ $contrato->fecha_firma ? $contrato->fecha_firma->format('d/m/Y') : 'Pendiente de firma' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600">Estado</span>
                                <p class="mt-1">
                                    @if($contrato->fecha_firma)
                                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                            Firmado
                                        </span>
                                    @else
                                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                                            Pendiente
                                        </span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Partes Involucradas -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6 flex items-center">
                            <i class="fas fa-users text-blue-600 mr-3"></i>
                            Partes Involucradas
                        </h2>
                        <div class="space-y-6">
                            <!-- Cliente -->
                            <div>
                                <span class="text-gray-600">Cliente</span>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-medium">{{ $contrato->cliente->nombre_completo }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $contrato->cliente->dni }}</p>
                                        <p>{{ $contrato->cliente->email }}</p>
                                        <p>{{ $contrato->cliente->telefono }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Inmueble -->
                            <div>
                                <span class="text-gray-600">Inmueble</span>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-medium">{{ $contrato->inmueble->titulo }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $contrato->inmueble->direccion }}</p>
                                        <p>{{ $contrato->inmueble->m2 }} m² - {{ $contrato->inmueble->habitaciones }} hab.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                @if($contrato->observaciones)
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4 flex items-center">
                            <i class="fas fa-comment text-blue-600 mr-3"></i>
                            Observaciones
                        </h2>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-line">{{ $contrato->observaciones }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
