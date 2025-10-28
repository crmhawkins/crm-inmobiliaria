<?php

namespace App\Http\Controllers;

use App\Models\Evento;
use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Models\HojaVisita;
use App\Models\TipoVivienda;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

class HojaVisitaController extends Controller
{
    /**
     * Mostrar el listado de hojas de visita
     */
    public function index()
    {
        $hojasVisita = HojaVisita::with(['cliente', 'inmueble', 'evento'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('hojas-visita.lista', compact('hojasVisita'));
    }

    /**
     * Mostrar formulario para crear hoja de visita desde un evento
     */
    public function create(Request $request)
    {
        $eventoId = $request->get('evento_id');
        $evento = null;
        $cliente = null;
        $inmueble = null;

        if ($eventoId) {
            $evento = Evento::findOrFail($eventoId);
            
            if ($evento->cliente_id) {
                $cliente = Clientes::find($evento->cliente_id);
            }
            
            if ($evento->inmueble_id) {
                $inmueble = Inmuebles::find($evento->inmueble_id);
            }
        }

        return view('hojas-visita.crear', compact('evento', 'cliente', 'inmueble'));
    }

    /**
     * Guardar la firma del cliente
     */
    public function storeSignature(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
            'evento_id' => 'required|exists:eventos,id',
        ]);

        try {
            $signature = $request->signature;
            $encoded_image = explode(",", $signature)[1];
            $decoded_image = base64_decode($encoded_image);
            $imageName = Str::random(10) . '.png';

            $rutaDirectorio = 'firmas_clientes/' . request()->session()->get('inmobiliaria');

            if (!File::exists(public_path($rutaDirectorio))) {
                File::makeDirectory(public_path($rutaDirectorio), 0777, true);
            }

            $rutaArchivo = public_path($rutaDirectorio . '/' . $imageName);
            File::put($rutaArchivo, $decoded_image);

            return response()->json([
                'success' => true,
                'message' => 'Firma guardada correctamente',
                'firma_path' => $rutaDirectorio . '/' . $imageName
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al guardar la firma: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guardar la hoja de visita completa
     */
    public function store(Request $request)
    {
        $request->validate([
            'evento_id' => 'required|exists:eventos,id',
            'firma_path' => 'required|string',
        ]);

        try {
            $evento = Evento::findOrFail($request->evento_id);
            $cliente = Clientes::find($evento->cliente_id);
            $inmueble = Inmuebles::find($evento->inmueble_id);

            if (!$cliente || !$inmueble) {
                return back()->with('error', 'El evento debe tener cliente e inmueble asignados.');
            }

            // Generar PDF
            $pdf = Pdf::loadView('hojas-visita.pdf', [
                'cliente' => $cliente,
                'inmueble' => $inmueble,
                'firma_path' => $request->firma_path,
                'fecha' => \Carbon\Carbon::parse($evento->fecha_inicio)->format('d/m/Y'),
            ]);

            $rutaDirectorio = 'hojas_visita/' . request()->session()->get('inmobiliaria');
            if (!File::exists(public_path($rutaDirectorio))) {
                File::makeDirectory(public_path($rutaDirectorio), 0777, true);
            }

            $nombreArchivo = $cliente->id . '-' . $inmueble->id . '-' . date('Y-m-d_H-i-s') . '.pdf';
            $rutaPdf = $rutaDirectorio . '/' . $nombreArchivo;
            $rutaCompleta = public_path($rutaPdf);

            $pdf->save($rutaCompleta);

            // Guardar en BD
            $hojaVisita = HojaVisita::create([
                'cliente_id' => $cliente->id,
                'inmueble_id' => $inmueble->id,
                'evento_id' => $evento->id,
                'fecha' => $evento->fecha_inicio,
                'ruta' => $rutaPdf,
                'firma' => $request->firma_path,
            ]);

            // Enviar email
            $nombreInmobiliaria = request()->session()->get('inmobiliaria') == 'sayco' ? "INMOBILIARIA SAYCO" : "INMOBILIARIA SANCER";
            
            Mail::raw("Estimado {$cliente->nombre_completo}, adjuntamos su hoja de visita del inmueble {$inmueble->titulo}.", 
                function ($message) use ($cliente, $inmueble, $nombreInmobiliaria, $rutaCompleta) {
                    $message->from('admin@grupocerban.com', $nombreInmobiliaria);
                    $message->to($cliente->email, $cliente->nombre_completo);
                    $message->subject($nombreInmobiliaria . " - Hoja de Visita");
                    $message->attach($rutaCompleta);
                });

            return redirect()->route('hojas-visita.index')
                ->with('success', 'Hoja de visita creada y enviada correctamente');

        } catch (\Exception $e) {
            return back()->with('error', 'Error al procesar: ' . $e->getMessage());
        }
    }

    /**
     * Descargar PDF de una hoja de visita
     */
    public function download(HojaVisita $hojaVisita)
    {
        $rutaArchivo = public_path($hojaVisita->ruta);
        
        if (!file_exists($rutaArchivo)) {
            return back()->with('error', 'El archivo PDF no existe.');
        }

        return response()->download($rutaArchivo);
    }

    /**
     * Ver detalles de una hoja de visita
     */
    public function show(HojaVisita $hojaVisita)
    {
        return view('hojas-visita.ver', compact('hojaVisita'));
    }

    /**
     * Eliminar una hoja de visita
     */
    public function destroy(HojaVisita $hojaVisita)
    {
        if (file_exists(public_path($hojaVisita->ruta))) {
            File::delete(public_path($hojaVisita->ruta));
        }
        
        if (file_exists(public_path($hojaVisita->firma))) {
            File::delete(public_path($hojaVisita->firma));
        }

        $hojaVisita->delete();

        return back()->with('success', 'Hoja de visita eliminada correctamente.');
    }
}
