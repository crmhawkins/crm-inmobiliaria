<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Lista de vendedores de vivienda
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableVendedor').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($vendedores != null)
                <table class="table" id="tableVendedor">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Editar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($vendedores as $vendedor)
                            <tr>
                                <td>{{ $vendedor->nombre_completo }}</td>
                                <td> <button type="button"
                                        @if (
                                            (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $vendedor->id }});"
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
