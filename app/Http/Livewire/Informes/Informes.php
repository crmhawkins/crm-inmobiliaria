<?php

namespace App\Http\Livewire\Informes;

use Livewire\Component;

class Informes extends Component
{
    public $tab = "tab1";
    public $datos;
    public $tipo_informe;
    public $tipos_informe;
    public $fecha_inicio;
    public $fecha_fin;
    public $servicio;

    public $categorias_informe;

    protected $listeners = [
        'seleccionarProducto' => 'selectProducto',
        'tiposSeleccionadosChanged' => 'handleTiposSeleccionadosChange',
        'categoriasSeleccionadasChanged' => 'handleCategoriasSeleccionadasChange',
    ];

    public function handleTiposSeleccionadosChange($tiposSeleccionados)
    {
        $this->tipos_informe = $tiposSeleccionados;
    }

    public function handleCategoriasSeleccionadasChange($categoriasSeleccionadas)
    {
        $this->categorias_informe = $categoriasSeleccionadas;
    }

    public function render()
    {
        return view('livewire.informes.informes');
    }

    public function cambioTab($tab){
        $this->tab = $tab;
        $this->provideTipoCategoria();
    }
    public function selectProducto($datos, $tipo_informe, $fecha_inicio, $fecha_fin, $servicio){
        $this->datos = $datos;
        $this->tipo_informe = $tipo_informe;
        $this->fecha_inicio = $fecha_inicio;
        $this->fecha_fin = $fecha_fin;
        $this->servicio = $servicio;
        $this->tab = "tab3";
    }

    public function provideTipoCategoria() {
        // Solo emite los valores si no están vacíos
        if (!empty($this->tipos_informe)) {
            $this->emit('provideTipos', $this->tipos_informe);
        }
        if (!empty($this->categorias_informe)) {
            $this->emit('provideCategorias', $this->categorias_informe);
        }
    }
}
