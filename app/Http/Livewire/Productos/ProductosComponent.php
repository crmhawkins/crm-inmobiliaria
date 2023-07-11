<?php

namespace App\Http\Livewire\Productos;

use Livewire\Component;

class ProductosComponent extends Component
{

    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab1";

    public $producto;

    public function render()
    {
        return view('livewire.productos.productos-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($producto){
        $this->tab = "tab2";
        $this->producto = $producto;
    }

}

