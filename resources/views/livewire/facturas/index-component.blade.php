<div class="container mx-auto">
    @if (count($facturas) > 0)
        <div class="card" wire:ignore>
            <h5 class="card-header">Facturas recientes</h5>
            <div class="card-body" x-data="{}" x-init="$nextTick(() => {
                $('#tableSinAsignar').DataTable({
                    responsive: true,
                    fixedHeader: true,
                    searching: false,
                    paging: false,
                });
            })">
                <table class="table" id="tableFacturas">
                    <thead>
                        <tr>
                            <th scope="col">Número</th>
                            <th scope="col">Tipo de documento</th>
                            <th scope="col">Presupuesto/s asociado/s</th>
                            <th scope="col">Descripción</th>
                            <th scope="col">Total</th>
                            <th scope="col">Total (IVA)</th>
                            <th scope="col">Método de pago</th>

                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facturas as $fact)
                            <tr>
                                <td>{{ $fact->numero_factura }}</th>
                                <td>{{ $fact->tipo_documento }}</th>
                                    @if ($fact->tipo_documento == 'factura')
                                <td>{{ $presupuestos->where('id', $fact->id_presupuesto)->first()->numero_presupuesto }}
                                </td>
                            @else
                                <td>
                                    @foreach ($fact->id_presupuesto as $presup)
                                        {{ $presupuestos->where('id', $presup)->first()->numero_presupuesto }} ,
                                    @endforeach
                                </td>
                        @endif
                        <td>{{ $fact->descripcion }}</td>
                        <td>{{ $fact->precio }} €</td>
                        <td>{{ $fact->precio_iva }} €</td>
                        <td>{{ $fact->metodo_pago }}</td>

                        <td>
                            @if ($fact->metodo_pago == 'No pagado')
                                <div class="col mb-2">
                                    <button type="button" class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $fact->id }});">Editar</button>
                                    <br>
                                </div>
                                <div class="col">
                                    <div class="dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button"
                                            data-bs-toggle="dropdown" aria-expanded="false">
                                            Cobrar </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Contado')">Contado</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Tarjeta de crédito')">Tarjeta de crédito</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Transferencia bancaria')">Transferencia
                                                bancaria</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Recibo bancario a 30 días')">Recibo bancario
                                                a 30 días</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Bizum')">Bizum</a>
                                            <a class="dropdown-item" href="#"
                                                wire:click="redirectToCaja('{{$fact->id}}', 'Financiado')">Financiado</a>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <button type="button" class="btn btn-primary boton-producto"
                                    onclick="Livewire.emit('seleccionarProducto', {{ $fact->id }});">Editar</button>
                            @endif
                        </td>


                        </tr>
    @endforeach
    </tbody>
    </table>
@else
    <h5> No hay facturas recientes.</h5>
    @endif

</div>
</div>
</div>
