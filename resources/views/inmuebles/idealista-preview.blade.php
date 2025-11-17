<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $inmueble->titulo }} - Vista Idealista</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f5f5;
            color: #333;
        }
        .idealista-header {
            background: #FF6B35;
            color: white;
            padding: 20px 0;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .idealista-logo {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -1px;
        }
        .property-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .property-gallery {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 30px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }
        .main-image {
            width: 100%;
            height: 500px;
            object-fit: cover;
            background: #e0e0e0;
        }
        .gallery-thumbnails {
            display: flex;
            gap: 8px;
            padding: 12px;
            background: white;
            overflow-x: auto;
        }
        .thumbnail {
            width: 100px;
            height: 75px;
            object-fit: cover;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s;
            border: 2px solid transparent;
        }
        .thumbnail:hover {
            transform: scale(1.05);
            border-color: #FF6B35;
        }
        .property-header {
            background: white;
            border-radius: 12px;
            padding: 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .property-price {
            font-size: 42px;
            font-weight: 700;
            color: #FF6B35;
            margin-bottom: 10px;
        }
        .property-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 15px;
        }
        .property-location {
            font-size: 18px;
            color: #666;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .property-features {
            display: flex;
            gap: 30px;
            flex-wrap: wrap;
            padding: 20px 0;
            border-top: 1px solid #e0e0e0;
            border-bottom: 1px solid #e0e0e0;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 16px;
            color: #333;
        }
        .feature-item i {
            color: #FF6B35;
            font-size: 20px;
        }
        .property-content {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-top: 20px;
        }
        .property-details {
            background: white;
            border-radius: 12px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .property-sidebar {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .sidebar-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        }
        .sidebar-card h3 {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 15px;
            color: #1a1a1a;
        }
        .energy-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 18px;
            margin-top: 10px;
        }
        .energy-a { background: #00A651; color: white; }
        .energy-b { background: #8BC34A; color: white; }
        .energy-c { background: #FFEB3B; color: #333; }
        .energy-d { background: #FF9800; color: white; }
        .energy-e { background: #FF5722; color: white; }
        .energy-f { background: #E91E63; color: white; }
        .energy-g { background: #9C27B0; color: white; }
        .description {
            line-height: 1.8;
            color: #555;
            font-size: 16px;
            margin-top: 20px;
        }
        .characteristics-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }
        .characteristic-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px;
            background: #f9f9f9;
            border-radius: 8px;
        }
        .characteristic-item i {
            color: #FF6B35;
            width: 20px;
        }
        .contact-button {
            background: #FF6B35;
            color: white;
            border: none;
            padding: 16px 32px;
            border-radius: 8px;
            font-size: 18px;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 15px;
        }
        .contact-button:hover {
            background: #e55a2b;
        }
        .badge-idealista {
            background: #FF6B35;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-left: 10px;
        }
        @media (max-width: 768px) {
            .property-content {
                grid-template-columns: 1fr;
            }
            .property-price {
                font-size: 32px;
            }
            .property-title {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <!-- Header estilo Idealista -->
    <div class="idealista-header">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div class="idealista-logo">idealista</div>
                <div class="text-end">
                    <small>Vista previa</small>
                </div>
            </div>
        </div>
    </div>

    <div class="property-container">
        @php
            // Usar datos de Idealista si están disponibles, sino usar datos del CRM
            $property = $idealistaData ?? null;
            $titulo = $property['reference'] ?? $property['code'] ?? $inmueble->titulo ?? 'Propiedad';
            $precio = $property['operation']['price'] ?? $inmueble->valor_referencia ?? 0;
            $ubicacion = $property['address']['streetName'] ?? $inmueble->ubicacion ?? '';
            $codPostal = $property['address']['postalCode'] ?? $inmueble->cod_postal ?? '';
            $ciudad = $property['address']['town'] ?? '';
            $habitaciones = $property['features']['rooms'] ?? $inmueble->habitaciones ?? null;
            $banos = $property['features']['bathroomNumber'] ?? $inmueble->banos ?? null;
            $m2 = $property['features']['areaConstructed'] ?? $property['features']['areaUsable'] ?? $inmueble->m2 ?? null;
            $tipo = $property['type'] ?? null;
            $descripcion = '';
            if ($property && isset($property['descriptions']) && is_array($property['descriptions'])) {
                foreach ($property['descriptions'] as $desc) {
                    if (isset($desc['language']) && $desc['language'] === 'es') {
                        $descripcion = $desc['text'] ?? '';
                        break;
                    }
                }
                if (empty($descripcion) && !empty($property['descriptions'])) {
                    $descripcion = $property['descriptions'][0]['text'] ?? '';
                }
            }
            if (empty($descripcion)) {
                $descripcion = $inmueble->descripcion ?? 'Sin descripción disponible.';
            }

            // Imágenes: priorizar las de Idealista
            $imagenes = [];
            if (!empty($idealistaImages)) {
                foreach ($idealistaImages as $img) {
                    if (is_string($img)) {
                        $imagenes[] = $img;
                    } elseif (is_array($img)) {
                        $url = $img['url'] ?? $img['imageUrl'] ?? $img['src'] ?? $img['image'] ?? null;
                        if ($url) {
                            $imagenes[] = $url;
                        }
                    }
                }
            }
            // Si no hay imágenes de Idealista, usar las del CRM
            if (empty($imagenes)) {
                $galeria = json_decode($inmueble->galeria ?? '[]', true);
                $imagenes = is_array($galeria) ? array_values($galeria) : [];
            }

            $latitude = $property['address']['latitude'] ?? $inmueble->latitude ?? null;
            $longitude = $property['address']['longitude'] ?? $inmueble->longitude ?? null;
            $propertyId = $inmueble->idealista_property_id ?? null;
        @endphp

        <!-- Galería de imágenes -->
        @if(count($imagenes) > 0)
            <div class="property-gallery">
                <img src="{{ $imagenes[0] }}" alt="{{ $titulo }}" class="main-image" id="mainImage">
                @if(count($imagenes) > 1)
                    <div class="gallery-thumbnails">
                        @foreach($imagenes as $index => $img)
                            <img src="{{ $img }}" alt="Imagen {{ $index + 1 }}" class="thumbnail"
                                 onclick="document.getElementById('mainImage').src='{{ $img }}'">
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="property-gallery">
                <div class="main-image d-flex align-items-center justify-content-center" style="background: #e0e0e0; color: #999;">
                    <i class="fas fa-image fa-3x"></i>
                </div>
            </div>
        @endif

        <!-- Header de la propiedad -->
        <div class="property-header">
            <div class="property-price">
                {{ number_format($precio, 0, ',', '.') }} €
            </div>
            <h1 class="property-title">
                {{ $titulo }}
                @if($propertyId)
                    <span class="badge-idealista">#{{ $propertyId }}</span>
                @endif
            </h1>
            <div class="property-location">
                <i class="fas fa-map-marker-alt"></i>
                <span>
                    @if($ubicacion){{ $ubicacion }}@endif
                    @if($codPostal){{ $ubicacion ? ', ' : '' }}{{ $codPostal }}@endif
                    @if($ciudad){{ ($ubicacion || $codPostal) ? ', ' : '' }}{{ $ciudad }}@endif
                </span>
            </div>
            <div class="property-features">
                @if($habitaciones)
                    <div class="feature-item">
                        <i class="fas fa-bed"></i>
                        <span>{{ $habitaciones }} {{ $habitaciones == 1 ? 'habitación' : 'habitaciones' }}</span>
                    </div>
                @endif
                @if($banos)
                    <div class="feature-item">
                        <i class="fas fa-bath"></i>
                        <span>{{ $banos }} {{ $banos == 1 ? 'baño' : 'baños' }}</span>
                    </div>
                @endif
                @if($m2)
                    <div class="feature-item">
                        <i class="fas fa-ruler-combined"></i>
                        <span>{{ $m2 }} m²</span>
                    </div>
                @endif
                @if($tipo)
                    <div class="feature-item">
                        <i class="fas fa-home"></i>
                        <span>{{ ucfirst($tipo) }}</span>
                    </div>
                @elseif($inmueble->tipoVivienda)
                    <div class="feature-item">
                        <i class="fas fa-home"></i>
                        <span>{{ $inmueble->tipoVivienda->nombre }}</span>
                    </div>
                @endif
            </div>
        </div>

        <div class="property-content">
            <!-- Contenido principal -->
            <div class="property-details">
                <h2 style="font-size: 24px; font-weight: 600; margin-bottom: 20px; color: #1a1a1a;">Descripción</h2>
                <div class="description">
                    {{ $descripcion }}
                </div>

                @php
                    $energyCert = null;
                    if ($property && isset($property['features']['energyCertificateRating'])) {
                        $energyCert = $property['features']['energyCertificateRating'];
                    } elseif ($inmueble->cert_energetico_elegido) {
                        $energyCert = $inmueble->cert_energetico_elegido;
                    }
                @endphp
                @if($energyCert && strtolower($energyCert) !== 'unknown' && strtolower($energyCert) !== 'in_process')
                    <div style="margin-top: 30px; padding: 20px; background: #f9f9f9; border-radius: 8px;">
                        <h3 style="font-size: 18px; margin-bottom: 10px;">Certificado energético</h3>
                        <span class="energy-badge energy-{{ strtolower($energyCert) }}">
                            {{ strtoupper($energyCert) }}
                        </span>
                        @if($inmueble->energy_certificate_status)
                            <p style="margin-top: 10px; color: #666; font-size: 14px;">
                                Estado: {{ $inmueble->energy_certificate_status }}
                            </p>
                        @endif
                    </div>
                @endif

                @if($caracteristicas->count() > 0)
                    <div style="margin-top: 30px;">
                        <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Características</h3>
                        <div class="characteristics-grid">
                            @foreach($caracteristicas as $car)
                                <div class="characteristic-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>{{ $car->nombre }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Características adicionales -->
                @php
                    $features = $property['features'] ?? [];
                    $hasElevator = $features['liftAvailable'] ?? $inmueble->has_elevator ?? false;
                    $hasTerrace = $features['terrace'] ?? $inmueble->has_terrace ?? false;
                    $hasBalcony = $features['balcony'] ?? $inmueble->has_balcony ?? false;
                    $hasParking = $features['parkingAvailable'] ?? $inmueble->has_parking ?? false;
                    $hasAC = $features['conditionedAir'] ?? $inmueble->has_air_conditioning ?? false;
                    $hasPool = $features['pool'] ?? $inmueble->has_private_pool ?? false;
                    $hasGarden = $features['garden'] ?? $inmueble->has_private_garden ?? false;
                    $furnished = $features['furnished'] ?? $inmueble->furnished ?? false;
                @endphp
                <div style="margin-top: 30px;">
                    <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Detalles adicionales</h3>
                    <div class="characteristics-grid">
                        @if($hasElevator)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Ascensor</span>
                            </div>
                        @endif
                        @if($hasTerrace)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Terraza</span>
                            </div>
                        @endif
                        @if($hasBalcony)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Balcón</span>
                            </div>
                        @endif
                        @if($hasParking)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Parking</span>
                            </div>
                        @endif
                        @if($hasAC)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Aire acondicionado</span>
                            </div>
                        @endif
                        @if($hasPool)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Piscina privada</span>
                            </div>
                        @endif
                        @if($hasGarden)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Jardín privado</span>
                            </div>
                        @endif
                        @if($furnished)
                            <div class="characteristic-item">
                                <i class="fas fa-check-circle"></i>
                                <span>Amueblado</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="property-sidebar">
                <div class="sidebar-card">
                    <h3>Información del inmueble</h3>
                    <div style="line-height: 2;">
                        @php
                            $estado = $property['state'] ?? null;
                            $conservation = $features['conservation'] ?? $inmueble->conservation_status ?? null;
                            $yearBuilt = $features['builtYear'] ?? $inmueble->year_built ?? null;
                            $m2Construidos = $features['areaConstructed'] ?? $inmueble->m2_construidos ?? null;
                        @endphp
                        @if($estado)
                            <div><strong>Estado:</strong> {{ ucfirst($estado) }}</div>
                        @elseif($inmueble->estado)
                            <div><strong>Estado:</strong> {{ $inmueble->estado }}</div>
                        @endif
                        @if($inmueble->disponibilidad)
                            <div><strong>Disponibilidad:</strong> {{ $inmueble->disponibilidad }}</div>
                        @endif
                        @if($m2Construidos)
                            <div><strong>M² construidos:</strong> {{ $m2Construidos }} m²</div>
                        @endif
                        @if($yearBuilt)
                            <div><strong>Año construcción:</strong> {{ $yearBuilt }}</div>
                        @endif
                        @if($conservation)
                            <div><strong>Conservación:</strong> {{ ucfirst($conservation) }}</div>
                        @endif
                        @if($property && isset($property['code']))
                            <div><strong>Código:</strong> {{ $property['code'] }}</div>
                        @endif
                    </div>
                </div>

                @if($inmueble->vendedor)
                    <div class="sidebar-card">
                        <h3>Contacto</h3>
                        <div style="line-height: 2;">
                            <div><strong>{{ $inmueble->vendedor->nombre_completo ?? 'Contacto' }}</strong></div>
                            @if($inmueble->vendedor->telefono)
                                <div><i class="fas fa-phone"></i> {{ $inmueble->vendedor->telefono }}</div>
                            @endif
                            @if($inmueble->vendedor->correo)
                                <div><i class="fas fa-envelope"></i> {{ $inmueble->vendedor->correo }}</div>
                            @endif
                        </div>
                        <button class="contact-button">
                            <i class="fas fa-phone me-2"></i>Contactar
                        </button>
                    </div>
                @endif

                @if($latitude && $longitude)
                    <div class="sidebar-card">
                        <h3>Ubicación</h3>
                        <div id="map" style="height: 250px; border-radius: 8px; overflow: hidden;"></div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($latitude && $longitude)
        <!-- Leaflet Maps -->
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
            crossorigin=""/>
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
            crossorigin=""></script>
        <script>
            const map = L.map('map').setView([{{ $latitude }}, {{ $longitude }}], 15);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                maxZoom: 19
            }).addTo(map);
            L.marker([{{ $latitude }}, {{ $longitude }}]).addTo(map)
                .bindPopup("{{ $titulo }}");
        </script>
    @endif
</body>
</html>

