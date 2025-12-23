@extends('layouts.app')

@section('encabezado', 'Dashboard')
@section('subtitulo', 'Vista general del sistema')

@section('head')
<link href="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<style>
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--corporate-green) 0%, var(--corporate-green-light) 100%);
    }

    .stat-card.idealista::before {
        background: linear-gradient(90deg, #FF6B35 0%, #FF8C42 100%);
    }

    .stat-card.facturacion::before {
        background: linear-gradient(90deg, #28a745 0%, #20c997 100%);
    }

    .stat-card.agenda::before {
        background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 15px;
    }

    .stat-icon.inmuebles {
        background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
        color: white;
    }

    .stat-icon.idealista {
        background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);
        color: white;
    }

    .stat-icon.clientes {
        background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        color: white;
    }

    .stat-icon.facturacion {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .stat-icon.agenda {
        background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);
        color: white;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2c3e50;
        margin: 10px 0;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
    }

    .stat-change {
        font-size: 0.85rem;
        margin-top: 5px;
    }

    .stat-change.positive {
        color: #28a745;
    }

    .stat-change.negative {
        color: #dc3545;
    }

    .chart-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 25px;
        margin-bottom: 25px;
    }

    .recent-item {
        padding: 15px;
        border-bottom: 1px solid #e9ecef;
        transition: background 0.2s ease;
    }

    .recent-item:hover {
        background: #f8f9fa;
    }

    .recent-item:last-child {
        border-bottom: none;
    }

    .quick-action-btn {
        background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 10px;
        transition: all 0.3s ease;
        font-weight: 500;
        text-decoration: none;
        display: inline-block;
    }

    .quick-action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(107, 142, 107, 0.4);
        color: white;
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    /* Responsive para móvil y tablet */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .chart-card {
            padding: 15px;
            margin-bottom: 15px;
        }

        .quick-action-btn {
            padding: 10px 16px;
            font-size: 0.9rem;
            margin-bottom: 8px;
            width: 100%;
            text-align: center;
        }

        .d-flex.flex-wrap.gap-2 {
            flex-direction: column;
        }

        .recent-item {
            padding: 12px;
        }
    }

    @media (max-width: 576px) {
        .stat-number {
            font-size: 1.75rem;
        }

        .stat-icon {
            width: 45px;
            height: 45px;
            font-size: 1.3rem;
        }

        .chart-card {
            padding: 12px;
        }

        .quick-action-btn {
            padding: 12px 16px;
            font-size: 0.85rem;
        }
    }

    /* Mejoras para touch devices */
    @media (hover: none) and (pointer: coarse) {
        .quick-action-btn, .btn {
            min-height: 44px;
            padding: 12px 20px;
        }

        .recent-item {
            min-height: 60px;
            padding: 15px;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Estadísticas principales -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body p-4">
                    <div class="stat-icon inmuebles">
                        <i class="fas fa-home"></i>
                    </div>
                    <div class="stat-number">{{ number_format($totalInmuebles) }}</div>
                    <div class="stat-label">Total Inmuebles</div>
                    <div class="stat-change positive">
                        <i class="fas fa-arrow-up"></i> {{ $inmueblesActivos }} activos
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card idealista">
                <div class="card-body p-4">
                    <div class="stat-icon idealista">
                        <i class="fas fa-building"></i>
                    </div>
                    <div class="stat-number">{{ number_format($inmueblesIdealista) }}</div>
                    <div class="stat-label">En Idealista</div>
                    @if($idealistaStats)
                        <div class="stat-change">
                            <i class="fas fa-info-circle"></i> {{ $idealistaStats['publishedAds'] ?? 0 }} publicados
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card">
                <div class="card-body p-4">
                    <div class="stat-icon clientes">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-number">{{ number_format($totalClientes) }}</div>
                    <div class="stat-label">Total Clientes</div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 mb-4">
            <div class="stat-card facturacion">
                <div class="card-body p-4">
                    <div class="stat-icon facturacion">
                        <i class="fas fa-euro-sign"></i>
                    </div>
                    <div class="stat-number">{{ number_format($ingresosMes, 0, ',', '.') }}€</div>
                    <div class="stat-label">Ingresos del Mes</div>
                    <div class="stat-change">
                        <i class="fas fa-file-invoice"></i> {{ $facturasMes }} facturas
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones rápidas -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="chart-card">
                <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Acciones Rápidas</h5>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('inmuebles.create') }}" class="quick-action-btn">
                        <i class="fas fa-plus me-2"></i>Nuevo Inmueble
                    </a>
                    <a href="{{ route('clientes.create') }}" class="quick-action-btn">
                        <i class="fas fa-user-plus me-2"></i>Nuevo Cliente
                    </a>
                    <a href="{{ route('inmuebles.idealista') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #FF6B35 0%, #FF8C42 100%);">
                        <i class="fas fa-building me-2"></i>Gestión Idealista
                    </a>
                    <a href="{{ route('agenda.index') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #6f42c1 0%, #5a32a3 100%);">
                        <i class="fas fa-calendar me-2"></i>Agenda
                    </a>
                    <a href="{{ route('facturacion.index') }}" class="quick-action-btn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                        <i class="fas fa-file-invoice me-2"></i>Facturación
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Gráficos -->
        <div class="col-lg-8 mb-4">
            <div class="chart-card">
                <h5 class="mb-4"><i class="fas fa-chart-line me-2"></i>Inmuebles por Mes (Últimos 6 meses)</h5>
                <canvas id="inmueblesPorMesChart" height="100"></canvas>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h6 class="mb-3"><i class="fas fa-chart-pie me-2"></i>Inmuebles por Tipo</h6>
                        <canvas id="inmueblesPorTipoChart"></canvas>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="chart-card">
                        <h6 class="mb-3"><i class="fas fa-chart-bar me-2"></i>Inmuebles por Estado</h6>
                        <canvas id="inmueblesPorEstadoChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información reciente -->
        <div class="col-lg-4">
            <!-- Inmuebles recientes -->
            <div class="chart-card mb-4">
                <h5 class="mb-3"><i class="fas fa-home me-2"></i>Inmuebles Recientes</h5>
                <div class="list-group list-group-flush">
                    @forelse($inmueblesRecientes as $inmueble)
                        <a href="{{ route('inmuebles.admin-show', $inmueble) }}" class="recent-item text-decoration-none">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ Str::limit($inmueble->titulo, 30) }}</h6>
                                    <p class="mb-1 small text-muted">{{ $inmueble->ubicacion }}</p>
                                    <span class="badge-modern bg-primary">{{ $inmueble->valor_referencia ? number_format($inmueble->valor_referencia, 0, ',', '.') . '€' : 'Sin precio' }}</span>
                                </div>
                                @if($inmueble->idealista_property_id)
                                    <span class="badge-modern" style="background: #FF6B35; color: white;">
                                        <i class="fas fa-building"></i>
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-muted text-center py-3">No hay inmuebles recientes</p>
                    @endforelse
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('inmuebles.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
            </div>

            <!-- Clientes recientes -->
            <div class="chart-card mb-4">
                <h5 class="mb-3"><i class="fas fa-users me-2"></i>Clientes Recientes</h5>
                <div class="list-group list-group-flush">
                    @forelse($clientesRecientes as $cliente)
                        <a href="{{ route('clientes.show', $cliente) }}" class="recent-item text-decoration-none">
                            <div>
                                <h6 class="mb-1">{{ $cliente->nombre_completo }}</h6>
                                <p class="mb-1 small text-muted">{{ $cliente->email ?? 'Sin email' }}</p>
                                @if($cliente->idealista_contact_id)
                                    <span class="badge-modern" style="background: #FF6B35; color: white;">
                                        <i class="fas fa-building me-1"></i>Idealista
                                    </span>
                                @endif
                            </div>
                        </a>
                    @empty
                        <p class="text-muted text-center py-3">No hay clientes recientes</p>
                    @endforelse
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('clientes.index') }}" class="btn btn-sm btn-outline-primary">Ver todos</a>
                </div>
            </div>

            <!-- Próximos eventos -->
            <div class="chart-card">
                <h5 class="mb-3">
                    <i class="fas fa-calendar-check me-2"></i>Próximos Eventos
                    @if($eventosHoy > 0)
                        <span class="badge-modern bg-danger">{{ $eventosHoy }} hoy</span>
                    @endif
                </h5>
                <div class="list-group list-group-flush">
                    @forelse($eventosProximos as $evento)
                        <div class="recent-item">
                            <h6 class="mb-1">{{ $evento->titulo ?? 'Evento' }}</h6>
                            <p class="mb-1 small text-muted">
                                <i class="fas fa-clock me-1"></i>
                                {{ $evento->fecha_inicio ? \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y H:i') : 'Sin fecha' }}
                            </p>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">No hay eventos próximos</p>
                    @endforelse
                </div>
                <div class="mt-3 text-center">
                    <a href="{{ route('agenda.index') }}" class="btn btn-sm btn-outline-primary">Ver agenda</a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Gráfico de inmuebles por mes
    const inmueblesPorMesData = @json($inmueblesPorMes);
    const ctxMes = document.getElementById('inmueblesPorMesChart').getContext('2d');
    new Chart(ctxMes, {
        type: 'line',
        data: {
            labels: inmueblesPorMesData.map(item => {
                const [year, month] = item.mes.split('-');
                return new Date(year, month - 1).toLocaleDateString('es-ES', { month: 'short', year: 'numeric' });
            }),
            datasets: [{
                label: 'Inmuebles',
                data: inmueblesPorMesData.map(item => item.total),
                borderColor: '#6b8e6b',
                backgroundColor: 'rgba(107, 142, 107, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Gráfico de inmuebles por tipo
    const inmueblesPorTipoData = @json($inmueblesPorTipo);
    const ctxTipo = document.getElementById('inmueblesPorTipoChart').getContext('2d');
    new Chart(ctxTipo, {
        type: 'doughnut',
        data: {
            labels: inmueblesPorTipoData.map(item => item.tipoVivienda?.nombre || 'Sin tipo'),
            datasets: [{
                data: inmueblesPorTipoData.map(item => item.total),
                backgroundColor: [
                    '#6b8e6b',
                    '#5a7c5a',
                    '#7fa07f',
                    '#FF6B35',
                    '#FF8C42',
                    '#007bff',
                    '#28a745'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });

    // Gráfico de inmuebles por estado
    const inmueblesPorEstadoData = @json($inmueblesPorEstado);
    const ctxEstado = document.getElementById('inmueblesPorEstadoChart').getContext('2d');
    new Chart(ctxEstado, {
        type: 'bar',
        data: {
            labels: inmueblesPorEstadoData.map(item => item.estado || 'Sin estado'),
            datasets: [{
                label: 'Cantidad',
                data: inmueblesPorEstadoData.map(item => item.total),
                backgroundColor: '#6b8e6b'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endsection

