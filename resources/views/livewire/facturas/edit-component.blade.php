

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection
<div class="container mx-auto">
    <h1>Facturas</h1>
    <h2>Editar Factura</h2>
    <br>

    <a href="/admin/factura/pdf/{{$identificador}}" class="btn btn-info text-white">Dercargar Factura PDF</a>
    <a href="/admin/certificado/{{$identificador}}" class="btn btn-info text-white">Descargar Certificado</a>
    <br><br>

        <form wire:submit.prevent="update">
            <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">

            <div class="mb-3 row d-flex align-items-center">
                <label for="numero_factura" class="col-sm-2 col-form-label">Número de Factura</label>
                <div class="col-sm-10">
                    <input type="text" wire:model="numero_factura" class="form-control" name="numero_factura"
                        id="numero_factura">
                    @error('numero_factura')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-3 row d-flex align-items-center">
                <label for="id_presupuesto" class="col-sm-2 col-form-label">Presupuesto asociado</label>
                <div class="col-sm-10" wire:ignore.self>
                    <select id="id_presupuesto" class="form-control js-example-responsive" wire:model="id_presupuesto">
                        <option value="0">-- Seleccione un presupuesto --</option>
                        @foreach ($presupuestos as $presup)
                            <option value="{{ $presup->id }}" {{ $id_presupuesto == $presup->id ? 'selected' : '' }}>{{ $presup->numero_presupuesto }} </option>
                        @endforeach
                    </select>
                    @error('id_presupuesto')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            @if ($estadoPresupuesto != 0)

                <div class="mb-3 row d-flex align-items-center">
                    <label for="detalles_presupuesto" class="col-sm-2 col-form-label">Detalles del presupuesto</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{ optional($presupuestoSeleccionado)->detalles }}" class="form-control" name="detalles_presupuesto" id="detalles_presupuesto">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="alumno" class="col-sm-2 col-form-label">Alumno</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($alumnoDePresupuestoSeleccionado)->nombre}}" class="form-control" name="alumno" id="alumno">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="curso" class="col-sm-2 col-form-label">Curso</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($cursoDePresupuestoSeleccionado)->nombre}}" class="form-control" name="curso" id="curso">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="total_sin_iva" class="col-sm-2 col-form-label">Precio sin IVA</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($presupuestoSeleccionado)->total_sin_iva}}" class="form-control" name="total_sin_iva" id="total_sin_iva">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>


                <div class="mb-3 row d-flex align-items-center">
                    <label for="iva" class="col-sm-2 col-form-label">Tipo de IVA</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($presupuestoSeleccionado)->iva}}" class="form-control" name="iva" id="iva">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="descuento" class="col-sm-2 col-form-label">Descuento</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($presupuestoSeleccionado)->descuento}}" class="form-control" name="descuento" id="descuento">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="precio" class="col-sm-2 col-form-label">Precio total</label>
                    <div class="col-sm-10">
                        <input readOnly type="text" placeholder="{{optional($presupuestoSeleccionado)->precio}}" class="form-control" name="precio" id="precio">
                        @error('detalles_presupuesto') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>



            @endif

            <div class="mb-3 row d-flex align-items-center">
                <label for="fecha_emision" class="col-sm-2 col-form-label">Fecha de emisión</label>
                <div class="col-sm-10">
                    <input type="text" wire:model.defer="fecha_emision" class="form-control" placeholder="15/02/2023"
                        id="datepicker">
                    @error('fecha_emision')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-3 row d-flex align-items-center">
                <label for="fecha_vencimiento" class="col-sm-2 col-form-label">Fecha de vencimiento</label>
                <div class="col-sm-10">
                    <input type="text" wire:model.defer="fecha_vencimiento" class="form-control" placeholder="18/02/2023"
                        id="datepicker2">
                    @error('fecha_vencimiento')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="mb-3 row d-flex align-items-center">
                <label for="descripcion" class="col-sm-2 col-form-label">Descripción </label>
                <div class="col-sm-10">
                  <input type="text" wire:model="descripcion" class="form-control" name="descripcion" id="descripcion" placeholder="Factura para el cliente Dani...">
                  @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3 row d-flex align-items-center">
                <label for="estado" class="col-sm-2 col-form-label">Estado</label>
                <div class="col-sm-10" wire:ignore.self>
                    <select id="estado" class="form-control" wire:model="estado"">
                        {{-- <option value="Pendiente">-- Seleccione un estado para el presupuesto--</option> --}}
                        <option value="Pendiente">Pendiente</option>
                        <option value="Aceptada">Aceptada</option>
                    </select>
                    @error('denominacion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3 row d-flex align-items-center">
                <label for="metodo_pago" class="col-sm-2 col-form-label">Método de pago</label>
                <div class="col-sm-10" wire:ignore.self>
                    <select id="metodo_pago" class="form-control" wire:model="metodo_pago"">
                        {{-- <option value="Pendiente">-- Seleccione un estado para el presupuesto--</option> --}}
                        <option value="No pagado">No pagado</option>
                        <option value="En efectivo">En efectivo</option>
                        <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                        <option value="Transferencia bancaria">Transferencia bancaria</option>
                    </select>
                    @error('denominacion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>



            <div class="mb-3 row d-flex align-items-center">
                <button type="submit" class="btn btn-outline-info">Guardar</button>
            </div>
                <div class="mb-3 row d-flex align-items-center">
                    <button wire:click="destroy" class="btn btn-outline-danger">Eliminar</button>
                </div>



            </form>
        </div>



        </div>

</div>


@section('scripts')
    <script>
        $.datepicker.regional['es'] = {
            closeText: 'Cerrar',
            prevText: '< Ant',
            nextText: 'Sig >',
            currentText: 'Hoy',
            monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
            monthNamesShort: ['Ene','Feb','Mar','Abr', 'May','Jun','Jul','Ago','Sep', 'Oct','Nov','Dic'],
            dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            dayNamesShort: ['Dom','Lun','Mar','Mié','Juv','Vie','Sáb'],
            dayNamesMin: ['Do','Lu','Ma','Mi','Ju','Vi','Sá'],
            weekHeader: 'Sm',
            dateFormat: 'dd/mm/yy',
            firstDay: 1,
            isRTL: false,
            showMonthAfterYear: false,
            yearSuffix: ''
        };
        $.datepicker.setDefaults($.datepicker.regional['es']);
        document.addEventListener('livewire:load', function () {


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
            console.log('select2')
            $("#datepicker").datepicker();

            $("#datepicker").on('change', function(e){
                @this.set('fecha_inicio', $('#datepicker').val());
                });
            $("#datepicker2").datepicker();

            $("#datepicker2").on('change', function(e){
                @this.set('fecha_fin', $('#datepicker2').val());
                });

        });
    </script>

    {{-- SCRIPT PARA SELECT 2 CON LIVEWIRE --}}
    <script>
        window.initSelect2 = () => {
            jQuery("#id_presupuesto").select2({
                minimumResultsForSearch: 2,
                allowClear: false
            });
        }

        initSelect2();
        window.livewire.on('select2', ()=>{
            initSelect2();
        });
    </script>

@endsection

