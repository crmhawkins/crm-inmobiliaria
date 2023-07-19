<?php

namespace App\Http\Livewire\Inmuebles;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab3";
    public $inmueble;

    public function render()
    {
        return view('livewire.inmuebles.tabs-component');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
    }

    public function selectProducto($inmueble)
    {
        if (
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1 ||
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0 ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null
        ) {
            $this->inmueble = $inmueble;
            $this->tab = "tab2";
        } else {
        }
    }
}
