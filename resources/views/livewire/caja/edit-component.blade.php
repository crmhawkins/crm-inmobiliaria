<div class="container mx-auto">
    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card mb-3">
            <h5 class="card-header">
                Movimiento de caja
            </h5>
            <div class="card-body">
                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
                    <div class="col-sm-10">
                        <input type="date" wire:model="fecha" class="form-control" name="fecha" id="fecha">
                        @error('nombre')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="descripcion" class="col-sm-2 col-form-label">Descripción</label>
                    <div class="col-sm-10">
                        <textarea wire:model="descripcion" class="form-control" name="descripcion" id="descripcion" rows="3">
                        </textarea>
                        @error('valor')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="cantidad" class="col-sm-2 col-form-label">Cantidad</label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="cantidad" class="form-control" name="cantidad" id="cantidad"
                            step="0.01">
                        @error('cantidad')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="metodo_pago" class="col-sm-2 col-form-label">Método de pago</label>
                    <div class="col-sm-10">
                        <select wire:model="metodo_pago" class="form-control" id="metodo_pago" name="metodo_pago">
                            <option value="Contado">Contado</option>
                            <option value="Tarjeta de crédito">Tarjeta de crédito</option>
                            <option value="Transferencia bancaria">Transferencia bancaria</option>
                            <option value="Recibo bancario a 30 días">Recibo bancario a 30 días</option>
                            <option value="Bizum">Bizum</option>
                            <option value="Financiado">Financiado</option>
                        </select> @error('metodo_pago')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <button type="submit" class="btn btn-outline-info">Guardar</button>
        </div>
    </form>
</div>
