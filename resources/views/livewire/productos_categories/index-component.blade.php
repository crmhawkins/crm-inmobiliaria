<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Categorías de productos
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableProductos').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($productosCategories != null)
                <table class="table" id="tableProductos">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">Tipo </th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($productosCategories as $producto)
                            <tr>
                                <td>{{ $producto->nombre }}</td>
                                <td>{{ $tipos_producto->find($producto->tipo_producto)->tipo_producto }}</td>
                                <td> <a href="productos-categories-edit/{{ $producto->id }}"
                                        class="btn btn-primary">Editar</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div>{{ $productosCategories->links() }}</div>
            @else
                <h5> Añade una categoría para tus productos. </h5>
            @endif
        </div>
    </div>
</div>
