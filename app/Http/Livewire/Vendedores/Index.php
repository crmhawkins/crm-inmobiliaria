<?php

namespace App\Http\Livewire\Vendedores;

use App\Models\User;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $vendedores;

    public function render()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->vendedores = User::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
        } else {
            $this->vendedores = User::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
        }
        return view('livewire.vendedores.index', [
            'vendedores' => $this->vendedores]);
    }

}
