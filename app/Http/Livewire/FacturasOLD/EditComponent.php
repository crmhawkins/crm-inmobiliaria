<?php

namespace App\Http\Livewire\Facturas;

use Livewire\Component;
use App\Models\Facturas;
use App\Models\Clients;
use App\Models\ConceptosFactura;
use App\Models\Productos;
use App\Models\Settings;
use Illuminate\Http\Request;
use PDF;

use Jantinnerezo\LivewireAlert\LivewireAlert;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $tipoCliente;

    public $id_cliente;

    
    public $indentificador;
    
    public $nameEmpresa;
    public $taxNumber;

    public $nameCliente;
    public $firstSurname;
    public $lastSurname;
    public $dni;
    public $numeroFactura;
    public $serie;
    public $fecha;
    public $descuentoTotal;
    public $total;
    public $conceptos = [];
    public $conceptosQuery;

    public $cantidad;
    public $descuento;
    public $precio;
    public $iva;



    public function mount() {
        $facturaQuery = Facturas::find($this->identificador);
        $clienteQuery = Clients::where('id',$facturaQuery->id_cliente)->first();
        $conceptosQuery = ConceptosFactura::where('id_factura', $facturaQuery->id)->get();
        $this->tipoCliente = $clienteQuery->tipoCliente;
        $this->particular = $clienteQuery;
        if ($this->tipoCliente == 1) {
            $this->nameCliente = $clienteQuery->nameCliente;
            $this->firstSurname = $clienteQuery->firstSurname;
            $this->lastSurname = $clienteQuery->lastSurname;
            $this->dni = $clienteQuery->dni;
            $this->numeroFactura = $facturaQuery->numeroFactura;
            $this->serie = $facturaQuery->serie;
            $this->fecha = $facturaQuery->fecha;
            $this->descuentoTotal = $facturaQuery->descuento;
            $this->total = $facturaQuery->total;
        }else{
            $this->nameEmpresa = $clienteQuery->nameEmpresa;
            $this->taxNumber = $clienteQuery->taxNumber;
            $this->numeroFactura = $facturaQuery->numeroFactura;
            $this->serie = $facturaQuery->serie;
            $this->fecha = $facturaQuery->fecha;
            $this->descuentoTotal = $facturaQuery->descuento;
            $this->total = $facturaQuery->total;
        }
        foreach ($conceptosQuery as $key => $concept) {
            $productosQuery = Productos::where('id', $concept->id_producto)->first();
            $concept['producto'] = $productosQuery;
        }
        array_push($this->conceptos, $conceptosQuery);
        


 
    }

    public function render()
    {
        return view('livewire.facturas.edit-component');
    }
}
