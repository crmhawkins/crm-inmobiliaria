<div class="container mx-auto">
    @if ($cliente && $inmueble)
        <div class="card mb-3">
            <h5 class="card-header bg-success text-white">
                <i class="fas fa-file-signature me-2"></i> Hoja de Visita - {{ $evento->titulo }}
            </h5>
            <div class="card-body">
                <!-- Información del Cliente -->
                <div class="mb-4 p-3 bg-light rounded">
                    <h6 class="fw-bold mb-3"><i class="fas fa-user me-2"></i>Cliente</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Nombre:</strong> {{ $cliente->nombre_completo }}</p>
                            <p><strong>DNI:</strong> {{ $cliente->dni }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Email:</strong> {{ $cliente->email }}</p>
                            <p><strong>Teléfono:</strong> {{ $cliente->telefono ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Información del Inmueble -->
                <div class="mb-4 p-3 bg-light rounded">
                    <h6 class="fw-bold mb-3"><i class="fas fa-home me-2"></i>Inmueble</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Título:</strong> {{ $inmueble->titulo }}</p>
                            <p><strong>Ubicación:</strong> {{ $inmueble->ubicacion }}</p>
                            <p><strong>Habitaciones:</strong> {{ $inmueble->habitaciones }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Baños:</strong> {{ $inmueble->banos }}</p>
                            <p><strong>Metros cuadrados:</strong> {{ $inmueble->m2 }}</p>
                            <p><strong>Precio referencia:</strong> {{ number_format($inmueble->valor_referencia, 2) }} €</p>
                        </div>
                    </div>
                </div>

                <!-- Firma del Cliente -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="fas fa-signature me-2"></i>Firma del Cliente</h6>
                    
                    @if (!$firma)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3 bg-white text-center">
                                    <canvas id="signature-pad" 
                                        style="border: 2px dashed #2ecc71; cursor: crosshair; touch-action: none; width: 100%; max-width: 600px; height: 250px;"></canvas>
                                </div>
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i> Dibuja tu firma en el recuadro usando el mouse o el dedo
                                </small>
                            </div>
                            <div class="col-12 mb-3">
                                <button type="button" class="btn btn-primary me-2" id="btnFirma">
                                    <i class="fas fa-save me-2"></i>Guardar firma
                                </button>
                                <button type="button" class="btn btn-secondary" id="btnClearFirma">
                                    <i class="fas fa-undo me-2"></i>Limpiar
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="row">
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3 bg-light">
                                    <img src="{{ asset('storage/' . $firma) }}" alt="Firma del cliente" class="img-fluid" 
                                        style="max-height: 150px; background: white; padding: 10px; border: 2px solid #2ecc71;">
                                </div>
                                <small class="text-success">
                                    <i class="fas fa-check-circle me-1"></i> Firma guardada correctamente
                                </small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="button" class="btn btn-success btn-lg w-100" wire:click="submit">
                                    <i class="fas fa-file-pdf me-2"></i>Generar y Enviar Hoja de Visita
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Este evento no tiene cliente o inmueble asignado.
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.9/dist/signature_pad.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        initSignaturePad();
    });

    document.addEventListener('livewire:load', function() {
        initSignaturePad();
    });

    document.addEventListener('livewire:update', function() {
        initSignaturePad();
    });

    function initSignaturePad() {
        const canvas = document.querySelector('#signature-pad');
        if (canvas && !canvas.hasAttribute('data-initialized')) {
            canvas.setAttribute('data-initialized', 'true');
            
            // Configurar el canvas con dimensiones correctas
            canvas.width = 600;
            canvas.height = 250;
            
            const signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 2,
                maxWidth: 3
            });

            const btnFirma = document.querySelector('#btnFirma');
            if (btnFirma) {
                btnFirma.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (signaturePad.isEmpty()) {
                        alert("Por favor proporciona tu firma primero.");
                    } else {
                        var data = signaturePad.toDataURL();
                        @this.set('signature', data);
                    }
                });
            }

            const btnClearFirma = document.querySelector('#btnClearFirma');
            if (btnClearFirma) {
                btnClearFirma.addEventListener('click', function(e) {
                    e.preventDefault();
                    signaturePad.clear();
                });
            }
        }
    }
</script>
