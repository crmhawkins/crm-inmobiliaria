@extends('layouts.app')

@section('encabezado', 'Detalle del Cliente')
@section('subtitulo', 'Información completa')

@section('content')
    <div class="container mt-4">
        <a href="{{ route('clientes.index') }}" class="btn btn-secondary mb-3">
            <i class="fa fa-arrow-left me-1"></i> Volver al listado
        </a>

        <div class="card mb-4">
            <div class="card-header">
                Datos del cliente
            </div>
            <div class="card-body">
                <p><strong>Nombre:</strong> {{ $cliente->nombre_completo }}</p>
                <p><strong>DNI:</strong> {{ $cliente->dni }}</p>
                <p><strong>Teléfono:</strong> {{ $cliente->telefono }}</p>
                <p><strong>Email:</strong> {{ $cliente->email }}</p>
                <p><strong>Dirección:</strong> {{ $cliente->direccion ?? 'No especificada' }}</p>
                <p><strong>Inmobiliaria:</strong>
                    @if (is_null($cliente->inmobiliaria))
                        Ambas
                    @elseif($cliente->inmobiliaria)
                        Sayco
                    @else
                        Sancer
                    @endif
                </p>
            </div>
        </div>

        @if ($intereses)
            <div class="card">
                <div class="card-header">
                    Intereses del cliente
                </div>
                <div class="card-body">
                    <p><strong>Ubicación:</strong> {{ $intereses['ubicacion'] ?? '-' }}</p>
                    <p><strong>Habitaciones:</strong>
                        {{ $intereses['habitaciones_min'] ?? '-' }} - {{ $intereses['habitaciones_max'] ?? '-' }}
                    </p>
                    <p><strong>Baños:</strong>
                        {{ $intereses['banos_min'] ?? '-' }} - {{ $intereses['banos_max'] ?? '-' }}
                    </p>
                    <p><strong>Metros cuadrados:</strong>
                        {{ $intereses['m2_min'] ?? '-' }} - {{ $intereses['m2_max'] ?? '-' }}
                    </p>
                    <p><strong>Estado:</strong> {{ $intereses['estado'] ?? '-' }}</p>
                    <p><strong>Disponibilidad:</strong> {{ $intereses['disponibilidad'] ?? '-' }}</p>

                    @php
                        $caracteristicasRaw = $intereses['otras_caracteristicas'] ?? [];
                        $otras = is_array($caracteristicasRaw) ? $caracteristicasRaw : (json_decode($caracteristicasRaw, true) ?? []);
                    @endphp
                    <p><strong>Otras características:</strong>
                        @if (!empty($otras_caracteristicas_nombres))
                            <ul>
                                @foreach ($otras_caracteristicas_nombres as $nombre)
                                    <li>{{ $nombre }}</li>
                                @endforeach
                            </ul>
                        @else
                            Ninguna
                        @endif
                    </p>
                </div>
            </div>
        @endif

        <div class="mt-4">
            <a href="{{ route('clientes.index') }}" class="btn btn-secondary">Volver al listado</a>
            <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">Editar cliente</a>
        </div>
    </div>
@endsection
