@extends('layouts.app')

@section('encabezado', 'Contratos del inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Admin Actions Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Contratos - {{ $inmueble->titulo }}
                </h1>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inmuebles.show', $inmueble) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <a href="{{ route('contratos.create', ['inmueble' => $inmueble->id]) }}"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        <i class="fas fa-plus mr-2"></i> Nuevo Contrato
                    </a>
                </div>
            </div>
        </div>

        <!-- Contracts List -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @forelse($inmueble->contratos as $contrato)
                    <div class="bg-gray-50 rounded-xl p-6">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-contract text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">Contrato #{{ $contrato->id }}</h3>
                                    <span class="text-sm text-gray-500">{{ $contrato->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('contratos.show', $contrato) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('contratos.edit', $contrato) }}"
                                    class="text-yellow-600 hover:text-yellow-800">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('contratos.destroy', $contrato) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este contrato?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="mt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Cliente:</span>
                                <span class="font-medium">{{ $contrato->cliente->nombre_completo }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tipo:</span>
                                <span class="font-medium">{{ $contrato->tipo }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Estado:</span>
                                <span class="font-medium">
                                    @switch($contrato->estado)
                                        @case('pendiente')
                                            <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-xs">
                                                Pendiente
                                            </span>
                                            @break
                                        @case('firmado')
                                            <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">
                                                Firmado
                                            </span>
                                            @break
                                        @case('cancelado')
                                            <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">
                                                Cancelado
                                            </span>
                                            @break
                                        @default
                                            {{ $contrato->estado }}
                                    @endswitch
                                </span>
                            </div>
                            @if($contrato->fecha_firma)
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Fecha firma:</span>
                                    <span class="font-medium">{{ $contrato->fecha_firma->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-file-contract text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No hay contratos</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza creando un nuevo contrato para este inmueble
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
