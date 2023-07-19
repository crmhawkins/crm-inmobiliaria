<?php

namespace App\Http\Livewire\Agenda;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TabsComponent extends Component
{
    protected $listeners = ['seleccionarProducto' => 'selectProducto'];
    public $tab = "tab3";
    public $evento;

    public function render()
    {
        return view('livewire.agenda.tabs-component');
    }

    public function cambioTab($tab)
    {
        $this->tab = $tab;
    }

    public function selectProducto($evento)
    {
        if (
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === 1 ||
            request()->session()->get('inmobiliaria') == 'sayco' && Auth::user()->inmobiliaria === null ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === 0 ||
            request()->session()->get('inmobiliaria') == 'sancer' && Auth::user()->inmobiliaria === null
        ) {
            $this->evento = $evento;
            $this->tab = "tab2";
        } else {
        }
    }
}
