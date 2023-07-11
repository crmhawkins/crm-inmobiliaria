<?php

namespace App\Http\Livewire\Ecotasa;
use App\Models\Ecotasa;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $ecotasa;

    public function render()
    {
            $this->ecotasa = Ecotasa::where('diametro_mayor_1400', 0)->paginate(5);

        return view('livewire.ecotasa.index', [
            'ecotasa' => $this->ecotasa]);
    }

}
