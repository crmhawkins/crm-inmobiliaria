<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Almacen;
use App\Models\Alumno;
use App\Models\Cursos;
use App\Models\Empresa;
use App\Models\Presupuesto;
use App\Models\Facturas;
use App\Models\Neumatico;
use App\Models\OrdenTrabajo;
use App\Models\Productos;
use App\Models\Reserva;
use Carbon\Carbon;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class CreateComponent extends Component
{

    use LivewireAlert;

    public $numero_factura;
    public $id_presupuesto = 0; // 0 por defecto por si no se selecciona ninguno
    public $fecha_emision;
    public $tipo_documento;
    public $fecha_vencimiento;
    public $descripcion;
    public $documentos;
    public $observaciones;
    public $precio;
    public $precio_iva;


    public $estado = "Pendiente";
    public $metodo_pago = "No Pagado";

    public $listaPresupuestos = [];

    public $productos;
    public $tareas;
    public $neumaticos;
    public $presupuestos;
    public $reservas;


    public $ident;


    public function mount()
    {
        $this->presupuestos = Presupuesto::all();
        $this->reservas = Reserva::all();
        $this->productos = Productos::all();
        $this->neumaticos = Neumatico::all();
        $this->tareas = OrdenTrabajo::all();
    }

    public function render()
    {
        return view('livewire.facturas.create-component');
    }


    // Al hacer submit en el formulario
    public function submit($metodo_pago)
    {
        $this->metodo_pago = $metodo_pago;

        if ($this->tipo_documento == 'albaran_credito') {
            foreach ($this->listaPresupuestos as $presupuestos) {
                $this->presupuestos->where('id', $presupuestos)->first()->update([
                    'estado' => 'Facturada'
                ]);
            }
            $this->id_presupuesto = json_encode($this->listaPresupuestos);
        } else {
            $this->presupuestos->where('id', $this->id_presupuesto)->first()->update([
                'estado' => 'Facturada'
            ]);
        }
        if($this->metodo_pago != 'No pagado'){
            $this->estado = "Pagada";
        }

        // Validación de datos
        $validatedData = $this->validate(
            [
                'numero_factura' => 'required',
                'id_presupuesto' => 'required',
                'fecha_emision' => 'required',
                'fecha_vencimiento' => 'nullable',
                'descripcion' => 'nullable',
                'tipo_documento' => 'required',
                'documentos' => 'nullable',
                'observaciones' => 'nullable',
                'estado' => 'required',
                'precio' => 'required',
                'precio_iva' => 'required',
                'metodo_pago' => 'nullable',

            ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',
            ]
        );

        // Guardar datos validados
        $facturasSave = Facturas::create($validatedData);


        // Alertas de guardado exitoso
        if ($facturasSave) {
            $this->ident = $facturasSave->id;

            $reservas = Reserva::where('presupuesto_id', $facturasSave->id_presupuesto)->get();

            foreach ($reservas as $reserva) {
                $reserva->update([
                    'estado' => "Aceptado"
                ]);

                $stock = Almacen::where('cod_producto', Productos::where('id', $reserva->producto_id)->first()->cod_producto)->where('nombre', Presupuesto::where('id', $facturasSave->id_presupuesto)->first()->servicio)->first();
                $existencias = $stock->existencias_depositos -= $reserva->cantidad;
                $stock->update([
                    'existencias_depositos' => $existencias
                ]);
            }

            $this->alert('success', 'Factura registrada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información de la factura!', [
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

        if ($this->metodo_pago == 'No pagado') {
            return redirect()->route('facturas.index');
        } else {
            $this->redirectToCaja($this->metodo_pago);
        }
    }

    public function addPresupuesto()
    {
        array_push($this->listaPresupuestos, $this->id_presupuesto);
        $this->id_presupuesto = 0;
        $this->addPrecio();
    }

    public function addObservaciones()
    {
        if ($this->tipo_documento == 'factura') {
            $this->observaciones = $this->tareas->where('id_presupuesto', $this->id_presupuesto)->first()->observaciones;
        } else {
            foreach ($this->listaPresupuestos as $presupuesto) {
                $this->observaciones = [$this->presupuestos->where('id', $presupuesto)->first()->numero_presupuesto => $this->tareas->where('id_presupuesto', $presupuesto)->first()->observaciones];
            }
        }
    }

    public function addDocumentos()
    {
        if ($this->tipo_documento == 'factura') {
            $this->documentos = json_decode($this->tareas->where('id_presupuesto', $this->id_presupuesto)->first()->documentos, true);
        } else {
            $this->documentos = [];
            foreach ($this->listaPresupuestos as $presupuesto) {
                $this->documentos = array_merge($this->documentos, json_decode($this->tareas->where('id_presupuesto', $presupuesto)->first()->documentos, true));
            }
        }
    }

    public function addPrecio()
    {
        if ($this->tipo_documento == 'factura') {
            if ($this->id_presupuesto > 0) {
                $this->precio = $this->presupuestos->where('id', $this->id_presupuesto)->first()->precio;
                $this->precio_iva = round(($this->precio) + ($this->precio * 0.21), 2);
            }
        } else {
            foreach ($this->listaPresupuestos as $presupuesto) {
                $this->precio += $this->presupuestos->where('id', $presupuesto)->first()->precio;
            }
            $this->precio_iva = round(($this->precio) + ($this->precio * 0.21), 2);
        }
    }

    public function numeroFactura()
    {
        $fecha = new Carbon($this->fecha_emision);
        $year = $fecha->year;
        $facturas = Facturas::all();
        $contador = 1;
        foreach ($facturas as $factura) {
            $fecha2 = new Carbon($factura->fecha_emision);
            $year2 = $fecha2->year;
            if ($year == $year2) {
                if ($fecha->gt($fecha2)) {
                    $contador++;
                }
            }
        }

        if ($contador < 10) {
            $this->numero_factura = "0" . $contador . "/" . $year;
        } else {
            $this->numero_factura = $contador . "/" . $year;
        }
    }

    public function redirectToCaja($metodo_pago)
    {
        session()->flash('factura', $this->ident);
        session()->flash('metodo_pago', $metodo_pago);

        return redirect()->route('caja.index');
    }
}
