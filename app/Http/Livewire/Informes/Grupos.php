<?php

namespace App\Http\Livewire\Informes;

use App\Models\GrupoInformes;
use App\Models\ProductosCategories;
use App\Models\TipoProducto;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Grupos extends Component
{
    use LivewireAlert;
    public $tipos_producto;
    public $productos_categoria;
    public $grupos;
    public $grupo = 0;
    public $tipoTodos = 0;
    public $catTodos = 0;

    public $tiposSeleccionados = [];
    public $categoriasSeleccionadas = [];
    protected $listeners = [
        'provideTipos' => 'getTiposPadre',
        'provideCategorias' => 'getCategoriasPadre',
    ];

    public function mount()
    {
        $this->grupos = GrupoInformes::all();
        $this->tipos_producto = TipoProducto::all();
    }
    public function render()
    {
        $this->productos_categoria = ProductosCategories::whereIn('tipo_producto', $this->tiposSeleccionados)->get();
        return view('livewire.informes.grupos', ['productos_categoria' => $this->productos_categoria]);
    }


    public function seleccionarCatTodos()
    {
        if ($this->catTodos != 0) {
            foreach ($this->productos_categoria as $categoria) {
                $this->categoriasSeleccionadas[] = $categoria->id;
            }
        } else {
            $this->categoriasSeleccionadas = [];
        }
    }
    public function seleccionarTipoTodos()
    {
        if ($this->tipoTodos != 0) {
            foreach ($this->tipos_producto as $tipo) {
                $this->tiposSeleccionados[] = $tipo->id;
            }
        } else {
            $this->tiposSeleccionados = [];
        }
    }

    public function updatedTiposSeleccionados()
    {
        $this->emit('tiposSeleccionadosChanged', $this->tiposSeleccionados);
        if ($this->grupo != 0) {
            if ($this->tiposSeleccionados != json_decode(GrupoInformes::where('id', $this->grupo)->first()->tipos_producto, true)) {
                $this->grupo = 0;
            }
        }
    }

    public function updatedGrupo()
    {
        if ($this->grupo != 0) {
            $this->tiposSeleccionados = json_decode(GrupoInformes::where('id', $this->grupo)->first()->tipos_producto, true);
            $this->categoriasSeleccionadas = json_decode(GrupoInformes::where('id', $this->grupo)->first()->categorias, true);
        }
    }

    public function updatedCategoriasSeleccionadas()
    {
        $this->emit('categoriasSeleccionadasChanged', $this->categoriasSeleccionadas);
        if ($this->grupo != 0) {
            if ($this->categoriasSeleccionadas != json_decode(GrupoInformes::where('id', $this->grupo)->first()->categorias, true)) {
                $this->grupo = 0;
            }
        }
    }

    public function getTiposPadre($tipos_informe)
    {
        if (empty($this->tiposSeleccionados)) {
            $this->tiposSeleccionados = $tipos_informe;
        }
    }

    public function getCategoriasPadre($categorias_informe)
    {
        if (empty($this->categoriasSeleccionadas)) {
            $this->categoriasSeleccionadas = $categorias_informe;
        }
    }

    public function addGrupo()
    {
        if (empty($this->categoriasSeleccionadas)) {
            $this->alert('warning', 'Elige primero una categoría');
        } else{
            if ($this->grupo == 0) {
                $data = [
                    'tipos_producto' => json_encode($this->tiposSeleccionados),
                    'categorias' => json_encode($this->categoriasSeleccionadas),
                ];
                $grupo = GrupoInformes::create($data);
                $grupoSave = $grupo->save();

                if ($grupoSave) {
                    $this->grupo = $grupoSave->id;
                }
            }
        }

    }
}
