<div id="containerTrabajadores">
    @if ($trabajadores->count() > 0)
        <div class="card" wire:ignore>
            <h5 class="card-header">Resultados</h5>
            <div class="card-body" x-data="{}" x-init="$nextTick(() => {
                $('#tableEmpresas').DataTable({
                    responsive: true,
                    fixedHeader: true,
                    searching: false,
                    paging: false,
                });
            })">
                <table class="table responsive" id="tableEmpresas">
                    <thead>
                        <tr>
                            <th scope="col">Nombre</th>
                            <th scope="col">ID de usuario</th>
                            <th scope="col">Puesto</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trabajadores as $trabajador)
                            <tr>
                                <td>{{ $trabajador->name }} {{ $trabajador->surname }} </td>
                                <td>{{ $trabajador->username }} </td>
                                <td>{{ $trabajador->role }} </td>
                                <td> <button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $trabajador->id }});">Ver/Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3>¡No hay usuarios registrados!</h3>
            </div>
        </div>
    @endif
</div>
