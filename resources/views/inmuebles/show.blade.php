@extends('layouts.app')

@section('encabezado', 'Detalle del inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
    @php
        $galeria = json_decode($inmueble->galeria ?? '[]');
    @endphp
    @php $imagenes = array_values((array) $galeria); @endphp

    <div class="container my-4">
        <div class="row">
            <!-- Galería principal -->
            <div class="col-md-8">
                <div class="mb-3">
                    @if (isset($imagenes[0]))
                        <img src="{{ $imagenes[0] }}" class="img-fluid rounded w-100"
                            style="max-height: 450px; object-fit: cover;">
                    @endif
                </div>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    @foreach ($imagenes as $img)
                        <img src="{{ $img }}" class="rounded" width="120" height="90"
                            style="object-fit: cover;">
                    @endforeach
                </div>

                <h2>{{ $inmueble->valor_referencia ? number_format($inmueble->valor_referencia, 0, ',', '.') . ' €' : 'Precio no especificado' }}</h2>
                <p class="text-muted h5 mb-3">
                    {{ $inmueble->tipoVivienda->nombre ?? 'Inmueble' }} en {{ $inmueble->ubicacion }}
                </p>

                <!-- Iconos destacados -->
                <div class="mb-3 d-flex flex-wrap gap-4 fs-5">
                    @if($inmueble->habitaciones)
                        <div><i class="bi bi-door-open"></i> {{ $inmueble->habitaciones }} habs.</div>
                    @endif
                    @if($inmueble->banos)
                        <div><i class="bi bi-badge-wc"></i> {{ $inmueble->banos }} baños</div>
                    @endif
                    @if($inmueble->m2)
                        <div><i class="bi bi-aspect-ratio"></i> {{ $inmueble->m2 }} m²</div>
                    @endif
                    @if ($inmueble->has_terrace)
                        <div><i class="bi bi-tree"></i> Terraza</div>
                    @endif
                    @if ($inmueble->has_balcony)
                        <div><i class="bi bi-columns-gap"></i> Balcón</div>
                    @endif
                </div>

                <!-- Descripción -->
                <p class="mt-4 fs-6">
                    {{ $inmueble->descripcion }}
                </p>

                <!-- Características -->
                @if ($caracteristicas->count())
                    <h5 class="mt-5">Características adicionales</h5>
                    <div class="d-flex flex-wrap gap-2 mt-2">
                        @foreach ($caracteristicas as $car)
                            <span class="badge bg-light text-dark border">{{ $car->nombre }}</span>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Tarjeta lateral de contacto -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Contactar</h5>
                        <p class="card-text">Puedes contactar con nosotros para más detalles sobre este inmueble.</p>
                        <a href="tel:{{ config('app.contacto_telefono') }}" class="btn btn-outline-primary w-100 mb-2">
                            <i class="bi bi-telephone"></i> Llamar
                        </a>
                        <a href="mailto:{{ config('app.contacto_email') }}" class="btn btn-primary w-100 mb-3">
                            <i class="bi bi-envelope"></i> Contactar
                        </a>
                        <div class="input-group">
                            <input type="text" value="https://sayco.herasoft.ai/inmueble/{{ $inmueble->id }}"
                                class="form-control" id="share-url" readonly>
                            <button class="btn btn-outline-primary" type="button" id="copyButton">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const copyButton = document.getElementById('copyButton');
            const shareUrl = document.getElementById('share-url');

            copyButton.addEventListener('click', async function() {
                try {
                    // Intenta usar el API moderno de portapapeles
                    await navigator.clipboard.writeText(shareUrl.value);
                    showCopiedFeedback();
                } catch (err) {
                    // Fallback para navegadores que no soportan el API moderno
                    shareUrl.select();
                    try {
                        document.execCommand('copy');
                        showCopiedFeedback();
                    } catch (err) {
                        console.error('No se pudo copiar el texto: ', err);
                    }
                }
            });

            function showCopiedFeedback() {
                const originalHtml = copyButton.innerHTML;
                copyButton.innerHTML = '<i class="bi bi-clipboard-check"></i> ¡Copiado!';
                copyButton.disabled = true;

                setTimeout(() => {
                    copyButton.innerHTML = originalHtml;
                    copyButton.disabled = false;
                }, 2000);
            }
        });
    </script>

@endsection
