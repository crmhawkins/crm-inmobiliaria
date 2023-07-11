

@section('head')
    @vite(['resources/sass/app.scss'])
@endsection
<div class="container mx-auto">
    <h1>Productos</h1>
    <h2>Editar Productos</h2>
    <br>


            <form wire:submit.prevent="update">
                <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
                <div class="mb-3 row d-flex align-items-center">

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="dni" class="col-sm-2 col-form-label">DNI </label>
                        <div class="col-sm-10">
                          <input type="text" wire:model="dni" class="form-control" name="dni" id="dni" placeholder="7515763200P">
                          @error('dni') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <label for="nombre" class="col-sm-2 col-form-label">Nombre </label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="nombre" class="form-control" name="nombre" id="nombre" placeholder="Carlos">
                      @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="email" class="col-sm-2 col-form-label">Email</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="email" class="form-control" name="email" id="email" placeholder="ejemplo@gmail.com">
                      @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="telefono" class="col-sm-2 col-form-label">Teléfono</label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="telefono" class="form-control" name="telefono" id="telefono" placeholder="956812502">
                      @error('telefono') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="direccion" class="col-sm-2 col-form-label">Dirección </label>
                    <div class="col-sm-10">
                      <input type="text" wire:model="direccion" class="form-control" name="direccion" id="direccion" placeholder="Calle Baldomero nº 12">
                      @error('direccion') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <button type="submit" class="btn btn-outline-info">Guardar</button>
                </div>




            </form>
            <div class="mb-3 row d-flex align-items-center">
              <button wire:click="destroy" class="btn btn-outline-danger">Eliminar</button>
          </div>
        </div>

</div>


