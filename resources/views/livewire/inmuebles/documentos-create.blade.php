<div>
	    <script src="https://code.jquery.com/jquery-3.6.1.min.js"
        integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script>
    <h5 class="card-header">
        Documentos adjuntos al inmueble
    </h5>
    <div class="card-body">
        @if($docs != null)
            @foreach (json_decode($docs->rutas, true) as $documentoIndex => $document)
            <li class="mb-1"><a href="{{$document}}" class="btn btn-primary"> Ver documento "{{basename(urldecode($document))}}"</a></li>
			<li class="mb-1"><button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal{{$documentoIndex}}">
 Enviar documento "{{basename(urldecode($document))}}"</button></li>
<div class="modal fade" id="exampleModal{{$documentoIndex}}" tabindex="-1" aria-labelledby="exampleModalLabel{{$documentoIndex}}" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel{{$documentoIndex}}">Selecciona un cliente</h5>
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
                <button type="button" class="btn btn-primary" data-dismiss="modal" wire:click.prevent="enviarCorreo( '{{$document}}' )">Enviar correo</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
            @endforeach
        @endif
    </div>
    <h5 class="card-header">
        Añadir documento
    </h5>
    <div class="card-body">

        <div class="input-group">
            <span class="input-group-btn">
                <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-secondary">
                    <i class="fa fa-picture-o"></i> Seleccionar documento
                </a>
            </span>
            <input id="thumbnail" name="documento" wire:model="documento" class="form-control" type="text">
        </div>
        @if (!empty($documento))
            <button class="btn btn-primary mt-3" wire:click.prevent="addDocumento">Adjuntar documento</button>
        @endif

        <script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>

        <script>
            $('#lfm').on('click', function() {
                var route_prefix = '/laravel-filemanager' || '';
                var type = $(this).data('type') || 'documentos';
                var target_input = document.getElementById('thumbnail');

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
