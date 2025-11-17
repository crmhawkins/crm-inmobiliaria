<div class="container mx-auto">
    <style>
        .invoice-form-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .invoice-header {
            background: var(--corporate-green-gradient);
            color: white;
            padding: 25px 30px;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .invoice-header i {
            font-size: 1.6rem;
        }
        .article-item {
            background: #f8faf9;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            border: 2px solid #e8f0e8;
            transition: all 0.3s ease;
        }
        .article-item:hover {
            border-color: var(--corporate-green-light);
            box-shadow: 0 4px 15px rgba(107, 142, 107, 0.1);
        }
        .total-section {
            background: var(--corporate-green-lightest);
            border-radius: 12px;
            padding: 25px;
            margin-top: 20px;
        }
        .total-amount {
            font-size: 2rem;
            font-weight: 700;
            color: var(--corporate-green-dark);
        }
    </style>
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="invoice-form-section">
            <div class="invoice-header">
                <i class="fas fa-file-invoice-dollar"></i>
                Añadir datos de factura
            </div>
            <div class="card-body p-4">
                <div class="mb-3 row d-flex align-items-center">
                    <label for="cliente" class="col-sm-3 col-form-label"><h5>Cliente:</h5></label>
                    <div x-data="" x-init="$('#select2-cliente-create').select2();
                    $('#select2-cliente-create').on('change', function(e) {
                        var data = $('#select2-cliente-create').select2('val');
                        @this.set('cliente', data);
                    });">
                        <div class="col" wire:ignore>
                            <select class="form-control" id="select2-cliente-create">
                                <option value="">-- Elige un cliente --</option>
                                @foreach ($clientes as $cliente)
                                    <option value={{ $cliente->id }}>{{ $cliente->nombre_completo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="numero_factura" class="col-sm-3 col-form-label"><h5>Número de factura</h5></label>
                    <div class="col-sm-12">
                        <input type="number" wire:model="numero_factura" class="form-control" id="numero_factura"
                            placeholder="Número de factura">
                        @error('numero_factura')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha" class="col-sm-3 col-form-label"><h5>Fecha de facturación</h5></label>
                    <div class="col-sm-6">
                        <input type="date" wire:model="fecha" class="form-control" id="fecha">
                        <input type="date" wire:model="fecha_vencimiento" class="form-control" id="fecha_vencimiento">
                        @error('fecha')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <h5> Artículos </h5>
                <hr />

                @foreach ($articulosArray as $index => $articulo)
                    <div class="mb-3 row d-flex align-items-center">
                        <div class="col-sm-5">
                            <label for="descripcion.{{ $index }}" class="form-label">Descripción</label>
                            <input type="text" wire:model="articulosArray.{{ $index }}.descripcion"
                                class="form-control" id="descripcion.{{ $index }}">
                        </div>
                        <div class="col-sm-3">
                            <label for="importe.{{ $index }}" class="form-label">Importe</label>
                            <input type="number" wire:model="articulosArray.{{ $index }}.importe"
                                class="form-control" id="importe.{{ $index }}">
                        </div>
                        <div class="col-sm-2">
                            <label for="impuesto.{{ $index }}" class="form-label">Impuesto</label>
                            <select wire:model="articulosArray.{{ $index }}.impuesto" class="form-control"
                                id="impuesto.{{ $index }}">
                                <option value="0">-- Sin impuestos --</option>
                                <option value="21">-- IVA normal --</option>
                                <option value="10">-- IVA reducido --</option>
                                <option value="4">-- IVA superreducido --</option>

                            </select>
                        </div>
                        <div class="col-sm-2">
                            <button type="button" class="btn btn-danger"
                                wire:click="removeArticulo({{ $index }})">Eliminar</button>
                        </div>
                    </div>
                @endforeach

                <div class="mb-3 row d-flex align-items-center">
                    <div class="col-sm-10">
                        <button type="button" class="btn btn-primary" wire:click="addArticulo">Añadir artículo</button>
                    </div>
                </div>
                <hr />
                <div class="mb-3 row d-flex text-end">
                    <div class="col-10">
                        <h6>Subtotal:</h6>
                    </div>
                    <div class="col-2">
                        <h6>{{ $subtotal }} €</h6>
                    </div>
                </div>
                <div class="mb-3 row d-flex text-end">

                    <div class="col-10">

                        <h3>Total:</h3>
                    </div>
                    <div class="col-2">
                        <h3>{{ $total }} € </h3>
                    </div>

                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="condiciones" class="col-sm-4 col-form-label"><h5>Condiciones y forma de pago</h5></label>
                    <div class="col-sm-12">
                        <textarea wire:model="condiciones" class="form-control" id="condiciones" placeholder="Condiciones"></textarea>
                        @error('condiciones')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</div>
