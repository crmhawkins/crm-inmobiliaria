<?php

namespace App\Http\Controllers;

use App\Models\DocInmueble;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentosController extends Controller
{
    public function download(DocInmueble $documento)
    {
        if (!Storage::exists($documento->ruta)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return Storage::download($documento->ruta, $documento->nombre);
    }

    public function store(Request $request)
    {
        $request->validate([
            'documento' => 'required|file|max:10240', // 10MB max
            'inmueble_id' => 'required|exists:inmuebles,id'
        ]);

        $file = $request->file('documento');
        $path = $file->store('documentos/inmuebles');

        $documento = DocInmueble::create([
            'nombre' => $file->getClientOriginalName(),
            'ruta' => $path,
            'inmueble_id' => $request->inmueble_id,
            'tipo' => $file->getClientMimeType(),
            'descripcion' => $request->descripcion ?? null
        ]);

        return response()->json([
            'success' => true,
            'documento' => $documento
        ]);
    }

    public function destroy(DocInmueble $documento)
    {
        if (Storage::exists($documento->ruta)) {
            Storage::delete($documento->ruta);
        }

        $documento->delete();

        return back()->with('success', 'Documento eliminado correctamente.');
    }
}
