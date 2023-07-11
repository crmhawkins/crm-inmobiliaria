<?php

namespace App\Http\Livewire\Proveedores;

use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];

    public $tab = "tab1";
    public $proveedor;
    public function render()
    {
        return view('livewire.proveedores.tabs-component');
    }

    public function cambioTab($tab)
    {
        $this->tab = $tab;
    }
    public function selectProducto($proveedor)
    {
        $this->proveedor = $proveedor;
        $this->tab = "tab2";
    }
}
