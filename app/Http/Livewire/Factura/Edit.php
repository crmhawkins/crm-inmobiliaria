<?php

namespace App\Http\Livewire\Factura;

use Livewire\Component;
use App\Models\Factura;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Edit extends Component
{
    use LivewireAlert;

    public $identificador;

    public $factura;

    public function mount(){
        $this->factura = Factura::find($this->identificador);
;
        }

    public function render()
    {

        return view('livewire.factura.edit');
    }

    public function update()
    {

            $validatedData = $this->validate(
                [
                    'cliente' => 'required',
                    'numero_factura' => 'required',
                    'fecha' => 'required',
                    'fecha_vencimiento' => 'nullable',
                    'condiciones' => 'required',
                    'articulos' => 'nullable',
                    'subtotal' => 'nullable',
                    'total' => 'nullable',
                    'ruta_pdf' => 'nullable',
                    'inmobiliaria' => 'nullable',

                ],
                // Mensajes de error
                [
                    'cliente.required' => 'Selecciona un cliente.',
                    'fecha.required' => 'Escoge una fecha de emisión.',
                    'condiciones.required' => 'Indica las condiciones y métodos de pago.',
                ]
            );



        // Guardar datos validados
        // Encuentra el alumno identificado
        $factura = Factura::find($this->identificador);

        // Guardar datos validados
        $facturaSave = $factura->update([
            'nombre' => $this->nombre,
            'valor' => $this->valor,
            'descripcion' => $this->valor,
            'peso_min' => $this->peso_min,
            'peso_max' => $this->peso_max,
            'diametro_mayor_1400' => $this->diametro_mayor_1400,

        ]);

        // Alertas de guardado exitoso
        if ($facturaSave) {
            $this->alert('success', '¡Factura actualizada correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido guardar la información del factura!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function destroy(){
        // $product = Productos::find($this->identificador);
        // $product->delete();

        $this->alert('warning', '¿Seguro que desea borrar el factura? No hay vuelta atrás', [
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
        return redirect()->route('factura.index');

    }
    // Función para cuando se llama a la alerta
    public function confirmDelete()
    {
        $factura = Factura::find($this->identificador);
        $factura->delete();
        return redirect()->route('factura.index');

    }
}
