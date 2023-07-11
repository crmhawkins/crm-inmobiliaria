<?php

namespace App\Http\Livewire\Trabajadores;

use Livewire\Component;

class TrabajadoresComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab1";

    public $user;

    public function boot()
    {
        $this->emit('contentLoaded');
    }
    public function render()
    {
        return view('livewire.trabajadores.trabajadores-component');
    }

    public function cambioTab($tab)
    {
        $this->emit('contentLoaded');
        $this->tab = $tab;
    }

    public function selectProducto($user)
    {
        $this->tab = "tab2";
        $this->user = $user;
    }
}
