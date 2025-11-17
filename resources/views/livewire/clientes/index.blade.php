<div class="container mx-auto">
    <style>
        .page-header-modern {
            background: var(--corporate-green-gradient);
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
        .page-header-modern h5 i {
            font-size: 1.8rem;
        }
        .card-modern {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        .table-modern {
            margin-bottom: 0;
        }
        .table-modern thead th {
            background: var(--corporate-green-lightest);
            color: var(--corporate-green-dark);
            font-weight: 600;
            border-bottom: 2px solid var(--corporate-green);
            padding: 15px;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
        }
        .table-modern tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #e8f0e8;
        }
        .table-modern tbody tr:hover {
            background: #f8faf9;
            transition: background 0.2s ease;
        }
    </style>
    <div class="card card-modern mb-4">
        <div class="page-header-modern">
            <h5>
                <i class="fas fa-users"></i>
                Lista de clientes
            </h5>
        </div>
        <div class="card-body p-4" x-data="{}" x-init="$nextTick(() => {
            $('#tableCliente').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
                language: {
                    emptyTable: 'No hay clientes registrados'
                }
            });
        })">
            @if ($clientes != null)
                <table class="table table-modern" id="tableCliente">
                    <thead>
                        <tr>
                            <th scope="col">Nombre completo</th>
                            <th scope="col">DNI</th>
                            <th scope="col">Email</th>
                            <th scope="col">Más información</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($clientes as $cliente)
                            <tr>
                                <td><strong>{{ $cliente->nombre_completo }}</strong></td>
                                <td>{{ $cliente->dni }}</td>
                                <td>{{ $cliente->email }}</td>
                                <td style="display:none;"></td>
                                <td>
                                    <button type="button"
                                        @if (
                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                                        class="btn btn-primary btn-sm boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $cliente->id }});"
                                        @else
                                        class="btn btn-secondary btn-sm boton-producto" disabled
                                        @endif>
                                        <i class="fas fa-edit me-1"></i>Ver/Editar
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
