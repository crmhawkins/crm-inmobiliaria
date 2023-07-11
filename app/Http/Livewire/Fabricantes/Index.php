<?php

namespace App\Http\Livewire\Fabricantes;

use App\Models\Fabricante;
use Livewire\Component;

class Index extends Component
{
    // public $search;
    public $fabricantes;

    public function mount()
    {
        $this->fabricantes = Fabricante::all();
    }

    public function render()
    {
        return view('livewire.fabricantes.index');
    }
    public function seleccionarProducto($fabricante)
    {
        $this->emit("seleccionarProducto", $fabricante);
    }
}
