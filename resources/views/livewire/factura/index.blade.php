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
                <i class="fas fa-file-invoice-dollar"></i>
                Lista de facturas
            </h5>
        </div>
        <div class="card-body p-4" x-data="{}" x-init="$nextTick(() => {
            $('#tableFactura').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
                language: {
                    emptyTable: 'No hay facturas registradas'
                }
            });
        })">
            @if ($facturas != null)
                <table class="table table-modern" id="tableFactura">
                    <thead>
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facturas as $factura)
                            <tr>
                                <td><strong>{{ $clientes->where('id', $factura->cliente)->first()->nombre_completo }}</strong></td>
                                <td>{{ $factura->fecha }}</td>
                                <td><strong class="text-success">{{ number_format($factura->total, 2, ',', '.') }} €</strong></td>
                                <td>
                                    <a href="{{ '../' . $factura->ruta_pdf }}"
                                        class="btn btn-primary btn-sm boton-producto">
                                        <i class="fas fa-file-pdf me-1"></i>Ver documento
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
