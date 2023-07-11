<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Ecotasas de neumáticos ≤ 1400 mm
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableEcotasa').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($ecotasa != null)
                <table class="table" id="tableEcotasa">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Peso</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($ecotasa as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $producto->valor }}€</td>
                                @if ($producto->peso_min == null)
                                    <td> ≤ {{ $producto->peso_max }}</td>
                                @elseif($producto->peso_max == null)
                                    <td> > {{ $producto->peso_min }}</td>
                                @else
                                    <td> > {{ $producto->peso_min }} y ≤ {{ $producto->peso_max }}</td>
                                @endif
                                <td> <button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $producto->id }});">Ver/Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>{{ $ecotasa->links() }}</div>
            @else
                <h5> Añade las ecotasas para tus productos. </h5>
            @endif
        </div>
    </div>
</div>
