<?php

namespace App\Http\Livewire\Caja;

use App\Models\CobroCaja;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use App\Models\Facturas;
use App\Models\Presupuesto;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;
    public $factura;
    public $metodo_pago;
    public $fecha;

    public $cantidad;
    public $descripcion;
    public $facturas;

    public $presupuestos;

    public function mount(){
        $this->facturas = Facturas::all();
        $this->presupuestos = Presupuesto::all();

        $movimiento = CobroCaja::find($this->identificador);

        $this->fecha = date('Y-m-d', strtotime($movimiento->fecha));
        $this->cantidad = $movimiento->cantidad;
        $this->descripcion = $movimiento->descripcion;
        $this->metodo_pago = $movimiento->metodo_pago;

        if (!empty(session('factura'))) {
            $this->factura = session('factura');
            $this->metodo_pago = session('metodo_pago');
            $this->cantidad = $this->facturas->where('id', $this->factura)->first()->precio_iva;
            $this->descripcion = "Marca: " . $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->marca . '&nbsp;'
            . "Modelo: " . $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->modelo . '&nbsp;'
            . "Matricula: " . $this->presupuestos->where('id', $this->facturas->where('id', $this->factura)->first()->id_presupuesto)->first()->matricula . '&nbsp;';
        }
    }

    public function render()
    {
        return view('livewire.caja.edit-component');
    }

    public function update()
    {
        // Validación de datos
        $this->validate([
            'fecha' => 'required',
            'metodo_pago' => 'required',
            'descripcion' => 'nullable',
            'cantidad' => 'required',
        ],
            // Mensajes de error
            [
                'fecha.required' => 'Indique un nº de factura.',
                'metodo_pago.required' => 'Ingrese una fecha de emisión',
                'cantidad.required' => 'Seleccione un presupuesto',
            ]);

        // Encuentra el identificador
        $movimiento = CobroCaja::find($this->identificador);

        // Guardar datos validados
        $movimientoSave = $movimiento->update([
            'fecha' => $this->fecha,
            'metodo_pago' => $this->metodo_pago,
            'descripcion' => $this->descripcion,
            'cantidad' => $this->cantidad,
        ]);

        if ($movimientoSave) {
            $this->alert('success', 'Movimiento actualizado correctamente!', [
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

        session()->flash('message', 'Movimiento actualizado correctamente.');

        $this->emit('productUpdated');
    }

      // Eliminación
      public function destroy(){

        $this->alert('warning', '¿Seguro que desea borrar el la factura? No hay vuelta atrás', [
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
            'listarPresupuesto'
        ];
    }

    // Función para cuando se llama a la alerta
    public function confirmed()
    {
        // Do something
        return redirect()->route('facturas.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $factura = Facturas::find($this->identificador);
        $factura->delete();
        return redirect()->route('facturas.index');

    }
}
