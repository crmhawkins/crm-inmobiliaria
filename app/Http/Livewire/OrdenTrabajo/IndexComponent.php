<?php

namespace App\Http\Livewire\OrdenTrabajo;

use App\Models\OrdenTrabajo;
use App\Models\Trabajador;
use App\Models\Clients;
use App\Models\Presupuesto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Livewire\Component;

class IndexComponent extends Component
{
    // public $search;
    public $tareas;
    public $tareaSel;
    public $tareaMostrar;
    public $clientes;
    public $trabajadores;

    public $pagina;
    public $porPagina = 10;
    protected $tabla;

    public function mount()
    {
        $this->tareas = OrdenTrabajo::where('operarios', null)->get();
        $this->clientes = Clients::all();
        $this->trabajadores = Trabajador::all();
        if(count($this->tareas) > 0){
            $this->tareaSel = $this->tareas->last()->id;
        }
    }

    public function seleccionarProducto($id)
    {
        $this->emit("seleccionarProducto", $id);
    }


    public function render()
    {
        $this->tareaMostrar = $this->tareas->find($this->tareaSel);
        return view('livewire.orden-trabajo.index-component');
    }

    public function pagination(Collection $data)
    {
        $items = $data->forPage($this->pagina, $this->porPagina);
        $totalResults = $data->count();

        return new LengthAwarePaginator(
            $items,
            $totalResults,
            $this->porPagina,
            $this->pagina,
            // Esta parte (options) la copie de lo que hace por defecto el paginador de Laravel haciendo un dd()
            [
                'path' => url()->current(),
                'pageName' => 'pagina',
            ]
        );
    }

}
