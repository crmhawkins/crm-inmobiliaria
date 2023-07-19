<div>
    <h5 class="card-header">
        Contrato de arras
    </h5>

    <div class="card-body">
        @if($contratoArras != null)
            <li><a href="{{$contratoArras}}" class="btn btn-primary"> Ver contrato de arras</a></li>
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
