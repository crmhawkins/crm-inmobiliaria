<?php

namespace App\Http\Livewire\Facturas;

use App\Models\Presupuestos;
use App\Models\Cursos;
use App\Models\Alumno;
use App\Models\Facturas;
use App\Models\Empresa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class EditComponent extends Component
{
    use LivewireAlert;

    public $identificador;


    public $numero_factura;
    public $id_presupuesto;
    public $fecha_emision;
    public $fecha_vencimiento;
    public $descripcion;
    public $estado;
    public $metodo_pago;

    public $alumnosSinEmpresa;
    public $alumnosConEmpresa;
    public $cursos;

    public $presupuestos;

    public $estadoPresupuesto;
    public $presupuestoSeleccionado;
    public $alumnoDePresupuestoSeleccionado;
    public $cursoDePresupuestoSeleccionado;


    public function mount()
    {
        $facturas = Facturas::find($this->identificador);

        $this->presupuestos = Presupuesto::all();

        $this->numero_factura = $facturas->numero_factura;
        $this->id_presupuesto = $facturas->id_presupuesto;
        $this->fecha_emision = $facturas->fecha_emision;
        $this->fecha_vencimiento = $facturas->fecha_vencimiento;
        $this->descripcion = $facturas->descripcion;
        $this->estado = $facturas->estado;
        $this->metodo_pago = $facturas->metodo_pago;


        if ($this->id_presupuesto > 0 || $this->id_presupuesto != null) {
            $this->listarPresupuesto($this->id_presupuesto);
        }


    }

    public function render()
    {

        // $this->tipoCliente == 0;
        return view('livewire.facturas.edit-component');
    }

    // Al hacer update en el formulario
    public function update()
    {
        // Validación de datos
        $this->validate([
            'numero_factura' => 'required',
            'id_presupuesto' => 'required|numeric|min:1',
            'fecha_emision' => 'required',
            'fecha_vencimiento' => '',
            'descripcion' => '',
            'estado' => 'required',
            'metodo_pago' => '',
        ],
            // Mensajes de error
            [
                'numero_factura.required' => 'Indique un nº de factura.',
                'fecha_emision.required' => 'Ingrese una fecha de emisión',
                'id_presupuesto.min' => 'Seleccione un presupuesto',
            ]);

        // Encuentra el identificador
        $facturas = Facturas::find($this->identificador);

        // Guardar datos validados
        $facturasSave = $facturas->update([
            'numero_factura' => $this->numero_factura,
            'id_presupuesto' => $this->id_presupuesto,
            'fecha_emision' => $this->fecha_emision,
            'fecha_vencimiento' => $this->fecha_vencimiento,
            'descripcion' => $this->descripcion,
            'estado' => $this->estado,
            'metodo_pago' => $this->metodo_pago,

        ]);

        if ($facturasSave) {
            $this->alert('success', 'Factura actualizada correctamente!', [
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

        session()->flash('message', 'Factura actualizada correctamente.');

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

    public function listarPresupuesto($id){
        $this->id_presupuesto = $id;
    if($this->id_presupuesto != null){
        $this->estadoPresupuesto = 1;
        $this->presupuestoSeleccionado = Presupuestos::where('id', $this->id_presupuesto)->first();
        $this->alumnoDePresupuestoSeleccionado = Alumno::where('id', $this->presupuestoSeleccionado->alumno_id)->first();
        $this->cursoDePresupuestoSeleccionado = Cursos::where('id', $this->presupuestoSeleccionado->curso_id)->first();
    } else{
        $this->estadoPresupuesto = 0;

    }
}
}
