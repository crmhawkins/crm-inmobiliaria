<?php

namespace App\Http\Livewire\Inmuebles;

use App\Models\Inmuebles;
use App\Models\TipoVivienda;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;

class IdealistaIndex extends Component
{
    use WithPagination;
    use LivewireAlert;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $estadoFilter = '';
    public $tipoFilter = '';
    public $sortBy = 'idealista_synced_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingEstadoFilter()
    {
        $this->resetPage();
    }

    public function updatingTipoFilter()
    {
        $this->resetPage();
    }

    public function sortByField($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function render()
    {
        $query = Inmuebles::query()
            ->whereNotNull('idealista_property_id')
            ->with(['tipoVivienda', 'vendedor']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('titulo', 'like', '%' . $this->search . '%')
                    ->orWhere('ubicacion', 'like', '%' . $this->search . '%')
                    ->orWhere('idealista_code', 'like', '%' . $this->search . '%')
                    ->orWhere('idealista_property_id', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->estadoFilter) {
            $query->where('disponibilidad', $this->estadoFilter);
        }

        if ($this->tipoFilter) {
            $query->where('tipo_vivienda_id', $this->tipoFilter);
        }

        $query->orderBy($this->sortBy, $this->sortDirection);

        $inmuebles = $query->paginate(20);
        $tiposVivienda = TipoVivienda::all();

        return view('livewire.inmuebles.idealista-index', [
            'inmuebles' => $inmuebles,
            'tiposVivienda' => $tiposVivienda,
        ]);
    }
}

