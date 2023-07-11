<div id="containerProveedores">
    <div class="card mb-3">
        <h5 class="card-header">
            Proveedores
        </h5>
        <div class="card-body"  x-data="{}" x-init="$nextTick(() => {
            $('#tableProveedor').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if (count($proveedores) > 0)
                <table class="table" id="tableProveedor">
                    <thead>
                        <tr>
                            <th scope="col">ID del proveedor</th>
                            <th scope="col">DNI</th>
                            <th scope="col">Nombre fiscal</th>
                            <th scope="col">Nombre comercial</th>
                            <th scope="col">Email</th>
                            <th scope="col">Dirección</th>
                            <th scope="col">Teléfono</th>
                            <th scope="col">Observaciones</th>
                            <th scope="col"><strong>Editar proveedor</strong></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($proveedores as $proveedor)
                            <tr>
                                <th scope="row">{{ $proveedor->id }}</th>
                                <td>{{ $proveedor->dni }}</td>
                                <td>{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->nombre }}</td>
                                <td>{{ $proveedor->email }}</td>
                                <td>{{ $proveedor->direccion }}</td>
                                <td>{{ $proveedor->telefono }}</td>
                                <td>{{ $proveedor->observaciones }}</td>
                                <td> <button type="button" class="btn btn-primary boton-producto"
                                    onclick="Livewire.emit('seleccionarProducto', {{ $proveedor->id }});">Ver/Editar</button>
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h5>No hay proveedores en la base de datos</h5>
            @endif
            <br>
        </div>
    </div>
</div>
