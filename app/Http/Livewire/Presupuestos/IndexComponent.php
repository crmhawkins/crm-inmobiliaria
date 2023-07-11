<?php

namespace App\Http\Livewire\Presupuestos;

use App\Models\Almacen;
use App\Models\OrdenTrabajo;
use App\Models\Productos;
use App\Models\Trabajador;
use App\Models\Clients;
use App\Models\Reserva;
use App\Models\Presupuesto;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class IndexComponent extends Component
{
    use LivewireAlert;
    public $presupuestos;
    public $clientes;
    public $trabajadores;

    public $filtro_busqueda = "";
    public $filtro_categoria = "";

    public $categorias;

    public $pagina;
    public $porPagina = 10;
    protected $tabla;

    protected $listeners = ['refreshComponent' => '$refresh'];


    public function mount()
    {
        $modelo = new Presupuesto;
        $this->presupuestos = Presupuesto::all();
        $this->clientes = Clients::all();
        $this->trabajadores = Trabajador::all();
        $this->categorias = [
            'numero_presupuesto' => "Número de presupuesto",
            'fecha_emision' => "Fecha de emisión",
            'cliente_id' => "ID de cliente",
            'nombre_cliente' => "Nombre de cliente",
            'estado' => "Estado",
            'matricula' => "Matricula",
            'kilometros' => "Kilometros",
            'trabajador_id' => "ID de trabajador",
            'precio' => "Importe total"
        ];
    }

    public function seleccionarProducto($id)
    {
        $this->emit("seleccionarProducto", $id);
    }

    public function render()
    {
        $this->tabla = $this->pagination($this->presupuestos);
        return view('livewire.presupuestos.index-component', [
            'tabla' => $this->tabla,
        ]);
    }

    /**
     * @return void
     */
    public function filtroCat()
    {
        if ($this->filtro_categoria != "" && $this->filtro_busqueda != "") {
            $this->alert('warning', "Hola");
            $this->presupuestos = Presupuesto::where($this->filtro_categoria, 'LIKE', '%' . $this->filtro_busqueda . '%')->get();
        } else {
            $this->presupuestos = Presupuesto::all();
        }

        $this->tabla = $this->pagination($this->presupuestos);
        $this->emit("refreshComponent");
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

    public function aceptarPresupuesto($id)
    {
        $presupuesto = $this->presupuestos->where('id', $id)->first();
        $presupuesto->update([
            'estado' => "Aceptado"
        ]);
        $orden = new OrdenTrabajo;
        $orden->fecha = $presupuesto->fecha_emision;
        $orden->id_cliente = $presupuesto->cliente_id;
        $orden->id_presupuesto = $presupuesto->id;
        $ordenSave = $orden->save();

        if ($ordenSave) {
            $this->alert('success', '¡Presupuesto aceptado correctamente!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
                'showConfirmButton' => true,
                'onConfirmed' => 'confirmed',
                'confirmButtonText' => 'ok',
                'timerProgressBar' => true,
            ]);
        } else {
            $this->alert('error', '¡No se ha podido crear una tarea!', [
                'position' => 'center',
                'timer' => 3000,
                'toast' => false,
            ]);
        }
    }

    public function rechazarPresupuesto($id)
    {
        $presupuesto = $this->presupuestos->find($id);
        $presupuesto->update([
            'estado' => "Rechazado"
        ]);
        $rechazados = Reserva::where('presupuesto_id', $id)->get();
        foreach ($rechazados as $reserva) {
            $pro = $reserva->producto_id;
            $articulo = Almacen::where('cod_producto', Productos::where('id', $pro)->first()->cod_producto)->first();
                $articulo->update([
                    'existencias' => ($articulo->existencias_almacenes += $reserva->cantidad),
                    'existencias_depositos' => ($articulo->existencias_depositos -= $reserva->cantidad)
                ]);
            $reserva->estado = "Rechazado";
        }
    }
}
