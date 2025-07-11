@extends('layouts.app')

@section('encabezado', 'Hojas de visita del inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Admin Actions Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Hojas de Visita - {{ $inmueble->titulo }}
                </h1>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inmuebles.show', $inmueble) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <a href="{{ route('visitas.create', ['inmueble' => $inmueble->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i> Nueva Visita
                    </a>
                </div>
            </div>
        </div>

        <!-- Visits List -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($visitas as $visita)
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-clipboard-list text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Visita #{{ $visita->id }}</h3>
                                    <span class="text-sm text-gray-500">{{ $visita->fecha_visita->format('d/m/Y H:i') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('visitas.show', $visita) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('visitas.edit', $visita) }}"
                                    class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('visitas.destroy', $visita) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar esta hoja de visita?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-medium">{{ $visita->cliente->nombre_completo }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Vendedor:</span>
                                <span class="font-medium">{{ $visita->vendedor->nombre_completo }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Estado:</span>
                                <span class="font-medium">
                                    @switch($visita->estado)
                                        @case('pendiente')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                Pendiente
                                            </span>
                                            @break
                                        @case('realizada')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                Realizada
                                            </span>
                                            @break
                                        @case('cancelada')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                Cancelada
                                            </span>
                                            @break
                                        @default
                                            {{ $visita->estado }}
                                    @endswitch
                                </span>
                            </div>
                            @if($visita->observaciones)
                                <div class="mt-3">
                                    <span class="text-gray-600 text-sm">Observaciones:</span>
                                    <p class="text-sm text-gray-800 mt-1">{{ $visita->observaciones }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-clipboard-list text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No hay hojas de visita</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza creando una nueva hoja de visita para este inmueble
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
