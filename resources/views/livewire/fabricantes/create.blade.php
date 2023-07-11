<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card">
            <h5 class="card-header">{{ __('Añadir fabricante') }}</h5>
            <div class="card-body">
                <div class="row mb-3">
                    <label for="nombre" class="col-md-4 col-form-label text-md-end">{{ __('Nombre') }}</label>

                    <div class="col-md-6">
                        <input id="nombre" type="text" class="form-control @error('name') is-invalid @enderror"
                            name="nombre" value="{{ old('nombre') }}" required wire:model="nombre"
                            autocomplete="nombre" autofocus>

                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row d-flex align-items-center">
            <button type="submit" class="btn btn-primary">
                {{ __('Añadir fabricante') }}
            </button>
        </div>
    </form>
</div>
