<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Presupuesto;
use App\Models\Facturas;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $presupuestos;
    public $facturas;


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->facturas = Facturas::all();
    }

    public function render()
    {

        return view('livewire.facturas.index-component');
    }

    public function seleccionarProducto($factura)
    {
        $this->emit("seleccionarProducto", $factura);
    }
    public function redirectToCaja($factura, $metodo_pago)
    {
        session()->flash('factura', $factura);
        session()->flash('metodo_pago', $metodo_pago);

        return redirect()->route('caja.index');
    }

}
