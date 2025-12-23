@extends('layouts.app')

@section('encabezado', 'Documentos del inmueble')
@section('subtitulo', $inmueble->titulo)

@section('content')
    <div class="container mx-auto px-4 py-8">
        <!-- Admin Actions Bar -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <h1 class="text-2xl font-bold text-gray-800">
                    Documentos - {{ $inmueble->titulo }}
                </h1>
                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('inmuebles.show', $inmueble) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                        <i class="fas fa-arrow-left mr-2"></i> Volver
                    </a>
                    <button type="button"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition"
                        onclick="document.getElementById('upload-doc').click()">
                        <i class="fas fa-upload mr-2"></i> Subir Documento
                    </button>
                </div>
            </div>
        </div>

        <!-- Document List -->
        <div class="bg-white rounded-3xl shadow-xl p-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($documentos ?? [] as $documento)
                    <div class="bg-gray-50 rounded-xl p-6 flex flex-col">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-file-alt text-blue-600"></i>
                                </div>
                                <div>
                                    <h3 class="font-semibold text-gray-800">{{ $documento->nombre }}</h3>
                                    <span class="text-sm text-gray-500">{{ $documento->created_at->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="flex items-center space-x-2">
                                <a href="{{ route('documentos.download', $documento) }}"
                                    class="text-blue-600 hover:text-blue-800">
                                    <i class="fas fa-download"></i>
                                </a>
                                <form action="{{ route('documentos.destroy', $documento) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este documento?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600">{{ $documento->descripcion }}</p>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900">No hay documentos</h3>
                        <p class="mt-2 text-sm text-gray-500">
                            Comienza subiendo algunos documentos para este inmueble
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Hidden File Input -->
        <input type="file"
            id="upload-doc"
            class="hidden"
            accept=".pdf,.doc,.docx,.xls,.xlsx"
            onchange="handleFileUpload(this)">
    </div>

    <script>
        function handleFileUpload(input) {
            if (input.files && input.files[0]) {
                const formData = new FormData();
                formData.append('documento', input.files[0]);
                formData.append('inmueble_id', '{{ $inmueble->id }}');
                formData.append('_token', '{{ csrf_token() }}');

                fetch('{{ route('documentos.store') }}', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Error al subir el documento');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al subir el documento');
                });
            }
        }
    </script>
@endsection
