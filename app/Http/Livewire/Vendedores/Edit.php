<?php

namespace App\Http\Livewire\Vendedores;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;
use App\Models\Vendedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;
    public $vendedores;
    public $nombre_completo;
    public $dni;
    public $role;
    public $password;
    public $ubicacion;
    public $telefono;
    public $email;
    public $inmobiliaria;
    public function mount()
    {
        $this->vendedores = User::find($this->identificador);
        $this->nombre_completo = $this->vendedores->nombre_completo;
        $this->dni = $this->vendedores->dni;
        $this->role = $this->vendedores->role;
        $this->ubicacion = $this->vendedores->ubicacion;
        $this->telefono = $this->vendedores->telefono;
        $this->email = $this->vendedores->email;

        if($this->inmobiliaria != null){
            $this->inmobiliaria = $this->vendedores->inmobiliaria;
        }
    }

    public function render()
    {

        return view('livewire.vendedores.edit');
    }

    public function update()
    {

        if($this->password != null){
            $this->password = Hash::make($this->password);
        }

        $validatedData = $this->validate(
            [
                'nombre_completo' => 'required',
                'dni' => 'required',
                'role' => 'required',
                'ubicacion' => 'required',
                'password' => 'nullable',
                'telefono' => 'required',
                'email' => 'required',
                'inmobiliaria' => 'nullable',
            ],
            // Mensajes de error
            [
                'nombre_completo.required' => 'El nombre es obligatorio.',
                'dni.required' => 'El nombre es obligatorio.',
                'role.required' => 'El nombre es obligatorio.',
                'ubicacion.required' => 'El nombre es obligatorio.',
                'telefono.required' => 'El nombre es obligatorio.',
                'email.required' => 'El nombre es obligatorio.',
                'inmobiliaria.required' => 'El nombre es obligatorio.',

            ]
        );
        // Guardar datos validados
        // Encuentra el alumno identificado
        $vendedores = User::find($this->identificador);

        // Guardar datos validados
        $vendedoresSave = $vendedores->update([
            'nombre_completo' => $this->nombre_completo,
            'dni' => $this->dni,
            'role' => $this->role,
            'ubicacion' => $this->ubicacion,
            'password' => $this->password,
            'telefono' => $this->telefono,
            'email' => $this->email,
            'inmobiliaria' => $this->inmobiliaria,
        ]);

        // Alertas de guardado exitoso
        if ($vendedoresSave) {
            $this->alert('success', '¡Vendedor actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del vendedor!', [
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
        return redirect()->route('vendedores.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $vendedores = User::find($this->identificador);
        $vendedores->delete();
        return redirect()->route('vendedores.index');
    }

    public function generarPassword(){
        $this->password = Str::random(10);
    }
}
