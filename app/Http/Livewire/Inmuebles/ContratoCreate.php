<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\ContratoArras;
use Livewire\Component;

class ContratoCreate extends Component
{
    public $inmueble_id;
    public $contrato;
    public $contratoArras;

    public function mount($inmueble_id = null){
        $this->inmueble_id = $inmueble_id;
        if($inmueble_id != null){
            if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();
            }
        }
    }
    public function render()
    {
        if($this->inmueble_id != null){
            if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {
                $this->contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();
            }
        }
        return view('livewire.inmuebles.contrato-create');
    }

    public function addContrato()
    {
        if (ContratoArras::where('inmueble_id', $this->inmueble_id)->exists()) {

            // Obtener el ContratoArras
            $contratoArras = ContratoArras::where('inmueble_id', $this->inmueble_id)->first();

            // Actualiza la ruta del ContratoArras
            $contratoArras->ruta = $this->contrato; // Sobrescribe cualquier ruta existente
            $contratoArras->save();
        } else {
            // Si el ContratoArras no existe, se crea uno nuevo
            $contratoArras = new ContratoArras;
            $contratoArras->inmueble_id = $this->inmueble_id;
            $contratoArras->ruta = $this->contrato; // Asume que $rutaNueva contiene la nueva ruta
            $contratoArras->save();
        }
    }
}
