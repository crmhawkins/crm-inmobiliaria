<div>
    <h5 class="card-header">
        Documentos adjuntos al inmueble
    </h5>
    <div class="card-body">
        @if($docs != null)
            @foreach (json_decode($docs->rutas, true) as $documentoIndex => $documento)
            <li class="mb-1"><a href="{{$documento}}" class="btn btn-primary"> Ver documento "{{basename(urldecode($documento))}}"</a></li>
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
