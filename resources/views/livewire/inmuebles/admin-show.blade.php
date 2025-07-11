<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Admin Show View - Inmueble #{{ $identificador }}</h5>
                </div>
                <div class="card-body">
                    <p><strong>Título:</strong> {{ $titulo ?? 'No disponible' }}</p>
                    <p><strong>Descripción:</strong> {{ $descripcion ?? 'No disponible' }}</p>
                    <p><strong>Valor:</strong> {{ number_format($valor_referencia ?? 0, 0, ',', '.') }} €</p>
                    <p><strong>Ubicación:</strong> {{ $ubicacion ?? 'No disponible' }}</p>
                    <p><strong>Habitaciones:</strong> {{ $habitaciones ?? 'No disponible' }}</p>
                    <p><strong>Baños:</strong> {{ $banos ?? 'No disponible' }}</p>
                    <p><strong>M²:</strong> {{ $m2 ?? 'No disponible' }}</p>

                    <div class="mt-3">
                        <a href="{{ route('inmuebles.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Volver al listado
                        </a>
                        <a href="{{ route('inmuebles.edit', $identificador) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-2"></i>Editar
                        </a>
                        <button type="button" class="btn btn-danger" wire:click="destroy">
                            <i class="fas fa-trash me-2"></i>Eliminar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
