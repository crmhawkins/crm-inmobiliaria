<?php

namespace App\Http\Livewire\Ecotasa;

use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab1";

    public $ecotasa;

    public function render()
    {
        return view('livewire.ecotasa.tabs-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($ecotasa)
    {
        $this->ecotasa = $ecotasa;
        $this->tab = "tab3";
    }
}
