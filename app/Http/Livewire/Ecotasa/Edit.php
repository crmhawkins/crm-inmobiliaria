<?php

namespace App\Http\Livewire\Ecotasa;

use Livewire\Component;
use App\Models\Ecotasa;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;
    public $nombre;
    public $valor;
    public $peso_min;
    public $peso_max;
    public $diametro_mayor_1400;
    public $ecotasa;

    public function mount(){
        $this->ecotasa = Ecotasa::find($this->identificador);

        $this->nombre = $this->ecotasa->nombre;
        $this->valor = $this->ecotasa->valor;
        $this->peso_min = $this->ecotasa->peso_min;
        $this->peso_max = $this->ecotasa->peso_max;
        $this->diametro_mayor_1400 = $this->ecotasa->diametro_mayor_1400;
        }

    public function render()
    {

        return view('livewire.ecotasa.edit');
    }

    public function update()
    {
        // Validación de datos
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
                    'descripcion' => 'required',
                    'peso_min' => 'nullable',
                    'peso_max' => 'nullable',
                    'diametro_mayor_1400' => 'required',
                ],
                // Mensajes de error
                [
                    'nombre.required' => 'El nombre es obligatorio.',
                    'valor.required' => 'El valor de la ecotasa es obligatorio.',
                    'descripcion.required' => 'Menciona los vehículos que usarán este neumático.',
                    'diametro_mayor_1400.required' => 'La dirección es obligatoria.',
                ]
            );
        }

        // Guardar datos validados
        // Encuentra el alumno identificado
        $ecotasa = Ecotasa::find($this->identificador);

        // Guardar datos validados
        $ecotasaSave = $ecotasa->update([
            'nombre' => $this->nombre,
            'valor' => $this->valor,
            'descripcion' => $this->valor,
            'peso_min' => $this->peso_min,
            'peso_max' => $this->peso_max,
            'diametro_mayor_1400' => $this->diametro_mayor_1400,

        ]);

        // Alertas de guardado exitoso
        if ($ecotasaSave) {
            $this->alert('success', '¡Ecotasa actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del ecotasa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy(){
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el ecotasa? No hay vuelta atrás', [
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
        return redirect()->route('ecotasa.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $ecotasa = Ecotasa::find($this->identificador);
        $ecotasa->delete();
        return redirect()->route('ecotasa.index');

    }
}
