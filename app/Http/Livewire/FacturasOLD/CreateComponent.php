<?php

namespace App\Http\Livewire\Facturas;

use Livewire\Component;
use App\Models\Clients;
use App\Models\Facturas;
use App\Models\Iva;
use App\Models\Productos;
use App\Models\ConceptosFactura;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;


class CreateComponent extends Component
{
    use LivewireAlert;

    public $tipoCliente;
    public $empresa;
    public $particular;

    public $empresaCliente;
    public $id_cliente = 0;

    public $numeroFactura;
    public $serie;
    public $descuentoTotal;
    public $fecha;

    public $productos;    
    public $response;
    public $indentificador;

    public $nameProducto;
    public $cantidad;
    public $descuento;
    public $precio;
    public $iva;



    public $updateMode = false;
    public $inputs = [];
    public $inputsProductos = [];
    public $i = 0;

    public $data;

    public $anio;
    public $mes;
    
    public function mount(){
        $this->particular = Clients::where('tipoCliente', 1)->get();
        $this->empresa = Clients::where('tipoCliente', 2)->get();
        $this->anio = Carbon::now()->format('Y');
        $this->mes = Carbon::now()->format('m');
        $this->productos = Productos::all();
        
    }
    public function render()
    {
        if ($this->tipoCliente == 1) {
            $facturas = Facturas::all()->last();
            if ($facturas == null) {
                $this->numeroFactura = 'PAR'. $this->anio . $this->mes;
                $this->serie = '000001';
            } else {
                $this->numeroFactura = 'PAR'. $this->anio . $this->mes;
                $this->serie = str_pad($facturas->serie+1, 6, '0', STR_PAD_LEFT);
            }
        } else {
            $facturas = Facturas::all()->last();
            if ($facturas == null) {
                $this->numeroFactura = 'EMP'. $this->anio . $this->mes;
                $this->serie = '000001';
            } else {
                $this->numeroFactura = 'EMP'. $this->anio . $this->mes;

                $this->serie = str_pad($facturas->serie+1, 6, '0', STR_PAD_LEFT);
            }
        }
        
        return view('livewire.facturas.create-component');
    }

    public function submitCliente(){
        // Validamos los datos recibidos
        $validatedData = $this->validate([
            'id_cliente' => 'required',
            'numeroFactura' => 'required',
            'serie' => 'required',
            'fecha' => 'required', 
            'descuentoTotal' => 'required', 
            'nameProducto.0' => 'required',
            'cantidad.0' => 'required',
            'precio.0' => 'required',
            'iva.0' => 'required',
            'descuento.0' => 'required',
            'nameProducto.*' => 'required',
            'cantidad.*' => 'required',
            'precio.*' => 'required',
            'iva.*' => 'required',
            'descuento.*' => 'required',

        ]);

        $totalSuma = 0;
        $totalSinIva = 0;
        $arrayPrueba = [];

        foreach($this->nameProducto as $index => $concepto){

            $producto = Productos::where('nombre', $concepto)->first();
            $ivaDelProducto = Iva::where('id', $producto->id_tipo_iva)->first();
            // dd($ivaDelProducto);

            $canti = $this->cantidad[$index];
            // dd($canti);

            if($this->descuento[$index] > 0){
                // Pasamos el iva a decimal
                $ivanItem =  $producto->iva/100;
                // Pasamos el descuento a decimalñ
                $descuentoItem = $this->descuento[$index] / 100;
                // Calculamos el precio con descuento
                $precioDescontado = $producto->precio - ($producto->precio * $descuentoItem);
                // Calculamos el total del precio con iva incluido
                $precioConIva = $precioDescontado + ($precioDescontado * $ivanItem);
                // Total multiplicando precio por cantidad
                $totalSuma = $precioConIva * $canti;
            }
            

            $ivanItem =  $ivaDelProducto->iva/100;
            $precioConIva = $producto->precio + ($producto->precio * $ivanItem);
            $precioTotal = $precioConIva * $canti;
            array_push($arrayPrueba, $precioTotal);
            $totalSuma = $totalSuma + $precioTotal;
        }
        // dd($arrayPrueba);
        $input  = str_replace('/','-',$this->fecha);
        $format = 'Y-m-d';
        // dd($input );
        $date = Carbon::parse($input)->format('Y-m-d');

        $dataCrearFactura = [
            'serie' => $validatedData['serie'],
            'numeroFactura' => $validatedData['numeroFactura'],
            'fecha' => $date,
            'id_cliente' => $this->id_cliente,
            'total' => $totalSuma,
            'descuento' => $this->descuentoTotal,

        ];
        // dd($dataCrearFactura);
        

        // $validatedData->fecha = 
        // $validatedData['total'] = $totalSuma;
        // $validatedData->numeroFactura = intval($validatedData->numeroFactura);
        // Creamos el cliente en la DB
        $clienteSave = Facturas::create( $dataCrearFactura );
        // $sumaConceptosSinIva = 0;
        // $sumaConceptosConIva = 0;

        // dd($clienteSave);
        foreach($this->nameProducto as $index => $concepto){

            $producto = Productos::where('nombre', $concepto)->first();
            $ivaDelProducto = Iva::where('id', $producto->id_tipo_iva)->first();

            $canti = $this->cantidad[$index];
            $ivanItem = $ivaDelProducto->iva/100;

            if($this->descuento[$index] > 0){
                // Pasamos el iva a decimal
                // Pasamos el descuento a decimalñ
                $descuentoItem = $this->descuento[$index] / 100;
                // Calculamos el precio con descuento
                $precioDescontado = $producto->precio - ($producto->precio * $descuentoItem);
                // Calculamos el total del precio con iva incluido
                $precioConIva = $precioDescontado + ($precioDescontado * $ivanItem);
                // Total multiplicando precio por cantidad
                $sumaConceptosConIva = $precioConIva * $canti;
                $sumaConceptosSinIva = $precioDescontado * $canti;

            }

            $precioConIva = $producto->precio + ($producto->precio * $ivanItem);
            $sumaConceptosConIva = $precioConIva * $canti;
            $sumaConceptosSinIva = $producto->precio * $canti;
            
            $dataConcepto = [
                'id_factura' => $clienteSave->id,
                'id_producto' => $producto->id,
                'precio' => $producto->precio,
                'cantidad' => $canti,
                'iva' => $ivaDelProducto->iva,
                'descuento' => $this->descuento[$index],
                'total' => $sumaConceptosConIva,
                'total_sin_iva' => $sumaConceptosSinIva
            ];

            $conceptos = ConceptosFactura::create($dataConcepto);
        }

        if ($clienteSave) {
            $this->alert('success', 'Se ha registrado correctamente la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
                ]);
        }else{
            $this->alert('error', 'No se ha podido guardar la información de la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function submit(){
        // Validamos los datos recibidos
        $validatedData = $this->validate([
            'id_cliente' => 'required',
            'numeroFactura' => 'required',
            'serie' => 'required',
            'fecha' => 'required', 
            'descuentoTotal' => 'required', 
            'nameProducto.0' => 'required',
            'cantidad.0' => 'required',
            'precio.0' => 'required',
            'iva.0' => 'required',
            'descuento.0' => 'required',
            'nameProducto.*' => 'required',
            'cantidad.*' => 'required',
            'precio.*' => 'required',
            'iva.*' => 'required',
            'descuento.*' => 'required',

        ]);

        $totalSuma = 0;
        $totalSinIva = 0;
        $arrayPrueba = [];

        foreach($this->nameProducto as $index => $concepto){

            $producto = Productos::where('nombre', $concepto)->first();
            $ivaDelProducto = Iva::where('id', $producto->id_tipo_iva)->first();
            // dd($ivaDelProducto);

            $canti = $this->cantidad[$index];
            // dd($canti);

            if($this->descuento[$index] > 0){
                // Pasamos el iva a decimal
                $ivanItem =  $producto->iva/100;
                // Pasamos el descuento a decimalñ
                $descuentoItem = $this->descuento[$index] / 100;
                // Calculamos el precio con descuento
                $precioDescontado = $producto->precio - ($producto->precio * $descuentoItem);
                // Calculamos el total del precio con iva incluido
                $precioConIva = $precioDescontado + ($precioDescontado * $ivanItem);
                // Total multiplicando precio por cantidad
                $totalSuma = $precioConIva * $canti;
            }
            

            $ivanItem =  $ivaDelProducto->iva/100;
            $precioConIva = $producto->precio + ($producto->precio * $ivanItem);
            $precioTotal = $precioConIva * $canti;
            array_push($arrayPrueba, $precioTotal);
            $totalSuma = $totalSuma + $precioTotal;
        }
        // dd($arrayPrueba);
        $input  = str_replace('/','-',$this->fecha);
        $format = 'Y-m-d';
        // dd($input );
        $date = Carbon::parse($input)->format('Y-m-d');

        $dataCrearFactura = [
            'serie' => $validatedData['serie'],
            'numeroFactura' => $validatedData['numeroFactura'],
            'fecha' => $date,
            'id_cliente' => $this->id_cliente,
            'total' => $totalSuma,
            'descuento' => $this->descuentoTotal,

        ];
        // dd($dataCrearFactura);
        

        // $validatedData->fecha = 
        // $validatedData['total'] = $totalSuma;
        // $validatedData->numeroFactura = intval($validatedData->numeroFactura);
        // Creamos el cliente en la DB
        $clienteSave = Facturas::create( $dataCrearFactura );
        // $sumaConceptosSinIva = 0;
        // $sumaConceptosConIva = 0;

        // dd($clienteSave);
        foreach($this->nameProducto as $index => $concepto){

            $producto = Productos::where('nombre', $concepto)->first();
            $ivaDelProducto = Iva::where('id', $producto->id_tipo_iva)->first();

            $canti = $this->cantidad[$index];
            $ivanItem = $ivaDelProducto->iva/100;

            if($this->descuento[$index] > 0){
                // Pasamos el iva a decimal
                // Pasamos el descuento a decimalñ
                $descuentoItem = $this->descuento[$index] / 100;
                // Calculamos el precio con descuento
                $precioDescontado = $producto->precio - ($producto->precio * $descuentoItem);
                // Calculamos el total del precio con iva incluido
                $precioConIva = $precioDescontado + ($precioDescontado * $ivanItem);
                // Total multiplicando precio por cantidad
                $sumaConceptosConIva = $precioConIva * $canti;
                $sumaConceptosSinIva = $precioDescontado * $canti;

            }

            $precioConIva = $producto->precio + ($producto->precio * $ivanItem);
            $sumaConceptosConIva = $precioConIva * $canti;
            $sumaConceptosSinIva = $producto->precio * $canti;
            
            $dataConcepto = [
                'id_factura' => $clienteSave->id,
                'id_producto' => $producto->id,
                'precio' => $producto->precio,
                'cantidad' => $canti,
                'iva' => $ivaDelProducto->iva,
                'descuento' => $this->descuento[$index],
                'total' => $sumaConceptosConIva,
                'total_sin_iva' => $sumaConceptosSinIva
            ];

            $conceptos = ConceptosFactura::create($dataConcepto);
        }

        if ($clienteSave) {
            $this->alert('success', 'Se ha registrado correctamente la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
                ]);
        }else{
            $this->alert('error', 'No se ha podido guardar la información de la empresa!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
    public function add($i)
    {

        $product = Productos::where('nombre', $this->nameProducto[ $this->i ])->first();
        $i = $i + 1;
        $this->i = $i;
        array_push($this->inputs ,$i);
        // dd($this->inputs);
    }

    public function setSomeProperty() {
        foreach ( $this->productos as $key => $item) {
            if ($item->nombre == $this->nameProducto[0]) {
                $tipoIva = Iva::where('id',$item->id_tipo_iva )->first();
               $this->precio[0] = $item->precio;

               $this->iva[0] = $tipoIva->iva;
            }
        }
    }
    public function setSomeProperty2() {
        foreach ( $this->productos as $key => $item) {
            if ($item->nombre == $this->nameProducto[$this->i]) {
                $tipoIva = Iva::where('id',$item->id_tipo_iva )->first();
               $this->precio[$this->i] = $item->precio;

               $this->iva[$this->i] = $tipoIva->iva;
            }
        }
    }

    public function remove($i)
    {
        unset($this->inputs[$i]);
    }

    private function resetInputFields(){
        $this->nameProducto = '';
        $this->cantidad = '';
    }

    public function hydrate() {
        $this->emit('select2');
    }

     // Función para cuando se llama a la alerta
     public function getListeners()
     {
         return [
             'confirmed',
         ];
     }
 
     // Función para cuando se llama a la alerta
     public function confirmed()
     {
         // Do something
         return redirect()->route('factura.index');
 
     }
}
