<div class="container mx-auto" style="overflow-y:scroll !important;">
    <div class="card">
        <h5 class="card-header">Datos básicos</h5>
        <div class="card-body">
            <form wire:submit.prevent="submit">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

                <div class="mb-3 row d-flex align-items-center">
                    <label for="numero_factura" class="col-sm-2 col-form-label">Tipo de documento</label>
                    <div class="col-sm-10">
                        <select wire:model="tipo_documento" class="form-control" name="tipo_documento"
                            id="tipo_documento">
                            <option selected value="">-- Selecciona el tipo de documento --</option>
                            <option value="albaran_credito">Albarán de crédito</option>
                            <option value="factura">Factura</option>
                        </select>
                        @error('tipo_documento')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="numero_factura" class="col-sm-2 col-form-label">Número de factura</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="numero_factura" class="form-control" name="numero_factura"
                            id="numero_factura" disabled>
                        @error('numero_factura')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($tipo_documento == 'albaran_credito')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="id_presupuesto" class="col-sm-2 col-form-label">Presupuestos asociados</label>
                        <ul>
                            @foreach ($listaPresupuestos as $presupuesto)
                                <li>{{ $presupuestos->find($presupuesto)->numero_presupuesto }}</li>
                            @endforeach
                        </ul>
                        <div class="col-sm-10" wire:ignore.self>
                            <select id="id_presupuesto" class="form-control js-example-responsive"
                                wire:model="id_presupuesto">
                                <option value="0">-- Seleccione un presupuesto --</option>
                                @foreach ($presupuestos as $presup)
                                    @if (in_array($presup->id, $listaPresupuestos))
                                    @else
                                        <option value="{{ $presup->id }}">{{ $presup->numero_presupuesto }} </option>
                                    @endif
                                @endforeach
                            </select>
                            <button type="button" wire:click.prevent="addPresupuesto">Añadir presupuesto</button>
                            @error('id_presupuesto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @elseif($tipo_documento == 'factura')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="id_presupuesto" class="col-sm-2 col-form-label">Presupuesto asociado</label>
                        <div class="col-sm-10" wire:ignore.self>
                            <select id="id_presupuesto" class="form-control js-example-responsive"
                                wire:model="id_presupuesto" wire:change="addPrecio">
                                <option value="0">-- Seleccione un presupuesto --</option>
                                @foreach ($presupuestos as $presup)
                                    <option value="{{ $presup->id }}">{{ $presup->numero_presupuesto }} </option>
                                @endforeach
                            </select>
                            @error('id_presupuesto')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif

                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_emision" class="col-sm-2 col-form-label">Fecha de emisión</label>
                    <div class="col-sm-10">
                        <input type="date" wire:model.defer="fecha_emision" class="form-control"
                            placeholder="15/02/2023" wire:change='numeroFactura' id="datepicker">
                        @error('fecha_emision')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_vencimiento" class="col-sm-2 col-form-label">Fecha de vencimiento</label>
                    <div class="col-sm-10">
                        <input type="date" wire:model.defer="fecha_vencimiento" class="form-control"
                            placeholder="18/02/2023" id="datepicker2">
                        @error('fecha_vencimiento')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="descripcion" class="form-control" name="descripcion"
                            id="descripcion" placeholder="Factura para el cliente Dani...">
                        @error('descripcion')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                    <div class="col-sm-10" wire:ignore.self>
                        <select id="estado" class="form-control" wire:model="estado">
                            {{-- <option value="Pendiente">-- Seleccione un estado para el presupuesto--</option> --}}
                            <option value="Pendiente">Pendiente</option>
                            <option value="Aceptada">Aceptada</option>
                        </select>
                        @error('denominacion')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>


        </div>
    </div>
    <br>
    @if ($tipo_documento == 'factura')
        @if ($id_presupuesto != 0)
            <div class="card">
                <h5 class="card-header">Concepto de materiales y mano de obra</h5>
                <div class="card-body">
                    <div x-data="{}" x-init="$nextTick(() => {
                        $('#tableProductos').DataTable({
                            responsive: true,
                            fixedHeader: true,
                            searching: false,
                            paging: false,
                        });
                    })">
                        <div class="mb-3 row d-flex align-items-center">
                            <table class="table responsive" id="tableProductos">
                                <thead>
                                    <tr>
                                        <th scope="col">Código</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Tiempo</th>
                                        <th scope="col">Etiquetado europeo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (json_decode($presupuestos->find($id_presupuesto)->listaArticulos) as $productoE => $cantidad)
                                        <tr>
                                            <td>{{ $productos->find($productoE)->cod_producto }}</td>
                                            <td>{{ $productos->find($productoE)->descripcion }}</td>
                                            <td>{{ $productos->find($productoE)->precio_venta }}€</td>
                                            @if ($productos->find($productoE)->mueve_existencias == 1)
                                                <td>{{ $cantidad }}</td>
                                                <td>n/a</td>
                                            @else
                                                <td>n/a</td>
                                                <td> <input type="text"
                                                        wire:model="tiempo_lista.{{ $productoE }}"
                                                        class="form-control"> </td>
                                            @endif
                                            @if ($productos->find($productoE)->tipo_producto == 2)
                                                <td class="display:none">
                                                    <ul>
                                                        <li><b>Resistencia a la rodadura</b>:
                                                            {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->resistencia_rodadura }}
                                                        </li>
                                                        <li><b>Eficacia del frenado sobre suelo mojado</b>:
                                                            {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->agarre_mojado }}
                                                        </li>
                                                        <li><b>Ruido de rodadura exterior</b>:
                                                            {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->emision_ruido }}
                                                            dB
                                                        </li>
                                                    </ul>
                                                </td>
                                            @else
                                                <td class="display:none">n/a</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                <tbody>
                            </table>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="precio" class="col-sm-2 col-form-label">Total</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="precio" class="form-control" name="precio"
                                    step="0.01" id="precio" disabled>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="precio_iva" class="col-sm-2 col-form-label">Total con IVA</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="precio_iva" class="form-control" name="precio_iva"
                                    step="0.01" id="precio_iva" disabled>
                                @error('precio_iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <br>
        @endif
    @elseif($tipo_documento == 'albaran_credito')
        @if (isset($listaPresupuestos[0]))
            <div class="card">
                <h5 class="card-header">Concepto de materiales y mano de obra</h5>
                <div class="card-body">
                    <div x-data="{}" x-init="$nextTick(() => {
                        $('#tableProducto2').DataTable({
                            responsive: true,
                            fixedHeader: true,
                            searching: false,
                            paging: false,
                        });
                    })">
                        <div class="mb-3 row d-flex align-items-center">
                            <table class="table responsive" id="tableProducto2">
                                <thead>
                                    <tr>
                                        <th scope="col">Código</th>
                                        <th scope="col">Descripción</th>
                                        <th scope="col">Precio</th>
                                        <th scope="col">Cantidad</th>
                                        <th scope="col">Tiempo</th>
                                        <th scope="col">Etiquetado europeo</th>
                                        <th scope="col">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($listaPresupuestos as $presupuesto)
                                        @foreach (json_decode($presupuestos->find($presupuesto)->listaArticulos) as $productoE => $cantidad)
                                            <tr>
                                                <td>{{ $productos->find($productoE)->cod_producto }}</td>
                                                <td>{{ $productos->find($productoE)->descripcion }}</td>
                                                <td>{{ $productos->find($productoE)->precio_venta }}€</td>
                                                @if ($productos->find($productoE)->mueve_existencias == 1)
                                                    <td>{{ $cantidad }}</td>
                                                    <td>n/a</td>
                                                @else
                                                    <td>n/a</td>
                                                    <td> <input type="text"
                                                            wire:model="tiempo_lista.{{ $productoE }}"
                                                            class="form-control"> </td>
                                                @endif
                                                @if ($productos->find($productoE)->tipo_producto == 2)
                                                    <td class="display:none">
                                                        <ul>
                                                            <li><b>Resistencia a la rodadura</b>:
                                                                {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->resistencia_rodadura }}
                                                            </li>
                                                            <li><b>Eficacia del frenado sobre suelo mojado</b>:
                                                                {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->agarre_mojado }}
                                                            </li>
                                                            <li><b>Ruido de rodadura exterior</b>:
                                                                {{ $neumaticos->where('articulo_id', $productos->find($productoE)->cod_producto)->first()->emision_ruido }}
                                                                dB
                                                            </li>
                                                        </ul>
                                                    </td>
                                                @else
                                                    <td class="display:none">n/a</td>
                                                @endif
                                                <td>{{ $productos->find($productoE)->precio_venta * $cantidad }}€
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                <tbody>
                            </table>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="precio" class="col-sm-2 col-form-label">Total</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="precio" class="form-control" name="precio"
                                    step="0.01" id="precio" disabled>
                                @error('precio')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="mb-3 row d-flex align-items-center">
                            <label for="precio_iva" class="col-sm-2 col-form-label">Total con IVA</label>
                            <div class="col-sm-10">
                                <input type="text" wire:model="precio_iva" class="form-control" name="precio_iva"
                                    step="0.01" id="precio_iva" disabled>
                                @error('precio_iva')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br>

        @endif
    @endif
    <br>
    @if ($tipo_documento == 'factura')
        @if ($id_presupuesto != 0)
            @if ($observaciones != null)
                <div class="card">
                    <h5 class="card-header">Comentarios</h5>
                    <div class="card-body">
                        <div class="mb-3 row d-flex align-items-center">
                            <div class="col-sm-10">
                                <textarea wire:model="observaciones" class="form-control" name="observaciones" id="observaciones" rows="3"
                                    disabled></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                @if ($documentos != null)
                    <div class="card">
                        <h5 class="card-header">Imágenes adjuntas</h5>
                        <div class="card-body">
                            @foreach ($documentos as $documento)
                                <div class="documento">
                                    @if (Str::endsWith($documento, ['.png', '.jpg', '.jpeg', '.gif']))
                                        <!-- Mostrar vista previa de la imagen -->
                                        <img src="{{ Storage::url($documento) }}" alt="Documento"
                                            style=" width: 100%">
                                    @elseif (Str::endsWith($documento, ['.pdf']))
                                        {{ substr($documento, 11) }} : <a class="btn btn-primary"
                                            href="{{ Storage::url($documento) }}" target="_blank">Ver Documento</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <br>
                @else
                    <div class="mb-3 row d-flex align-items-center">
                        <button type="button" class="btn btn-primary" wire:click.prevent="addDocumentos">Añadir
                            imágenes
                            desde
                            orden de trabajo</button>
                    </div>
                    <br>
                @endif
            @else
                @if ($documentos != null)
                    <div class="card">
                        <h5 class="card-header">Imágenes adjuntas</h5>
                        <div class="card-body">
                            @foreach ($documentos as $documento)
                                <div class="documento">
                                    @if (Str::endsWith($documento, ['.png', '.jpg', '.jpeg', '.gif']))
                                        <!-- Mostrar vista previa de la imagen -->
                                        <img src="{{ Storage::url($documento) }}" alt="Documento"
                                            style=" width: 100%">
                                    @elseif (Str::endsWith($documento, ['.pdf']))
                                        {{ substr($documento, 11) }} : <a class="btn btn-primary"
                                            href="{{ Storage::url($documento) }}" target="_blank">Ver Documento</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <br>
                @else
                    <div class="mb-3 row d-flex align-items-center">
                        <button type="button" class="btn btn-primary" wire:click.prevent="addDocumentos">Añadir
                            imágenes
                            desde
                            orden de trabajo</button>
                    </div>
                    <br>
                @endif
                <div class="mb-3 row d-flex align-items-center">
                    <button type="button" class="btn btn-primary" wire:click.prevent="addObservaciones">Añadir
                        comentarios
                        desde orden de trabajo</button>
                </div>
                <br>
            @endif
        @endif
    @else
        @if (isset($listaPresupuestos[0]))
            @if ($observaciones != null)
                <div class="card">
                    <h5 class="card-header">Comentarios</h5>
                    <div class="card-body">
                        @foreach ($observaciones as $presupuesto => $comentario)
                            <div class="mb-3 row d-flex align-items-center">
                                <div class="col-sm-10">
                                    <h5>{{ $presupuesto }}</h5>
                                    <textarea class="form-control" name="observaciones" id="observaciones" rows="3" disabled>{{ $comentario }}</textarea>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <br>
                @if ($documentos != null)
                    <div class="card">
                        <h5 class="card-header">Imágenes adjuntas</h5>
                        <div class="card-body">
                            @foreach ($documentos as $documento)
                                <div class="documento">
                                    @if (Str::endsWith($documento, ['.png', '.jpg', '.jpeg', '.gif']))
                                        <!-- Mostrar vista previa de la imagen -->
                                        <img src="{{ Storage::url($documento) }}" alt="Documento"
                                            style=" width: 100%">
                                    @elseif (Str::endsWith($documento, ['.pdf']))
                                        {{ substr($documento, 11) }} : <a class="btn btn-primary"
                                            href="{{ Storage::url($documento) }}" target="_blank">Ver Documento</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <br>
                @else
                    <div class="mb-3 row d-flex align-items-center">
                        <button type="button" class="btn btn-primary" wire:click.prevent="addDocumentos">Añadir
                            imágenes
                            desde
                            orden de trabajo</button>
                    </div>
                    <br>
                @endif
            @else
                @if ($documentos != null)
                    <div class="card">
                        <h5 class="card-header">Imágenes adjuntas</h5>
                        <div class="card-body">
                            @foreach ($documentos as $documento)
                                <div class="documento">
                                    @if (Str::endsWith($documento, ['.png', '.jpg', '.jpeg', '.gif']))
                                        <!-- Mostrar vista previa de la imagen -->
                                        <img src="{{ Storage::url($documento) }}" alt="Documento"
                                            style=" width: 100%">
                                    @elseif (Str::endsWith($documento, ['.pdf']))
                                        {{ substr($documento, 11) }} : <a class="btn btn-primary"
                                            href="{{ Storage::url($documento) }}" target="_blank">Ver Documento</a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <br>
                @else
                    <div class="mb-3 row d-flex align-items-center">
                        <button type="button" class="btn btn-primary" wire:click.prevent="addDocumentos">Añadir
                            imágenes
                            desde
                            orden de trabajo</button>
                    </div>
                    <br>
                @endif
                <div class="mb-3 row d-flex align-items-center">
                    <button type="button" class="btn btn-primary" wire:click.prevent="addObservaciones">Añadir
                        comentarios
                        desde orden de trabajo</button>
                </div>
                <br>
            @endif
        @endif
    @endif

    <div class="row d-flex align-items-center">
        <button class="btn btn-primary" wire:click="submit('No pagado')">Guardar factura sin cobrar
        </button>
    </div>
    <div class="p-1"></div>
    <div class="dropdown row d-flex align-items-center">
        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
            aria-expanded="false">
            Cobrar factura directamente </button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="#" wire:click="submit('Contado')">Contado</a>
            <a class="dropdown-item" href="#" wire:click="submit('Tarjeta de crédito')">Tarjeta de crédito</a>
            <a class="dropdown-item" href="#" wire:click="submit('Transferencia bancaria')">Transferencia
                bancaria</a>
            <a class="dropdown-item" href="#" wire:click="submit('Recibo bancario a 30 días')">Recibo bancario
                a 30 días</a>
            <a class="dropdown-item" href="#" wire:click="submit('Bizum')">Bizum</a>
            <a class="dropdown-item" href="#" wire:click="submit('Financiado')">Financiado</a>
        </div>
    </div>
    <br>
    <br>
    </form>
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
                $('.js-example-responsive').select2({
                    placeholder: "-- Seleccione un presupuesto --",
                    width: 'resolve'
                }).on('change', function() {
                    var selectedValue = $(this).val();
                    // Llamamos a la función listarPresupuesto() pasando el valor seleccionado
                    Livewire.emit('listarPresupuesto', selectedValue);
                });

            });
        </script>
    @endsection
</div>
