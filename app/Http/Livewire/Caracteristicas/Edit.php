<?php

namespace App\Http\Livewire\Caracteristicas;

use Livewire\Component;
use App\Models\Caracteristicas;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;
    public $caracteristicas;
    public $nombre;

    public function mount()
    {
        $this->caracteristicas = Caracteristicas::find($this->identificador);
        $this->nombre = $this->caracteristicas->nombre;
    }

    public function render()
    {

        return view('livewire.caracteristicas.edit');
    }

    public function update()
    {
        // Validación de datos
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
        // Encuentra el alumno identificado
        $caracteristicas = Caracteristicas::find($this->identificador);

        // Guardar datos validados
        $caracteristicasSave = $caracteristicas->update([
            'nombre' => $this->nombre,
        ]);

        // Alertas de guardado exitoso
        if ($caracteristicasSave) {
            $this->alert('success', '¡Caracteristica actualizada correctamente!', [
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

    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar la caracteristica? No hay vuelta atrás', [
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
        return redirect()->route('caracteristicas.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $caracteristicas = Caracteristicas::find($this->identificador);
        $caracteristicas->delete();
        return redirect()->route('caracteristicas.index');
    }
}
