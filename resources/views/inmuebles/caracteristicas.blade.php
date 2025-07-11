@extends('layouts.app')

@section('encabezado', 'Características del inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Admin Actions Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Características - {{ $inmueble->titulo }}
                </h1>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inmuebles.show', $inmueble) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <button type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        onclick="document.getElementById('modal-caracteristicas').classList.remove('hidden')">
                        <i class="fas fa-plus mr-2"></i> Añadir Características
                    </button>
                </div>
            </div>
        </div>

        <!-- Características Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Características Básicas -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-home text-blue-600 mr-3"></i>
                    Características Básicas
                </h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Superficie</span>
                        <span class="font-medium">{{ $inmueble->m2 }} m²</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Superficie construida</span>
                        <span class="font-medium">{{ $inmueble->m2_construidos }} m²</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Habitaciones</span>
                        <span class="font-medium">{{ $inmueble->habitaciones }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Baños</span>
                        <span class="font-medium">{{ $inmueble->banos }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Año construcción</span>
                        <span class="font-medium">{{ $inmueble->year_built ?? 'No especificado' }}</span>
                    </div>
                </div>
            </div>

            <!-- Características Adicionales -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-plus-circle text-blue-600 mr-3"></i>
                    Características Adicionales
                </h2>
                <div class="space-y-3">
                    @if($inmueble->has_elevator)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Ascensor</span>
                        </div>
                    @endif
                    @if($inmueble->has_parking)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Plaza de parking</span>
                        </div>
                    @endif
                    @if($inmueble->has_storage_room)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Trastero</span>
                        </div>
                    @endif
                    @if($inmueble->has_terrace)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Terraza ({{ $inmueble->terrace_surface ?? 0 }} m²)</span>
                        </div>
                    @endif
                    @if($inmueble->has_balcony)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Balcón</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Equipamiento -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-plug text-blue-600 mr-3"></i>
                    Equipamiento
                </h2>
                <div class="space-y-3">
                    @if($inmueble->has_air_conditioning)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Aire acondicionado</span>
                        </div>
                    @endif
                    @if($inmueble->has_heating)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Calefacción</span>
                        </div>
                    @endif
                    @if($inmueble->has_equipped_kitchen)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Cocina equipada</span>
                        </div>
                    @endif
                    @if($inmueble->has_wardrobe)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Armarios empotrados</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Eficiencia Energética -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-leaf text-blue-600 mr-3"></i>
                    Eficiencia Energética
                </h2>
                <div class="space-y-4">
                    @if($inmueble->cert_energetico)
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Certificado energético</span>
                            <span class="font-medium">{{ $inmueble->cert_energetico_elegido }}</span>
                        </div>
                        @if($inmueble->consumption_efficiency_value)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Consumo energético</span>
                                <span class="font-medium">{{ $inmueble->consumption_efficiency_value }} kWh/m²año</span>
                            </div>
                        @endif
                        @if($inmueble->emissions_efficiency_value)
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Emisiones CO2</span>
                                <span class="font-medium">{{ $inmueble->emissions_efficiency_value }} kgCO2/m²año</span>
                            </div>
                        @endif
                    @else
                        <div class="text-center text-gray-500">
                            <i class="fas fa-info-circle mb-2 text-2xl"></i>
                            <p>Certificado energético no disponible</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Características Exteriores -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-tree text-blue-600 mr-3"></i>
                    Características Exteriores
                </h2>
                <div class="space-y-3">
                    @if($inmueble->has_private_garden)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Jardín privado</span>
                        </div>
                    @endif
                    @if($inmueble->has_community_pool)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Piscina comunitaria</span>
                        </div>
                    @endif
                    @if($inmueble->has_private_pool)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Piscina privada</span>
                        </div>
                    @endif
                    @if($inmueble->has_tennis_court)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Pista de tenis</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Seguridad y Acceso -->
            <div class="bg-white rounded-3xl shadow-xl p-8">
                <h2 class="text-xl font-semibold mb-6 flex items-center">
                    <i class="fas fa-shield-alt text-blue-600 mr-3"></i>
                    Seguridad y Acceso
                </h2>
                <div class="space-y-3">
                    @if($inmueble->has_security_door)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Puerta de seguridad</span>
                        </div>
                    @endif
                    @if($inmueble->has_surveillance)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Vigilancia</span>
                        </div>
                    @endif
                    @if($inmueble->has_alarm)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Alarma</span>
                        </div>
                    @endif
                    @if($inmueble->has_24h_access)
                        <div class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <span>Acceso 24h</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Añadir Características -->
    <div id="modal-caracteristicas" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden">
        <div class="min-h-screen px-4 text-center">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>
            <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
            <div class="inline-block w-full max-w-3xl p-6 my-8 text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Añadir Características</h3>
                    <button type="button" class="text-gray-400 hover:text-gray-500"
                        onclick="document.getElementById('modal-caracteristicas').classList.add('hidden')">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form action="{{ route('inmuebles.update', $inmueble) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Características Básicas -->
                        <div>
                            <h4 class="font-semibold text-lg mb-4">Características Básicas</h4>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Superficie (m²)</label>
                                    <input type="number" name="m2" value="{{ $inmueble->m2 }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Superficie construida (m²)</label>
                                    <input type="number" name="m2_construidos" value="{{ $inmueble->m2_construidos }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Habitaciones</label>
                                    <input type="number" name="habitaciones" value="{{ $inmueble->habitaciones }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Baños</label>
                                    <input type="number" name="banos" value="{{ $inmueble->banos }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>

                        <!-- Características Adicionales -->
                        <div>
                            <h4 class="font-semibold text-lg mb-4">Características Adicionales</h4>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_elevator" value="1"
                                        {{ $inmueble->has_elevator ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2">Ascensor</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_parking" value="1"
                                        {{ $inmueble->has_parking ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2">Plaza de parking</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_storage_room" value="1"
                                        {{ $inmueble->has_storage_room ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2">Trastero</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="has_terrace" value="1"
                                        {{ $inmueble->has_terrace ? 'checked' : '' }}
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <span class="ml-2">Terraza</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end space-x-3">
                        <button type="button"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50"
                            onclick="document.getElementById('modal-caracteristicas').classList.add('hidden')">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Guardar cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
