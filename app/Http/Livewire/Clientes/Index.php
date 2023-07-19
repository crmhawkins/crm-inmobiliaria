<?php

namespace App\Http\Livewire\Clientes;
use App\Models\Clientes;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $clientes;

    public function render()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->clientes = Clientes::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->clientes = Clientes::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }
        return view('livewire.clientes.index', [
            'clientes' => $this->clientes]);
    }

}
