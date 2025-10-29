<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HojaFirma;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AgendaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return view('agenda.index');
    }

    /**
     * Descargar PDF de hoja de firma
     *
     * @param HojaFirma $hojaFirma
     * @return \Illuminate\Http\Response
     */
    public function descargarPDFHojaFirma(HojaFirma $hojaFirma)
    {
        $hojaFirma->load('evento.cliente', 'evento.inmueble');

        $cliente = $hojaFirma->evento->cliente;
        $inmueble = $hojaFirma->evento->inmueble;

        // Convertir firma base64 a archivo temporal si existe
        $rutaFirmaTemporal = null;
        if ($hojaFirma->firma_cliente) {
            try {
                // Verificar si es una imagen base64
                if (preg_match('/data:image\/(\w+);base64,/', $hojaFirma->firma_cliente, $matches)) {
                    $imageData = $hojaFirma->firma_cliente;
                    $imageType = $matches[1]; // png, jpeg, etc.
                    
                    // Decodificar base64
                    $imageData = str_replace('data:image/' . $imageType . ';base64,', '', $imageData);
                    $imageData = base64_decode($imageData);
                    
                    // Crear directorio temporal en public (accesible para DomPDF)
                    $dirTemp = public_path('temp_firmas');
                    if (!File::exists($dirTemp)) {
                        File::makeDirectory($dirTemp, 0777, true);
                    }
                    
                    // Guardar imagen temporal
                    $nombreArchivoTemp = 'firma_' . $hojaFirma->id . '_' . time() . '.' . $imageType;
                    $rutaFirmaTemporalCompleta = $dirTemp . '/' . $nombreArchivoTemp;
                    file_put_contents($rutaFirmaTemporalCompleta, $imageData);
                    
                    // Usar solo el nombre del archivo relativo para public_path en la vista
                    $rutaFirmaTemporal = 'temp_firmas/' . $nombreArchivoTemp;
                } else {
                    // Si no es base64, puede ser una ruta de archivo
                    $rutaFirmaTemporal = $hojaFirma->firma_cliente;
                }
            } catch (\Exception $e) {
                \Log::error('Error procesando imagen de firma: ' . $e->getMessage());
                // Continuar sin la imagen si hay error
                $rutaFirmaTemporal = null;
            }
        }

        $datos = [
            'hoja_firma' => $hojaFirma,
            'evento' => $hojaFirma->evento,
            'cliente' => $cliente,
            'inmueble' => $inmueble,
            'fecha' => now()->format('d/m/Y'),
            'ruta_firma_temporal' => $rutaFirmaTemporal,
        ];

        $pdf = Pdf::loadView('agenda.hoja-firma-pdf', $datos);

        $nombreArchivo = 'hoja_firma_' . $hojaFirma->id . '_' . ($hojaFirma->fecha_firma ? $hojaFirma->fecha_firma->format('Ymd_His') : date('Ymd_His')) . '.pdf';

        $response = $pdf->download($nombreArchivo);

        // Limpiar archivo temporal después de generar el PDF
        if (isset($rutaFirmaTemporalCompleta) && $rutaFirmaTemporalCompleta && File::exists($rutaFirmaTemporalCompleta)) {
            try {
                // Usar register_shutdown_function para eliminar después de enviar la respuesta
                register_shutdown_function(function() use ($rutaFirmaTemporalCompleta) {
                    if (File::exists($rutaFirmaTemporalCompleta)) {
                        File::delete($rutaFirmaTemporalCompleta);
                    }
                });
            } catch (\Exception $e) {
                \Log::warning('No se pudo eliminar archivo temporal de firma: ' . $e->getMessage());
            }
        }

        return $response;
    }
}
