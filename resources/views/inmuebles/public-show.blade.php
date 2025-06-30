<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $inmueble->titulo }} - Inmobiliaria</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #64748b;
            --accent-color: #f59e0b;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --light-bg: #f8fafc;
            --border-color: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #334155;
            background-color: var(--light-bg);
        }

        .navbar {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
        }

        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .property-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .property-location {
            font-size: 1.2rem;
            opacity: 0.9;
            margin-bottom: 1.5rem;
        }

        .price-badge {
            background: var(--accent-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            display: inline-block;
        }

        .main-content {
            padding: 3rem 0;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }

        .card-header {
            background: linear-gradient(135deg, var(--primary-color), #1d4ed8);
            color: white;
            border: none;
            padding: 1.5rem;
            font-weight: 600;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-item {
            display: flex;
            align-items: center;
            padding: 1rem;
            background: white;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        .feature-icon {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            flex-shrink: 0;
        }

        .feature-text {
            font-weight: 500;
        }

        .gallery-section {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .main-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            border-radius: 15px 15px 0 0;
        }

        .image-placeholder {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 15px 15px 0 0;
            color: var(--secondary-color);
            font-size: 1.2rem;
        }

        .contact-section {
            background: linear-gradient(135deg, var(--success-color), #059669);
            color: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
        }

        .contact-btn {
            background: white;
            color: var(--success-color);
            border: none;
            padding: 1rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .contact-btn:hover {
            background: var(--light-bg);
            transform: translateY(-2px);
            color: var(--success-color);
        }

        .specs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .spec-item {
            background: var(--light-bg);
            padding: 1rem;
            border-radius: 10px;
            text-align: center;
        }

        .spec-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary-color);
            display: block;
        }

        .spec-label {
            font-size: 0.9rem;
            color: var(--secondary-color);
            margin-top: 0.25rem;
        }

        .characteristics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .characteristic-category {
            background: var(--light-bg);
            padding: 1.5rem;
            border-radius: 10px;
        }

        .characteristic-category h6 {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1rem;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
        }

        .characteristic-list {
            list-style: none;
            padding: 0;
        }

        .characteristic-list li {
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
        }

        .characteristic-list li:last-child {
            border-bottom: none;
        }

        .characteristic-list li i {
            color: var(--success-color);
            margin-right: 0.5rem;
            width: 16px;
        }

        .map-container {
            height: 300px;
            border-radius: 15px;
            overflow: hidden;
            margin-top: 1rem;
        }

        .footer {
            background: #1e293b;
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }

        @media (max-width: 768px) {
            .property-title {
                font-size: 2rem;
            }

            .feature-grid {
                grid-template-columns: 1fr;
            }

            .specs-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .characteristics-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-home me-2"></i>
                Inmobiliaria
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-content text-center">
                <h1 class="property-title">{{ $inmueble->titulo }}</h1>
                <p class="property-location">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    {{ $inmueble->ubicacion }}
                    @if ($inmueble->cod_postal)
                        , {{ $inmueble->cod_postal }}
                    @endif
                </p>
                @if ($inmueble->valor_referencia)
                    <div class="price-badge">
                        <i class="fas fa-euro-sign me-1"></i>
                        {{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €
                    </div>
                @endif
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <section class="main-content">
        <div class="container">
            <div class="row">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <!-- Gallery Section -->
                    <div class="gallery-section">
                        @if ($inmueble->galeria)
                            @php
                                $galeria = json_decode($inmueble->galeria, true);
                                $mainImage = $galeria['1'] ?? null;
                            @endphp
                            @if ($mainImage)
                                <img src="{{ $mainImage }}" alt="{{ $inmueble->titulo }}" class="main-image">
                            @else
                                <div class="image-placeholder">
                                    <i class="fas fa-image me-2"></i>
                                    Sin imagen disponible
                                </div>
                            @endif
                        @else
                            <div class="image-placeholder">
                                <i class="fas fa-image me-2"></i>
                                Sin imagen disponible
                            </div>
                        @endif
                    </div>

                    <!-- Description -->
                    @if ($inmueble->descripcion)
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-align-left me-2"></i>
                                Descripción
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $inmueble->descripcion }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Specifications -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-ruler-combined me-2"></i>
                            Especificaciones
                        </div>
                        <div class="card-body">
                            <div class="specs-grid">
                                @if ($inmueble->m2)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->m2 }}</span>
                                        <span class="spec-label">m²</span>
                                    </div>
                                @endif
                                @if ($inmueble->m2_construidos)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->m2_construidos }}</span>
                                        <span class="spec-label">m² Construidos</span>
                                    </div>
                                @endif
                                @if ($inmueble->habitaciones)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->habitaciones }}</span>
                                        <span class="spec-label">Habitaciones</span>
                                    </div>
                                @endif
                                @if ($inmueble->banos)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->banos }}</span>
                                        <span class="spec-label">Baños</span>
                                    </div>
                                @endif
                                @if ($inmueble->year_built)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->year_built }}</span>
                                        <span class="spec-label">Año Construcción</span>
                                    </div>
                                @endif
                                @if ($inmueble->land_area)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->land_area }}</span>
                                        <span class="spec-label">m² Terreno</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Characteristics -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-list-check me-2"></i>
                            Características
                        </div>
                        <div class="card-body">
                            <div class="characteristics-grid">
                                <!-- Basic Features -->
                                <div class="characteristic-category">
                                    <h6><i class="fas fa-home me-2"></i>Características Básicas</h6>
                                    <ul class="characteristic-list">
                                        @if ($inmueble->furnished)
                                            <li><i class="fas fa-check"></i>Amueblado</li>
                                        @endif
                                        @if ($inmueble->has_elevator)
                                            <li><i class="fas fa-check"></i>Ascensor</li>
                                        @endif
                                        @if ($inmueble->has_terrace)
                                            <li><i class="fas fa-check"></i>Terraza</li>
                                        @endif
                                        @if ($inmueble->has_balcony)
                                            <li><i class="fas fa-check"></i>Balcón</li>
                                        @endif
                                        @if ($inmueble->has_parking)
                                            <li><i class="fas fa-check"></i>Parking</li>
                                        @endif
                                        @if ($inmueble->has_air_conditioning)
                                            <li><i class="fas fa-check"></i>Aire acondicionado</li>
                                        @endif
                                        @if ($inmueble->has_heating)
                                            <li><i class="fas fa-check"></i>Calefacción</li>
                                        @endif
                                        @if ($inmueble->has_security_door)
                                            <li><i class="fas fa-check"></i>Puerta de seguridad</li>
                                        @endif
                                        @if ($inmueble->has_equipped_kitchen)
                                            <li><i class="fas fa-check"></i>Cocina equipada</li>
                                        @endif
                                        @if ($inmueble->has_wardrobe)
                                            <li><i class="fas fa-check"></i>Armarios empotrados</li>
                                        @endif
                                        @if ($inmueble->has_storage_room)
                                            <li><i class="fas fa-check"></i>Trastero</li>
                                        @endif
                                        @if ($inmueble->pets_allowed)
                                            <li><i class="fas fa-check"></i>Mascotas permitidas</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Outdoor Features -->
                                <div class="characteristic-category">
                                    <h6><i class="fas fa-tree me-2"></i>Exteriores</h6>
                                    <ul class="characteristic-list">
                                        @if ($inmueble->has_private_garden)
                                            <li><i class="fas fa-check"></i>Jardín privado</li>
                                        @endif
                                        @if ($inmueble->has_yard)
                                            <li><i class="fas fa-check"></i>Patio</li>
                                        @endif
                                        @if ($inmueble->has_community_pool)
                                            <li><i class="fas fa-check"></i>Piscina comunitaria</li>
                                        @endif
                                        @if ($inmueble->has_private_pool)
                                            <li><i class="fas fa-check"></i>Piscina privada</li>
                                        @endif
                                        @if ($inmueble->has_tennis_court)
                                            <li><i class="fas fa-check"></i>Pista de tenis</li>
                                        @endif
                                        @if ($inmueble->has_gym)
                                            <li><i class="fas fa-check"></i>Gimnasio</li>
                                        @endif
                                        @if ($inmueble->has_sports_area)
                                            <li><i class="fas fa-check"></i>Zona deportiva</li>
                                        @endif
                                        @if ($inmueble->has_children_area)
                                            <li><i class="fas fa-check"></i>Zona infantil</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Comfort Features -->
                                <div class="characteristic-category">
                                    <h6><i class="fas fa-couch me-2"></i>Confort</h6>
                                    <ul class="characteristic-list">
                                        @if ($inmueble->has_jacuzzi)
                                            <li><i class="fas fa-check"></i>Jacuzzi</li>
                                        @endif
                                        @if ($inmueble->has_sauna)
                                            <li><i class="fas fa-check"></i>Sauna</li>
                                        @endif
                                        @if ($inmueble->has_suite_bathroom)
                                            <li><i class="fas fa-check"></i>Baño suite</li>
                                        @endif
                                        @if ($inmueble->has_home_automation)
                                            <li><i class="fas fa-check"></i>Domótica</li>
                                        @endif
                                        @if ($inmueble->has_internet)
                                            <li><i class="fas fa-check"></i>Internet</li>
                                        @endif
                                        @if ($inmueble->has_alarm)
                                            <li><i class="fas fa-check"></i>Alarma</li>
                                        @endif
                                        @if ($inmueble->has_24h_access)
                                            <li><i class="fas fa-check"></i>Acceso 24h</li>
                                        @endif
                                        @if ($inmueble->has_community_area)
                                            <li><i class="fas fa-check"></i>Zona comunitaria</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Appliances -->
                                <div class="characteristic-category">
                                    <h6><i class="fas fa-plug me-2"></i>Electrodomésticos</h6>
                                    <ul class="characteristic-list">
                                        @if ($inmueble->has_home_appliances)
                                            <li><i class="fas fa-check"></i>Electrodomésticos</li>
                                        @endif
                                        @if ($inmueble->has_oven)
                                            <li><i class="fas fa-check"></i>Horno</li>
                                        @endif
                                        @if ($inmueble->has_washing_machine)
                                            <li><i class="fas fa-check"></i>Lavadora</li>
                                        @endif
                                        @if ($inmueble->has_microwave)
                                            <li><i class="fas fa-check"></i>Microondas</li>
                                        @endif
                                        @if ($inmueble->has_fridge)
                                            <li><i class="fas fa-check"></i>Frigorífico</li>
                                        @endif
                                        @if ($inmueble->has_tv)
                                            <li><i class="fas fa-check"></i>TV</li>
                                        @endif
                                        @if ($inmueble->has_office_kitchen)
                                            <li><i class="fas fa-check"></i>Cocina de oficina</li>
                                        @endif
                                        @if ($inmueble->has_laundry)
                                            <li><i class="fas fa-check"></i>Lavandería</li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Materials -->
                                <div class="characteristic-category">
                                    <h6><i class="fas fa-palette me-2"></i>Materiales</h6>
                                    <ul class="characteristic-list">
                                        @if ($inmueble->has_parquet)
                                            <li><i class="fas fa-check"></i>Parquet</li>
                                        @endif
                                        @if ($inmueble->has_stoneware)
                                            <li><i class="fas fa-check"></i>Gres</li>
                                        @endif
                                        @if ($inmueble->has_smoke_outlet)
                                            <li><i class="fas fa-check"></i>Salida de humos</li>
                                        @endif
                                        @if ($inmueble->has_loading_area)
                                            <li><i class="fas fa-check"></i>Zona de carga</li>
                                        @endif
                                        @if ($inmueble->has_internal_transport)
                                            <li><i class="fas fa-check"></i>Transporte interno</li>
                                        @endif
                                        @if ($inmueble->has_access_code)
                                            <li><i class="fas fa-check"></i>Código de acceso</li>
                                        @endif
                                        @if ($inmueble->has_free_parking)
                                            <li><i class="fas fa-check"></i>Parking gratuito</li>
                                        @endif
                                        @if ($inmueble->nearby_public_transport)
                                            <li><i class="fas fa-check"></i>Transporte público cercano</li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Energy Certificate -->
                    @if ($inmueble->cert_energetico_elegido || $inmueble->energy_certificate_status)
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-leaf me-2"></i>
                                Certificación Energética
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if ($inmueble->cert_energetico_elegido)
                                        <div class="col-md-6">
                                            <div class="spec-item">
                                                <span
                                                    class="spec-value">{{ $inmueble->cert_energetico_elegido }}</span>
                                                <span class="spec-label">Calificación Energética</span>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($inmueble->energy_certificate_status)
                                        <div class="col-md-6">
                                            <div class="spec-item">
                                                <span
                                                    class="spec-value">{{ $inmueble->energy_certificate_status }}</span>
                                                <span class="spec-label">Estado del Certificado</span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Location -->
                    @if ($inmueble->latitude && $inmueble->longitude)
                        <div class="card mb-4">
                            <div class="card-header">
                                <i class="fas fa-map me-2"></i>
                                Ubicación
                            </div>
                            <div class="card-body">
                                <div class="map-container" id="map">
                                    <!-- Google Maps will be loaded here -->
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Contact Section -->
                    <div class="contact-section">
                        <h4><i class="fas fa-phone me-2"></i>¿Te interesa este inmueble?</h4>
                        <p>Contacta con nosotros para más información o para concertar una visita.</p>
                        <a href="tel:+34123456789" class="contact-btn">
                            <i class="fas fa-phone me-2"></i>
                            Llamar ahora
                        </a>
                        <br>
                        <a href="mailto:info@inmobiliaria.com" class="contact-btn">
                            <i class="fas fa-envelope me-2"></i>
                            Enviar email
                        </a>
                    </div>

                    <!-- Property Details -->
                    <div class="card mt-4">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-2"></i>
                            Detalles del Inmueble
                        </div>
                        <div class="card-body">
                            <div class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="feature-text">
                                    <strong>Tipo:</strong><br>
                                    {{ $inmueble->tipoVivienda->nombre ?? 'No especificado' }}
                                </div>
                            </div>

                            @if ($inmueble->estado)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <div class="feature-text">
                                        <strong>Estado:</strong><br>
                                        {{ $inmueble->estado }}
                                    </div>
                                </div>
                            @endif

                            @if ($inmueble->disponibilidad)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-tag"></i>
                                    </div>
                                    <div class="feature-text">
                                        <strong>Disponibilidad:</strong><br>
                                        {{ $inmueble->disponibilidad }}
                                    </div>
                                </div>
                            @endif

                            @if ($inmueble->conservation_status)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="feature-text">
                                        <strong>Conservación:</strong><br>
                                        {{ $inmueble->conservation_status }}
                                    </div>
                                </div>
                            @endif

                            @if ($inmueble->vendedor)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="feature-text">
                                        <strong>Vendedor:</strong><br>
                                        {{ $inmueble->vendedor->nombre_completo ?? 'No especificado' }}
                                    </div>
                                </div>
                            @endif

                            @if ($inmueble->referencia_catastral)
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-file-alt"></i>
                                    </div>
                                    <div class="feature-text">
                                        <strong>Ref. Catastral:</strong><br>
                                        {{ $inmueble->referencia_catastral }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Additional Features -->
                    @if ($inmueble->terrace_surface || $inmueble->has_terrace)
                        <div class="card mt-4">
                            <div class="card-header">
                                <i class="fas fa-umbrella-beach me-2"></i>
                                Terraza
                            </div>
                            <div class="card-body">
                                @if ($inmueble->terrace_surface)
                                    <div class="spec-item">
                                        <span class="spec-value">{{ $inmueble->terrace_surface }}</span>
                                        <span class="spec-label">m² de Terraza</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5><i class="fas fa-home me-2"></i>Inmobiliaria</h5>
                    <p>Tu socio de confianza en bienes raíces</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p>&copy; {{ date('Y') }} Inmobiliaria. Todos los derechos reservados.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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
                    styles: [{
                            "featureType": "all",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#f5f5f5"
                            }]
                        },
                        {
                            "featureType": "all",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#616161"
                            }]
                        },
                        {
                            "featureType": "all",
                            "elementType": "labels.text.stroke",
                            "stylers": [{
                                "color": "#f5f5f5"
                            }]
                        },
                        {
                            "featureType": "administrative.land_parcel",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#bdbdbd"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#eeeeee"
                            }]
                        },
                        {
                            "featureType": "poi",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#757575"
                            }]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#e5e5e5"
                            }]
                        },
                        {
                            "featureType": "poi.park",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#9e9e9e"
                            }]
                        },
                        {
                            "featureType": "road",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#ffffff"
                            }]
                        },
                        {
                            "featureType": "road.arterial",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#757575"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#dadada"
                            }]
                        },
                        {
                            "featureType": "road.highway",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#616161"
                            }]
                        },
                        {
                            "featureType": "road.local",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#9e9e9e"
                            }]
                        },
                        {
                            "featureType": "transit.line",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#e5e5e5"
                            }]
                        },
                        {
                            "featureType": "transit.station",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#eeeeee"
                            }]
                        },
                        {
                            "featureType": "water",
                            "elementType": "geometry",
                            "stylers": [{
                                "color": "#c9c9c9"
                            }]
                        },
                        {
                            "featureType": "water",
                            "elementType": "labels.text.fill",
                            "stylers": [{
                                "color": "#9e9e9e"
                            }]
                        }
                    ]
                });

                const marker = new google.maps.Marker({
                    position: location,
                    map: map,
                    title: "{{ $inmueble->titulo }}",
                    icon: {
                        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                            <svg width="32" height="32" viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <circle cx="16" cy="16" r="12" fill="#2563eb" stroke="white" stroke-width="2"/>
                                <circle cx="16" cy="16" r="4" fill="white"/>
                            </svg>
                        `),
                        scaledSize: new google.maps.Size(32, 32),
                        anchor: new google.maps.Point(16, 16)
                    }
                });
            }
        </script>
        <script async defer
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMap">
        </script>
    @endif
</body>

</html>
