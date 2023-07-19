<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Lista de tipos de vivienda
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableTipo').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($tipos != null)
                <table class="table" id="tableTipo">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tipos as $tipo)
                            <tr>
                                <td>{{ $tipo->nombre }}</td>
                                <td> <button type="button"
                                        @if (
                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $tipo->id }});"
                                        @else                                         class="btn btn-secondary boton-producto" disabled @endif>Ver/Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
