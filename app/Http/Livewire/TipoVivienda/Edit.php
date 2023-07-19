<?php

namespace App\Http\Livewire\Tipovivienda;

use Livewire\Component;
use App\Models\TipoVivienda;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;
    public $tipovivienda;
    public $nombre;

    public function mount()
    {
        $this->tipovivienda = TipoVivienda::find($this->identificador);
        $this->nombre = $this->tipovivienda->nombre;
    }

    public function render()
    {

        return view('livewire.tipovivienda.edit');
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
        $tipovivienda = TipoVivienda::find($this->identificador);

        // Guardar datos validados
        $tipoviviendaSave = $tipovivienda->update([
            'nombre' => $this->nombre,
        ]);

        // Alertas de guardado exitoso
        if ($tipoviviendaSave) {
            $this->alert('success', '¡Tipo de vivienda actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del tipo de vivienda!', [
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

        $this->alert('warning', '¿Seguro que desea borrar el tipo de vivienda? No hay vuelta atrás', [
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
        return redirect()->route('tipovivienda.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $tipovivienda = TipoVivienda::find($this->identificador);
        $tipovivienda->delete();
        return redirect()->route('tipovivienda.index');
    }
}
