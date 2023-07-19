<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Clientes;
use App\Models\Evento;
use App\Models\Inmuebles;
use App\Models\Intereses;
use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $titulo;
    public $descripcion;
    public $fecha_inicio;
    public $fecha_fin;
    public $tipo_tarea;
    public $cliente_id;
    public $inmueble_id;
    public $clientes;
    public $inmuebles;
    public $inmobiliaria = null;

    public function mount()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
            $this->inmuebles = Inmuebles::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
            $this->inmuebles = Inmuebles::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }
    }

    // Renderizado del Componente
    public function render()
    {
        return view('livewire.agenda.create');
    }

    public function submit()
    {
        //El tipo de tarea: Opción 1 crea un título y descripción automáticos en base al cliente y al inmueble elegidos.
        if ($this->tipo_tarea == 'opcion_1') {
            $this->titulo = "Cita con " . Clientes::where('id', $this->cliente_id)->first()->nombre_completo;
            $this->descripcion = "Cliente citado: " . Clientes::where('id', $this->cliente_id)->first()->nombre_completo . "<br>" .
                "Inmueble en relación a la cita: " . Inmuebles::where('id', $this->inmueble_id)->first()->titulo;
        }

        // Si no hay fecha de final, se ajusta la misma que la fecha de inicio.

        if ($this->fecha_fin == null) {
            $this->fecha_fin = $this->fecha_inicio;
        }

        //Si no se añade la opción de que esté en ambas inmobiliarias (devuelve null), se comprueba la inmobiliaria actual y se envía.

        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        //Validación

        $validatedData = $this->validate(
            [
                'titulo' => 'required',
                'descripcion' => 'required',
                'fecha_inicio' => 'required',
                'fecha_fin' => 'required',
                'tipo_tarea' => 'required',
                'cliente_id' => 'nullable',
                'inmueble_id' => 'nullable',
                'inmobiliaria' => 'nullable',
            ],
            // Mensajes de error
            [
                'fecha_inicio.required' => 'Introduce una fecha de inicio.',
            ]
        );

        // Guardar datos validados
        $eventoSave = Evento::create($validatedData);

        // Alertas de guardado exitoso
        if ($eventoSave) {
            $this->alert('success', '¡Cita agendada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la cita en la agenda!', [
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
        return redirect()->route('agenda.index');
    }
}
