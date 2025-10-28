<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Evento;
use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Models\HojaVisita;
use App\Models\TipoVivienda;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;

class VisitaFromEvento extends Component
{
    use LivewireAlert;

    public $identificador;
    public $evento;
    public $cliente;
    public $inmueble;
    public $fecha;
    public $ruta;
    public $firma;
    public $signature;

    protected $listeners = ['confirmed'];

    public function mount($identificador)
    {
        $this->identificador = $identificador;
        $this->evento = Evento::find($identificador);
        
        if ($this->evento) {
            if ($this->evento->cliente_id) {
                $this->cliente = Clientes::find($this->evento->cliente_id);
            }
            if ($this->evento->inmueble_id) {
                $this->inmueble = Inmuebles::find($this->evento->inmueble_id);
            }
            $this->fecha = Carbon::parse($this->evento->fecha_inicio)->format('Y-m-d');
            
            // Verificar si ya existe una firma guardada para este evento
            $hojaVisitaExistente = HojaVisita::where('evento_id', $this->evento->id)->first();
            if ($hojaVisitaExistente && $hojaVisitaExistente->firma) {
                $this->firma = $hojaVisitaExistente->firma;
            }
        }
    }

    public function saveSignature()
    {
        if (!$this->signature) {
            $this->alert('error', 'Por favor, proporciona tu firma.', [
                'position' => 'center',
                'timer' => 3000,
            ]);
            return;
        }

        try {
            $encoded_image = explode(",", $this->signature)[1];
            $decoded_image = base64_decode($encoded_image);
            $imageName = Str::random(10) . '.png';

            $rutaDirectorio = 'firmas_clientes/' . request()->session()->get('inmobiliaria');
            $rutaCompleta = public_path($rutaDirectorio);

            // Crear directorio si no existe
            if (!File::exists($rutaCompleta)) {
                File::makeDirectory($rutaCompleta, 0777, true);
            }

            // Guardar imagen directamente en public
            $rutaArchivo = $rutaCompleta . '/' . $imageName;
            File::put($rutaArchivo, $decoded_image);

            $this->firma = $rutaDirectorio . '/' . $imageName;
            
            // Limpiar el signature data para que muestre la imagen guardada
            $this->signature = null;
            
            $this->alert('success', 'Firma guardada correctamente.', [
                'position' => 'center',
                'timer' => 2000,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Error al guardar la firma: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 5000,
            ]);
        }
    }

    public function submit()
    {
        if (!$this->firma) {
            $this->alert('error', 'Por favor, guarda la firma primero.', [
                'position' => 'center',
                'timer' => 3000,
            ]);
            return;
        }

        if (!$this->cliente || !$this->inmueble) {
            $this->alert('error', 'Este evento no tiene cliente o inmueble asignado.', [
                'position' => 'center',
                'timer' => 3000,
            ]);
            return;
        }

        try {
            // Generar PDF
            $pdf = Pdf::loadView('inmuebles.visitaPDF', ['datos' => [
                'firma' => $this->firma,
                'cliente' => [
                    'nombre_completo' => $this->cliente->nombre_completo,
                    'dni' => $this->cliente->dni,
                    'email' => $this->cliente->email,
                ],
                'inmueble' => [
                    'titulo' => $this->inmueble->titulo,
                    'descripcion' => $this->inmueble->descripcion,
                    'm2' => $this->inmueble->m2,
                    'm2_construidos' => $this->inmueble->m2_construidos,
                    'valor_referencia' => $this->inmueble->valor_referencia,
                    'habitaciones' => $this->inmueble->habitaciones,
                    'banos' => $this->inmueble->banos,
                    'tipo_vivienda_id' => TipoVivienda::where('id', $this->inmueble->tipo_vivienda_id)->first()->nombre ?? 'N/A',
                    'vendedor_id' => User::where('id', $this->inmueble->vendedor_id)->first()->nombre_completo ?? 'N/A',
                    'ubicacion' => $this->inmueble->ubicacion,
                    'cod_postal' => $this->inmueble->cod_postal,
                    'cert_energetico' => $this->inmueble->cert_energetico,
                    'cert_energetico_elegido' => $this->inmueble->cert_energetico_elegido,
                    'estado' => $this->inmueble->estado,
                    'galeria' => $this->inmueble->galeria,
                    'disponibilidad' => $this->inmueble->disponibilidad,
                    'otras_caracteristicas' => $this->inmueble->otras_caracteristicas,
                    'referencia_catastral' => $this->inmueble->referencia_catastral,
                ]
            ]]);

            $rutaDirectorio = 'hojas_visita/' . request()->session()->get('inmobiliaria');

            if (!File::exists(public_path($rutaDirectorio))) {
                File::makeDirectory(public_path($rutaDirectorio), 0777, true);
            }

            $rutaPdf = $rutaDirectorio . '/' . $this->cliente->id . '-' . $this->inmueble->id . '-' . $this->fecha . '.pdf';
            $rutaCompleta = public_path($rutaPdf);

            $pdf->save($rutaCompleta);

            // Guardar la ruta sin "storage/" porque está en public/
            $this->ruta = $rutaDirectorio . '/' . $this->cliente->id . '-' . $this->inmueble->id . '-' . $this->fecha . '.pdf';

            // Enviar email (opcional)
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $nombre_inmobiliaria = "INMOBILIARIA SAYCO";
            } else {
                $nombre_inmobiliaria = "INMOBILIARIA SANCER";
            }

            $texto = 'Buenas, ' . $this->cliente->nombre_completo . ". Se le adjunta la hoja de visita del inmueble que ha firmado.";

            Mail::raw($texto, function ($message) use ($nombre_inmobiliaria, $rutaPdf) {
                $message->from('admin@grupocerban.com', $nombre_inmobiliaria);
                $message->to($this->cliente->email, $this->cliente->nombre_completo);
                $message->to(env('MAIL_USERNAME'));
                $message->subject($nombre_inmobiliaria . " - Hoja de visita del inmueble " . $this->inmueble->titulo);
                $message->attach(public_path($rutaPdf));
            });

            // Guardar en BD
            $hojaVisita = HojaVisita::create([
                'cliente_id' => $this->cliente->id,
                'inmueble_id' => $this->inmueble->id,
                'fecha' => $this->fecha,
                'ruta' => $this->ruta,
                'firma' => $this->firma,
                'evento_id' => $this->evento->id,
            ]);

            \Log::info("Hoja de visita creada: ID {$hojaVisita->id}, Ruta: {$this->ruta}, Firma: {$this->firma}");

            $this->alert('success', '¡Hoja de visita registrada y enviada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'Descargar PDF',
                'timerProgressBar' => true,
            ]);

        } catch (\Exception $e) {
            $this->alert('error', 'Error al procesar la hoja de visita: ' . $e->getMessage(), [
                'position' => 'center',
                'timer' => 5000,
            ]);
        }
    }

    public function confirmed()
    {
        if ($this->ruta && file_exists(public_path($this->ruta))) {
            return response()->download(public_path($this->ruta))->deleteFileAfterSend(false);
        }
    }

    public function render()
    {
        return view('livewire.agenda.visita-from-evento');
    }
}
