<div class="container-fluid">
    <style>
        .page-header-modern {
            background: linear-gradient(135deg, #6b8e6b 0%, #5a7c5a 100%);
            color: white;
            padding: 30px;
            border-radius: 12px 12px 0 0;
            margin-bottom: 0;
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.2);
        }
        .page-header-modern h5 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .idealista-badge {
            background: #6b8e6b;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-block;
            margin-left: 10px;
        }
        .property-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }
        .property-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
        }
        .idealista-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            border-left: 4px solid #6b8e6b;
        }
        .idealista-info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }
        .idealista-info-item:last-child {
            margin-bottom: 0;
        }
        .idealista-info-label {
            font-weight: 600;
            color: #6b8e6b;
        }
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            margin-bottom: 25px;
        }
    </style>

    <div class="page-header-modern">
        <h5>
            <i class="fas fa-building"></i>
            Inmuebles de Idealista
            <span class="badge bg-light text-dark ms-2">{{ $inmuebles->total() }} inmuebles</span>
        </h5>
    </div>

    <div class="card card-modern">
        <div class="card-body">
            <div class="search-bar">
                <div class="row g-3">
                    <div class="col-md-4">
                        <input type="text"
                               class="form-control"
                               placeholder="Buscar por título, ubicación o ID Idealista..."
                               wire:model.debounce.300ms="search">
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" wire:model="estadoFilter">
                            <option value="">Todos los estados</option>
                            <option value="disponible">Disponible</option>
                            <option value="no disponible">No disponible</option>
                            <option value="pendiente">Pendiente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select class="form-control" wire:model="tipoFilter">
                            <option value="">Todos los tipos</option>
                            @foreach($tiposVivienda as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-secondary w-100" wire:click="$set('search', '')">
                            <i class="fas fa-times"></i> Limpiar
                        </button>
                    </div>
                </div>
            </div>

            @if($inmuebles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th wire:click="sortByField('titulo')" style="cursor: pointer;">
                                    Título
                                    @if($sortBy === 'titulo')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Ubicación</th>
                                <th wire:click="sortByField('valor_referencia')" style="cursor: pointer;">
                                    Precio
                                    @if($sortBy === 'valor_referencia')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Características</th>
                                <th>ID Idealista</th>
                                <th wire:click="sortByField('idealista_synced_at')" style="cursor: pointer;">
                                    Sincronizado
                                    @if($sortBy === 'idealista_synced_at')
                                        <i class="fas fa-sort-{{ $sortDirection === 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($inmuebles as $inmueble)
                                <tr>
                                    <td>
                                        <strong>{{ $inmueble->titulo }}</strong>
                                        @if($inmueble->idealista_code)
                                            <br><small class="text-muted">Código: {{ $inmueble->idealista_code }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $inmueble->ubicacion }}
                                        @if($inmueble->cod_postal)
                                            <br><small class="text-muted">{{ $inmueble->cod_postal }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($inmueble->valor_referencia, 0, ',', '.') }} €</strong>
                                    </td>
                                    <td>
                                        <small>
                                            <i class="fas fa-bed"></i> {{ $inmueble->habitaciones ?? '-' }} hab. |
                                            <i class="fas fa-bath"></i> {{ $inmueble->banos ?? '-' }} baños |
                                            <i class="fas fa-ruler-combined"></i> {{ $inmueble->m2 ?? '-' }}m²
                                        </small>
                                    </td>
                                    <td>
                                        <span class="idealista-badge">#{{ $inmueble->idealista_property_id }}</span>
                                    </td>
                                    <td>
                                        <small>
                                            @if($inmueble->idealista_synced_at)
                                                {{ \Carbon\Carbon::parse($inmueble->idealista_synced_at)->format('d/m/Y H:i') }}
                                            @else
                                                -
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <a href="{{ route('inmuebles.show', $inmueble->id) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> Ver
                                        </a>
                                        <a href="{{ route('inmuebles.idealista-preview', $inmueble->id) }}"
                                           class="btn btn-sm btn-warning" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> Vista Idealista
                                        </a>
                                        @if(auth()->check() && (auth()->user()->inmobiliaria == 1 || auth()->user()->inmobiliaria === null))
                                            <a href="{{ route('inmuebles.edit', $inmueble->id) }}"
                                               class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i> Editar
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $inmuebles->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-building fa-3x text-muted mb-3"></i>
                    <h4>No hay inmuebles de Idealista</h4>
                    <p class="text-muted">Ejecuta <code>php artisan idealista:sync-properties</code> para importar inmuebles.</p>
                </div>
            @endif
        </div>
    </div>
</div>

