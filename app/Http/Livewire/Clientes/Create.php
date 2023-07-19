<?php

namespace App\Http\Livewire\Clientes;

use App\Models\Caracteristicas;
use App\Models\Clientes;
use App\Models\Inmuebles;
use App\Models\Intereses;
use App\Models\TipoVivienda;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Create extends Component
{
    use LivewireAlert;
    use WithPagination;
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

    public function mount()
    {
        $this->tipos_vivienda = TipoVivienda::all();
        $this->caracteristicas = Caracteristicas::all();
    }

    // Renderizado del Componente
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
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $query->where('inmobiliaria', true)->orWhere('inmobiliaria', null);
        } else {
            $query->where('inmobiliaria', false)->orWhere('inmobiliaria', null);
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

        $detect = new \Detection\MobileDetect;
        if($detect->isMobile()){
            $this->intereses_inmuebles = $query->paginate(1);
        }else{
            $this->intereses_inmuebles = $query->paginate(3);
        }

        return view('livewire.clientes.create', [
            'intereses_inmuebles' => $this->intereses_inmuebles,
        ]);
    }

    public function submit()
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

        $validatedData = $this->validate(
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
        $clientesSave = Clientes::create($validatedData);

        // Alertas de guardado exitoso
        if ($clientesSave) {
            $this->alert('success', '¡Cliente registrado correctamente!', [
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

    public function getListeners()
    {
        return [
            'confirmed'
        ];
    }

    public function confirmed()
    {
        return redirect()->route('clientes.index');
    }
}
