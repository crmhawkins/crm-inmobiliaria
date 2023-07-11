<?php

namespace App\Http\Livewire\Trabajadores;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $name;
    public $role;
    public $username;
    public $surname;
    public $email;
    public $password;




    public function mount()
    {
        $usuarios = User::find($this->identificador);

        $this->name = $usuarios->name;
        $this->role = $usuarios->role;
        $this->username = $usuarios->username;
        $this->surname = $usuarios->surname;
        $this->email = $usuarios->email;
        $this->password = $usuarios->password;

    }

    public function render()
    {
        return view('livewire.trabajadores.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        $this->validate(
            [
                'username' => 'required',
                'name' => 'required',
                'surname' => 'required',
                'role' => 'required',
                'email' => 'required',
                'password' => ['required', 'string', 'min:8']
            ],
            // Mensajes de error
            [
                'nombre.required' => 'El ID de usuario es obligatorio.',
                'name.required' => 'El nombre es obligatorio.',
                'surname.required' => 'El apellido es obligatorio.',
                'role.required' => 'El puesto es obligatorio.',
                'email.required' => 'El correo es obligatorio.',
                'password.required' => 'La contraseña es obligatoria.',

            ]
        );

        // Encuentra el identificador
        $usuarios = User::find($this->identificador);

        // Guardar datos validados
        $usuariosSave = $usuarios->update([
            'username' => $this->username,
            'name' => $this->name,
            'surname' => $this->surname,
            'role' => $this->role,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        if ($usuariosSave) {
            $this->alert('success', 'Usuario actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del usuario!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }

        session()->flash('message', 'Usuario actualizado correctamente.');

        $this->emit('userUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el usuario? No hay vuelta atrás', [
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
        return redirect()->route('trabajadores.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $usuarios = Trabajador::find($this->identificador);
        $usuarios->delete();
        return redirect()->route('trabajadores.index');

    }
}
