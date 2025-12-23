@extends('layouts.app')

@section('encabezado', 'Propiedades en Idealista')
@section('subtitulo', 'Lista completa de propiedades sincronizadas con Idealista')

@section('content')
<div class="container-fluid py-4">
    <style>
        .property-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 20px;
            background: white;
        }

        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .property-header {
            background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 12px 12px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .property-body {
            padding: 20px;
        }

        .property-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .badge-active {
            background: #28a745;
            color: white;
        }

        .badge-inactive {
            background: #6c757d;
            color: white;
        }

        .badge-pending {
            background: #ffc107;
            color: #212529;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            min-width: 150px;
        }

        .info-value {
            color: #333;
            text-align: right;
            flex: 1;
        }

        .filter-buttons {
            margin-bottom: 20px;
        }

        .filter-btn {
            margin-right: 10px;
            margin-bottom: 10px;
        }
    </style>

    <!-- Filtros y estadísticas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-3 mb-md-0">
                            <h5 class="mb-0">Filtros</h5>
                        </div>
                        <div class="filter-buttons">
                            <a href="{{ route('inmuebles.idealista-list') }}"
                               class="btn btn-sm {{ !$state ? 'btn-primary' : 'btn-outline-primary' }} filter-btn">
                                <i class="fas fa-list me-1"></i>Todas ({{ $totalProperties }})
                            </a>
                            <a href="{{ route('inmuebles.idealista-list', ['state' => 'active']) }}"
                               class="btn btn-sm {{ $state === 'active' ? 'btn-success' : 'btn-outline-success' }} filter-btn">
                                <i class="fas fa-check-circle me-1"></i>Activas ({{ $activeProperties }})
                            </a>
                            <a href="{{ route('inmuebles.idealista-list', ['state' => 'inactive']) }}"
                               class="btn btn-sm {{ $state === 'inactive' ? 'btn-secondary' : 'btn-outline-secondary' }} filter-btn">
                                <i class="fas fa-times-circle me-1"></i>Inactivas ({{ $inactiveProperties }})
                            </a>
                            <a href="{{ route('inmuebles.idealista') }}"
                               class="btn btn-sm btn-outline-dark filter-btn">
                                <i class="fas fa-arrow-left me-1"></i>Volver
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de propiedades -->
    <div class="row">
        @forelse($properties as $property)
            <div class="col-md-6 col-lg-4">
                <div class="property-card">
                    <div class="property-header">
                        <div>
                            <strong>ID: {{ $property['propertyId'] ?? 'N/A' }}</strong>
                        </div>
                        <div>
                            @if(($property['state'] ?? '') === 'active')
                                <span class="property-badge badge-active">
                                    <i class="fas fa-check-circle me-1"></i>Activa
                                </span>
                            @elseif(($property['state'] ?? '') === 'inactive')
                                <span class="property-badge badge-inactive">
                                    <i class="fas fa-times-circle me-1"></i>Inactiva
                                </span>
                            @else
                                <span class="property-badge badge-pending">
                                    <i class="fas fa-clock me-1"></i>Pendiente
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="property-body">
                        <!-- Referencia -->
                        @if(!empty($property['reference']))
                            <div class="info-row">
                                <span class="info-label">Referencia:</span>
                                <span class="info-value"><strong>{{ $property['reference'] }}</strong></span>
                            </div>
                        @endif

                        <!-- Tipo -->
                        <div class="info-row">
                            <span class="info-label">Tipo:</span>
                            <span class="info-value text-capitalize">{{ $property['type'] ?? 'N/A' }}</span>
                        </div>

                        <!-- Dirección -->
                        @if(!empty($property['address']))
                            <div class="info-row">
                                <span class="info-label">Dirección:</span>
                                <span class="info-value">
                                    {{ $property['address']['streetName'] ?? '' }}
                                    {{ $property['address']['streetNumber'] ?? '' }},
                                    {{ $property['address']['town'] ?? '' }}
                                    ({{ $property['address']['postalCode'] ?? '' }})
                                </span>
                            </div>
                        @endif

                        <!-- Operación y Precio -->
                        @if(!empty($property['operation']))
                            <div class="info-row">
                                <span class="info-label">Operación:</span>
                                <span class="info-value">
                                    <span class="text-capitalize">{{ $property['operation']['type'] ?? 'N/A' }}</span>
                                    @if(!empty($property['operation']['price']))
                                        - <strong>{{ number_format($property['operation']['price'], 0, ',', '.') }} €</strong>
                                    @endif
                                </span>
                            </div>
                        @endif

                        <!-- Características -->
                        @if(!empty($property['features']))
                            <div class="info-row">
                                <span class="info-label">Habitaciones:</span>
                                <span class="info-value">{{ $property['features']['rooms'] ?? '-' }}</span>
                            </div>
                            <div class="info-row">
                                <span class="info-label">Baños:</span>
                                <span class="info-value">{{ $property['features']['bathroomNumber'] ?? '-' }}</span>
                            </div>
                            @if(!empty($property['features']['areaConstructed']))
                                <div class="info-row">
                                    <span class="info-label">Superficie:</span>
                                    <span class="info-value">{{ $property['features']['areaConstructed'] }} m²</span>
                                </div>
                            @endif
                        @endif

                        <!-- Descripciones -->
                        @if(!empty($property['descriptions']) && is_array($property['descriptions']))
                            <div class="info-row">
                                <span class="info-label">Descripciones:</span>
                                <span class="info-value">
                                    @foreach($property['descriptions'] as $desc)
                                        <span class="badge bg-info me-1">{{ strtoupper($desc['language'] ?? '') }}</span>
                                    @endforeach
                                </span>
                            </div>
                        @endif

                        <!-- Contacto -->
                        @if(!empty($property['contactId']))
                            <div class="info-row">
                                <span class="info-label">Contacto ID:</span>
                                <span class="info-value">{{ $property['contactId'] }}</span>
                            </div>
                        @endif

                        <!-- Acciones -->
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-grid gap-2">
                                @if(!empty($property['_local_id']))
                                    <a href="{{ route('inmuebles.admin-show', $property['_local_id']) }}"
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye me-1"></i>Ver en CRM
                                    </a>
                                @else
                                    <button class="btn btn-sm btn-outline-secondary" disabled>
                                        <i class="fas fa-info-circle me-1"></i>No sincronizado en CRM
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-home fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">No se encontraron propiedades</h5>
                        <p class="text-muted">No hay propiedades en Idealista con los filtros seleccionados.</p>
                        <a href="{{ route('inmuebles.idealista') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-1"></i>Volver a Gestión Idealista
                        </a>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

