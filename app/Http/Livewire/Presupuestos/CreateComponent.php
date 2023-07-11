<?php

namespace App\Http\Livewire\Presupuestos;

use App\Models\Presupuesto;
use App\Models\Clients;
use App\Models\Reserva;
use App\Models\Trabajador;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Carbon\Carbon;
use App\Models\Productos;
use App\Models\ListaAlmacen;
use App\Models\OrdenTrabajo;
use App\Models\Almacen;
use Auth;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $servicio = "";
    public $numero_presupuesto;
    public $fecha_emision;
    public $cliente_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $estado;
    public $matricula;
    public $kilometros;
    public $trabajador_id = Auth::id(); // 0 por defecto por si no se selecciona ninguna
    public $precio = 0;
    public $observaciones = "";
    public $origen;
    public $marca;
    public $modelo;

    public $clientes;
    public $trabajadores;
    public $productos;
    public $almacenes;
    public $existencias_productos;
    public $lista = [];
    public $listaArticulos;
    public $producto_seleccionado;
    public $cantidad;
    public $orden_id;
    public $vehiculo_renting = false;


    public function mount()
    {
        $this->clientes = Clients::all(); // datos que se envian al select2
        $this->trabajadores = Trabajador::all(); // datos que se envian al select2
        $this->almacenes = ListaAlmacen::all();
        $this->existencias_productos = Almacen::all();
    }

    public function render()
    {
        if($this->servicio == ""){
            $this->productos = Productos::where('mueve_existencias', 0)->get();
        }else{
            $this->productos = Productos::where('almacen', ListaAlmacen::where('nombre', $this->servicio)->first()->id)->orWhere('mueve_existencias', 0)->get();
        }
        return view('livewire.presupuestos.create-component');
    }

    // Al hacer submit en el formulario
    public function submit()
    {
        $this->estado = "Pendiente";

        foreach ($this->lista as $pro => $cantidad) {
            if (Productos::where('id', $pro)->first()->mueve_existencias == 1) {
                $articulo = Almacen::where('cod_producto', Productos::where('id', $pro)->first()->cod_producto)->first();
                $articulo->update([
                    'existencias' => ($articulo->existencias_almacenes -= $cantidad),
                    'existencias_depositos' => ($articulo->existencias_depositos += $cantidad)
                ]);
            }
        }
        $this->listaArticulos = json_encode($this->lista);

        // Validación de datos
        $validatedData = $this->validate(
            [
                'numero_presupuesto' => 'required',
                'fecha_emision' => 'required',
                'cliente_id' => 'required',
                'matricula' => 'required',
                'kilometros' => 'required',
                'trabajador_id' => 'required',
                'listaArticulos' => 'required',
                'precio' => 'required',
                'origen' => 'required',
                'marca' => 'required',
                'servicio' => 'required',
                'modelo' => 'required',
                'vehiculo_renting' => 'required',
                'estado' => 'nullable',
                'observaciones' => 'nullable',

            ],
            // Mensajes de error
            [
                'numero_presupuesto.required' => 'El número de presupuesto es obligatorio.',
                'fecha_emision.required' => 'La fecha de emision es obligatoria.',
                'cliente_id.required' => 'El cliente es obligatorio.',
                'matricula.required' => 'La matricula del coche es obligatoria.',
                'vehiculo_renting.required' => 'Marca si el vehículo es de renting o no',
                'kilometros.required' => 'Los kilometros del coche son obligatorios',
                'trabajador_id.required' => 'El trabajador es obligatorio.',
                'precio.required' => 'El precio es obligatorio',
                'observaciones.required' => 'La observación es obligatoria',
            ]
        );

        // Guardar datos validados
        $presupuesosSave = Presupuesto::create($validatedData);
        $this->orden_id = $presupuesosSave->id;

        // Alertas de guardado exitoso
        if ($presupuesosSave) {

            foreach ($this->lista as $pro => $cantidad) {
                if (Productos::where('id', $pro)->first()->mueve_existencias == 1) {
                    $reserva = Reserva::create();
                    $reserva->cantidad = $cantidad;
                    $reserva->estado = "Pendiente";
                    $reserva->presupuesto_id = $presupuesosSave->id;
                    $reserva->producto_id = $pro;
                    $reserva->save();
                }
            }

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
            $this->alert('error', '¡No se ha podido guardar la información del presupuesto!', [
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
            'calcularPrecio',
            'listarAlmacen',
            'numeroPresupuesto',
            'añadirProducto',
            'reducir',
            'precioFinal',
            'seleccionarProducto',
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('orden-trabajo.index');
    }
    public function numeroPresupuesto()
    {
        $fecha = new Carbon($this->fecha_emision);
        $year = $fecha->year;
        $presupuestos = Presupuesto::all();
        $contador = 1;
        foreach ($presupuestos as $presupuesto) {
            $fecha2 = new Carbon($presupuesto->fecha_emision);
            $year2 = $fecha2->year;
            if ($year == $year2) {
                if ($fecha->gt($fecha2)) {
                    $contador++;
                }
            }
        }

        if ($contador < 10) {
            $this->numero_presupuesto = "0" . $contador . "/" . $year;
        } else {
            $this->numero_presupuesto = $contador . "/" . $year;
        }
    }


    public function añadirProducto()
    {
        if ($this->producto_seleccionado != null) {
            $producto = Productos::where('id', $this->producto_seleccionado)->firstOrFail();

            if ($producto->mueve_existencias == 0) {
                if (!isset($this->lista[$this->producto_seleccionado])) {
                    $this->lista[$this->producto_seleccionado] = 1;
                } else {
                    $this->alert('info', "Ya has añadido este servicio.");
                }
            } else {
                $almacen = Almacen::where('cod_producto', $producto->cod_producto)->firstOrFail();

                if ($almacen->existencias >= 1) {
                    if ($almacen->existencias >= $this->cantidad) {
                        if (isset($this->lista[$this->producto_seleccionado])) {
                            if ($this->lista[$this->producto_seleccionado] + $this->cantidad > $almacen->existencias) {
                                $this->lista[$this->producto_seleccionado] = $almacen->existencias;
                                $this->alert('warning', "¡Estás intentando añadir más allá de las existencias!");
                            } else {
                                $this->lista[$this->producto_seleccionado] += $this->cantidad;
                            }
                        } else {
                            $this->lista[$this->producto_seleccionado] = $this->cantidad;
                        }
                    } else {
                        $this->alert('warning', "¡Estás intentando añadir más allá de las existencias!");
                    }
                } else {
                    $this->alert('warning', "¡Artículo sin existencias!");
                }
            }
            $this->precio = 0;
            foreach ($this->lista as $prod => $valo) {
                $anadir = Productos::where('id', $prod)->firstOrFail()->precio_venta;
                $this->precio += ($anadir * $valo);
            }
        }
    }

    public function reducir($id)
    {
        if (isset($this->lista[$id])) {
            if ($this->lista[$id] - 1 <= 0) {
                $this->precio -= ((Productos::where('id', $id)->first()->precio_venta) * $this->lista[$id]);
                unset($this->lista[$id]);
            } else {
                $this->lista[$id] -= 1;
                $this->precio -= ((Productos::where('id', $id)->first()->precio_venta));
            }
        } else {
            $this->alert('warning', "Este producto no está en la lista");
        }
    }

    public function aumentar($id)
    {
        $producto = Productos::where('id', $id)->first();
        if (isset($this->lista[$id])) {
            if (($this->lista[$id] + 1) > Almacen::where('cod_producto', $producto->cod_producto)->first()->existencias) {
                $this->alert('warning', "Existencias máximas alcanzadas.");
            } else {
                $this->lista[$id] += 1;
                $this->precio += ((Productos::where('id', $id)->first()->precio_venta));
            }
        } else {
            $this->alert('warning', "Este producto no está en la lista");
        }
    }


    public function updatedServicio()
    {
        $this->emit('refreshTomSelect');
    }
    public function updatedProducto_seleccionado()
    {
        $this->emit('refreshTomSelect');
    }
}
