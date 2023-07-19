<?php

namespace App\Http\Livewire\Vendedores;

use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab3";
    public $vendedor;

    public function render()
    {
        return view('livewire.vendedores.tabs-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($vendedor)
    {
        $this->vendedor = $vendedor;
        $this->tab = "tab2";
    }
}
