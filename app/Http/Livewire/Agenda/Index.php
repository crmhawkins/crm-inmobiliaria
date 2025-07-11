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

    public function seleccionarProducto($eventoId)
    {
        $this->emit('eventoSeleccionado', $eventoId);
    }

        protected $listeners = ['eliminarEvento'];

    public function eliminarEvento($eventoId)
    {
        $evento = Evento::find($eventoId);

        if ($evento) {
            $evento->delete();

            $this->alert('success', 'Evento eliminado correctamente.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'confirmButtonText' => 'OK',
                'timerProgressBar' => true,
            ]);

            // Recargar los eventos
            if (request()->session()->get('inmobiliaria') == 'sayco') {
                $this->eventos = Evento::where('inmobiliaria', true)->orWhere('inmobiliaria', null)->get();
            } else {
                $this->eventos = Evento::where('inmobiliaria', false)->orWhere('inmobiliaria', null)->get();
            }
        } else {
            $this->alert('error', 'No se pudo eliminar el evento.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
}
