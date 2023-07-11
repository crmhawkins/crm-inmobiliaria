<div class="container mx-auto">

    <script></script>
    @php
        use Carbon\CarbonInterval;
    @endphp
    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card">
            <h5 class="card-header">Datos del presupuesto</h5>
            <div class="card-body">
                <label for="numero_presupuesto" class="col-sm-2 col-form-label">Número de presupuesto</label>
                <input type="text" wire:model="numero_presupuesto" class="form-control" name="numero_presupuesto"
                    id="numero_presupuesto" disabled>

                <label for="fecha" class="col-sm-2 col-form-label">Fecha de emisión </label>
                <input type="datetime-local" wire:model="fecha" class="form-control" name="fecha" id="fecha"
                    disabled>

                <label for="fecha_emision" class="col-sm-2 col-form-label">Trabajador que ha asignado el
                    presupuesto</label>
                <input type="text"
                    value="{{ $users->where('id', $trabajador_id)->first()->name . ' ' . $users->where('id', $trabajador_id)->first()->surname }}"
                    class="form-control" name="trabajador_id" id="trabajador_id" disabled>

                <label for="descripcion" class="col-form-label">Descripción de la tarea</label>
                <textarea wire:model="descripcion" class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Datos del cliente</h5>
            <div class="card-body">
                <label for="id_cliente" class="col-sm-2 col-form-label">Nombre del cliente</label>
                <input type="text" class="form-control" wire:model="id_cliente" id="id_cliente"
                    value="{{ $tarea->presupuesto->cliente->nombre }}" disabled>
                <label for="fecha_emision" class="col-sm-2 col-form-label">Teléfono del cliente</label>
                <input type="text" class="form-control" value="{{ $tarea->presupuesto->cliente->telefono }}"
                    disabled>
                <label for="fecha_emision" class="col-sm-2 col-form-label">Matrícula del coche</label>
                <input type="text" class="form-control" value="{{ $tarea->presupuesto->matricula }}" disabled>
                <label for="fecha_emision" class="col-sm-2 col-form-label">Marca</label>
                <input type="text" class="form-control" value="{{ $tarea->presupuesto->marca }}" disabled>
                <label for="fecha_emision" class="col-sm-2 col-form-label">Modelo</label>
                <input type="text" class="form-control" value="{{ $tarea->presupuesto->modelo }}" disabled>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Trabajos solicitados</h5>
            <div class="card-body">
                <ul>
                    @foreach ($solicitados as $solicitado)
                        <li>{{ $solicitado }}</li>
                    @endforeach
                </ul>
                <br>
                <label for="nuevoSolicitado">Añadir trabajo solicitado</label><br>
                <input type="text" wire:model="nuevoSolicitado" id="nuevoSolicitado" name="nuevoSolicitado">
                <button wire:click.prevent="agregarSolicitado" class="btn btn-primary">Añadir</button>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Trabajos a realizar</h5>
            <div class="card-body">
                <ul>
                    @foreach ($realizables as $realizar)
                        <li>{{ $realizar }}</li>
                    @endforeach
                </ul>
                <br>
                <label for="nuevoRealizar">Añadir trabajo a realizar</label><br>
                <input type="text" wire:model="nuevoRealizar" id="nuevoRealizar" name="nuevoRealizar">
                <button wire:click.prevent="agregarRealizar" class="btn btn-primary">Añadir</button>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Observaciones</h5>
            <div class="card-body">
                <label for="observaciones" class="col-form-label">Escribe tus observaciones</label>
                <textarea wire:model="observaciones" class="form-control" name="observaciones" id="observaciones" rows="3"></textarea>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Concepto de materiales y mano de obra</h5>
            <div class="card-body">
                @if (count($lista) != 0)
                    <div class="mb-3 row d-flex align-items-center" wire:ignore>
                        <table class="table responsive" id="tableProductos">
                            <thead>
                                <tr>
                                    <th scope="col">Código</th>
                                    <th scope="col">Nombre</th>
                                    <th scope="col">Precio</th>
                                    <th scope="col">Cantidad</th>
                                    <th scope="col">Tiempo</th>
                                    <th scope="col">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($lista as $productoE => $cantidad)
                                    <tr>
                                        <td>{{ $productos->where('id', $productoE)->first()->cod_producto }}</td>
                                        <td>{{ $productos->where('id', $productoE)->first()->descripcion }}</td>
                                        <td>{{ $productos->where('id', $productoE)->first()->precio_venta }}€</td>
                                        @if ($productos->where('id', $productoE)->first()->mueve_existencias == 1)
                                            <td>{{ $cantidad }}</td>
                                            <td></td>
                                        @else
                                            <td></td>
                                            <td> <input type="text" wire:model="tiempo_lista.{{ $productoE }}"
                                                    class="form-control"> </td>
                                        @endif
                                        <td>{{ $productos->where('id', $productoE)->first()->precio_venta * $cantidad }}€
                                        </td>
                                    </tr>
                                @endforeach
                            <tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Operarios</h5>
            <div class="card-body">
                @if (count($trabajadores) != 0)
                    <div class="mb-3 row d-flex align-items-center">
                        <table class="table responsive" id="tableUsers">
                            <thead>
                                <tr>
                                    <th scope="col">Operario</th>
                                    <th scope="col">Tiempo empleado</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($trabajadores as $trabajador)
                                    <tr>
                                        <td>{{ $users->where('id', $trabajador)->first()->name . ' ' . $users->where('id', $trabajador)->first()->surname }}
                                        </td>
                                        <td>
                                            <input type="text"
                                                @if (isset(json_decode($tarea->operarios_tiempo, true)[$trabajador])) value="{{ CarbonInterval::seconds(json_decode($tarea->operarios_tiempo, true)[$trabajador])->cascade()->format('%H:%I:%S') }}" @endif
                                                class="form-control" disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            <tbody>
                        </table>
                    </div>
                @endif
                <label for="trabajadorSeleccionado" class="col-form-label">Añadir trabajador/operario</label>
                <select wire:model="trabajadorSeleccionado" class="form-control" name="trabajadorSeleccionado"
                    id="trabajadorSeleccionado">
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
                <br>
                <button wire:click.prevent="agregarTrabajador" class="btn btn-primary mt-2">Añadir</button>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Daños localizados en el vehículo</h5>
            <div class="card-body">
                <ul>
                    @foreach ($daños as $daño)
                        <li>{{ $daño }}</li>
                    @endforeach
                </ul>
                <br>
                <label for="nuevoRealizar">Añadir daño localizado</label><br>
                <input type="text" wire:model="nuevoDaño">
                <button wire:click.prevent="agregarDaño" class="btn btn-primary">Añadir</button>
            </div>
        </div>
        <br>
        <div class="card">
            <h5 class="card-header">Documentos adjuntos</h5>
            <div class="card-body">
                @foreach ($rutasDocumentos as $documento)
                    <div class="documento">
                        @if (Str::endsWith($documento, ['.png', '.jpg', '.jpeg', '.gif']))
                            <!-- Mostrar vista previa de la imagen -->
                            <img src="{{ Storage::url($documento) }}" alt="Documento" style=" width: 100%">
                        @elseif (Str::endsWith($documento, ['.pdf']))
                            {{ substr($documento, 11) }} : <a class="btn btn-primary"
                                href="{{ Storage::url($documento) }}" target="_blank">Ver Documento</a>
                        @endif
                    </div>
                @endforeach
                <label for="documentosArray">Subir documento</label>
                <input type="file" class="form-control" id="documentosArray" wire:model="documentosArray"
                    multiple>
                <br>
                <button type="button" class="btn btn-primary" wire:click.prevent="subirArchivo">Subir
                    documento</button>
            </div>
        </div>

        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>
    </form>

</div>

</div>


@section('scripts')
    <script>
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre',
                'Octubre', 'Noviembre', 'Diciembre'
            ],
            monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
            dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function() {


        })
        $(document).ready(function() {
            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e) {
                @this.set('fecha_inicio', $('#datepicker').val());
            });
            $("#datepicker2").datepicker();

            $("#datepicker2").on('change', function(e) {
                @this.set('fecha_fin', $('#datepicker2').val());
            });

        });
    </script>
@endsection
