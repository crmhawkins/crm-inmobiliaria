<?php

namespace App\Http\Livewire\Clients;

use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];

    public $tab = "tab1";
    public $cliente;
    public function render()
    {
        return view('livewire.clients.tabs-component');
    }

    public function cambioTab($tab)
    {
        $this->tab = $tab;
    }
    public function selectProducto($cliente)
    {
        $this->cliente = $cliente;
        $this->tab = "tab2";
    }
}
