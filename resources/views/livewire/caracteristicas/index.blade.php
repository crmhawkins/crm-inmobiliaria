<div class="container mx-auto">
    <div class="card card-modern mb-4">
        <div class="page-header-modern">
            <h5>
                <i class="fas fa-tags"></i>
                Lista de características de inmueble
            </h5>
        </div>
        <div class="card-body p-4" x-data="{}" x-init="$nextTick(() => {
            $('#tableCaracteristica').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
                language: {
                    emptyTable: 'No hay características registradas'
                }
            });
        })">
            @if ($caracteristicas != null)
                <table class="table table-modern" id="tableCaracteristica">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($caracteristicas as $caracteristica)
                            <tr>
                                <td><strong>{{ $caracteristica->nombre }}</strong></td>
                                <td>
                                    <button type="button"
                                        @if (
                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null))
                                        class="btn btn-primary btn-sm"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $caracteristica->id }});"
                                        @else
                                        class="btn btn-secondary btn-sm" disabled
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
