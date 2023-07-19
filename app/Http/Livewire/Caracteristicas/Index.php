<?php

namespace App\Http\Livewire\Caracteristicas;

use App\Models\Caracteristicas;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $caracteristicas;

    public function render()
    {
            $this->caracteristicas = Caracteristicas::all();

        return view('livewire.caracteristicas.index', [
            'caracteristicas' => $this->caracteristicas]);
    }

}
