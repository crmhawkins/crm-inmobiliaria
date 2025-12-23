<div class="container mx-auto">
    <style>
        .agenda-form-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 25px;
        }
        .agenda-header {
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
        .agenda-header i {
            font-size: 1.6rem;
        }
        .signature-card {
            background: linear-gradient(135deg, var(--corporate-green-lightest) 0%, white 100%);
            border: 2px solid var(--corporate-green-light);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
    <div class="mb-4">
        <button type="button" class="btn btn-success btn-lg shadow-sm" wire:click="abrirModalFirma" wire:loading.attr="disabled" id="btnHojaFirma">
            <span wire:loading.remove wire:target="abrirModalFirma">
                <i class="fas fa-signature me-2"></i>
                Hoja de Firma
            </span>
            <span wire:loading wire:target="abrirModalFirma" style="display: none;">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                <span>Cargando...</span>
            </span>
        </button>
        <div id="modal-state-indicator" data-show-modal="{{ $showFirmaModal ?? false ? 'true' : 'false' }}" style="display: none;"></div>
    </div>
    <form wire:submit.prevent="update">
        <input type="hidden" name="csrf-token" value="{{ csrf_token() }}">
        <div class="agenda-form-section">
            <div class="agenda-header">
                <i class="fas fa-calendar-plus"></i>
                Añadir cita a la agenda
            </div>
            <div class="card-body p-4">
                <div class="mb-3 row d-flex align-items-center">
                    <label class="col-sm-2 col-form-label">Tipo de cita</label>
                    <div class="col-sm-10">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_tarea" id="tipo_tarea_1"
                                wire:model="tipo_tarea" value="opcion_1" {{ $tipo_tarea == 'opcion_1' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipo_tarea_1">
                                Cita con cliente
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="tipo_tarea" id="tipo_tarea_2"
                                wire:model="tipo_tarea" value="opcion_2" {{ $tipo_tarea == 'opcion_2' ? 'checked' : '' }}>
                            <label class="form-check-label" for="tipo_tarea_2">
                                Personalizado
                            </label>
                        </div>
                        @error('tipo_tarea')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                @if ($tipo_tarea == 'opcion_1')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="cliente" class="col-sm-3 col-form-label"><strong>Cliente:</strong></label>
                        <div x-data="" x-init="$nextTick(() => {
                            if ($('#select2-cliente-edit').length) {
                                $('#select2-cliente-edit').select2({
                                    placeholder: '-- Elige un cliente --',
                                    allowClear: true
                                });

                                // Establecer valor seleccionado
                                @if($cliente_id)
                                    $('#select2-cliente-edit').val('{{ $cliente_id }}').trigger('change');
                                @endif

                                $('#select2-cliente-edit').on('change', function(e) {
                                    var data = $('#select2-cliente-edit').select2('val');
                                    @this.set('cliente_id', data);
                                });
                            }
                        });">
                            <div class="col" wire:ignore>
                                <small class="text-muted">Clientes disponibles: {{ $clientes->count() }}</small>
                                <select class="form-control" id="select2-cliente-edit">
                                    <option value="">-- Elige un cliente --</option>
                                    @foreach ($clientes as $cliente)
                                        <option value="{{ $cliente->id }}" {{ $cliente_id == $cliente->id ? 'selected' : '' }}>
                                            {{ $cliente->nombre_completo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="inmueble" class="col-sm-3 col-form-label"><strong>Inmueble :</strong></label>
                        <div x-data="" x-init="$nextTick(() => {
                            if ($('#select2-inmueble-edit').length) {
                                $('#select2-inmueble-edit').select2({
                                    placeholder: '-- Elige un inmueble --',
                                    allowClear: true
                                });

                                // Establecer valor seleccionado
                                @if($inmueble_id)
                                    $('#select2-inmueble-edit').val('{{ $inmueble_id }}').trigger('change');
                                @endif

                                $('#select2-inmueble-edit').on('change', function(e) {
                                    var data = $('#select2-inmueble-edit').select2('val');
                                    @this.set('inmueble_id', data);
                                });
                            }
                        });">
                            <div class="col" wire:ignore>
                                <small class="text-muted">Inmuebles disponibles: {{ $inmuebles->count() }}</small>
                                <select class="form-control" id="select2-inmueble-edit">
                                    <option value="">-- Elige un inmueble --</option>
                                    @foreach ($inmuebles as $inmueble)
                                        <option value="{{ $inmueble->id }}" {{ $inmueble_id == $inmueble->id ? 'selected' : '' }}>
                                            {{ $inmueble->titulo }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                @elseif($tipo_tarea == 'opcion_2')
                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre_completo" class="col-sm-2 col-form-label">Título de la cita</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="titulo" class="form-control" name="titulo" id="titulo"
                                placeholder="Título para mostrar en la agenda">
                            @error('titulo')
                                {{-- @php $message is automatically available in @error directives --}}
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3 row d-flex align-items-center">
                        <label for="nombre_completo" class="col-sm-2 col-form-label">Descripción</label>
                        <div class="col-sm-10">
                            <input type="text" wire:model="descripcion" class="form-control" name="descripcion"
                                id="descripcion" placeholder="Descripción completa sobre la cita">
                            @error('descripcion')
                                {{-- @php $message is automatically available in @error directives --}}
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @endif


                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_inicio" class="col-sm-2 col-form-label">Fecha de inicio</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" wire:model="fecha_inicio" class="form-control" name="fecha_inicio"
                            id="fecha_inicio"
                            placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                        @error('fecha_inicio')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="mb-3 row d-flex align-items-center">
                    <label for="fecha_fin" class="col-sm-2 col-form-label">Fecha de finalización</label>
                    <div class="col-sm-10">
                        <input type="datetime-local" wire:model="fecha_fin" class="form-control" name="fecha_fin"
                            id="fecha_fin" placeholder="Nombre del cliente con apellidos (ej; Pepe Pérez González...)">
                        @error('fecha_fin')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3 row d-flex align-items-center">
                    <label for="inmobiliaria" class="col-sm-3 col-form-label">¿Esta entrada en la agenda pertenece a
                        ambas
                        inmobiliarias?</label>
                    <div class="col">
                        <input type="checkbox" wire:model="inmobiliaria" name="inmobiliaria" id="inmobiliaria">
                        @error('inmobiliaria')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-3 row d-flex align-items-center">
            <div class="col-sm-4">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            <div class="col-sm-4 text-end">
                <button type="button" class="btn btn-danger" wire:click="destroy">
                    <i class="fas fa-trash me-2"></i>Eliminar evento
                </button>
            </div>
        </div>

        @if(isset($eventos) && $eventos && $eventos->hojasFirma && $eventos->hojasFirma->count() > 0)
        <div class="signature-card">
            <h5 class="mb-3" style="color: var(--corporate-green-dark);">
                <i class="fas fa-file-signature me-2"></i>Hoja de Firma Generada
            </h5>
            <div>
                @foreach($eventos->hojasFirma as $hojaFirma)
                    <div class="mb-2">
                        @if($hojaFirma->ruta_pdf)
                        <a href="{{ asset($hojaFirma->ruta_pdf) }}" target="_blank" class="btn btn-info me-2">
                            <i class="fas fa-file-pdf me-2"></i>Ver PDF Guardado
                        </a>
                        @endif
                        <a href="{{ route('agenda.hoja-firma.pdf', $hojaFirma->id) }}" target="_blank" class="btn btn-success">
                            <i class="fas fa-download me-2"></i>Descargar PDF
                        </a>
                        <small class="text-muted ms-2 d-block mt-2">
                            Generado el {{ $hojaFirma->fecha_firma ? $hojaFirma->fecha_firma->format('d/m/Y H:i') : 'N/A' }}
                        </small>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </form>
</div>

<!-- Modal de Hoja de Firma -->
<div class="modal fade" id="hojaFirmaModal" tabindex="-1" role="dialog" aria-labelledby="hojaFirmaModalLabel" wire:key="modal-firma-{{ $identificador }}" wire:ignore.self>
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="hojaFirmaModalLabel">Hoja de Firma</h5>
                <button type="button" class="btn-close" onclick="cerrarModal()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Firma Cliente -->
                <div class="mb-4">
                    <h6 class="mb-3">Firma del Cliente</h6>
                    <div class="mb-3">
                        <label class="form-label">Nombre del Cliente</label>
                        <input type="text" id="nombreClienteInput" value="{{ $nombreCliente ?? ($eventos->cliente->nombre_completo ?? '') }}" class="form-control" placeholder="Nombre completo del cliente" readonly style="background-color: #f0f0f0;">
                        <small class="text-muted">Obtenido automáticamente de la cita</small>
                        @error('nombreCliente')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="border rounded p-2 mb-2" style="background: #fff; position: relative; touch-action: none; -ms-touch-action: none; overflow: hidden;">
                        <canvas id="firmaClienteCanvas" style="border: 1px solid #ddd; cursor: crosshair; width: 100%; height: 250px; touch-action: none !important; -ms-touch-action: none !important; -webkit-touch-callout: none; -webkit-user-select: none; user-select: none; background: white; display: block; position: relative; z-index: 10; -webkit-tap-highlight-color: transparent; pointer-events: auto; max-width: 100%;"></canvas>
                        <div style="position: absolute; top: 10px; left: 10px; font-size: 12px; color: #999; pointer-events: none; z-index: 2;">Desliza el dedo o usa el ratón para firmar</div>
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-secondary" onclick="limpiarCanvas()">
                            <i class="fas fa-eraser"></i> Limpiar
                        </button>
                        <button type="button" class="btn btn-sm btn-primary" onclick="guardarFirmaCanvas()">
                            <i class="fas fa-save"></i> Guardar Firma
                        </button>
                    </div>
                    <div id="firmaGuardadaAlert" class="alert alert-success alert-dismissible fade show" role="alert" style="display: none;">
                        <i class="fas fa-check-circle"></i> Firma guardada correctamente
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Observaciones</label>
                    <textarea id="observacionesTextarea" class="form-control" rows="3" placeholder="Observaciones adicionales...">{{ $observaciones ?? '' }}</textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarModal()">Cerrar</button>
                <button type="button" class="btn btn-success" onclick="guardarFirmaYGenerarPDF()" id="btnGenerarPDF">
                    <i class="fas fa-file-pdf me-2"></i>Generar y Guardar PDF
                </button>
            </div>
        </div>
    </div>
</div>

<script>
(function() {
    let modalInstance = null;

    function initModal() {
        const modalElement = document.getElementById('hojaFirmaModal');
        if (modalElement) {
            if (!modalInstance) {
                console.log('Inicializando instancia del modal...');
                modalInstance = new bootstrap.Modal(modalElement, {
                    backdrop: true,
                    keyboard: true
                });
            }
            return modalInstance;
        } else {
            console.warn('Elemento del modal no encontrado en el DOM');
            return null;
        }
    }

    function abrirModal() {
        console.log('Intentando abrir modal...');
        const instance = initModal();
        if (instance) {
            console.log('Instancia del modal encontrada, mostrando...');
            instance.show();

            // Inicializar canvas después de que el modal se muestre
            const modalElement = document.getElementById('hojaFirmaModal');
            if (modalElement) {
                const handleShown = function() {
                    console.log('Modal completamente mostrado, inicializando canvas...');
                    // Disparar evento personalizado para inicializar canvas
                    window.dispatchEvent(new CustomEvent('modal-firma-shown'));
                    // También inicializar directamente después de un pequeño delay
                    setTimeout(function() {
                        console.log('Inicializando canvas directamente desde abrirModal...');
                        const canvasEl = document.getElementById('firmaClienteCanvas');
                        console.log('Canvas element encontrado:', canvasEl);
                        if (canvasEl) {
                            // Llamar a initCanvas directamente desde el scope global
                            if (typeof window.forceInitCanvasSignature === 'function') {
                                console.log('Llamando a forceInitCanvasSignature...');
                                window.forceInitCanvasSignature();
                            } else {
                                console.log('forceInitCanvasSignature no está disponible aún, esperando...');
                                setTimeout(function() {
                                    if (typeof window.forceInitCanvasSignature === 'function') {
                                        window.forceInitCanvasSignature();
                                    } else {
                                        console.error('forceInitCanvasSignature nunca se hizo disponible');
                                    }
                                }, 500);
                            }
                        } else {
                            console.log('Canvas no encontrado aún, reintentando...');
                            setTimeout(handleShown, 200);
                        }
                    }, 500);
                    modalElement.removeEventListener('shown.bs.modal', handleShown);
                };
                modalElement.addEventListener('shown.bs.modal', handleShown);
            }
        } else {
            console.error('No se pudo inicializar el modal');
        }
    }

    window.cerrarModal = function() {
        const modalElement = document.getElementById('hojaFirmaModal');

        // Cerrar usando Bootstrap si existe la instancia
        if (modalInstance) {
            modalInstance.hide();
        }

        // También cerrar manualmente para asegurar
        if (modalElement) {
            // Remover clases de Bootstrap
            modalElement.classList.remove('show');
            modalElement.setAttribute('aria-hidden', 'true');
            modalElement.style.display = 'none';

            // Eliminar backdrop si existe
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });

            // Restaurar el scroll del body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }

        // Llamar al método Livewire para cerrar
        try {
            @this.call('cerrarModalFirma');
        } catch(e) {
            console.log('Error cerrando modal:', e);
        }
    };

    // Escuchar eventos de cierre del modal de Bootstrap
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('hojaFirmaModal');
        if (modalElement) {
            // Cerrar cuando se hace clic fuera del modal
            modalElement.addEventListener('hidden.bs.modal', function() {
                // Limpiar backdrop si queda alguno
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(function(backdrop) {
                    backdrop.remove();
                });
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            });

            // Cerrar cuando se presiona Escape
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modalElement.classList.contains('show')) {
                    window.cerrarModal();
                }
            });
        }
    });

    // Escuchar evento de Livewire para limpiar backdrop cuando se cierra el modal
    window.addEventListener('modal-firma-cerrado', function() {
        setTimeout(function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(function(backdrop) {
                backdrop.remove();
            });
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            const modalElement = document.getElementById('hojaFirmaModal');
            if (modalElement) {
                modalElement.classList.remove('show');
                modalElement.setAttribute('aria-hidden', 'true');
                modalElement.style.display = 'none';
            }
        }, 100);
    });

    // También escuchar evento de limpieza directa
    window.addEventListener('limpiar-backdrop-modal', function() {
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(function(backdrop) {
            backdrop.remove();
        });
        document.body.classList.remove('modal-open');
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Escuchar eventos de Livewire para abrir el modal
    document.addEventListener('livewire:load', function() {
        initModal();
    });

    // Escuchar cuando Livewire emite que debe abrir el modal (evento personalizado)
    window.addEventListener('modal-firma-abierto', function() {
        console.log('Evento modal-firma-abierto recibido');
        setTimeout(function() {
            abrirModal();
        }, 300);
    });

    // También escuchar eventos de Livewire directamente usando Livewire.on
    if (typeof Livewire !== 'undefined') {
        document.addEventListener('livewire:initialized', function() {
            console.log('Livewire inicializado, registrando listener');
            Livewire.on('modal-firma-abierto', function() {
                console.log('Livewire event modal-firma-abierto recibido vía Livewire.on');
                setTimeout(function() {
                    abrirModal();
                }, 300);
            });
        });
    }

    // Escuchar actualizaciones de Livewire y verificar si debe abrirse
    document.addEventListener('livewire:update', function() {
        setTimeout(function() {
            // Leer el estado desde el DOM después de que Livewire actualice
            const indicator = document.getElementById('modal-state-indicator');
            if (indicator && indicator.dataset.showModal === 'true') {
                const modalEl = document.getElementById('hojaFirmaModal');
                if (modalEl && !modalEl.classList.contains('show')) {
                    console.log('Abriendo modal desde livewire:update');
                    abrirModal();
                }
            }
        }, 300);
    });

    // Inicializar cuando se carga la página
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initModal();
        });
    } else {
        setTimeout(function() {
            initModal();
        }, 500);
    }
})();
</script>

<script>
(function() {
    let canvas = null;
    let ctx = null;
    let isDrawing = false;
    let lastX = 0;
    let lastY = 0;
    let canvasInitialized = false;

    function initCanvas() {
        const canvasEl = document.getElementById('firmaClienteCanvas');

        if (!canvasEl) {
            console.log('Canvas no encontrado en initCanvas');
            return;
        }

        console.log('Inicializando canvas...', canvasEl);

        // Si ya está inicializado y es el mismo canvas, no reinicializar
        if (canvasInitialized && canvas === canvasEl) {
            console.log('Canvas ya inicializado');
            return;
        }

        // Si hay un canvas anterior diferente, remover listeners
        if (canvas && canvas !== canvasEl) {
            console.log('Canvas cambió, reinicializando...');
            canvasInitialized = false;
        }

        canvas = canvasEl;
        ctx = canvas.getContext('2d');

        if (!ctx) {
            console.error('No se pudo obtener el contexto 2D del canvas');
            return;
        }

        canvasInitialized = true;
        console.log('Canvas inicializado correctamente');

        // Ajustar tamaño del canvas - usar un tamaño fijo para mejor compatibilidad
        let container = canvas.parentElement;
        const containerWidth = container ? (container.clientWidth - 20) : 600;

        // Obtener el ancho real del contenedor
        const computedStyle = window.getComputedStyle(container);
        const padding = parseFloat(computedStyle.paddingLeft) + parseFloat(computedStyle.paddingRight);
        const actualWidth = container.clientWidth - padding - 4; // 4px para el borde

        // Guardar estos valores para usarlos después de clonar
        window._canvasWidth = actualWidth > 0 ? actualWidth : 600;
        window._canvasHeight = 250;

        // Configurar tamaño inicial del canvas
        canvas.width = window._canvasWidth;
        canvas.height = window._canvasHeight;
        canvas.style.width = '100%';
        canvas.style.height = '250px';

        // Guardar el contexto después de ajustar el tamaño
        ctx = canvas.getContext('2d');

        // Configurar estilo de dibujo
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        // Cargar firma existente si hay
        @isset($firmaCliente)
        @if($firmaCliente && ($showFirmaModal ?? false))
        try {
            const img = new Image();
            img.onload = function() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);
                ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            };
            img.onerror = function() {
                console.log('Error cargando imagen de firma');
            };
            img.src = '{{ $firmaCliente }}';
        } catch(e) {
            console.log('Error al cargar firma:', e);
        }
        @endif
        @endisset

        function getEventPos(e) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            let x, y;

            // Manejar eventos táctiles
            if (e.touches && e.touches.length > 0) {
                const touch = e.touches[0];
                x = (touch.clientX - rect.left) * scaleX;
                y = (touch.clientY - rect.top) * scaleY;
            }
            // Manejar eventos changedTouches (para touchend)
            else if (e.changedTouches && e.changedTouches.length > 0) {
                const touch = e.changedTouches[0];
                x = (touch.clientX - rect.left) * scaleX;
                y = (touch.clientY - rect.top) * scaleY;
            }
            // Manejar eventos de mouse
            else {
                x = (e.clientX - rect.left) * scaleX;
                y = (e.clientY - rect.top) * scaleY;
            }

            // Asegurar que esté dentro del canvas
            x = Math.max(0, Math.min(x, canvas.width));
            y = Math.max(0, Math.min(y, canvas.height));

            return { x, y };
        }

        function startDrawing(e) {
            e.preventDefault();
            e.stopPropagation();
            isDrawing = true;
            const pos = getEventPos(e);
            lastX = pos.x;
            lastY = pos.y;

            // Marcar el punto inicial
            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(lastX, lastY);
            ctx.stroke();
        }

        function draw(e) {
            if (!isDrawing) {
                console.log('draw llamado pero isDrawing es false');
                return;
            }
            e.preventDefault();
            e.stopPropagation();

            const pos = getEventPos(e);

            ctx.beginPath();
            ctx.moveTo(lastX, lastY);
            ctx.lineTo(pos.x, pos.y);
            ctx.stroke();

            lastX = pos.x;
            lastY = pos.y;
        }

        function stopDrawing(e) {
            if (isDrawing) {
                isDrawing = false;
            }
            if (e) {
                e.preventDefault();
            }
        }

        // Eventos táctiles (para móviles) - mejorado para mejor compatibilidad
        const touchStartHandler = function(e) {
            console.log('touchstart detectado en canvas', e.touches.length, 'touches');
            e.preventDefault();
            e.stopImmediatePropagation();
            e.stopPropagation();
            // Solo procesar el primer toque
            if (e.touches.length > 0) {
                console.log('Iniciando dibujo táctil');
                startDrawing(e);
            }
        };

        const touchMoveHandler = function(e) {
            if (isDrawing) {
                e.preventDefault();
                e.stopImmediatePropagation();
                e.stopPropagation();
                // Solo procesar el primer toque
                if (e.touches.length > 0) {
                    draw(e);
                }
            } else {
                e.preventDefault();
            }
        };

        const touchEndHandler = function(e) {
            console.log('touchend detectado');
            e.preventDefault();
            e.stopPropagation();
            stopDrawing(e);
        };

        const touchCancelHandler = function(e) {
            console.log('touchcancel detectado');
            e.preventDefault();
            e.stopPropagation();
            stopDrawing(e);
        };

        // Remover listeners anteriores si existen (para evitar duplicados)
        // Clonar el canvas para remover todos los listeners
        const canvasWidth = window._canvasWidth || 600;
        const canvasHeight = window._canvasHeight || 250;

        const newCanvas = canvas.cloneNode(false);
        const parent = canvas.parentNode;
        parent.replaceChild(newCanvas, canvas);
        canvas = newCanvas;
        ctx = canvas.getContext('2d');

        // Reconfigurar el canvas después de clonarlo
        canvas.width = canvasWidth;
        canvas.height = canvasHeight;
        canvas.style.width = '100%';
        canvas.style.height = '250px';
        ctx.strokeStyle = '#000000';
        ctx.lineWidth = 3;
        ctx.lineCap = 'round';
        ctx.lineJoin = 'round';

        console.log('Canvas clonado y reconfigurado. Dimensiones:', canvas.width, 'x', canvas.height);

        // Registrar eventos de mouse
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', stopDrawing);
        canvas.addEventListener('mouseleave', stopDrawing);

        // Registrar eventos táctiles
        canvas.addEventListener('touchstart', touchStartHandler, { passive: false });
        canvas.addEventListener('touchmove', touchMoveHandler, { passive: false });
        canvas.addEventListener('touchend', touchEndHandler, { passive: false });
        canvas.addEventListener('touchcancel', touchCancelHandler, { passive: false });

        console.log('Event listeners registrados después de clonar canvas');

        // También prevenir eventos táctiles en el contenedor padre
        // Reutilizar la variable container ya declarada arriba
        container = canvas.parentElement;
        if (container) {
            container.addEventListener('touchstart', function(e) {
                if (e.target === canvas || canvas.contains(e.target)) {
                    // Permitir que el evento llegue al canvas
                    return;
                }
                e.preventDefault();
            }, { passive: false });
            container.addEventListener('touchmove', function(e) {
                if (e.target === canvas || canvas.contains(e.target)) {
                    return;
                }
                e.preventDefault();
            }, { passive: false });
        }

        // Verificar que el canvas puede recibir eventos
        console.log('Canvas element:', canvas);
        console.log('Canvas parent:', canvas.parentElement);
        console.log('Canvas computed style touch-action:', window.getComputedStyle(canvas).touchAction);
        console.log('Canvas dimensions:', canvas.width, 'x', canvas.height);
        console.log('Canvas style dimensions:', canvas.style.width, 'x', canvas.style.height);
        console.log('Event listeners registrados en canvas');
        console.log('Canvas está listo para dibujar. Prueba tocando la pantalla.');

        // Test: verificar que el canvas está visible y accesible
        const rect = canvas.getBoundingClientRect();
        console.log('Canvas bounding rect:', rect);
        console.log('Canvas visible:', rect.width > 0 && rect.height > 0);

        // Añadir un listener de prueba en modo capture para verificar que los eventos llegan
        canvas.addEventListener('touchstart', function(e) {
            console.log('TEST CAPTURE: touchstart capturado en fase capture');
        }, { passive: false, capture: true });
    }

    // Observar cuando se abre el modal
    const observer = new MutationObserver(function(mutations) {
        const modal = document.getElementById('hojaFirmaModal');
        if (modal && modal.classList.contains('show')) {
            console.log('Modal abierto detectado, inicializando canvas...');
            setTimeout(function() {
                // Resetear el flag solo si el canvas cambió
                const canvasEl = document.getElementById('firmaClienteCanvas');
                if (canvasEl) {
                    if (canvasEl !== canvas) {
                        console.log('Canvas cambió, reinicializando...');
                        canvasInitialized = false;
                    }
                    if (!canvasInitialized) {
                        initCanvas();
                    } else {
                        console.log('Canvas ya estaba inicializado');
                    }
                } else {
                    console.log('Canvas no encontrado en el DOM');
                }
            }, 300);
        }
    });

    // Función para forzar inicialización del canvas
    function forceInitCanvas() {
        console.log('Forzando inicialización del canvas...');
        canvasInitialized = false;
        const canvasEl = document.getElementById('firmaClienteCanvas');
        if (canvasEl) {
            console.log('Canvas encontrado, llamando a initCanvas...');
            initCanvas();
        } else {
            console.log('Canvas no encontrado, reintentando en 200ms...');
            setTimeout(forceInitCanvas, 200);
        }
    }

    // Exponer funciones globalmente para acceso desde otros scripts
    window.initCanvasSignature = initCanvas;
    window.forceInitCanvasSignature = forceInitCanvas;

    // Observar cambios en el DOM
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('hojaFirmaModal');
        if (modal) {
            observer.observe(modal, { attributes: true, attributeFilter: ['class'] });

            // Listener directo para cuando el modal se muestra (Bootstrap)
            modal.addEventListener('shown.bs.modal', function() {
                console.log('Modal shown.bs.modal event disparado');
                setTimeout(forceInitCanvas, 100);
            });
        }

        // Escuchar evento personalizado cuando el modal se muestra
        window.addEventListener('modal-firma-shown', function() {
            console.log('Evento modal-firma-shown recibido');
            setTimeout(forceInitCanvas, 100);
        });

        // Inicializar si el canvas ya está disponible
        setTimeout(function() {
            const canvasEl = document.getElementById('firmaClienteCanvas');
            if (canvasEl) {
                console.log('Canvas encontrado en DOMContentLoaded, inicializando...');
                initCanvas();
            } else {
                console.log('Canvas no encontrado en DOMContentLoaded');
            }
        }, 500);
    });

    // Reinicializar cuando Livewire actualiza
    document.addEventListener('livewire:update', function() {
        console.log('Livewire update detectado');
        setTimeout(function() {
            const modal = document.getElementById('hojaFirmaModal');
            const canvasEl = document.getElementById('firmaClienteCanvas');
            if (modal && modal.classList.contains('show') && canvasEl) {
                console.log('Modal visible y canvas encontrado en livewire:update');
                // Resetear el flag solo si el canvas cambió
                if (canvasEl !== canvas) {
                    console.log('Canvas cambió en livewire:update, reinicializando...');
                    canvasInitialized = false;
                }
                if (!canvasInitialized) {
                    initCanvas();
                } else {
                    console.log('Canvas ya inicializado en livewire:update');
                }
            }
        }, 400);
    });
})();

window.limpiarCanvas = function() {
    const canvas = document.getElementById('firmaClienteCanvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }

    // Ocultar alerta de firma guardada
    const alerta = document.getElementById('firmaGuardadaAlert');
    if (alerta) {
        alerta.style.display = 'none';
    }

    // Deshabilitar botón de generar PDF
    const btnPDF = document.getElementById('btnGenerarPDF');
    if (btnPDF) {
        btnPDF.disabled = true;
    }

    // Llamar a Livewire para limpiar
    try {
        @this.call('limpiarFirma');
    } catch(e) {
        console.log('Error limpiando firma:', e);
    }
};

// La función cerrarModal ya está definida arriba con limpieza completa del backdrop

window.guardarFirmaCanvas = function() {
    const canvas = document.getElementById('firmaClienteCanvas');
    if (!canvas) {
        alert('El canvas no está disponible');
        return;
    }

    // Verificar que hay algo dibujado
    const ctx = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    let hasContent = false;

    for (let i = 0; i < data.length; i += 4) {
        if (data[i] !== 255 || data[i+1] !== 255 || data[i+2] !== 255) {
            hasContent = true;
            break;
        }
    }

    if (!hasContent) {
        alert('Por favor, dibuja una firma antes de guardar');
        return;
    }

    try {
        const firmaData = canvas.toDataURL('image/png');
        const observaciones = document.getElementById('observacionesTextarea')?.value || '';

        // Guardar usando Livewire pasando los datos como parámetros
        @this.call('guardarFirma', firmaData, observaciones).then(function() {
            // Mostrar alerta de éxito
            const alerta = document.getElementById('firmaGuardadaAlert');
            if (alerta) {
                alerta.style.display = 'block';
            }

            // Habilitar botón de generar PDF
            const btnPDF = document.getElementById('btnGenerarPDF');
            if (btnPDF) {
                btnPDF.disabled = false;
            }
        }).catch(function(error) {
            console.error('Error guardando firma:', error);
            alert('Error al guardar la firma. Por favor, inténtalo de nuevo.');
        });
    } catch(e) {
        console.error('Error guardando firma:', e);
        alert('Error al guardar la firma. Por favor, inténtalo de nuevo.');
    }
};

window.guardarFirmaYGenerarPDF = function() {
    const canvas = document.getElementById('firmaClienteCanvas');
    if (!canvas) {
        alert('El canvas no está disponible');
        return;
    }

    // Verificar que hay algo dibujado
    const ctx = canvas.getContext('2d');
    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
    const data = imageData.data;
    let hasContent = false;

    for (let i = 0; i < data.length; i += 4) {
        if (data[i] !== 255 || data[i+1] !== 255 || data[i+2] !== 255) {
            hasContent = true;
            break;
        }
    }

    if (!hasContent) {
        alert('Por favor, dibuja una firma antes de generar el PDF');
        return;
    }

    try {
        const firmaData = canvas.toDataURL('image/png');
        const observaciones = document.getElementById('observacionesTextarea')?.value || '';

        // Generar PDF pasando los datos como parámetros
        @this.call('guardarFirmasYGenerarPDF', firmaData, observaciones).then(function() {
            // Cerrar modal después de generar
            setTimeout(function() {
                window.cerrarModal();
            }, 1000);
        }).catch(function(error) {
            console.error('Error generando PDF:', error);
            alert('Error al generar el PDF. Por favor, inténtalo de nuevo.');
        });
    } catch(e) {
        console.error('Error generando PDF:', e);
        alert('Error al generar el PDF. Por favor, inténtalo de nuevo.');
    }
};
</script>

<script>
    // Reinicializar Select2 cuando Livewire actualiza
    document.addEventListener('livewire:load', function() {
        initializeSelect2();
    });

    document.addEventListener('livewire:update', function() {
        setTimeout(function() {
            initializeSelect2();
        }, 100);
    });

    function initializeSelect2() {
        // Reinicializar cliente select
        if ($('#select2-cliente-edit').length && !$('#select2-cliente-edit').hasClass('select2-hidden-accessible')) {
            $('#select2-cliente-edit').select2({
                placeholder: '-- Elige un cliente --',
                allowClear: true
            });

            @if($cliente_id)
                $('#select2-cliente-edit').val('{{ $cliente_id }}').trigger('change');
            @endif
        }

        // Reinicializar inmueble select
        if ($('#select2-inmueble-edit').length && !$('#select2-inmueble-edit').hasClass('select2-hidden-accessible')) {
            $('#select2-inmueble-edit').select2({
                placeholder: '-- Elige un inmueble --',
                allowClear: true
            });

            @if($inmueble_id)
                $('#select2-inmueble-edit').val('{{ $inmueble_id }}').trigger('change');
            @endif
        }
    }
</script>
