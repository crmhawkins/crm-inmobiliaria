<?php

namespace App\Http\Livewire\Trabajadores;

use App\Models\Trabajador;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $name;
    public $role;
    public $username;
    public $surname;
    public $email;
    public $password;
    public $inactive = "true";




    public function mount()
    {
    }

    public function render()
    {
        return view('livewire.trabajadores.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        $validatedData = $this->validate(
            [
                'username' => 'required',
                'name' => 'required',
                'surname' => 'required',
                'role' => 'required',
                'email' => 'required',
                'inactive' => 'nullable',
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

        $validatedData['password'] = Hash::make($validatedData['password']);
        $validatedData['inactive'] = false;

        // Guardar datos validados
        $usuariosSave = User::create($validatedData);

        // Alertas de guardado exitoso
        if ($usuariosSave) {
            $this->alert('success', '¡Usuario registrado correctamente!', [
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
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('trabajadores.index');
    }
}
