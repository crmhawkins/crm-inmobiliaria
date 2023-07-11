<div class="container mx-auto">
    <div class="card" wire:ignore>
        <h5 class="card-header">Resultados</h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableSinAsignar').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($tareas->count() > 0)
                <table class="table responsive" id="tableSinAsignar">
                    <thead>
                        <tr>
                            <th scope="col">Número</th>
                            <th scope="col">ID de cliente</th>
                            <th scope="col">Nombre de cliente</th>
                            <th scope="col">Fecha emisión</th>
                            <th scope="col">PVP</th>
                            <th scope="col">Asignar tarea</th>

                        </tr>
                    </thead>
                    <tbody>
                        {{-- Recorre los presupuestos --}}
                        @foreach ($tareas as $tarea)
                            <tr>
                                <td>{{ $tarea->presupuesto->numero_presupuesto }}</th>

                                <td>{{ $tarea->presupuesto->cliente->id }} </td>

                                <td>{{ $tarea->presupuesto->cliente->nombre }} </td>

                                <td>{{ $tarea->presupuesto->fecha_emision }}</th>

                                <td>{{ $tarea->presupuesto->precio }} </td>

                                <td> <button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $tarea->id }});">Asignar</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <h3>¡No hay tareas sin asignar!</h3>
            @endif
            </tbody>
            </table>
        </div>
    </div>
</div>
