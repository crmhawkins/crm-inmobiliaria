<?php

namespace App\Http\Livewire\Caja;

use Livewire\Component;

class CajaComponent extends Component
{
    public $tab = "tab1";
    public $movimiento;
    public $factura;
    public $metodo_pago;

    protected $listeners = ['seleccionarProducto' => 'selectProducto'];

    public function mount()
    {
        if (!empty(session('factura'))) {
            $this->tab = "tab3";
        }

        if (!empty(session('tarea'))) {
            $this->tab = "tab3";
        }
    }

    public function render()
    {
        return view('livewire.caja.caja-component');
    }

    public function cambioTab($tab)
    {
        $this->tab = $tab;
    }
    public function selectProducto($movimiento)
    {
        $this->movimiento = $movimiento;
        $this->tab = "tab2";
    }
}
