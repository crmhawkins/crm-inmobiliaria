<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\HojaVisita;
use Livewire\Component;

class VisitaIndex extends Component
{
    public $hojas_visita;

    public function mount($inmueble_id = null){
        if($inmueble_id != null){
            $this->hojas_visita = HojaVisita::where('inmueble_id', $inmueble_id)->get();
        }
    }
    public function render()
    {
        return view('livewire.inmuebles.visita-index');
    }
}
