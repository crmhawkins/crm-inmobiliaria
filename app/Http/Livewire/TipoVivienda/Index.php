<?php

namespace App\Http\Livewire\Tipovivienda;

use App\Models\TipoVivienda;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $tipos;

    public function render()
    {
            $this->tipos = TipoVivienda::all();

        return view('livewire.tipovivienda.index', [
            'tipos' => $this->tipos]);
    }

}
