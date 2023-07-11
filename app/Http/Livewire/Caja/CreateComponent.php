<?php

namespace App\Http\Livewire\Caja;

use App\Models\CobroCaja;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Facturas;
use App\Models\OrdenTrabajo;
use App\Models\Presupuesto;

class CreateComponent extends Component
{
    use LivewireAlert;

    public $factura;
    public $tarea;

    public $metodo_pago;
    public $fecha;

    public $cantidad;
    public $descripcion;
    public $facturas;
    public $tareas;
    public $presupuestos;

    public function mount()
    {
        $this->facturas = Facturas::all();
        $this->presupuestos = Presupuesto::all();
        $this->tareas = OrdenTrabajo::all();

        if (!empty(session('factura'))) {
            $this->factura = session('factura');
            $this->metodo_pago = session('metodo_pago');
            $this->cantidad = $this->facturas->where('id', $this->factura)->first()->precio_iva;
            $this->descripcion = $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->marca
                . " " . $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->modelo
                . " (" . $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->matricula . ')';
        }

        if (!empty(session('tarea'))) {
            $this->factura = session('tarea');
            $this->tarea = $this->tareas->where('id', $this->factura)->first();
            $this->metodo_pago = session('metodo_pago');
            $this->cantidad = round($this->tarea->presupuesto->precio + ($this->tarea->presupuesto->precio * 0.21), 2);
            $this->descripcion = $this->tarea->presupuesto->marca
                . " " . $this->tarea->presupuesto->modelo
                . " (" . $this->tarea->presupuesto->matricula . ')';
        }
    }

    public function render()
    {
        return view('livewire.caja.create-component');
    }

    public function submit()
    {

        // Validación de datos
        $validatedData = $this->validate(
            [
                'fecha' => 'required',
                'metodo_pago' => 'required',
                'descripcion' => 'nullable',
                'cantidad' => 'required',
            ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',
            ]
        );

        // Guardar datos validados
        $facturasSave = CobroCaja::create($validatedData);


        // Alertas de guardado exitoso
        if ($facturasSave) {

            if ($this->tarea != null) {

                // Guardar datos validados
                $tareaSave = $this->tarea->update([
                    'estado' => "Facturada"
                ]);

                $tareaSave = $this->tarea->presupuesto->update([
                    'estado' => "Pagada"
                ]);

                if ($tareaSave) {
                    $this->alert('success', '¡Movimiento registrado correctamente!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'onConfirmed' => 'confirmed',
                        'confirmButtonText' => 'ok',
                        'timerProgressBar' => true,
                    ]);
                } else {
                    $this->alert('error', '¡No se ha podido guardar la información del movimiento!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                    ]);
                }
            } else {
                if ($this->factura != null) {
                    $factSave = $this->factura->update([
                        'estado' => "Pagada"
                    ]);

                    if ($factSave) {
                        $this->alert('success', '¡Movimiento registrado correctamente!', [
                            'position' => 'center',
                            'timer' => 3000,
                            'toast' => false,
                            'showConfirmButton' => true,
                            'onConfirmed' => 'confirmed',
                            'confirmButtonText' => 'ok',
                            'timerProgressBar' => true,
                        ]);
                    } else {
                        $this->alert('error', '¡No se ha podido guardar la información del movimiento!', [
                            'position' => 'center',
                            'timer' => 3000,
                            'toast' => false,
                        ]);
                    }
                } else {
                    $this->alert('success', 'Factura registrada correctamente!', [
                        'position' => 'center',
                        'timer' => 3000,
                        'toast' => false,
                        'showConfirmButton' => true,
                        'onConfirmed' => 'confirmed',
                        'confirmButtonText' => 'ok',
                        'timerProgressBar' => true,
                    ]);
                }
            }
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
        return redirect()->route('caja.index');
    }
}
