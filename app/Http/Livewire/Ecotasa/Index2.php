<?php

namespace App\Http\Livewire\Ecotasa;
use App\Models\Ecotasa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index2 extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $ecotasa;

    public function render()
    {
            $this->ecotasa = Ecotasa::where('diametro_mayor_1400', 1)->paginate(5);

        return view('livewire.ecotasa.index2', [
            'ecotasa' => $this->ecotasa]);
    }

    public function seleccionarProducto($ecotasa)
    {
        $this->emit("seleccionarProducto", $ecotasa);
    }

}
