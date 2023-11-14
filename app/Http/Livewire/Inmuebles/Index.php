<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Inmuebles;
use App\Models\Caracteristicas;
use App\Models\TipoVivienda;
use App\Models\Clientes;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Mail;

class Index extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';
    public $inmuebles;
    public $acordeon_activo;
    public $caracteristicas;
    public $clientes;
    public $cliente_id = 1;
    public $imagenes_correo = [];
    public $otras_caracteristicasArray = [];

    public $m2_max;
    public $opcionesPrecio;
    public $opcionesTamano;
    public $estadoSeleccionados = [];
    public $habitacionesSeleccionadas = [];
    public $banosSeleccionados = [];
    public $m2_min;
    public $valor_min;
    public $valor_max;
    public $ubicacion;
    public $tipos_vivienda;
    public $tipos_seleccionados = [];
    public $disponibilidad_seleccionados = [];
    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->opcionesPrecio = $this->opcionesDePrecio();
        $this->opcionesTamano = $this->opcionesDeTamano();
        $this->caracteristicas = Caracteristicas::all();
        $this->clientes = Clientes::all();
        $this->inmuebles = Inmuebles::all();
        $this->tipos_vivienda = TipoVivienda::all();
    }

    private function opcionesDePrecio()
    {
        return range(20000, 1000000, 20000); // Asegúrate de que esto retorne un array
    }

    private function opcionesDeTamano()
    {
        return range(20, 1000, 20); // Asegúrate de que esto retorne un array
    }
    public function updateInmuebles()
    {
        $query = Inmuebles::query();

        if (!empty($this->habitacionesSeleccionadas)) {
            if (in_array('4', $this->habitacionesSeleccionadas)) {
                $query->whereIn('habitaciones', $this->habitacionesSeleccionadas)->orWhere('habitaciones', '>', '4');
            } else {
                $query->whereIn('habitaciones', $this->habitacionesSeleccionadas);
            }
        }
        if (!empty($this->banosSeleccionados)) {
            if (in_array('3', $this->banosSeleccionados)) {
                $query->whereIn('banos', $this->banosSeleccionados)->orWhere('banos', '>', '3');
            } else {
                $query->whereIn('banos', $this->banosSeleccionados);
            }
        }

        if (!empty($this->estadoSeleccionados)) {
            $query->whereIn('estado', $this->estadoSeleccionados);
        }
        if (!empty($this->tipos_seleccionados)) {
            $query->whereIn('tipo_vivienda_id', $this->tipos_seleccionados);
        }
        if (!empty($this->disponibilidad_seleccionados)) {
            $query->whereIn('disponibilidad', $this->disponibilidad_seleccionados);
        }
        if (!empty($this->otras_caracteristicasArray)) {
            $query->whereJsonContains('otras_caracteristicas', $this->otras_caracteristicasArray);
        }
        if ($this->valor_min != null & $this->valor_max != null) {
            $query->whereBetween('valor_referencia', [floatval($this->valor_min), floatval($this->valor_max)]);
        }
        if ($this->m2_min != null & $this->m2_max != null) {
            $query->whereBetween('m2', [$this->m2_min, $this->m2_max]);
        }
        if ($this->ubicacion != "" && $this->ubicacion != null) {
            $query->where('ubicacion', 'LIKE',"%". $this->ubicacion ."%")->orWhere('cod_postal', 'LIKE',"%". $this->ubicacion ."%");
        }
        $this->inmuebles = $query->get();

        $this->emit('refreshComponent');
    }
    public function render()
    {
        return view('livewire.inmuebles.index');
    }

    public function setActiveInmueble($acordeon_activo)
    {
        $this->acordeon_activo = $acordeon_activo;
        $this->cliente_id = 1;
        $this->imagenes_correo = [];
    }

    public function updated($propertyName)
    {
        if (
            $propertyName == 'habitacionesSeleccionadas' ||
            $propertyName == 'banosSeleccionados' ||
            $propertyName == 'otras_caracteristicasArray' ||
            $propertyName == 'valor_min' ||
            $propertyName == 'valor_max' ||
            $propertyName == 'm2_min' ||
            $propertyName == 'm2_max' ||
            $propertyName == 'tipos_seleccionados' ||
            $propertyName == 'disponibilidad_seleccionados' ||
            $propertyName == 'ubicacion'
        ) {
            $this->updateInmuebles();
        }
    }
}
