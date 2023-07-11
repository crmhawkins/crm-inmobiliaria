<?php

namespace App\Http\Livewire\Proveedores;

use Livewire\Component;
use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $dni;
    public $nombre;
    public $email;
    public $telefono;
    public $direccion;
    public $observaciones;

    public function mount(){
        $proveedor = Proveedores::find($this->identificador);

        $this->dni = $proveedor->dni;
        $this->nombre = $proveedor->nombre;
        $this->direccion = $proveedor->direccion;
        $this->telefono = $proveedor->telefono;
        $this->email = $proveedor->email;
        }

    public function render()
    {
        
        return view('livewire.proveedores.edit-component');
    }

    public function update()
    {
        // Validación de datos
        $this->validate([
            'dni' => 'required',
            'nombre' => 'required',
            'email' => ['required', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'],
            'telefono' => 'required',
            'direccion' => 'required',
        ],
            // Mensajes de error
            [
                'dni.required' => 'El DNI es obligatorio.',
                'nombre.required' => 'El nombre es obligatorio.',
                'email.required' => 'El email es obligatorio.',
                'email.regex' => 'Introduce un email válido',
                'telefono.required' => 'El teléfono es obligatorio.',
                'direccion.required' => 'La dirección es obligatoria.',
            ]);

        // Guardar datos validados
        // Encuentra el alumno identificado
        $proveedor = Proveedores::find($this->identificador);

        // Guardar datos validados
        $proveedoresSave = $proveedor->update([
            'dni' => $this->dni,
            'nombre' => $this->nombre,
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'observaciones' => $this->observaciones,

        ]);

        // Alertas de guardado exitoso
        if ($proveedoresSave) {
            $this->alert('success', '¡Proveedor actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del proveedor!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy(){
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el proveedor? No hay vuelta atrás', [
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
        return redirect()->route('proveedores.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $proveedor = Proveedores::find($this->identificador);
        $proveedor->delete();
        return redirect()->route('proveedores.index');

    }
}
