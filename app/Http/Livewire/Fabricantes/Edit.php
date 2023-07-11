<?php

namespace App\Http\Livewire\Fabricantes;

use App\Models\Fabricante;
use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;

    public $nombre;




    public function mount()
    {
        $fabricante = Fabricante::find($this->identificador);

        $this->nombre = $fabricante->nombre;

    }

    public function render()
    {
        return view('livewire.fabricantes.edit');
    }

    // Al hacer update en el formulario
    public function update()
    {
        $this->validate(
            [
                'nombre' => 'required',
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El nombre es obligatorio.',

            ]
        );

        // Encuentra el identificador
        $fabricante = Fabricante::find($this->identificador);

        // Guardar datos validados
        $usuariosSave = $fabricante->update([
            'nombre' => $this->nombre,
        ]);

        if ($usuariosSave) {
            $this->alert('success', '¡Fabricante actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del fabricante!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', '¡Fabricante actualizado correctamente!');

        $this->emit('userUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el fabricante? No hay vuelta atrás', [
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

    // Función para cuando se llama a la alerta
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
        return redirect()->route('fabricantes.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $usuarios = Fabricante::find($this->identificador);
        $usuarios->delete();
        return redirect()->route('fabricantes.index');

    }
}
