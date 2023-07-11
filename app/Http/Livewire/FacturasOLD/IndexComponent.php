<?php

namespace App\Http\Livewire\Facturas;
use App\Models\Facturas;
use App\Models\Clients;
use PDF;
use Livewire\Component;

class IndexComponent extends Component
{
    public $facturas;

    public function mount()
    {
        $this->facturas = Facturas::all();
        foreach ($this->facturas as $key => $fac) {
            $cliente = Clients::where('id', $fac->id_cliente)->first();
            $fac['cliente'] = $cliente;
        }

    }

    public function render()
    {
        return view('livewire.facturas.index-component');
    }

    public function pdf()
    {
        // Se llama a los productos
        $this->facturas = Facturas::all();

        // Se llama a la vista Liveware y se le pasa los productos. En la vista se epecifican los estilos del PDF
        $pdf = PDF::loadView('livewire.facturas.pdf-component', ['facturas'=>$this->facturas]);
        return $pdf->stream();

    }

}
