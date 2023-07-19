<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Inmuebles;
use App\Models\Caracteristicas;
use App\Models\Clientes;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $inmuebles;
    public $acordeon_activo;
    public $caracteristicas;
    public $clientes;
    public $cliente_correo;
    public $otras_caracteristicasArray = [];

    public $titulo;
    public $descripcion;
    public $disponibilidad;
    public $estado;
    public $habitaciones_min;
    public $habitaciones_max;
    public $banos_min;
    public $banos_max;
    public $m2_min;
    public $m2_max;
    public $ubicacion;

    public function mount()
    {
        $this->caracteristicas = Caracteristicas::all();
        $this->clientes = Clientes::all();
    }


    public function render()
    {
        $query = Inmuebles::query();


        if ($this->titulo != null && $this->titulo != "") {
            $query->where('titulo', 'LIKE', '%' . $this->titulo . '%');
        }

        if ($this->descripcion != null && $this->descripcion != "") {
            $query->where('descripcion', 'LIKE', '%' . $this->descripcion . '%');
        }

        if ($this->disponibilidad != null && $this->disponibilidad != "") {
            $query->where('disponibilidad', $this->disponibilidad);
        }

        if (!empty($this->otras_caracteristicasArray)) {
            foreach ($this->otras_caracteristicasArray as $caracteristica) {
                $query->whereJsonContains('otras_caracteristicas', strval($caracteristica));
            }
        }

        if ($this->estado != null && $this->estado != "") {
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

        if ($this->ubicacion != null && $this->ubicacion != "") {
            $query->where('ubicacion', 'LIKE', '%' . $this->ubicacion . '%');
        }

        $this->inmuebles = $query->paginate(5);

        return view('livewire.inmuebles.index', [
            'inmuebles' => $this->inmuebles,
        ]);
    }

    public function setActiveInmueble($acordeon_activo)
    {
        $this->acordeon_activo = $acordeon_activo;
    }
}
