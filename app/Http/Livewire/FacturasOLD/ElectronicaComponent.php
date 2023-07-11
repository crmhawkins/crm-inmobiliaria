<?php

namespace App\Http\Livewire\Facturas;

use Livewire\Component;
use josemmo\Facturae\Facturae;
use josemmo\Facturae\FacturaeParty;
use josemmo\Facturae\FacturaeItem;
use josemmo\Facturae\Common\FacturaeSigner;
use App\Models\Settings;
use App\Models\Facturas;
use App\Models\ConceptosFactura;
use App\Models\Productos;
use App\Models\Clients;


use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Carbon\Carbon;

class ElectronicaComponent extends Component
{   
    public $identificador;

    public function mount(){

        $empresa = Settings::all();
        $factura = Facturas::where('id', $this->identificador)->first();
        $cliente = Clients::where('id', $factura->id_cliente)->first();
        $conceptos = ConceptosFactura::where('id_factura', $factura->id)->get();

        foreach ($conceptos as $key => $concepto) {
            $producto = Productos::where('id', $concepto->id_producto)->first();
            $concepto['producto'] = $producto;
        }



        $fac = new Facturae();

        // Asignamos el número EMP2017120003 a la factura
        // Nótese que Facturae debe recibir el lote y el
        // número separados
        $fac->setNumber($factura->numeroFactura,$factura->serie);

        // Asignamos la fecha
        $fecha = Carbon::createFromDate($factura->fecha)->format('Y-m-d');
        $fac->setIssueDate($fecha);

        // Incluimos los datos del vendedor
        $fac->setSeller(new FacturaeParty([
            "taxNumber" => $empresa[0]->taxNumber,
            "name"      => $empresa[0]->name,
            "address"   => $empresa[0]->adress,
            "postCode"  => $empresa[0]->postCode,
            "town"      => $empresa[0]->ciudad,
            "province"  => $empresa[0]->province
        ]));

        // Incluimos los datos del comprador,
        // con finos demostrativos el comprador será
        // una persona física en vez de una empresa
        if($cliente->tipoCliente == 1){
            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => false,       // Importante!
                "taxNumber"     => $cliente->dni,
                "name"          => $cliente->nameCliente,
                "firstSurname"  => $cliente->firstSurname,
                "lastSurname"   => $cliente->lastSurname,
                "address"       => $cliente->adressCliente,
                "postCode"      => $cliente->postCodeCliente,
                "town"          => $cliente->ciudadCliente,
                "province"      => $cliente->provinceCliente
            ]));
        }else {
            $fac->setBuyer(new FacturaeParty([
                "isLegalEntity" => true,       // Importante!
                "taxNumber"     => $cliente->taxNumber,
                "name"          => $cliente->nameEmpresa,
                "address"       => $cliente->address,
                "postCode"      => $cliente->postCode,
                "town"          => $cliente->ciudad,
                "province"      => $cliente->province,
            ]));
        }
        foreach ($conceptos as $key => $concepto) {
            if ($concepto->descuento > 0) {
                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->producto->cod_producto,
                    "name" => $concepto->producto->nombre,
                    "unitPriceWithoutTax" => $concepto->precio,
                    "quantity" => $concepto->cantidad,
                    // NOTA: estos descuentos y cargos se
                    // aplican sobre la base imponible
                    "discounts" => [
                        //   ["reason" => "Descuento del 20%", "rate" => 20],
                          ["reason" => "Descuento", "amount" => $concepto->descuento]
                    ],
                        # code.
                    // "charges" => [
                    //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
                    // ]
                    "taxes" => [Facturae::TAX_IVA => $concepto->iva]
                ]));       
            }else {
                $fac->addItem(new FacturaeItem([
                    "articleCode" => $concepto->producto->cod_producto,
                    "name" => $concepto->producto->nombre,
                    "unitPriceWithoutTax" => $concepto->precio,
                    "quantity" => $concepto->cantidad,
                    // NOTA: estos descuentos y cargos se
                    // aplican sobre la base imponible
                    // "discounts" => [
                    //     //   ["reason" => "Descuento del 20%", "rate" => 20],
                    //       ["reason" => "Descuento", "amount" => $concepto->descuento]
                    // ],
                        # code.
                    // "charges" => [
                    //   ["reason" => "Recargo del 1,30%", "rate" => 1.3]
                    // ]
                    "taxes" => [Facturae::TAX_IVA => $concepto->iva]
                ]));
            }
            
        }
        



        // $fac->addDiscount('Descuento ', 20);
        // Firmamos la factura con el certificado
        $encryptedStore = file_get_contents("ipoint.pfx");
        $fac->sign($encryptedStore, null, "06102023");

        // ... y exportarlo a un archivo
        $fac->export($factura->numeroFactura.$factura->serie.".xsig");

        $myFile = public_path($factura->numeroFactura.$factura->serie.".xsig");
        // dd($myFile);
        $headers = 'Content-Type: application/xsig';
        // return response()->download(public_path($factura->numeroFactura.$factura->serie.".xsig"));
        return Storage::disk('public_local')->download($factura->numeroFactura.$factura->serie.".xsig");
        return response()->download($myFile, $headers);
        // return  Storage::disk('local')->download($factura->numeroFactura.$factura->serie.".xsig");
        


        session()->flash('message', 'Users Created Successfully.');
    }

    public function render()
    {
        return view('livewire.facturas.electronica-component');
    }
}
