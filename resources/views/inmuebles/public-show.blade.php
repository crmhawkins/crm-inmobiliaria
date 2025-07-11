<!DOCTYPE html>
<html lang="es" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $inmueble->titulo }} - Sayco</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#ecfeff',
                            100: '#cffafe',
                            200: '#a5f3fc',
                            300: '#67e8f9',
                            400: '#22d3ee',
                            500: '#06b6d4',
                            600: '#0891b2',
                            700: '#0e7490',
                            800: '#155e75',
                            900: '#164e63',
                        },
                    },
                },
            },
        }
    </script>

    <style>
        .glass-effect {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .image-gallery {
            transition: all 0.5s ease;
        }

        .image-gallery:hover {
            transform: scale(1.02);
        }

        .gradient-text {
            background: linear-gradient(135deg, #06b6d4, #0891b2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>

<body class="bg-gray-50 font-sans">

    <!-- Hero Section -->
    <section class="pt-24 pb-16 bg-gradient-to-br from-primary-600 via-primary-700 to-primary-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-10"></div>
        <div class="absolute inset-0 bg-gradient-to-b from-black/20 to-transparent"></div>
        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-block px-4 py-1 rounded-full bg-white/20 text-sm mb-6">
                    Propiedad Destacada
                </span>
                <h1 class="text-4xl md:text-6xl font-bold mb-4 leading-tight">{{ $inmueble->titulo }}</h1>
                <p class="text-xl opacity-90 mb-8 flex items-center justify-center">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ $inmueble->ubicacion }}
                    @if ($inmueble->cod_postal)
                        , {{ $inmueble->cod_postal }}
                    @endif
                </p>
                @if ($inmueble->valor_referencia)
                    <div class="inline-block bg-white text-primary-600 px-8 py-4 rounded-2xl text-2xl font-bold shadow-xl transform hover:scale-105 transition-all duration-300 hover:shadow-2xl">
                        <i class="fas fa-euro-sign mr-2"></i>
                        {{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €
                    </div>
                @endif
            </div>
        </div>
        <div class="absolute bottom-0 left-0 right-0 h-32 bg-gradient-to-t from-gray-50 to-transparent"></div>
    </section>

    <!-- Quick Stats -->
    <div class="container mx-auto px-4 -mt-8 relative z-20">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @if ($inmueble->m2)
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-ruler text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-gray-800">{{ $inmueble->m2 }} m²</span>
                            <span class="text-gray-500 text-sm">Superficie</span>
                        </div>
                    </div>
                </div>
            @endif
            @if ($inmueble->habitaciones)
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bed text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-gray-800">{{ $inmueble->habitaciones }}</span>
                            <span class="text-gray-500 text-sm">Habitaciones</span>
                        </div>
                    </div>
                </div>
            @endif
            @if ($inmueble->banos)
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-bath text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-gray-800">{{ $inmueble->banos }}</span>
                            <span class="text-gray-500 text-sm">Baños</span>
                        </div>
                    </div>
                </div>
            @endif
            @if ($inmueble->m2_construidos)
                <div class="bg-white rounded-2xl p-6 shadow-lg hover:shadow-xl transition-shadow">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center">
                            <i class="fas fa-building text-primary-600 text-xl"></i>
                        </div>
                        <div>
                            <span class="block text-2xl font-bold text-gray-800">{{ $inmueble->m2_construidos }} m²</span>
                            <span class="text-gray-500 text-sm">Construidos</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column (2/3) -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Gallery Section -->
                    <div class="bg-white rounded-3xl shadow-xl overflow-hidden image-gallery">
                        @if ($inmueble->galeria)
                            @php
                                $galeria = json_decode($inmueble->galeria, true);
                            @endphp
                            @if (is_array($galeria) && count($galeria) > 0)
                                <!-- Main Image -->
                                <div class="relative aspect-[16/9] overflow-hidden">
                                    <img src="{{ $galeria['1'] ?? array_values($galeria)[0] }}"
                                        alt="{{ $inmueble->titulo }}"
                                        class="w-full h-full object-cover transition-transform duration-700 hover:scale-110"
                                        id="main-gallery-image">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                                </div>

                                <!-- Thumbnail Gallery -->
                                @if (count($galeria) > 1)
                                    <div class="p-6">
                                        <div class="grid grid-cols-6 gap-3">
                                            @foreach ($galeria as $key => $imageUrl)
                                                <div class="aspect-square rounded-xl overflow-hidden cursor-pointer transform hover:scale-105 transition-all duration-300 shadow-md hover:shadow-xl"
                                                    onclick="changeMainImage('{{ $imageUrl }}')">
                                                    <img src="{{ $imageUrl }}"
                                                        alt="{{ $inmueble->titulo }} - Imagen {{ $key }}"
                                                        class="w-full h-full object-cover">
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @else
                                <div class="aspect-video bg-gray-100 flex items-center justify-center">
                                    <i class="fas fa-image text-5xl text-gray-300"></i>
                                </div>
                            @endif
                        @endif
                    </div>

                    <!-- Description -->
                    @if ($inmueble->descripcion)
                        <div class="bg-white rounded-3xl shadow-xl p-8">
                            <h2 class="text-3xl font-bold mb-6 gradient-text inline-block">
                                <i class="fas fa-align-left mr-3"></i>
                                Descripción
                            </h2>
                            <p class="text-gray-600 leading-relaxed text-lg">{{ $inmueble->descripcion }}</p>
                        </div>
                    @endif

                    <!-- Characteristics -->
                    <div class="bg-white rounded-3xl shadow-xl p-8">
                        <h2 class="text-3xl font-bold mb-8 gradient-text inline-block">
                            <i class="fas fa-list-check mr-3"></i>
                            Características
                        </h2>
                        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <!-- Basic Features -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-home text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Básicas</h3>
                                </div>
                                <div class="space-y-4">
                                    @if ($inmueble->furnished)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Amueblado</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_elevator)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Ascensor</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_wardrobe)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Armarios empotrados</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_surveillance)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Vigilancia</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_equipped_kitchen)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Cocina equipada</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_storage_room)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Trastero</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_security_door)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Puerta de seguridad</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_balcony)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Balcón</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->pets_allowed)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Mascotas permitidas</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Outdoor Features -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100 hover:bg-gray-100/80 transition-all duration-300">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-tree text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Exteriores y Zonas Comunes</h3>
                                </div>

                                <div class="grid grid-cols-1 gap-4">
                                    @if ($inmueble->has_private_garden)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Jardín privado</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_yard)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Patio</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_terrace)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Terraza
                                            @if ($inmueble->terrace_surface)
                                                ({{ $inmueble->terrace_surface }}m²)
                                            @endif
                                            </span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_balcony)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Balcón</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_community_pool)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Piscina comunitaria</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_private_pool)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Piscina privada</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_tennis_court)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Pista de tenis</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_gym)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Gimnasio</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_sports_area)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Zona deportiva</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_jacuzzi)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Jacuzzi</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_sauna)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Sauna</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_community_area)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Zona comunitaria</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_children_area)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Zona infantil</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_loading_area)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Zona de carga</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_storage_room)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Trastero</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_24h_access)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Acceso 24h</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_free_parking)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Parking gratuito</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_parking)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Plaza de parking</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->nearby_public_transport)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Transporte público cercano</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_internal_transport)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Transporte interno</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Comfort Features -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-couch text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Confort</h3>
                                </div>
                                <div class="space-y-4">
                                    @if ($inmueble->has_air_conditioning)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Aire acondicionado</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_heating)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Calefacción</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_jacuzzi)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Jacuzzi</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_sauna)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Sauna</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_home_automation)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Domótica</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_internet)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Internet</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_suite_bathroom)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Baño suite</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_alarm)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Alarma</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Business Features -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-building text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Características Comerciales</h3>
                                </div>
                                <div class="space-y-4">
                                    @if ($inmueble->has_loading_area)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Zona de carga</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_24h_access)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Acceso 24h</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_internal_transport)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Transporte interno</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_access_code)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Código de acceso</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_free_parking)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Parking gratuito</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->nearby_public_transport)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Transporte público cercano</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Appliances -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-plug text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Electrodomésticos</h3>
                                </div>
                                <div class="space-y-4">
                                    @if ($inmueble->has_home_appliances)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Electrodomésticos</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_oven)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Horno</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_washing_machine)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Lavadora</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_microwave)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Microondas</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_fridge)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Frigorífico</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_tv)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">TV</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_office_kitchen)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Cocina de oficina</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_laundry)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Lavandería</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Materials -->
                            <div class="feature-card bg-gray-50 rounded-2xl p-6 border border-gray-100">
                                <div class="flex items-center space-x-3 mb-6">
                                    <div class="w-10 h-10 bg-primary-100 rounded-xl flex items-center justify-center">
                                        <i class="fas fa-palette text-primary-600"></i>
                                    </div>
                                    <h3 class="text-xl font-semibold text-gray-800">Materiales</h3>
                                </div>
                                <div class="space-y-4">
                                    @if ($inmueble->has_parquet)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Parquet</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_stoneware)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Gres</span>
                                        </div>
                                    @endif
                                    @if ($inmueble->has_smoke_outlet)
                                        <div class="flex items-center space-x-3 text-gray-600">
                                            <span class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-check text-green-500"></i>
                                            </span>
                                            <span class="text-sm">Salida de humos</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location Map -->
                    @if ($inmueble->latitude && $inmueble->longitude)
                        <div class="bg-white rounded-3xl shadow-xl overflow-hidden">
                            <div class="p-8">
                                <h2 class="text-3xl font-bold mb-6 gradient-text inline-block">
                                    <i class="fas fa-map mr-3"></i>
                                    Ubicación
                                </h2>
                            </div>
                            <div class="h-[500px] rounded-b-3xl overflow-hidden" id="map"></div>
                        </div>
                    @endif
                </div>

                <!-- Right Column (1/3) -->
                <div class="lg:col-span-1 space-y-8">
                    <!-- Contact Section -->
                    <div class="bg-gradient-to-br from-primary-500 to-primary-700 rounded-3xl shadow-xl p-8 text-white sticky top-24">
                        <div class="absolute inset-0 bg-pattern opacity-10 rounded-3xl"></div>
                        <div class="relative">
                            <span class="inline-block px-4 py-1 rounded-full bg-white/20 text-sm mb-6">
                                Contacto Directo
                            </span>
                            <h3 class="text-3xl font-bold mb-6">¿Te interesa este inmueble?</h3>
                            <p class="text-lg mb-8 opacity-90">Contacta con nosotros para más información o para concertar una visita.</p>
                            <div class="space-y-4">
                                <a href="tel:+34123456789"
                                    class="block w-full bg-white text-primary-600 text-center py-4 rounded-xl font-semibold hover:bg-gray-50 transition-colors duration-300 shadow-lg hover:shadow-xl transform hover:scale-105">
                                    <i class="fas fa-phone mr-2"></i>
                                    Llamar ahora
                                </a>
                                <a href="mailto:info@sayco.com"
                                    class="block w-full bg-white/10 backdrop-blur text-white text-center py-4 rounded-xl font-semibold hover:bg-white/20 transition-colors duration-300 border border-white/20">
                                    <i class="fas fa-envelope mr-2"></i>
                                    Enviar email
                                </a>
                                <div class="relative">
                                    <input type="text"
                                        value="https://crm.sayco.com/inmueble/{{ $inmueble->id }}"
                                        class="w-full bg-white/10 backdrop-blur text-white py-4 pl-4 pr-24 rounded-xl font-semibold border border-white/20"
                                        id="share-url"
                                        readonly>
                                    <button onclick="copyShareUrl()"
                                        class="absolute right-2 top-1/2 -translate-y-1/2 bg-white text-primary-600 px-4 py-2 rounded-lg font-semibold hover:bg-gray-50 transition-colors duration-300">
                                        Copiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Property Details -->
                    <div class="bg-white rounded-3xl shadow-xl p-8">
                        <h3 class="text-2xl font-bold mb-8 gradient-text inline-block">
                            <i class="fas fa-info-circle mr-3"></i>
                            Detalles del Inmueble
                        </h3>
                        <div class="space-y-6">
                            <div class="p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors duration-300">
                                <div class="flex items-center space-x-4">
                                    <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-home text-primary-600 text-xl"></i>
                                    </div>
                                    <div>
                                        <span class="block text-sm text-gray-500 mb-1">Tipo de Propiedad</span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            {{ $inmueble->tipoVivienda->nombre ?? 'No especificado' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            @if ($inmueble->estado)
                                <div class="p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-star text-primary-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm text-gray-500 mb-1">Estado</span>
                                            <span class="text-lg font-semibold text-gray-900">{{ $inmueble->estado }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @if ($inmueble->vendedor)
                                <div class="p-6 bg-gray-50 rounded-2xl hover:bg-gray-100 transition-colors duration-300">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-primary-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-primary-600 text-xl"></i>
                                        </div>
                                        <div>
                                            <span class="block text-sm text-gray-500 mb-1">Asesor Inmobiliario</span>
                                            <span class="text-lg font-semibold text-gray-900">
                                                {{ $inmueble->vendedor->nombre_completo ?? 'No especificado' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-16 relative overflow-hidden">
        <div class="absolute inset-0 bg-pattern opacity-5"></div>
        <div class="container mx-auto px-4 relative">
            <div class="grid md:grid-cols-2 gap-12">
                <div>
                    <h4 class="text-3xl font-bold flex items-center mb-6">
                        <i class="fas fa-home mr-3"></i>
                        Sayco
                    </h4>
                    <p class="text-gray-400 text-lg">Tu socio de confianza en bienes raíces</p>
                </div>
                <div class="md:text-right">
                    <p class="text-gray-400">&copy; {{ date('Y') }} Sayco. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Google Maps -->
    @if ($inmueble->latitude && $inmueble->longitude)
        <script>
            function initMap() {
                const location = {
                    lat: {{ $inmueble->latitude }},
                    lng: {{ $inmueble->longitude }}
                };

                const map = new google.maps.Map(document.getElementById('map'), {
                    zoom: 15,
                    center: location,
                    styles: [
                        {
                            "featureType": "water",
                            "elementType": "geometry",
                            "stylers": [{"color": "#e9e9e9"},{"lightness": 17}]
                        },
                        {
                            "featureType": "landscape",
                            "elementType": "geometry",
                            "stylers": [{"color": "#f5f5f5"},{"lightness": 20}]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "geometry.fill",
                            "stylers": [{"color": "#ffffff"},{"lightness": 17}]
                        }
                    ]
                });

                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: "{{ $inmueble->titulo }}",
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg width="40" height="40" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="20" cy="20" r="16" fill="#06b6d4" stroke="white" stroke-width="3"/>
                                <circle cx="20" cy="20" r="6" fill="white"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(40, 40),
                        anchor: new google.maps.Point(20, 20)
                    }
                });
            }
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap">
        </script>
    @endif

    <script>
        function changeMainImage(imageUrl) {
            const mainImage = document.getElementById('main-gallery-image');
            if (mainImage) {
                mainImage.style.opacity = '0';
                setTimeout(() => {
                    mainImage.src = imageUrl;
                    mainImage.style.opacity = '1';
                }, 300);
            }
        }

        // Add smooth fade transition to main image
        document.addEventListener('DOMContentLoaded', function() {
            const mainImage = document.getElementById('main-gallery-image');
            if (mainImage) {
                mainImage.style.transition = 'opacity 0.3s ease';
            }
        });

        function copyShareUrl() {
            const shareUrl = document.getElementById('share-url');
            shareUrl.select();
            document.execCommand('copy');

            // Cambiar temporalmente el texto del botón
            const button = shareUrl.nextElementSibling;
            const originalText = button.textContent;
            button.textContent = '¡Copiado!';

            setTimeout(() => {
                button.textContent = originalText;
            }, 2000);
        }
    </script>
</body>

</html>

