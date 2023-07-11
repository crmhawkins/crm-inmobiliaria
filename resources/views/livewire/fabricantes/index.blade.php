<div id="containerFabricantes">
    @if ($fabricantes->count() > 0)
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
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($fabricantes as $fabricante)
                            <tr>
                                <td>{{ $fabricante->nombre }}</td>
                                <td> <button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $fabricante->id }});">Ver/Editar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3>¡No hay fabricantes en la base de datos!</h3>
            </div>
        </div>
    @endif
</div>
