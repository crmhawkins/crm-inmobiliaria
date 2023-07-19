<?php

namespace App\Http\Livewire\Tipovivienda;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab3";
    public $tipo;

    public function render()
    {
        return view('livewire.tipovivienda.tabs-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($tipo)
    {
        if (
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1 ||
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0 ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null
        ) {
            $this->tipo = $tipo;
            $this->tab = "tab2";
        } else {
        }
    }
}
