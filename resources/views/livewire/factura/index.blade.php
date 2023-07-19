<div class="container mx-auto">
    <div class="card mb-3">
        <h5 class="card-header">
            Lista de facturas
        </h5>
        <div class="card-body" x-data="{}" x-init="$nextTick(() => {
            $('#tableFactura').DataTable({
                responsive: true,
                fixedHeader: true,
                searching: false,
                paging: false,
            });
        })">
            @if ($facturas != null)
                <table class="table" id="tableFactura">
                    <thead>
                        <tr>
                            <th scope="col">Cliente</th>
                            <th scope="col">Fecha</th>
                            <th scope="col">Total</th>
                            <th scope="col">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($facturas as $factura)
                            <tr>
                                <td>{{ $clientes->where('id', $factura->cliente)->first()->nombre_completo }}</td>
                                <td>{{ $factura->fecha }}</td>
                                <td>{{ $factura->total }}</td>
                                <td>
                                    <ul>
                                        <li><button type="button"
                                                @if (
                                                    (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-primary boton-producto"
                                        onclick="Livewire.emit('seleccionarProducto', {{ $factura->id }});"
                                        @else                                         class="btn btn-secondary boton-producto" disabled @endif>Ver/Editar</button>
                                        </li>
                                        <li><a href="{{ '../' . $factura->ruta_pdf }}"
                                                class="btn btn-primary boton-producto">Ver documento</a></li>
                                        <li><button type="button"
                                                @if (
                                                    (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1) ||
                                                        (Request::session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0) ||
                                                        (Request::session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null)) class="btn btn-secondary boton-producto" wire:click="enviarCorreo('{{ $factura->id }}')" disabled
                                            @else                                         class="btn btn-secondary boton-producto" disabled @endif>Enviar
                                                por correo</button></li>
                                    </ul>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
