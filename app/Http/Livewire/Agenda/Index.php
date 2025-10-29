<?php

namespace App\Http\Livewire\Agenda;

use App\Models\Evento;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination, LivewireAlert;
    protected $paginationTheme = 'bootstrap';
    public $eventos;

    public function mount()
    {
        $this->cargarEventos();
    }
    
    private function cargarEventos()
    {
        if (request()->session()->get('inmobiliaria') == 'sayco') {
            $this->eventos = Evento::where(function($query) {
                $query->where('inmobiliaria', true)->orWhereNull('inmobiliaria');
            })
            ->orderBy('fecha_inicio', 'desc') // Más recientes primero
            ->get();
        } else {
            $this->eventos = Evento::where(function($query) {
                $query->where('inmobiliaria', false)->orWhereNull('inmobiliaria');
            })
            ->orderBy('fecha_inicio', 'desc') // Más recientes primero
            ->get();
        }
    }
    public function render()
    {
        // Asegurar que los eventos se carguen en cada render
        if (empty($this->eventos)) {
            $this->cargarEventos();
        }
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
            $this->cargarEventos();
        } else {
            $this->alert('error', 'No se pudo eliminar el evento.', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }
}
