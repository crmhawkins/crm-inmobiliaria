<div>
    <h5 class="card-header">
        Administrar contrato ya subido
    </h5>

    <div class="card-body">
        @if($contratoArras != null)
            <li><a href="{{$contratoArras}}" class="btn btn-primary"> Ver contrato de arras</a></li>
		            <li><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalContrato">
 Enviar contrato</button>
<div class="modal fade" id="modalContrato" tabindex="-1" aria-labelledby="modalLabelContrato" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabelContrato">Selecciona un cliente</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <select class="form-control" wire:model="cliente_id">
                    @foreach($clientes as $cliente)
                        <option value="{{ $cliente->id }}">{{ $cliente->nombre_completo }}</option>
                    @endforeach
                </select>
            </div>
			<div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal" wire:click.prevent="enviarCorreo( '{{$contratoArras}}' )">Enviar correo</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div> </li>

        @endif
    </div>
    <h5 class="card-header">
        Añadir contrato
    </h5>
    <div class="card-body">
        <div class="input-group">
            <span class="input-group-btn">
                <a id="lfm2" data-input="thumbnail2" data-preview="holder" class="btn btn-secondary">
                    <i class="fa fa-picture-o"></i> Subir contrato
                </a>
            </span>
            <input id="thumbnail2" name="contrato" wire:model="contrato" class="form-control" type="text">
        </div>
        @if (!empty($contrato))
            <button class="btn btn-primary mt-3" wire:click.prevent="addContrato">Adjuntar contrato</button>
        @endif

        <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

        <script>
            $('#lfm2').on('click', function() {
                var route_prefix = '/laravel-filemanager' || '';
                var type = $(this).data('type') || 'contratos';
                var target_input = document.getElementById('thumbnail2');

                window.open(route_prefix + '?type=' + type || 'file', 'FileManager',
                    'width=900,height=600');
                window.SetUrl = function(items) {
                    var file_path = items.map(function(item) {
                        return item.url;
                    }).join(',');

                    // set the value of the desired input to image url
                    target_input.value = file_path;
                    target_input.dispatchEvent(new Event('input'));

                    // trigger change event
                    window.livewire.emit('fileSelected', file_path);
                };
                return false;
            });
        </script>
    </div>

</div>
