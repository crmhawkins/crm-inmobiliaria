<?php

namespace App\Http\Livewire\Presupuestos;

use App\Models\Almacen;
use App\Models\Presupuesto;
use Carbon\Carbon;
use App\Models\Clients;
use App\Models\ListaAlmacen;
use App\Models\Trabajador;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Productos;


class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;

    public $numero_presupuesto;

    public $fecha_emision;
    public $cliente_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $matricula;
    public $kilometros;
    public $trabajador_id = 0; // 0 por defecto por si no se selecciona ninguna
    public $precio = 0;
    public $origen;
    public $marca;
    public $modelo;

    public $observaciones = "";

    public $clientes;
    public $trabajadores;

    public $lista = []; // Se usa para generar factura de cliente o particular
    public $listaArticulos; // Para mostrar los inputs del alumno o empresa

    public $producto_seleccionado;
    public $servicio;

    public $producto;
    public $productos;

    public $almacenes;

    public $cantidad;

    public $vehiculo_renting;

    public function mount()
    {
        $presupuestos = Presupuesto::find($this->identificador);
        $this->clientes = Clients::all(); // datos que se envian al select2
        $this->trabajadores = Trabajador::all(); // datos que se envian al select2
        $this->productos = Productos::all(); // datos que se envian al select2
        $this->almacenes = ListaAlmacen::all();

        $this->numero_presupuesto = $presupuestos->numero_presupuesto;
        $this->fecha_emision = $presupuestos->fecha_emision;
        $this->cliente_id = $presupuestos->cliente_id;
        $this->trabajador_id = $presupuestos->trabajador_id;
        $this->lista = (array) json_decode($presupuestos->listaArticulos);
        $this->kilometros = $presupuestos->kilometros;
        $this->matricula = $presupuestos->matricula;
        $this->precio = $presupuestos->precio;
        $this->origen = $presupuestos->origen;
        $this->marca = $presupuestos->marca;
        $this->modelo = $presupuestos->modelo;
        $this->observaciones = $presupuestos->observaciones;

    }

    public function render()
    {
        return view('livewire.presupuestos.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        $this->listaArticulos = json_encode($this->lista);
        // Validación de datos
        $this->validate([
            'numero_presupuesto' => 'required',
            'fecha_emision' => 'required',
            'cliente_id' => 'required',
            'trabajador_id' => 'required',
            'matricula' => 'required',
            'listaArticulos' => 'required',
            'precio' => 'required',
            'origen' => 'required',
            'vehiculo_renting' => 'required',
            'marca' => 'required',
            'modelo' => 'required',
            'kilometros' => 'required',
            'observaciones' => 'required',
        ],
            // Mensajes de error
            [
                'numero_presupuesto.required' => 'El número de presupuesto es obligatorio.',
                'fecha_emision.required' => 'La fecha de emision es obligatoria.',
                'alumno_id.required' => 'El alumno es obligatorio.',
                'curso_id.required' => 'El curso es obligatorio.',
                'detalles.required' => 'Los detalles son obligatorios',
                'precio.required' => 'El precio es obligaorio',
                'estado.required' => 'El estado es obligatorio',
                'observaciones.required' => 'La observación es obligatoria',
            ]);

        // Encuentra el identificador
        $presupuestos = Presupuesto::find($this->identificador);

        // Guardar datos validados
        $presupuestosSave = $presupuestos->update([
            'numero_presupuesto' => $this->numero_presupuesto,
            'fecha_emision' => $this->fecha_emision,
            'cliente_id' => $this->cliente_id,
            'trabajador_id' => $this->trabajador_id,
            'matricula' => $this->matricula,
            'marca' => $this->marca,
            'modelo' => $this->modelo,
            'precio' => $this->precio,
            'origen' => $this->origen,
            'listaArticulos' => $this->listaArticulos,
            'vehiculo_renting' => $this->vehiculo_renting,
            'kilometros' => $this->kilometros,
            'observaciones' => $this->observaciones,

        ]);

        if ($presupuestosSave) {
            $this->alert('success', '¡Presupuesto actualizado correctamente!', [
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

        session()->flash('message', 'Presupuesto actualizado correctamente.');

        $this->emit('productUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el presupuesto? No hay vuelta atrás', [
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
        return redirect()->route('presupuestos.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $presupuesto = Presupuesto::find($this->identificador);
        $presupuesto->delete();
        return redirect()->route('presupuestos.index');

    }

    public function numeroPresupuesto(){
        $fecha = new Carbon($this->fecha_emision);
        $year = $fecha->year;
        $presupuestos = Presupuesto::all();
        $contador = 1;
        foreach($presupuestos as $presupuesto){
            $fecha2 = new Carbon($presupuesto->fecha_emision);
            $year2 = $fecha2->year;
            if($year == $year2){
                if($fecha->gt($fecha2)){
                    $contador++;
                }
            }
        }

        if($contador < 10){
            $this->numero_presupuesto = "0" . $contador . "/" . $year;
        } else{
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
}

