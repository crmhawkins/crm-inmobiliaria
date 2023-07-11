<?php

namespace App\Http\Livewire\Productoscategories;

use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab1";

    public $categoria;
    public function render()
    {
        return view('livewire.productos_categories.tabs-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($ecotasa)
    {
        $this->categoria = $ecotasa;
        $this->tab = "tab2";
    }
}
