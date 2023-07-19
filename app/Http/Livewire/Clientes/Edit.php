<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Caracteristicas;
use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use Livewire\Component;
use App\Models\Clientes;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\WithPagination;

class Edit extends Component
{
    use LivewireAlert;
    use WithPagination;

    public $identificador;
    protected $paginationTheme = 'bootstrap';

    public $nombre_completo;
    public $dni;
    public $telefono;
    public $email;
    public $intereses;
    protected $intereses_inmuebles;
    public $caracteristicas;
    public $otras_caracteristicasArray = [];

    public $disponibilidad;
    public $estado;
    public $habitaciones_min;
    public $habitaciones_max;
    public $banos_min;
    public $banos_max;
    public $m2_min;
    public $m2_max;
    public $ubicacion;

    public $tipos_vivienda;
    public $inmobiliaria = null;
    public $clientes;

    public function mount()
    {
        $this->tipos_vivienda = TipoVivienda::all();
        $this->caracteristicas = Caracteristicas::all();
        $this->clientes = Clientes::find($this->identificador);
        $this->nombre_completo = $this->clientes->nombre_completo;
        $this->dni = $this->clientes->dni;
        $this->telefono = $this->clientes->telefono;
        $this->email = $this->clientes->email;

        if ($this->clientes->inmobiliaria != null) {
            $this->inmobiliaria = null;
        } else {
            $this->inmobiliaria = true;
        };

        $intereses = json_decode($this->clientes->intereses, true);

        $this->disponibilidad = $intereses["disponibilidad"];
        $this->estado = $intereses["estado"];
        $this->habitaciones_min = $intereses["habitaciones_min"];
        $this->habitaciones_max = $intereses["habitaciones_max"];
        $this->banos_min = $intereses["banos_min"];
        $this->banos_max = $intereses["banos_max"];
        $this->m2_min = $intereses["m2_min"];
        $this->m2_max = $intereses["m2_max"];
        $this->ubicacion = $intereses["ubicacion"];
        if ($intereses["otras_caracteristicas"] == null) {
            $this->otras_caracteristicasArray = [];
        } else {
            $this->otras_caracteristicasArray = json_decode($intereses["otras_caracteristicas"], true);
        }
    }

    public function render()
    {
        $query = Inmuebles::query();

        if ($this->disponibilidad) {
            $query->where('disponibilidad', $this->disponibilidad);
        }

        if (!empty($this->otras_caracteristicasArray)) {
            foreach ($this->otras_caracteristicasArray as $caracteristica) {
                $query->whereJsonContains('otras_caracteristicas', strval($caracteristica));
            }
        }

        if ($this->estado) {
            $query->where('estado', $this->estado);
        }

        if ($this->habitaciones_min) {
            $query->where('habitaciones', '>=', $this->habitaciones_min);
        }

        if ($this->habitaciones_max) {
            $query->where('habitaciones', '<=', $this->habitaciones_max);
        }

        if ($this->banos_min) {
            $query->where('banos', '>=', $this->banos_min);
        }

        if ($this->banos_max) {
            $query->where('banos', '<=', $this->banos_max);
        }

        if ($this->m2_min) {
            $query->where('m2', '>=', $this->m2_min);
        }

        if ($this->m2_max) {
            $query->where('m2', '<=', $this->m2_max);
        }

        if ($this->ubicacion) {
            $query->where('ubicacion', 'LIKE', '%' . $this->ubicacion . '%');
        }

        $this->intereses_inmuebles = $query->paginate(3);


        return view('livewire.clientes.edit', [
            'intereses_inmuebles' => $this->intereses_inmuebles,
        ]);
    }

    public function update()
    {

        if ($this->inmobiliaria == null) {
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->inmobiliaria = true;
            } else {
                $this->inmobiliaria = false;
            }
        } else {
            $this->inmobiliaria = null;
        }

        $this->intereses = json_encode([
            'disponibilidad' => $this->disponibilidad,
            'estado' => $this->estado,
            'habitaciones_min' => $this->habitaciones_min,
            'habitaciones_max' => $this->habitaciones_max,
            'banos_min' => $this->banos_min,
            'banos_max' => $this->banos_max,
            'm2_min' => $this->m2_min,
            'm2_max' => $this->m2_max,
            'ubicacion' => $this->ubicacion,
            'otras_caracteristicas' => json_encode($this->otras_caracteristicasArray)
        ]);

        $this->validate(
            [
                'nombre_completo' => 'required',
                'dni' => 'required',
                'telefono' => 'required',
                'email' => 'required',
                'intereses' => 'nullable',
                'inmobiliaria' => 'nullable',
            ],
            // Mensajes de error
            [
                'nombre_completo.required' => 'El nombre es obligatorio.',
                'dni.required' => 'El DNI del cliente es obligatorio.',
                'email.required' => 'El correo del cliente es obligatorio.',
                'telefono.required' => 'El teléfono del cliente es obligatorio.',
            ]
        );
        // Guardar datos validados
        // Encuentra el alumno identificado
        $clientes = Clientes::find($this->identificador);

        // Guardar datos validados
        $clientesSave = $clientes->update([
            'nombre_completo' => $this->nombre_completo,
            'dni' => $this->dni,
            'email' => $this->email,
            'intereses' => $this->intereses,
            'inmobiliaria' => $this->inmobiliaria,

        ]);

        // Alertas de guardado exitoso
        if ($clientesSave) {
            $this->alert('success', '¡Cliente actualizado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del cliente!', [
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

        $this->alert('warning', '¿Seguro que desea borrar el cliente? No hay vuelta atrás', [
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
        return redirect()->route('clientes.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $clientes = Clientes::find($this->identificador);
        $clientes->delete();
        return redirect()->route('clientes.index');
    }
}
