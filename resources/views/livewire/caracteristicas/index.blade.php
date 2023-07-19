<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Lista de caracteristicas de inmueble
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableCaracteristica').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($caracteristicas != null)
                <table class="table" id="tableCaracteristica">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($caracteristicas as $caracteristica)
                            <tr>
                                <td>{{ $caracteristica->nombre }}</td>
                                <td> <button type="button"
                                        @if (
                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-primary boton-producto"
                        onclick="Livewire.emit('seleccionarProducto', {{ $caracteristica->id }});"
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
