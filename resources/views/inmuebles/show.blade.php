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
                        <img src="{{ $imagenes[0] }}" id="imagenPrincipal" class="img-fluid rounded w-100"
                            style="max-height: 450px; object-fit: cover; cursor: pointer;"
                            onclick="mostrarImagen('{{ $imagenes[0] }}')">
                    @endif
                </div>
                @if(count($imagenes) > 1)
                    @php
                        $imagenesRestantes = array_slice($imagenes, 1);
                        $imagenesVisibles = array_slice($imagenesRestantes, 0, 3);
                        $imagenesOcultas = array_slice($imagenesRestantes, 3);
                    @endphp
                    <div class="row g-2 mb-4">
                        @foreach ($imagenesVisibles as $img)
                            <div class="col-6 col-sm-4 col-md-3">
                                <img src="{{ $img }}" class="img-fluid rounded w-100"
                                    style="height: 120px; object-fit: cover; cursor: pointer;"
                                    onclick="mostrarImagen('{{ $img }}')">
                            </div>
                        @endforeach

                        @if(count($imagenesOcultas) > 0)
                            <div class="col-6 col-sm-4 col-md-3">
                                <div class="position-relative rounded overflow-hidden"
                                     style="height: 120px; cursor: pointer;"
                                     onclick="abrirModalCarrusel()">
                                    <div class="position-relative h-100" style="display: flex; flex-direction: column;">
                                        @foreach(array_slice($imagenesOcultas, 0, 3) as $index => $img)
                                            <div class="position-absolute w-100 h-100" style="top: {{ $index * 5 }}px; left: {{ $index * 5 }}px; z-index: {{ 10 - $index }};">
                                                <img src="{{ $img }}" class="w-100 h-100"
                                                     style="object-fit: cover; filter: blur(2px); opacity: {{ 1 - ($index * 0.2) }};">
                                            </div>
                                        @endforeach
                                        <div class="position-absolute top-50 start-50 translate-middle" style="z-index: 20;">
                                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                                 style="width: 50px; height: 50px; font-size: 24px; font-weight: bold;">
                                                +{{ count($imagenesOcultas) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

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

    <!-- Modal para ver imagen en grande -->
    <div class="modal fade" id="imagenModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-transparent border-0">
                <div class="modal-header border-0">
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0 text-center">
                    <img id="imagenModalImg" src="" class="img-fluid rounded" style="max-height: 80vh;">
                </div>
            </div>
        </div>
    </div>

    <!-- Modal carrusel con todas las imágenes -->
    <div class="modal fade" id="carruselModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content bg-dark">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title text-white">Galería completa ({{ count($imagenes) }} imágenes)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div id="carouselImagenes" class="carousel slide" data-bs-ride="carousel">
                        <div class="carousel-inner">
                            @foreach($imagenes as $index => $img)
                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                    <img src="{{ $img }}" class="d-block w-100" style="max-height: 70vh; object-fit: contain;">
                                </div>
                            @endforeach
                        </div>
                        <button class="carousel-control-prev" type="button" data-bs-target="#carouselImagenes" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#carouselImagenes" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Siguiente</span>
                        </button>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <span class="text-white" id="contadorImagen">1 / {{ count($imagenes) }}</span>
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

        function mostrarImagen(url) {
            document.getElementById('imagenPrincipal').src = url;
            document.getElementById('imagenModalImg').src = url;
            const modal = new bootstrap.Modal(document.getElementById('imagenModal'));
            modal.show();
        }

        function abrirModalCarrusel() {
            const modal = new bootstrap.Modal(document.getElementById('carruselModal'));
            modal.show();
        }

        // Hacer clic en imagen principal también abre el modal
        document.addEventListener('DOMContentLoaded', function() {
            const imgPrincipal = document.getElementById('imagenPrincipal');
            if (imgPrincipal) {
                imgPrincipal.addEventListener('click', function() {
                    mostrarImagen(this.src);
                });
            }

            // Actualizar contador del carrusel
            const carousel = document.getElementById('carouselImagenes');
            if (carousel) {
                carousel.addEventListener('slid.bs.carousel', function (e) {
                    const activeIndex = e.to;
                    const total = {{ count($imagenes) }};
                    document.getElementById('contadorImagen').textContent = (activeIndex + 1) + ' / ' + total;
                });
            }
        });
    </script>

@endsection
