<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Evento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $eventos;

    public function mount()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->eventos = Evento::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->eventos = Evento::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }
    }
    public function render()
    {
        return view('livewire.agenda.index');
    }
}
