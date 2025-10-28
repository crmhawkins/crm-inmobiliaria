@extends('layouts.app')

@section('head')
    @vite(['resources/sass/app.scss'])
    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.9/dist/signature_pad.umd.min.js"></script>
    <style>
        .signature-container {
            position: relative;
            background: #ffffff;
            border: 2px dashed #2ecc71;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }

        .signature-canvas {
            cursor: crosshair;
            width: 100%;
            max-width: 800px;
            height: 300px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background: white;
        }

        .info-card {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .info-card h5 {
            margin: 0;
            color: white;
        }

        .btn-action {
            padding: 12px 24px;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
@endsection

@section('content')
@section('encabezado', 'Nueva Hoja de Visita')
@section('subtitulo', 'Complete y firme el formulario')

@if(!$evento)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle me-2"></i>
        No se ha proporcionado un evento. Por favor, acceda desde un evento en la Agenda.
    </div>
@elseif(!$cliente || !$inmueble)
    <div class="alert alert-danger">
        <i class="fas fa-times-circle me-2"></i>
        Este evento no tiene cliente o inmueble asignado. Por favor, edite el evento primero.
    </div>
@else
    <div class="container-fluid px-4 py-4">
        <div class="row">
            <!-- Información del Cliente e Inmueble -->
            <div class="col-md-4 mb-4">
                <div class="info-card">
                    <h5 class="mb-3">
                        <i class="fas fa-user-circle me-2"></i>CLIENTE
                    </h5>
                    <p class="mb-2"><strong>{{ $cliente->nombre_completo }}</strong></p>
                    <p class="mb-1"><i class="fas fa-id-card me-2"></i>{{ $cliente->dni }}</p>
                    <p class="mb-1"><i class="fas fa-envelope me-2"></i>{{ $cliente->email }}</p>
                    @if($cliente->telefono)
                        <p class="mb-0"><i class="fas fa-phone me-2"></i>{{ $cliente->telefono }}</p>
                    @endif
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">
                            <i class="fas fa-home text-success me-2"></i>INMUEBLE
                        </h5>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">{{ $inmueble->titulo }}</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <small class="text-muted d-block">Ubicación</small>
                                <strong>{{ $inmueble->ubicacion }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Habitaciones</small>
                                <strong>{{ $inmueble->habitaciones }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Baños</small>
                                <strong>{{ $inmueble->banos }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">m²</small>
                                <strong>{{ $inmueble->m2 }}</strong>
                            </div>
                            @if($inmueble->valor_referencia)
                                <div class="col-12">
                                    <small class="text-muted d-block">Precio</small>
                                    <strong class="text-success">{{ number_format($inmueble->valor_referencia, 2) }} €</strong>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Formulario de Firma -->
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-signature me-2"></i>Firma del Cliente
                        </h5>
                    </div>
                    <div class="card-body">
                        <form id="hojaVisitaForm">
                            <input type="hidden" name="evento_id" value="{{ $evento->id }}">
                            <input type="hidden" name="firma_path" id="firma_path" value="">
                            
                            <div id="signatureSection">
                                <div class="signature-container">
                                    <canvas id="signatureCanvas" class="signature-canvas"></canvas>
                                </div>
                                <div class="text-center mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Dibuje su firma en el recuadro usando el ratón o el dedo
                                    </small>
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-primary btn-action" onclick="guardarFirma()">
                                        <i class="fas fa-save me-2"></i>Guardar Firma
                                    </button>
                                    <button type="button" class="btn btn-secondary btn-action" onclick="limpiarFirma()">
                                        <i class="fas fa-redo me-2"></i>Limpiar
                                    </button>
                                </div>
                            </div>

                            <div id="previewSection" style="display: none;">
                                <div class="alert alert-success">
                                    <i class="fas fa-check-circle me-2"></i>Firma guardada correctamente
                                </div>
                                <div class="text-center mb-3">
                                    <img id="firmaPreview" src="" alt="Firma" class="img-fluid rounded border" style="max-height: 200px;">
                                </div>
                                <div class="d-flex justify-content-center gap-2">
                                    <button type="button" class="btn btn-success btn-lg px-5" onclick="finalizarHojaVisita()">
                                        <i class="fas fa-check-circle me-2"></i>Finalizar y Enviar
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="reintentarFirma()">
                                        <i class="fas fa-times me-2"></i>Cambiar Firma
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<script>
    let signaturePad;
    let firmaGuardada = null;

    document.addEventListener('DOMContentLoaded', function() {
        const canvas = document.getElementById('signatureCanvas');
        
        if (canvas) {
            // Configurar canvas para alta resolución
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            
            const ctx = canvas.getContext('2d');
            ctx.scale(ratio, ratio);
            
            signaturePad = new SignaturePad(canvas, {
                backgroundColor: 'rgb(255, 255, 255)',
                penColor: 'rgb(0, 0, 0)',
                minWidth: 2,
                maxWidth: 3
            });
        }
    });

    function guardarFirma() {
        if (!signaturePad || signaturePad.isEmpty()) {
            alert('Por favor, dibuje su firma antes de guardar.');
            return;
        }

        const signatureData = signaturePad.toDataURL();
        
        // Enviar al servidor
        fetch('{{ route("hojas-visita.store-signature") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                signature: signatureData,
                evento_id: {{ $evento->id }}
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                firmaGuardada = data.firma_path;
                document.getElementById('firma_path').value = data.firma_path;
                document.getElementById('signatureSection').style.display = 'none';
                document.getElementById('previewSection').style.display = 'block';
                document.getElementById('firmaPreview').src = '{{ asset("") }}' + data.firma_path;
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al guardar la firma. Por favor, intente nuevamente.');
        });
    }

    function limpiarFirma() {
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    function reintentarFirma() {
        firmaGuardada = null;
        document.getElementById('firma_path').value = '';
        document.getElementById('signatureSection').style.display = 'block';
        document.getElementById('previewSection').style.display = 'none';
        if (signaturePad) {
            signaturePad.clear();
        }
    }

    function finalizarHojaVisita() {
        if (!firmaGuardada) {
            alert('Por favor, guarde la firma primero.');
            return;
        }

        // Confirmar
        if (!confirm('¿Está seguro de que desea finalizar y enviar la hoja de visita?')) {
            return;
        }

        // Enviar formulario
        const form = document.getElementById('hojaVisitaForm');
        form.action = '{{ route("hojas-visita.store") }}';
        form.method = 'POST';
        
        // Añadir CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        form.submit();
    }
</script>
@endsection

