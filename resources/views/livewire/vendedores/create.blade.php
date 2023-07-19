<div class="container mx-auto">
    <form wire:submit.prevent="submit">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="card mb-3">
            <h5 class="card-header">
                Registrar nuevo vendedor
            </h5>
            <div class="card-body">
                <div class="mb-3 row d-flex align-items-center">
                    <label for="nombre_completo" class="col-sm-2 col-form-label"> <strong>Nombre</strong> </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="nombre_completo" class="form-control" name="nombre_completo"
                            id="nombre_completo" placeholder="Nombre completo del vendedor">
                        @error('nombre_completo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="dni" class="col-sm-2 col-form-label"> <strong>DNI</strong> </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="dni" class="form-control" name="dni" id="dni"
                            placeholder="Añade la identificación del vendedor.">
                        @error('dni')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="password" class="col-sm-2 col-form-label"> <strong>Contraseña</strong> </label>
                    <div class="col-sm-8">
                        <input type="text" wire:model="password" class="form-control" name="password" id="password"
                            placeholder="Añade la identificación del vendedor.">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-sm-2">
                        <button type="button" class="btn btn-primary" wire:click="generarPassword">Generar</button>
                    </div>
                </div>
                <p> Recuerda guardar la contraseña de forma segura antes de crear el usuario. </p>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="telefono" class="col-sm-2 col-form-label"> <strong>Teléfono</strong> </label>
                    <div class="col-sm-10">
                        <input type="number" wire:model="telefono" class="form-control" name="telefono" id="telefono"
                            placeholder="Teléfono del cliente (ej; 111223344)">
                        @error('telefono')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="email" class="col-sm-2 col-form-label"> <strong>Correo electrónico</strong> </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="email" class="form-control" name="email" id="email"
                            placeholder="Correo electrónico del cliente (ej; ejemplo@gmail.com)">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="ubicacion" class="col-sm-2 col-form-label"> <strong>Ubicación</strong> </label>
                    <div class="col-sm-10">
                        <input type="text" wire:model="ubicacion" class="form-control" name="ubicacion"
                            id="ubicacion" placeholder="Añade la ubicación del vendedor.">
                        @error('ubicacion')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row text-center">
                    <h5> Firma </h5>
                    @if ($firma == null)
                        <div class="col">
                            <canvas id="signature-pad" class="signature-pad border" width="500"
                                height="250"></canvas>
                        </div>
                </div>

                <button class="btn btn-primary" wire:click.debounce.150ms="saveSignature" id="btnFirma">Guardar
                    firma</button>
            @else
                <div class="col">
                    <img src='{{ asset('storage/' . $firma) }}'>
                </div>
            </div>
            @endif
        </div>
</div>
<script>
    $(document).ready(function() {

        const canvas = document.querySelector("canvas");
        const signaturePad = new SignaturePad(canvas);

        document.querySelector('#btnFirma').addEventListener('click', function() {
            if (signaturePad.isEmpty()) {
                alert("Por favor proporciona tu firma primero.");
            } else {
                var data = signaturePad.toDataURL();
                console.log(data);
                @this.set('signature', data)
            }
        });
    });
</script>
</div>
<div class="mb-3 row d-flex align-items-center">
    <button type="submit" class="btn btn-primary">Guardar</button>
</div>
</form>
</div>
