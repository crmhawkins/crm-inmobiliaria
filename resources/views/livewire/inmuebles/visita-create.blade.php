<div class="card mb-3">
    <style>
        body {
            background-color: #fff;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
    <h5 class="card-header">
        Datos básicos
    </h5>
    <div class="card-body">
        <h5>Seleccionar cliente</h5>
        <div class="mb-3 row d-flex align-items-center" wire:ignore>
            <div x-data="" x-init="$('#select2-cliente_firma-{{ $inmueble_id }}').select2();
            $('#select2-cliente_firma-{{ $inmueble_id }}').on('change', function(e) {
                var data = $('#select2-cliente_firma-{{ $inmueble_id }}').select2('val');
                @this.set('cliente_id', data);
                console.log(data);
            });" wire:ignore>
                <select class="form-control" id="select2-cliente_firma-{{ $inmueble_id }}">
                    @foreach ($clientes as $cliente)
                        <option value="{{ $cliente->id }}">
                            {{ $cliente->nombre_completo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <label for="fecha" class="col-sm-2 col-form-label">Fecha</label>
            <div class="col-sm-10">
                <input type="date" wire:model="fecha" class="form-control" name="fecha" id="fecha"
                    placeholder="Fecha">
                @error('fecha')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <br>
            <div class="row text-center">
                <h5> Firma </h5>
                @if ($firma == null)
                    <div class="col">
                        <canvas id="signature-pad" class="signature-pad border" width="500" height="250"></canvas>
                    </div>
            </div>

            <button class="btn btn-primary" wire:click.debounce.150ms="saveSignature" id="btnFirma">Guardar
                firma</button>
        @else
            <div class="col">
                <img src='{{ asset('storage/' . $firma) }}'>
            </div>
        </div>
        <button class="btn btn-primary" wire:click.prevent="submit">Crear hoja de visita</button>
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
