<style>
    :root {
        --corporate-green: #6b8e6b;
        --corporate-green-dark: #5a7c5a;
        --corporate-green-light: #7fa07f;
        --corporate-green-lightest: #e8f0e8;
    }

    .dashboard-container {
        padding: 20px 0;
    }

    .welcome-section {
        background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
        border-radius: 20px;
        padding: 40px;
        margin-bottom: 40px;
        color: white;
        position: relative;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(107, 142, 107, 0.3);
    }

    .welcome-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 0.3; }
        50% { opacity: 0.6; }
    }

    .welcome-content {
        position: relative;
        z-index: 1;
    }

    .welcome-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        text-shadow: 0 2px 10px rgba(0,0,0,0.2);
    }

    .welcome-subtitle {
        font-size: 1.2rem;
        opacity: 0.95;
        font-weight: 400;
    }

    .welcome-icon {
        font-size: 4rem;
        opacity: 0.3;
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(-50%) translateX(0); }
        50% { transform: translateY(-50%) translateX(-10px); }
    }

    .section-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        gap: 12px;
        position: relative;
        padding-bottom: 15px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 4px;
        background: var(--corporate-green-gradient);
        border-radius: 2px;
    }

    .section-title i.text-primary {
        color: var(--corporate-green) !important;
        font-size: 1.5rem;
    }

    .section-title i.text-warning {
        color: var(--corporate-green-dark) !important;
        font-size: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        color: #2d3748;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: var(--corporate-green-gradient);
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.4s ease;
    }

    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 40px rgba(107, 142, 107, 0.25);
    }

    .stat-card:hover::before {
        transform: scaleX(1);
    }

    .stat-card.gradient-blue {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-blue .stat-icon {
        background: linear-gradient(135deg, var(--corporate-green) 0%, var(--corporate-green-dark) 100%);
        color: white;
    }

    .stat-card.gradient-green {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-green .stat-icon {
        background: linear-gradient(135deg, var(--corporate-green-light) 0%, var(--corporate-green) 100%);
        color: white;
    }

    .stat-card.gradient-orange {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-orange .stat-icon {
        background: linear-gradient(135deg, #8fb88f 0%, var(--corporate-green-dark) 100%);
        color: white;
    }

    .stat-card.gradient-purple {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-purple .stat-icon {
        background: linear-gradient(135deg, var(--corporate-green) 0%, #7fa07f 100%);
        color: white;
    }

    .stat-card.gradient-yellow {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-yellow .stat-icon {
        background: linear-gradient(135deg, var(--corporate-green-light) 0%, var(--corporate-green-dark) 100%);
        color: white;
    }

    .stat-card.gradient-red {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7f0 100%);
    }

    .stat-card.gradient-red .stat-icon {
        background: linear-gradient(135deg, var(--corporate-green-dark) 0%, var(--corporate-green) 100%);
        color: white;
    }

    .stat-icon {
        width: 70px;
        height: 70px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 20px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(107, 142, 107, 0.2);
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
        box-shadow: 0 6px 20px rgba(107, 142, 107, 0.4);
    }

    .stat-value {
        font-size: 3rem;
        font-weight: 800;
        margin-bottom: 8px;
        line-height: 1;
        color: #2d3748;
        background: linear-gradient(135deg, var(--corporate-green-dark) 0%, var(--corporate-green) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        font-size: 1rem;
        color: #718096;
        font-weight: 600;
        margin-bottom: 10px;
        letter-spacing: 0.5px;
    }

    .stat-change {
        font-size: 0.9rem;
        margin-top: 12px;
        padding: 8px 12px;
        background: var(--corporate-green-lightest);
        border-radius: 8px;
        color: var(--corporate-green-dark);
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .stat-change i {
        font-size: 0.85rem;
    }

    .upcoming-events-card {
        background: white;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        border: none;
        border-top: 5px solid var(--corporate-green);
    }

    .upcoming-events-card h3 {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .event-item {
        padding: 20px;
        border-left: 5px solid var(--corporate-green);
        background: linear-gradient(90deg, #f7fafc 0%, #ffffff 100%);
        border-radius: 12px;
        margin-bottom: 15px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        position: relative;
        overflow: hidden;
    }

    .event-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: var(--corporate-green-gradient);
        transform: scaleY(0);
        transform-origin: bottom;
        transition: transform 0.3s ease;
    }

    .event-item:hover {
        background: linear-gradient(90deg, #edf2f7 0%, #ffffff 100%);
        transform: translateX(8px);
        box-shadow: 0 4px 15px rgba(107, 142, 107, 0.15);
    }

    .event-item:hover::before {
        transform: scaleY(1);
    }

    .event-item:last-child {
        margin-bottom: 0;
    }

    .event-title {
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 8px;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .event-title i.text-primary {
        color: var(--corporate-green) !important;
        font-size: 1.2rem;
    }

    .event-meta {
        font-size: 0.9rem;
        color: #718096;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 8px;
    }

    .event-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        background: var(--corporate-green-lightest);
        border-radius: 8px;
        font-weight: 500;
    }

    .event-meta i {
        color: var(--corporate-green);
    }

    .empty-state {
        text-align: center;
        padding: 60px 40px;
        color: #a0aec0;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    .empty-state p {
        font-size: 1.1rem;
        font-weight: 500;
    }

    .quick-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .quick-action-btn {
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.2);
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 10px;
        color: white;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .quick-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        border-color: rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
        color: white;
        text-decoration: none;
    }

    @media (max-width: 768px) {
        .welcome-title {
            font-size: 2rem;
        }

        .welcome-subtitle {
            font-size: 1rem;
        }

        .stat-value {
            font-size: 2.5rem;
        }

        .event-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
    }
</style>

<div class="dashboard-container">
    <!-- Bienvenida -->
    <div class="welcome-section">
        <div class="welcome-content">
            <div class="welcome-icon">
                <i class="fas fa-building"></i>
            </div>
            <div class="welcome-title">
                ¡Bienvenido, {{ Auth::user()->nombre_completo ?? Auth::user()->email ?? 'Usuario' }}!
            </div>
            <div class="welcome-subtitle">
                Aquí tienes un resumen de tu actividad inmobiliaria
            </div>
            <div class="quick-actions">
                <a href="/admin/clientes" class="quick-action-btn">
                    <i class="fas fa-users me-2"></i>Ver Clientes
                </a>
                <a href="/admin/inmuebles" class="quick-action-btn">
                    <i class="fas fa-home me-2"></i>Ver Inmuebles
                </a>
                <a href="/admin/agenda" class="quick-action-btn">
                    <i class="fas fa-calendar me-2"></i>Ver Agenda
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Principales -->
    <div class="section-title">
        <i class="fas fa-chart-line text-primary"></i>
        Resumen General
    </div>

    <div class="row g-4 mb-5">
        <!-- Tarjeta de Clientes -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-blue">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value">{{ $stats['total_clientes'] ?? 0 }}</div>
                <div class="stat-label">Total Clientes</div>
                @if(isset($stats['clientes_nuevos_mes']) && $stats['clientes_nuevos_mes'] > 0)
                    <div class="stat-change">
                        <i class="fas fa-arrow-up"></i>
                        <span>{{ $stats['clientes_nuevos_mes'] }} nuevos este mes</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de Inmuebles -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-green">
                <div class="stat-icon">
                    <i class="fas fa-home"></i>
                </div>
                <div class="stat-value">{{ $stats['total_inmuebles'] ?? 0 }}</div>
                <div class="stat-label">Total Inmuebles</div>
                @if(isset($stats['inmuebles_disponibles']))
                    <div class="stat-change">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ $stats['inmuebles_disponibles'] }} disponibles</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de Eventos -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-orange">
                <div class="stat-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <div class="stat-value">{{ $stats['total_eventos'] ?? 0 }}</div>
                <div class="stat-label">Total Citas</div>
                @if(isset($stats['eventos_hoy']) && $stats['eventos_hoy'] > 0)
                    <div class="stat-change">
                        <i class="fas fa-calendar-day"></i>
                        <span>{{ $stats['eventos_hoy'] }} hoy</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de Facturación -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-purple">
                <div class="stat-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="stat-value">{{ $stats['total_facturas'] ?? 0 }}</div>
                <div class="stat-label">Total Facturas</div>
                @if(isset($stats['facturacion_mes']) && $stats['facturacion_mes'] > 0)
                    <div class="stat-change">
                        <i class="fas fa-euro-sign"></i>
                        <span>{{ number_format($stats['facturacion_mes'], 2, ',', '.') }} € este mes</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tarjeta de Clientes Nuevos -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-yellow">
                <div class="stat-icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="stat-value">{{ $stats['clientes_nuevos_mes'] ?? 0 }}</div>
                <div class="stat-label">Clientes Nuevos</div>
                <div class="stat-change">
                    <i class="fas fa-calendar-alt"></i>
                    <span>Este mes</span>
                </div>
            </div>
        </div>

        <!-- Tarjeta de Inmuebles Disponibles -->
        <div class="col-lg-4 col-md-6 col-sm-6">
            <div class="stat-card gradient-red">
                <div class="stat-icon">
                    <i class="fas fa-key"></i>
                </div>
                <div class="stat-value">{{ $stats['inmuebles_disponibles'] ?? 0 }}</div>
                <div class="stat-label">Inmuebles Disponibles</div>
                <div class="stat-change">
                    <i class="fas fa-check-circle"></i>
                    <span>Listos para venta</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Próximos Eventos -->
    <div class="section-title">
        <i class="fas fa-clock text-warning"></i>
        Próximas Citas (7 días)
    </div>

    <div class="row">
        <div class="col-12">
            <div class="upcoming-events-card">
                @if(isset($stats['eventos_proximos']) && $stats['eventos_proximos']->count() > 0)
                    @foreach($stats['eventos_proximos'] as $evento)
                        <div class="event-item">
                            <div class="event-title">
                                <i class="fas fa-calendar-check text-primary"></i>
                                <span>{{ $evento->titulo }}</span>
                            </div>
                            <div class="event-meta">
                                <span>
                                    <i class="fas fa-clock"></i>
                                    {{ \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y H:i') }}
                                </span>
                                @if($evento->cliente)
                                    <span>
                                        <i class="fas fa-user"></i>
                                        {{ $evento->cliente->nombre_completo }}
                                    </span>
                                @endif
                                @if($evento->inmueble)
                                    <span>
                                        <i class="fas fa-home"></i>
                                        {{ Str::limit($evento->inmueble->titulo, 30) }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <i class="fas fa-calendar-times"></i>
                        <p>No hay citas programadas para los próximos 7 días</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
