@extends('layouts.app')

@section('encabezado', 'Detalle de la Visita')
@section('subtitulo', $visita->inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-3xl mx-auto">
            <!-- Admin Actions Bar -->
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <h1 class="text-2xl font-bold text-gray-800">
                        Visita #{{ $visita->id }}
                    </h1>
                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('inmuebles.visitas', $visita->inmueble) }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Volver
                        </a>
                        <a href="{{ route('visitas.edit', $visita) }}"
                            class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition">
                            <i class="fas fa-edit mr-2"></i> Editar
                        </a>
                        <form action="{{ route('visitas.destroy', $visita) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta visita?')">
                                <i class="fas fa-trash mr-2"></i> Eliminar
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Detalles de la Visita -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Información Principal -->
                    <div>
                        <h2 class="text-xl font-semibold mb-6 flex items-center">
                            <i class="fas fa-clipboard-list text-blue-600 mr-3"></i>
                            Información Principal
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <span class="text-gray-600">Fecha y Hora</span>
                                <p class="font-medium mt-1">
                                    {{ $visita->fecha_visita->format('d/m/Y') }} - {{ $visita->hora_visita }}
                                </p>
                            </div>
                            <div>
                                <span class="text-gray-600">Estado</span>
                                <p class="mt-1">
                                    @switch($visita->estado)
                                        @case('pendiente')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                                                Pendiente
                                            </span>
                                            @break
                                        @case('realizada')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                                                Realizada
                                            </span>
                                            @break
                                        @case('cancelada')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                                                Cancelada
                                            </span>
                                            @break
                                        @default
                                            {{ $visita->estado }}
                                    @endswitch
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
                                    <h3 class="font-medium">{{ $visita->cliente->nombre_completo }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $visita->cliente->dni }}</p>
                                        <p>{{ $visita->cliente->email }}</p>
                                        <p>{{ $visita->cliente->telefono }}</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Vendedor -->
                            <div>
                                <span class="text-gray-600">Vendedor</span>
                                <div class="mt-2 p-4 bg-gray-50 rounded-lg">
                                    <h3 class="font-medium">{{ $visita->vendedor->nombre_completo }}</h3>
                                    <div class="mt-2 text-sm text-gray-600">
                                        <p>{{ $visita->vendedor->email }}</p>
                                        <p>{{ $visita->vendedor->telefono }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Inmueble -->
                <div class="mt-8">
                    <h2 class="text-xl font-semibold mb-6 flex items-center">
                        <i class="fas fa-home text-blue-600 mr-3"></i>
                        Inmueble
                    </h2>
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-medium">{{ $visita->inmueble->titulo }}</h3>
                                <div class="mt-2 text-sm text-gray-600">
                                    <p>{{ $visita->inmueble->direccion }}</p>
                                    <p>{{ $visita->inmueble->m2 }} m² - {{ $visita->inmueble->habitaciones }} hab.</p>
                                </div>
                            </div>
                            <a href="{{ route('inmuebles.show', $visita->inmueble) }}"
                                class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-external-link-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Observaciones -->
                @if($visita->observaciones)
                    <div class="mt-8">
                        <h2 class="text-xl font-semibold mb-4 flex items-center">
                            <i class="fas fa-comment text-blue-600 mr-3"></i>
                            Observaciones
                        </h2>
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700 whitespace-pre-line">{{ $visita->observaciones }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
