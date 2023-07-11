<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;
use App\Models\ProductosCategories;
use App\Models\Neumatico;

class ProductosFilter extends ModelFilter
{

    public function cod_producto($codigo){
        return $this->whereLike('cod_producto', $codigo);
    }
    public function descripcion($codigo){
        return $this->whereLike('descripcion', $codigo);
    }
    public function tipo_producto($codigo){
        return $this->where('tipo_producto', $codigo);
    }
    public function ecotasa($codigo){
        return $this->where('ecotasa', $codigo);
    }
    public function fabricante($codigo){
        return $this->whereLike('fabricante', $codigo);
    }
    public function categoria_id($codigo){
        return $this->where('categoria_id', ProductosCategories::where('nombre', $codigo)->first()->id);
    }
    public function mueve_existencias($codigo){
        return $this->where('mueve_existencias', $codigo);
    }

    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = ['neumaticos' => [ 'resistencia_rodadura', 
    'agarre_mojado',
    'emision_ruido',
    'uso',
    'ancho',
    'serie',
    'llanta',
    'indice_carga',
    'codigo_velocidad',
    ]];
}
