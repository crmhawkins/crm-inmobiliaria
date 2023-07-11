<div class="container mx-auto">

    <div class="card mb-3">
        <h5 class="card-header">Buscador</h5>
        <div class="card-body">
            <h5>Selecciona el estado de la tarea</h5>
            <div class="col-sm-10" wire:ignore.self>
                <select name="tipo_producto" id="tipo_producto" wire:model="tipo_producto" wire:change="select_producto"
                    class="form-control">
                    <option selected value="">Todas las tareas</option>
                    <option value="Asignada">Asignada</option>
                    <option value="Completada">Completada</option>
                    <option value="Facturada">Facturada</option>
                </select>
            </div>
        </div>
    </div>


    <div class="card" wire:ignore>
        <h5 class="card-header">Resultados</h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableAsignar').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            <div wire:ignore>
                @if ($tareas->count() > 0)
                    <table class="table responsive" id="tableAsignar">
                        <thead>
                            <tr>
                                <th scope="col">Presupuesto</th>
                                <th scope="col">Operarios</th>
                                <th scope="col">Cliente</th>
                                <th scope="col">Fecha emisión</th>
                                <th scope="col">Marca vehículo</th>
                                <th scope="col">Modelo vehículo</th>
                                <th scope="col">Matrícula</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Recorre los presupuestos --}}
                            @foreach ($tareas as $tarea)
                                <tr>
                                    <td>{{ $tarea->presupuesto->numero_presupuesto }}</th>

                                    <td> @foreach(json_decode($tarea->operarios, true) as $operario)
                                        {{User::where('id', $operario)->name}} ,
                                        @endforeach
                                    </td>

                                    <td>{{ $tarea->presupuesto->cliente }} </td>

                                    <td>{{ $tarea->fecha }}</th>

                                    <td>{{ $tarea->presupuesto->marca }} </td>

                                    <td>{{ $tarea->presupuesto->modelo }} </td>

                                    <td>{{ $tarea->presupuesto->matricula }} </td>

                                    <td>{{ $tarea->presupuesto->precio }} </td>

                                    <td> <button type="button" class="btn btn-primary boton-producto"
                                            onclick="Livewire.emit('seleccionarProducto', {{ $tarea->id }});">Ver/Editar</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <h3>No existen tareas activas asignadas. </h3>
                @endif
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
