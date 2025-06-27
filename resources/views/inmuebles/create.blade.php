@extends('layouts.app')

@section('content')
<div class="container">
    <form action="{{ route('inmuebles.store') }}" method="POST">
        @csrf

        <div class="card mb-3">
            <h5 class="card-header">Datos básicos</h5>
            <div class="card-body">
                <div class="mb-3">
                    <label><strong>Título</strong></label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}" class="form-control">
                    @error('titulo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>Descripción</strong></label>
                    <textarea name="descripcion" class="form-control" rows="3">{{ old('descripcion') }}</textarea>
                    @error('descripcion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>M²</strong></label>
                    <input type="text" name="m2" value="{{ old('m2') }}" class="form-control">
                    @error('m2') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>Tipo de vivienda</strong></label>
                    <select name="tipo_vivienda_id" class="form-control">
                        <option value="">-- Elige --</option>
                        @foreach($tipos_vivienda as $tipo)
                            <option value="{{ $tipo->id }}" {{ old('tipo_vivienda_id') == $tipo->id ? 'selected' : '' }}>
                                {{ $tipo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('tipo_vivienda_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>Ubicación</strong></label>
                    <input type="text" name="ubicacion" value="{{ old('ubicacion') }}" class="form-control">
                    @error('ubicacion') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>Referencia catastral</strong></label>
                    <input type="text" name="referencia_catastral" value="{{ old('referencia_catastral') }}" class="form-control">
                    @error('referencia_catastral') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label><strong>Vendedor</strong></label>
                    <select name="vendedor_id" class="form-control">
                        <option value="">-- Elige --</option>
                        @foreach($vendedores as $vendedor)
                            <option value="{{ $vendedor->id }}" {{ old('vendedor_id') == $vendedor->id ? 'selected' : '' }}>
                                {{ $vendedor->nombre_completo }}
                            </option>
                        @endforeach
                    </select>
                    @error('vendedor_id') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <!-- Añade aquí más campos como habitaciones, baños, cert_energetico, etc., replicando los old() y @error -->

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">Guardar inmueble</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
