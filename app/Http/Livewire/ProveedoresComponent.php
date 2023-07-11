<?php

namespace App\Http\Livewire;
use App\Models\Proveedores;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ProveedoresComponent extends Component
{

    public $proveedores;

    public function mount()
    {
        $this->proveedores = Proveedores::all();
    }

    public function render()
    {
        return view('livewire.proveedores.proveedores-component', [
            'proveedores' => $this->proveedores,
        ]);
    }

    
}
