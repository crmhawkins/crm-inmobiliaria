<?php

namespace App\Http\Livewire\Productos;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\Productos;
use App\Models\Neumatico;
use App\Models\Almacen;
use App\Models\Ecotasa;
use App\Models\ListaAlmacen;
use App\Models\TipoProducto;
use App\Models\ProductosCategories;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use PDF;

/**
 * Summary of IndexComponent
 */
class IndexComponent extends Component
{
    use LivewireAlert;
    public $tipos_producto;
    public $categorias;
    public $tipo_producto = "";

    public $busqueda_codigo = "";
    public $busqueda_descripcion = "";


    public $busqueda_res_rod = "";

    public $busqueda_ag_moj = "";

    public $busqueda_em_ruido = "";
    public $busqueda_ancho = "";

    public $busqueda_serie = "";

    public $busqueda_llanta = "";
    public $busqueda_ic = "";

    public $busqueda_cv = "";
    public $busqueda_categoria = "";

    public $productos;
    public $neumaticos;
    public $almacenes;
    public $listAlmacenes;
    public $tasas;


    public $pagina;
    protected $tabla;
    public $porPagina = 10;

    protected $listeners = ['refreshComponent' => '$refresh'];


    public function mount()
    {
        $this->categorias = ProductosCategories::all();
        $this->tipos_producto = TipoProducto::all();
        $this->neumaticos = Neumatico::all();
        $this->productos = Productos::all();
        $this->almacenes = Almacen::all();
        $this->listAlmacenes = ListaAlmacen::all();
        $this->tasas = Ecotasa::all();

    }
    public function render()
    {
        $this->tabla = $this->pagination($this->productos);
        return view('livewire.productos.index-component', [
            'tabla' => $this->tabla
        ]);
    }
    public function seleccionarProducto($id)
    {
        $this->emit("seleccionarProducto", $id);
    }

    /**
     * @return void
     */
    public function select_producto()
    {
        $queryProductos = Productos::query();  // Inicializamos la consulta

        if ($this->tipo_producto == 2) {

            $queryNeumaticos = Neumatico::query();

            if (!empty($this->busqueda_res_rod) || $this->busqueda_res_rod != "") {
                $queryNeumaticos->where('resistencia_rodadura', $this->busqueda_res_rod);
            }

            if (!empty($this->busqueda_ag_moj) || $this->busqueda_ag_moj != "") {
                $queryNeumaticos->where('agarre_mojado', $this->busqueda_ag_moj);
            }

            if (!empty($this->busqueda_em_ruido) || $this->busqueda_em_ruido != "") {
                $queryNeumaticos->where('emision_ruido', $this->busqueda_em_ruido);
            }

            if (!empty($this->busqueda_ancho) || $this->busqueda_ancho != "") {
                $queryNeumaticos->where('ancho', $this->busqueda_ancho);
            }

            if (!empty($this->busqueda_serie) || $this->busqueda_serie != "") {
                $queryNeumaticos->where('serie', $this->busqueda_serie);
            }

            if (!empty($this->busqueda_llanta) || $this->busqueda_llanta != "") {
                $queryNeumaticos->where('llanta', $this->busqueda_llanta);
            }

            if (!empty($this->busqueda_ic) || $this->busqueda_ic != "") {
                $queryNeumaticos->where('indice_carga', $this->busqueda_ic);
            }

            if (!empty($this->busqueda_cv) || $this->busqueda_cv != "") {
                $queryNeumaticos->where('codigo_velocidad', $this->busqueda_cv);
            }

            $codigosNeumaticos = $queryNeumaticos->pluck('articulo_id')->toArray();

            $queryProductos = Productos::whereIn('id', $codigosNeumaticos)->getQuery();

            if(!empty($this->busqueda_codigo)){

                $queryProductos->where('cod_producto', 'like', '%' . $this->busqueda_codigo . '%');

            }

            if(!empty($this->busqueda_descripcion)){

                $queryProductos->where('descripcion', 'like', '%' . $this->busqueda_descripcion . '%');

            }
        } else{

            if (!empty($this->tipo_producto)) {
                $queryProductos->where('tipo_producto', $this->tipo_producto);
                $this->categorias = ProductosCategories::where('tipo_producto', $this->tipo_producto)->get();
            }

            if (!empty($this->busqueda_categoria)) {
                $queryProductos->where('categoria_id', $this->busqueda_categoria);
            }

            if(!empty($this->busqueda_codigo)){

                $queryProductos->where('cod_producto', 'like', '%' . $this->busqueda_codigo . '%');

            }

            if(!empty($this->busqueda_descripcion)){

                $queryProductos->where('descripcion', 'like', '%' . $this->busqueda_descripcion . '%');

            }

        }

        $this->productos = $queryProductos->get();

        $this->tabla = $this->pagination($this->productos);

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
}
