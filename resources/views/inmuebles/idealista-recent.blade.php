@extends('layouts.app')

@section('encabezado', 'Últimos Inmuebles en Idealista')
@section('subtitulo', 'Los 3 inmuebles más recientes subidos a Idealista')

@section('head')
    <style>
        .idealista-recent-container {
            padding: 20px 0;
        }
        .property-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .property-image-container {
            position: relative;
            width: 100%;
            height: 300px;
            overflow: hidden;
            background: #f0f0f0;
        }
        .property-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .property-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            background: #ff6b00;
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            z-index: 10;
        }
        .property-content {
            padding: 24px;
        }
        .property-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #ff6b00;
            margin-bottom: 10px;
        }
        .property-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }
        .property-location {
            color: #6b7280;
            font-size: 0.95rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .property-features {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        .property-feature {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #4b5563;
            font-size: 0.9rem;
        }
        .property-feature i {
            color: #6b8e6b;
        }
        .property-description {
            color: #6b7280;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .property-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .property-id {
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .property-date {
            color: #9ca3af;
            font-size: 0.85rem;
        }
        .btn-view {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.2s ease;
        }
        .btn-view:hover {
            transform: translateY(-2px);
            color: white;
        }
        .no-properties {
            text-align: center;
            padding: 60px 20px;
            color: #6b7280;
        }
        .no-properties i {
            font-size: 4rem;
            color: #d1d5db;
            margin-bottom: 20px;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid py-4">
        <div class="idealista-recent-container">
            @if(count($inmueblesData) > 0)
                @foreach($inmueblesData as $data)
                    @php
                        $inmueble = $data['inmueble'];
                        $idealistaData = $data['idealistaData'];
                        $idealistaImages = $data['idealistaImages'];

                        // Usar datos de Idealista si están disponibles, sino usar datos del CRM
                        $titulo = $idealistaData['reference'] ?? $idealistaData['code'] ?? $inmueble->titulo ?? 'Propiedad';
                        $precio = $idealistaData['operation']['price'] ?? $inmueble->valor_referencia ?? 0;
                        $ubicacion = $idealistaData['address']['streetName'] ?? $inmueble->ubicacion ?? '';
                        $codPostal = $idealistaData['address']['postalCode'] ?? $inmueble->cod_postal ?? '';
                        $habitaciones = $idealistaData['features']['rooms'] ?? $inmueble->habitaciones ?? null;
                        $banos = $idealistaData['features']['bathroomNumber'] ?? $inmueble->banos ?? null;
                        $m2 = $idealistaData['features']['areaConstructed'] ?? $idealistaData['features']['areaUsable'] ?? $inmueble->m2 ?? null;
                        $descripcion = !empty($idealistaData['descriptions']) ? $idealistaData['descriptions'][0]['text'] ?? '' : ($inmueble->descripcion ?? '');

                        // Obtener primera imagen
                        $primeraImagen = null;
                        if (!empty($idealistaImages)) {
                            $primeraImagen = is_array($idealistaImages[0]) ? ($idealistaImages[0]['url'] ?? $idealistaImages[0]['imageUrl'] ?? null) : $idealistaImages[0];
                        } elseif ($inmueble->galeria) {
                            $galeria = json_decode($inmueble->galeria, true);
                            if (!empty($galeria)) {
                                $primeraImagen = reset($galeria);
                            }
                        }
                    @endphp

                    <div class="property-card">
                        <div class="property-image-container">
                            @if($primeraImagen)
                                <img src="{{ $primeraImagen }}" alt="{{ $titulo }}" class="property-image">
                            @else
                                <div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #9ca3af;">
                                    <i class="fas fa-home" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                            <div class="property-badge">
                                ID: {{ $inmueble->idealista_property_id }}
                            </div>
                        </div>
                        <div class="property-content">
                            <div class="property-price">
                                @if($precio > 0)
                                    {{ number_format($precio, 0, ',', '.') }} €
                                @else
                                    Precio no especificado
                                @endif
                            </div>
                            <h3 class="property-title">{{ $titulo }}</h3>
                            <div class="property-location">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $ubicacion }}{{ $codPostal ? ', ' . $codPostal : '' }}</span>
                            </div>
                            <div class="property-features">
                                @if($habitaciones)
                                    <div class="property-feature">
                                        <i class="fas fa-bed"></i>
                                        <span>{{ $habitaciones }} hab.</span>
                                    </div>
                                @endif
                                @if($banos)
                                    <div class="property-feature">
                                        <i class="fas fa-bath"></i>
                                        <span>{{ $banos }} baños</span>
                                    </div>
                                @endif
                                @if($m2)
                                    <div class="property-feature">
                                        <i class="fas fa-ruler-combined"></i>
                                        <span>{{ $m2 }} m²</span>
                                    </div>
                                @endif
                            </div>
                            @if($descripcion)
                                <div class="property-description">
                                    {{ $descripcion }}
                                </div>
                            @endif
                            <div class="property-footer">
                                <div>
                                    <div class="property-id">Idealista ID: {{ $inmueble->idealista_property_id }}</div>
                                    <div class="property-date">
                                        Subido: {{ $inmueble->idealista_synced_at ? $inmueble->idealista_synced_at->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                                <a href="{{ route('inmuebles.idealista-preview', $inmueble->id) }}" class="btn-view" target="_blank">
                                    <i class="fas fa-external-link-alt"></i> Ver en Idealista
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-properties">
                    <i class="fas fa-inbox"></i>
                    <h3>No hay inmuebles subidos a Idealista</h3>
                    <p>Los inmuebles que subas a Idealista aparecerán aquí.</p>
                </div>
            @endif
        </div>
    </div>
@endsection

