<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Clientes;
use App\Models\Inmuebles;
use Livewire\Component;
use App\Models\Evento;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;

    public $eventos;
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
        $this->eventos = Evento::find($this->identificador);
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
            $this->inmuebles = Inmuebles::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
            $this->inmuebles = Inmuebles::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }

        $this->titulo =  $this->eventos->titulo;
        $this->descripcion =  $this->eventos->descripcion;
        $this->fecha_inicio =  $this->eventos->fecha_inicio;
        $this->fecha_fin =  $this->eventos->fecha_fin;
        $this->tipo_tarea =  $this->eventos->tipo_tarea;
        $this->cliente_id =  $this->eventos->cliente_id;
        $this->inmueble_id =  $this->eventos->inmueble_id;
        $this->inmobiliaria =  $this->eventos->inmobiliaria;
    }

    public function render()
    {

        return view('livewire.agenda.edit');
    }

    public function update()
    {
        if ($this->tipo_tarea == 'opcion_1') {
            $this->titulo = "Cita con " . Clientes::where('id', $this->cliente_id)->first()->nombre_completo;
            $this->descripcion = "Cliente citado: " . Clientes::where('id', $this->cliente_id)->first()->nombre_completo . "<br>" .
                "Inmueble en relación a la cita: " . Inmuebles::where('id', $this->inmueble_id)->first()->titulo;
        }

        if ($this->fecha_fin == null) {
            $this->fecha_fin = $this->fecha_inicio;
        }

        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        $this->validate(
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
        // Encuentra el alumno identificado
        $clientes = Evento::find($this->identificador);

        // Guardar datos validados
        $clientesSave = $clientes->update([
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'tipo_tarea' => $this->tipo_tarea,
            'cliente_id' => $this->cliente_id,
            'inmueble_id' => $this->inmueble_id,
            'inmobiliaria' => $this->inmobiliaria,
        ]);

        // Alertas de guardado exitoso
        if ($clientesSave) {
            $this->alert('success', '¡Clientes actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del clientes!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el clientes? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('agenda.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $clientes = Evento::find($this->identificador);
        $clientes->delete();
        return redirect()->route('agenda.index');
    }
}
