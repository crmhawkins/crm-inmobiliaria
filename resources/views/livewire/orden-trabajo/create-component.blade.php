@section('head')
    @vite(['resources/sass/app.scss'])
@endsection

@section('encabezado', 'Orden de trabajo')
@section('subtitulo', 'Crear tarea')

<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <br>

        <div style="border-bottom: 1px solid black; margin-bottom:10px;">
            <h1>Datos básicos</h1>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="fecha_emision" class="col-sm-2 col-form-label">Fecha de emisión</label>
            <div class="col-sm-10">
                <input disabled type="datetime-local" wire:model="fecha_emision" wire:change="numeroPresupuesto"
                    class="form-control" name="fecha_emision" id="fecha_emision">
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="numero_presupuesto" class="col-sm-2 col-form-label">Número de presupuesto</label>
            <div class="col-sm-10">
                <input type="text" wire:model="numero_presupuesto" class="form-control" name="numero_presupuesto"
                    id="numero_presupuesto" disabled>
                @error('numero_presupuesto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="estado" class="col-sm-2 col-form-label">Estado</label>
            <div class="col-sm-10">
                <fieldset class="form-group">
                    <input wire:model="estado" name="estado" type="radio" value="aceptado" /> Aceptado <br>
                    <input wire:model="estado" name="estado" type="radio" value="rechazado" /> Rechazado <br>
                    <input wire:model="estado" name="estado" type="radio" value="pendiente" /> Pendiente <br>
                </fieldset>
                @error('estado')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div class="mb-3 row d-flex align-items-center">
            <label for="observaciones" class="col-sm-2 col-form-label">Comentario</label>
            <div class="col-sm-10">
                <input type="text" wire:model="observaciones" class="form-control" name="observaciones"
                    id="observaciones">
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


        <div style="border-bottom: 1px solid black; margin-bottom:50px;"></div>
        <div style="border-bottom: 1px solid black; margin-bottom:10px;">
            <h1>Datos del cliente</h1>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="cliente_id" class="col-sm-2 col-form-label">Cliente</label>
            <div class="col-sm-10" wire:ignore>
                <select id="select2-cliente" class="form-control seleccion">
                    @foreach ($clientes as $clienteSel)
                        <option value="{{ $clienteSel->id }}">{{ $clienteSel->id }} - {{ $clienteSel->nombre }}
                        </option>
                    @endforeach

                </select>
                @error('denominacion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="trabajador_id" class="col-sm-2 col-form-label">Trabajador asignado</label>
            <div class="col-sm-10" wire:ignore>
                <select id="select2-trabajador" class="form-control seleccion">
                    @foreach ($trabajadores as $trabajadorSel)
                        <option value="{{ $trabajadorSel->id }}">{{ $trabajadorSel->id }} -
                            {{ $trabajadorSel->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('denominacion')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="matricula" class="col-sm-2 col-form-label">Matrícula</label>
            <div class="col-sm-10">
                <input type="text" wire:model="matricula" class="form-control" name="matricula" id="matricula">
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <label for="kilometros" class="col-sm-2 col-form-label">Kilómetros</label>
            <div class="col-sm-10">
                <input type="number" wire:model="kilometros" class="form-control" name="kilometros"
                    id="kilometros">
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div style="border-bottom: 1px solid black; margin-bottom:50px;"></div>
        <div style="border-bottom: 1px solid black; margin-bottom:10px;">
            <h1>Lista de artículos seleccionados</h1>
        </div>

        @if (count($lista) != 0)
            <div class="mb-3 row d-flex align-items-center">
                <table class="table" id="tableProductos" wire:change="añadirProducto">
                    <thead>
                        <tr>
                            <th scope="col">Código</th>
                            <th scope="col">Nombre</th>
                            <th scope="col">Precio</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lista as $productoID => $pCantidad)
                            @if ($pCantidad > 0)
                                @php
                                    $productoLista = $productos->where('id', $productoID)->first();
                                @endphp
                                <tr id={{ $productoLista->id }}>
                                    <td>{{ $productoLista->cod_producto }}</td>
                                    <td>{{ $productoLista->descripcion }}</td>
                                    <td>{{ $productoLista->precio_venta }}€</td>
                                    <td> <button class="btn btn-sm btn-primary"
                                            wire:click.prevent="reducir({{ $productoID }})">-</button>
                                        {{ $pCantidad }}
                                        <button class="btn btn-sm btn-primary"
                                            wire:click.prevent="aumentar({{ $productoID }})">+</button>
                                    </td>
                                    <td>{{ $productoLista->precio_venta * $pCantidad }}€
                                    </td>
                                <tr>
                            @endif
                        @endforeach
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                    <tbody>
                </table>
                <div>
        @endif

        <div class="mb-3 row d-flex align-items-center">
            <label for="precio" class="col-sm-2 col-form-label">Precio</label>
            <div class="col-sm-10">
                <input type="number" wire:model="precio" class="form-control" name="precio" id="precio"
                    disabled>
                @error('fecha_emision')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
        </div>


</div>


<div style="border-bottom: 1px solid black; margin-bottom:50px;"></div>
<div style="border-bottom: 1px solid black; margin-bottom:10px;">
    <h1>Finalizar presupuesto</h1>
</div>
<div class="mb-3 row d-flex align-items-center">
    <button type="submit" class="btn btn-primary">Crear orden de trabajo</button>
</div>
</form>

<div style="border-bottom: 1px solid black; margin-bottom:50px;"></div>
<div style="border-bottom: 1px solid black; margin-bottom:10px;">
    <h1>Opciones</h1>
</div>
<div class="mb-3 row d-flex align-items-center ">
    <a href="{{ route('clients.create') }}" class="btn btn-primary">Crear cliente</a>
</div>

</div>

</div>

</tbody>
</table>

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#select2-producto').select2({
                placeholder: "Seleccione un producto"
            });
            $('#select2-producto').on('change', function(e) {
                var data = $('#select2-producto').select2("val");
                @this.set('producto_seleccionado', data);
            });

            $('#select2-servicio').select2({
                placeholder: "Localización del servicio"
            });
            $('#select2-servicio').on('change', function(e) {
                var data = $('#select2-servicio').select2("val");
                @this.set('servicio', data);
            });

            $('#select2-origen').select2({
                placeholder: "Origen del presupuesto"
            });
            $('#select2-origen').on('change', function(e) {
                var data = $('#select2-origen').select2("val");
                @this.set('origen', data);
            });

            $('#select2-cliente').select2({
                placeholder: "Seleccione un cliente"
            });
            $('#select2-cliente').on('change', function(e) {
                var data = $('#select2-cliente').select2("val");
                @this.set('cliente_id', data);
            });

            $('#select2-trabajador').select2({
                placeholder: "Seleccione un trabajador"
            });
            $('#select2-trabajador').on('change', function(e) {
                var data = $('#select2-trabajador').select2("val");
                @this.set('trabajador_id', data);
            });

        });
    </script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

@endsection

{{-- , precio => {
      document.getElementById('precio').value = precio;
    } --}}
