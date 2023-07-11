<?php

namespace App\Http\Livewire\Fabricantes;

use Livewire\Component;

class Fabricantes extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab1";

    public $fabricante;

    public function boot()
    {
        $this->emit('contentLoaded');
    }
    public function render()
    {
        return view('livewire.fabricantes.fabricantes');
    }

    public function cambioTab($tab)
    {
        $this->emit('contentLoaded');
        $this->tab = $tab;
    }

    public function selectProducto($fabricante)
    {
        $this->tab = "tab2";
        $this->fabricante = $fabricante;
    }
}
