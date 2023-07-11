<?php

namespace App\Http\Livewire\Productos;

use App\Models\Fabricante;
use App\Models\Productos;
use App\Models\ProductosCategories;
use App\Models\TipoProducto;
use App\Models\Almacen;
use App\Models\ListaAlmacen;
use App\Models\Ecotasa;
use App\Models\Neumatico;
use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $productos;
    public $categorias;
    public $tipos_producto;
    public $almacenes;
    public $neumaticos;
    public $proveedores;
    public $fabricantes;
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
    public $llanta;
    public $indice_carga;
    public $codigo_velocidad;
    public $existencias;
    public $mueve_existencias;

    public $almacen = 1;
    public $nombre;


    public $existencias_almacenes;
    public $existencias_depositos;



    public function mount()
    {
        $this->almacenes = ListaAlmacen::all();
        $this->categorias = ProductosCategories::all();
        $this->tipos_producto = TipoProducto::all();
        $this->tasas = Ecotasa::all();
        $this->proveedores = Proveedores::all();
        $this->fabricantes = Fabricante::all();



        $product = Productos::find($this->identificador);

        $this->cod_producto = $product->cod_producto;
        $this->descripcion = $product->descripcion;
        $this->tipo_producto = $product->tipo_producto;
        $this->ecotasa = $product->ecotasa;
        $this->proveedor = $product->proveedor;

        $this->fabricante = $product->fabricante;
        $this->categoria_id = $product->categoria_id;
        $this->precio_baremo = $product->precio_baremo;
        $this->descuento = $product->descuento;
        $this->coeficiente = $product->coeficiente;
        $this->precio_costoNeto = $product->precio_costoNeto;
        $this->precio_venta = $product->precio_venta;
        if ($product->mueve_existencias == 1) {
            $this->mueve_existencias = true;
        } else {
            $this->mueve_existencias = false;
        }

        if ($product->tipo_producto == 2) {
            $neumatico = Neumatico::where('articulo_id', $product->cod_producto)->first();
            $this->articulo_id = $neumatico->articulo_id;
            $this->resistencia_rodadura = $neumatico->resistencia_rodadura;
            $this->agarre_mojado = $neumatico->agarre_mojado;
            $this->emision_ruido = $neumatico->emision_ruido;
            $this->uso = $neumatico->uso;
            $this->ancho = $neumatico->ancho;
            $this->serie = $neumatico->serie;
            $this->llanta = $neumatico->llanta;
            $this->indice_carga = $neumatico->indice_carga;
            $this->codigo_velocidad = $neumatico->codigo_velocidad;
        }



        if ($product->mueve_existencias == true) {
            $this->almacen = ListaAlmacen::where('id', $product->almacen)->first()->id;
            $this->nombre = ListaAlmacen::where('id', $product->almacen)->first()->nombre;
            $almacen = Almacen::where('nombre', $this->nombre)->where('cod_producto', $this->cod_producto)->first();
            $this->existencias = $almacen->existencias;
            $this->existencias_almacenes = $almacen->existencias_almacenes;
            $this->existencias_depositos = $almacen->existencias_depositos;
        }
    }

    public function render()
    {
        return view('livewire.productos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'cod_producto' => 'required',
            'descripcion'  => 'required',
            'tipo_producto' => 'required',
            'fabricante' => 'required',
            'proveedor' => 'required',
            'coeficiente' => 'nullable',
            'categoria_id' => 'nullable',
            'precio_baremo' => 'required',
            'descuento' => 'required',
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
            'mueve_existencias.required' => 'required',
        ]);

        // Encuentra el producto identificado
        $product = Productos::find($this->identificador);

        // Guardar datos validados
        $productSave = $product->update([
            'cod_producto' => $this->cod_producto,
            'descripcion' => $this->descripcion,
            'tipo_producto' => $this->tipo_producto,
            'fabricante' => $this->fabricante,
            'coeficiente' => $this->coeficiente,
            'categoria_id' => $this->categoria_id,
            'precio_baremo' => $this->precio_baremo,
            'descuento' => $this->descuento,
            'precio_costoNeto' => $this->precio_costoNeto,
            'precio_venta' => $this->precio_venta,
            'mueve_existencias' => $this->mueve_existencias,
        ]);

        if ($productSave) {

            if ($this->tipo_producto == 2) {
                $neumatico = Neumatico::where('articulo_id', $this->cod_producto)->first();

                $neumaticoSave = $neumatico->update([
                    'articulo_id' => $this->articulo_id,
                    'resistencia_rodadura' => $this->resistencia_rodadura,
                    'agarre_mojado' => $this->agarre_mojado,
                    'emision_ruido' => $this->emision_ruido,
                    'ancho' => $this->ancho,
                    'serie' => $this->serie,
                    'uso' => $this->uso,
                    'llanta' => $this->llanta,
                    'indice_carga' => $this->indice_carga,
                    'codigo_velocidad' => $this->codigo_velocidad,
                ]);
            }

            if ($this->mueve_existencias != false) {
                if (Almacen::where('nombre', $this->nombre)->where('cod_producto', $this->cod_producto)->first() != null) {
                    $almacen = Almacen::where('nombre', $this->nombre)->where('cod_producto', $this->cod_producto)->first();
                    $almacenSave = $almacen->update([
                        'nombre' => $this->nombre,
                        'existencias' => $this->existencias,
                        'existencias_almacenes' => $this->existencias,
                        'existencias_depositos' => $this->existencias_depositos,
                    ]);
                } else {
                    $this->alert('info', 'Nueva información de almacén creada');
                    $nuevoAlmacen = Almacen::create($this->validate([
                        'nombre' => 'required',
                        'cod_producto' => 'required',
                        'existencias' => 'required',
                        'existencias_almacenes' => 'required',
                        'existencias_depositos' => 'required'
                    ]));
                }
            }

            $this->alert('success', '¡Producto actualizado correctamente!', [
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

        session()->flash('message', 'Product updated successfully.');

        $this->emit('productUpdated');
    }

    // Elimina el producto
    public function destroy()
    {
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el producto? No hay vuelta atrás', [
            'position' => 'center',
            'timer' => 3000,
            'toast' => false,
            'showConfirmButton' => true,
            'onConfirmed' => 'confirmDelete',
            'confirmButtonText' => 'Sí',
            'showDenyButton' => true,
            'denyButtonText' => 'No',
            'timerProgressBar' => true,
        ]);
    }

    // Función para cuando se llama a la alerta
    public function getListeners()
    {
        return [
            'confirmed',
            'confirmDelete'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('productos.index');
    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $product = Productos::find($this->identificador);
        $product->delete();
        $neumatico = Neumatico::where('articulo_id', $product->cod_producto)->first();
        $neumatico->delete();
        $almacen = Almacen::where('cod_producto', $this->cod_producto);
        $almacen->delete();
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

    public function modificarExistencias()
    {
        if ($this->existencias_almacenes > 0) {
            $this->existencias = $this->existencias_almacenes + $this->existencias_depositos;
        } else {
            $this->existencias = $this->existencias_depositos;
        }
    }

    public function comprobarAlmacen()
    {
        if (Almacen::where('nombre', $this->almacen)->where('cod_producto', $this->cod_producto)->first() != null) {
            $almacen = Almacen::where('nombre', $this->almacen)->where('cod_producto', $this->cod_producto)->first();
            $this->nombre = $almacen->nombre;
            $this->existencias = $almacen->existencias;
            $this->existencias_almacenes = $almacen->existencias_almacenes;
            $this->existencias_depositos = $almacen->existencias_depositos;
        } else {
            $this->nombre = $this->almacen;
            $this->existencias = 0;
            $this->existencias_almacenes = 0;
            $this->existencias_depositos = 0;
        }
    }
}
