<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ContainerComponent extends Component
{
    public $tipo_producto;
    public function render()
    {
        return view('livewire.container-component');
    }

   }
