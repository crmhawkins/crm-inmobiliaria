<?php

namespace App\Http\Livewire\Caja;

use App\Models\Presupuesto;
use App\Models\CobroCaja;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $presupuestos;
    public $movimientos;


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->movimientos = CobroCaja::all();
    }

    public function render()
    {

        return view('livewire.caja.index-component');
    }

}
