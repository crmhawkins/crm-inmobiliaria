<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Clientes;
use App\Models\Evento;
use App\Models\HojaVisita;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Illuminate\Support\Str;

class VisitaCreate extends Component
{
    use LivewireAlert;
    public $clientes;
    public $inmueble_id;
    public $fecha;
    public $ruta;
    public $firma;
    public $cliente_id;
    public $signature;

    protected $listeners = ['saveSignature'];

    public function mount($inmueble_id = null)
    {
        $this->inmueble_id = $inmueble_id;
        $this->clientes = Clientes::all();
    }
    public function render()
    {
        return view('livewire.inmuebles.visita-create');
    }

    public function saveSignature()
    {
        $this->alert('warning', "hola");

        $encoded_image = explode(",", $this->signature)[1];
        $decoded_image = base64_decode($encoded_image);
        $imageName = Str::random(10) . '.' . "png";

        $rutaDirectorio = 'firmas_clientes/' . request()->session()->get('inmobiliaria');

        // Crea el directorio si no existe
        if (!File::exists(public_path($rutaDirectorio))) {
            File::makeDirectory(public_path($rutaDirectorio), 0777, true);
        }

        // Guardar el archivo
        Storage::disk('public')->put($rutaDirectorio . '/' . $imageName, $decoded_image);

        $this->firma = $rutaDirectorio . '/'. $imageName;
    }
    public function submit()
    {

        $clientePDF = Clientes::where('id', $this->cliente_id)->first();
        $inmueblePDF = Inmuebles::where('id', $this->inmueble_id)->first();

        $pdf = Pdf::loadView('inmuebles.visitaPDF', ['datos' => [
            'firma' => $this->firma,
            'cliente' => [
                'nombre_completo' => $clientePDF->nombre_completo,
                'dni' => $clientePDF->dni,
                'email' => $clientePDF->email,
            ],
            'inmueble' => [
                'titulo' => $inmueblePDF->titulo,
                'descripcion' => $inmueblePDF->descripcion,
                'm2' => $inmueblePDF->m2,
                'm2_construidos' => $inmueblePDF->m2_construidos,
                'valor_referencia' => $inmueblePDF->valor_referencia,
                'habitaciones' => $inmueblePDF->habitaciones,
                'banos' => $inmueblePDF->banos,
                'tipo_vivienda_id' => TipoVivienda::where('id', $inmueblePDF->tipo_vivienda_id)->first()->nombre,
                'vendedor_id' => User::where('id', $inmueblePDF->vendedor_id)->first()->nombre_completo,
                'ubicacion' => $inmueblePDF->ubicacion,
                'cod_postal' => $inmueblePDF->cod_postal,
                'cert_energetico' => $inmueblePDF->cert_energetico,
                'cert_energetico_elegido' => $inmueblePDF->cert_energetico_elegido,
                'estado' => $inmueblePDF->estado,
                'galeria' => $inmueblePDF->galeria,
                'disponibilidad' => $inmueblePDF->disponibilidad,
                'otras_caracteristicas' => $inmueblePDF->otras_caracteristicas,
                'referencia_catastral' => $inmueblePDF->referencia_catastral,
            ]
        ]]);

        // Ruta del directorio donde se guardará el archivo
        $rutaDirectorio = 'hojas_visita/' . request()->session()->get('inmobiliaria');

        // Crea el directorio si no existe
        if (!File::exists(public_path($rutaDirectorio))) {
            File::makeDirectory(public_path($rutaDirectorio), 0777, true);
        }

        // Ruta del archivo PDF
        $rutaPdf = $rutaDirectorio . '/' . $this->cliente_id . "-" . $this->inmueble_id . "-" . $this->fecha . '.pdf';
        $rutaCompleta = public_path($rutaPdf);

        // Guarda el PDF en un archivo en el servidor.
        $pdf->save($rutaCompleta);

        // Añade la ruta del PDF a los datos validados.
        $this->ruta = $rutaDirectorio . '/' . $this->cliente_id . "-" . $this->inmueble_id . "-" . $this->fecha . '.pdf';

        $eventoSave = Evento::create([
            'titulo' => 'Cita con ' .  $clientePDF->nombre_completo,
            'descripcion' => 'Cliente citado :' . $clientePDF->nombre_completo . "<br>" . 'Inmueble en relación a la cita: ' . $clientePDF->titulo,
            'fecha_inicio' => $this->fecha,
            'fecha_fin' => $this->fecha,
            'tipo_tarea' => 'opcion_1',
            'cliente_id' => $this->cliente_id,
            'inmueble_id' => $this->inmueble_id,
            'inmobiliaria' => $inmueblePDF->inmobiliaria,
        ]);

        $validatedData = $this->validate(
            [
                'cliente_id' => 'required',
                'inmueble_id' => 'required',
                'fecha' => 'required',
                'ruta' => 'required',
            ],
            // Mensajes de error
            [
                'cliente_id.required' => 'El cliente es obligatorio.',
                'inmueble_id.required' => 'Este error no debería aparecer, pero falta el inmueble.',
                'fecha.required' => 'Indica la fecha de la visita.',
                'ruta.required' => 'Este error no debería aparecer, pero falta la ruta del documento.',
            ]
        );

        // Guardar datos validados
        $visitaSave = HojaVisita::create($validatedData);



        // Alertas de guardado exitoso
        if ($visitaSave) {
            $this->alert('success', '¡Hoja de visita registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la hoja de visita!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function getListeners()
    {
        return [
            'confirmed'
        ];
    }

    public function confirmed()
    {
        return redirect()->route('inmuebles.index');
    }
}
