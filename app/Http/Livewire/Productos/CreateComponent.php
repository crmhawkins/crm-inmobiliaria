<?php

namespace App\Http\Livewire\Productos;

use App\Models\Almacen;
use App\Models\Ecotasa;
use App\Models\Fabricante;
use App\Models\ListaAlmacen;
use App\Models\Neumatico;
use App\Models\Productos;
use App\Models\ProductosCategories;
use App\Models\Proveedores;
use App\Models\TipoProducto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;


    public $productos;
    public $categorias;
    public $tipos_producto;
    public $almacenes;
    public $neumaticos;
    public $proveedores;
    public $tasas;



    public $cod_producto;
    public $proveedor;

    public $descripcion;
    public $tipo_producto;
    public $ecotasa;
    public $fabricante;
    public $categoria_id;
    public $precio_baremo;
    public $descuento;
    public $coeficiente = 1.6;
    public $precio_costoNeto;
    public $precio_venta;

    public $articulo_id;
    public $resistencia_rodadura;
    public $agarre_mojado;
    public $emision_ruido;
    public $uso;
    public $ancho;
    public $serie;
    public $fabricantes;

    public $llanta;
    public $indice_carga;
    public $codigo_velocidad;


    public $existencias;
    public $mueve_existencias = false;

    public $nombre;

    public $existencias_almacenes;


    public function mount()
    {
        $this->tipos_producto = TipoProducto::all();
        $this->categorias = ProductosCategories::all();
        $this->neumaticos = Neumatico::all();
        $this->almacenes = ListaAlmacen::all();
        $this->fabricantes = Fabricante::all();
        $this->proveedores = Proveedores::all();
        $this->tasas = Ecotasa::all();
    }

    public function render()
    {
        return view('livewire.productos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        $validatedData = $this->validate([
            'cod_producto' => 'required | unique',
            'descripcion'  => 'required',
            'tipo_producto' => 'required',
            'fabricante' => 'required',
            'proveedor' => 'nullable',
            'coeficiente' => 'nullable',
            'categoria_id' => 'nullable',
            'precio_baremo' => 'required',
            'descuento' => 'required',
            'ecotasa' => 'nullable',
            'precio_costoNeto' => 'required',
            'precio_venta' => 'required',
            'mueve_existencias' => 'required',
        ], [
            'cod_producto.required' => 'required',
            'descripcion.required'  => 'required',
            'tipo_producto.required' => 'required',
            'fabricante.required' => 'required',
            'precio_baremo.required' => 'required|numeric',
            'descuento.required' => 'required|numeric',
            'precio_costoNeto.required' => 'required|numeric',
            'precio_venta.required' => 'required|numeric',
            'mueve_existencias.required' => 'required'
        ]);



        // Guardar datos validados
        $productosSave = Productos::create($validatedData);

        if ($productosSave) {
            if ($this->tipo_producto == 2) {
                $this->articulo_id = $this->cod_producto;
                $validateData2 = $this->validate([
                    'articulo_id' => 'required',
                    'resistencia_rodadura' => 'required',
                    'agarre_mojado' => 'required',
                    'emision_ruido' => 'required',
                    'ancho' => 'required',
                    'serie' => 'required',
                    'uso' => 'nullable',
                    'llanta' => 'required',
                    'indice_carga' => 'required',
                    'codigo_velocidad' => 'required',
                ]);

                $neumaticosSave = Neumatico::create($validateData2);
            }

            if ($this->mueve_existencias == true) {
                $this->nombre = ListaAlmacen::where('id', $this->nombre)->first()->nombre;
                $this->existencias_almacenes = $this->existencias;
                $validateData3 = $this->validate([
                    'nombre' => 'required',
                    'cod_producto' => 'required',
                    'existencias' => 'required',
                    'existencias_almacenes' =>  'required',
                ]);

                $almacenSave = Almacen::create($validateData3);
            }
        }




        // Alertas de guardado exitoso
        if ($productosSave) {
            $this->alert('success', '¡Producto registrado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del producto!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
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
        return redirect()->route('productos.index');
    }

    public function precio_costo()
    {
        if ($this->precio_baremo != null) {
            if ($this->ecotasa != null) {
                $this->precio_costoNeto = $this->precio_baremo - $this->descuento;
                $this->precio_venta = ($this->precio_costoNeto * $this->coeficiente) + Ecotasa::find($this->ecotasa)->valor;
            } else {
                $this->precio_costoNeto = $this->precio_baremo - $this->descuento;
                $this->precio_venta = ($this->precio_costoNeto * $this->coeficiente);
            }
        }
    }
}
