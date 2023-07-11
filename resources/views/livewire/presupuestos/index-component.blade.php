<div id="contenedorPresupuestos">

    <div class="card">
        <h5 class="card-header">Buscador</h5>
        <div class="card-body">
            <div style="margin-bottom:10px;">
                <h2> Categoría </h2>
            </div>

            <div class="col-sm-10">
                <select name="filtro_categoria" id="filtro_categoria" wire:model="filtro_categoria" wire:change="filtroCat"
                    class="form-control">
                    <option selected value="">Todos los productos</option>
                    @foreach ($categorias as $categoria => $nombre_cat)
                        <option value="{{ $categoria }}">{{ $nombre_cat }}</option>
                    @endforeach
                </select>
            </div>
            <br>
            <div style="margin-bottom:10px;">
                <h2> Búsqueda </h2>
            </div>

            <div class="col-sm-10">
                <input type="text" wire:model="filtro_busqueda" class="form-control" name="filtro_busqueda"
                    wire:change="filtroCat" id="filtro_busqueda" placeholder="Presupuesto">
            </div>
        </div>
    </div>
    <br>
    <div class="card">
        <h5 class="card-header">Resultados</h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tablePresupuestos').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            <div wire:ignore>
                @if ($presupuestos->count() > 0)
                    <table class="table responsive" id="tablePresupuestos">
                        <thead>
                            <tr>
                                <th scope="col">Número</th>
                                <th scope="col">ID de cliente</th>
                                <th scope="col">Nombre de cliente</th>
                                <th scope="col">Fecha emisión</th>
                                <th scope="col">Marca vehículo</th>
                                <th scope="col">Modelo vehículo</th>
                                <th scope="col">Matrícula</th>
                                <th scope="col">Precio</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- Recorre los presupuestos --}}
                            @foreach ($tabla as $presup)
                                <tr>
                                    <td>{{ $presup->numero_presupuesto }}</th>

                                    <td>{{ $clientes->where('id', $presup->cliente_id)->first()->id }} </td>

                                    <td>{{ $clientes->where('id', $presup->cliente_id)->first()->nombre }} </td>

                                    <td>{{ $presup->fecha_emision }}</th>

                                    <td>{{ $presup->marca }} </td>

                                    <td>{{ $presup->modelo }} </td>

                                    <td>{{ $presup->matricula }} </td>

                                    <td>{{ $presup->precio }} </td>

                                    <td>{{ $presup->estado }} </td>


                                    <td> <button type="button" class="btn btn-primary boton-producto"
                                            onclick="Livewire.emit('seleccionarProducto', {{ $presup->id }});">Ver/Editar</button>
                                            @if($presup->estado == "Pendiente")
                                            <button type="button" class="btn btn-success btn-sm"
                                            wire:click="aceptarPresupuesto('{{$presup->id}}')">Aceptar</button>
                                            <button type="button" class="btn btn-danger btn-sm"
                                            wire:click="rechazarPresupuesto('{{$presup->id}}')">Rechazar</button>
                                            @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $tabla->links() }}
                @else
                    <h3>¡Peligro!</h3>
                @endif
                </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
