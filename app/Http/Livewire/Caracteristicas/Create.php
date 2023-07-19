<?php

namespace App\Http\Livewire\Caracteristicas;

use App\Models\Caracteristicas;
use App\Models\Inmuebles;
use App\Models\Intereses;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $nombre;

    public function mount()
    {

    }

    // Renderizado del Componente
    public function render()
    {
        return view('livewire.caracteristicas.create');
    }

    public function submit()
    {
        $validatedData = $this->validate(
            [
                'nombre' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',

            ]
        );

        // Guardar datos validados
        $caracteristicasSave = Caracteristicas::create($validatedData);

        // Alertas de guardado exitoso
        if ($caracteristicasSave) {
            $this->alert('success', '¡Caracteristica registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la caracteristica!', [
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
        return redirect()->route('caracteristicas.index');
    }
}
