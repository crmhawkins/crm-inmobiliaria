<?php

namespace App\Http\Livewire\Ecotasa;

use App\Models\Ecotasa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $nombre;
    public $valor;
    public $peso_min;
    public $peso_max;
    public $diametro_mayor_1400 = 0;

    public function mount()
    {
    }

    // Renderizado del Componente
    public function render()
    {
        return view('livewire.ecotasa.create');
    }

    public function submit()
    {
        if ($this->peso_min == null && $this->peso_max == null) {
            $validatedData = $this->validate(
                [
                    'nombre' => 'required',
                    'valor' => 'required',
                    'peso_min' => 'required',
                    'peso_max' => 'required',
                    'diametro_mayor_1400' => 'required',
                ],
                // Mensajes de error
                [
                    'nombre.required' => 'El nombre es obligatorio.',
                    'valor.required' => 'El valor de la ecotasa es obligatorio.',
                    'peso_min.required' => 'Rellena al menos uno de estos campos.',
                    'peso_max.required' => 'Rellena al menos uno de estos campos.',
                    'diametro_mayor_1400.required' => 'La dirección es obligatoria.',
                ]
            );
        } else {
            $validatedData = $this->validate(
                [
                    'nombre' => 'required',
                    'valor' => 'required',
                    'peso_min' => 'nullable',
                    'peso_max' => 'nullable',
                    'diametro_mayor_1400' => 'required',
                ],
                // Mensajes de error
                [
                    'nombre.required' => 'El nombre es obligatorio.',
                    'valor.required' => 'El valor de la ecotasa es obligatorio.',
                    'diametro_mayor_1400.required' => 'La dirección es obligatoria.',
                ]
            );
        }


        // Guardar datos validados
        $ecotasaSave = Ecotasa::create($validatedData);

        // Alertas de guardado exitoso
        if ($ecotasaSave) {
            $this->alert('success', '¡Alumno registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del alumno!', [
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
        return redirect()->route('ecotasa.index');
    }
}
