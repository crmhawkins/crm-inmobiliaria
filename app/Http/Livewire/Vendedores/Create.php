<?php

namespace App\Http\Livewire\Vendedores;

use App\Models\TipoVivienda;
use App\Models\Inmuebles;
use App\Models\Intereses;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Create extends Component
{
    use LivewireAlert;

    public $nombre_completo;
    public $dni;
    public $role = 'admin';
    public $password;
    public $ubicacion;
    public $telefono;
    public $email;
    public $signature;
    public $firma;
    public $inmobiliaria;


    public function mount()
    {

    }

    // Renderizado del Componente
    public function render()
    {
        return view('livewire.vendedores.create');
    }

    public function generarPassword(){
        $this->password = Str::random(10);
    }

    public function submit()
    {
        $this->password = Hash::make($this->password);

       $this->inmobiliaria = Auth::user()->inmobiliaria;

        $validatedData = $this->validate(
            [
                'nombre_completo' => 'required',
                'dni' => 'required',
                'role' => 'required',
                'ubicacion' => 'required',
                'password' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'inmobiliaria' => 'required',
            ],
            // Mensajes de error
            [
                'nombre_completo.required' => 'El nombre es obligatorio.',
                'dni.required' => 'El nombre es obligatorio.',
                'role.required' => 'El nombre es obligatorio.',
                'password.required' => 'El nombre es obligatorio.',
                'ubicacion.required' => 'El nombre es obligatorio.',
                'telefono.required' => 'El nombre es obligatorio.',
                'email.required' => 'El nombre es obligatorio.',
                'inmobiliaria.required' => 'El nombre es obligatorio.',

            ]
        );



        // Guardar datos validados
        $tipoviviendaSave = User::create($validatedData);

        // Alertas de guardado exitoso
        if ($tipoviviendaSave) {
            $this->alert('success', '¡Vendedor registrado correctamente!', [
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

    public function getListeners()
    {
        return [
            'confirmed'
        ];
    }

    public function confirmed()
    {
        return redirect()->route('vendedores.index');
    }
}
