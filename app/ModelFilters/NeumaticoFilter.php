<?php 

namespace App\ModelFilters;

use EloquentFilter\ModelFilter;

class NeumaticoFilter extends ModelFilter
{

    public function resistencia_rodadura($codigo){
        return $this->where('resistencia_rodadura', $codigo);
    }
    public function agarre_mojado($codigo){
        return $this->where('agarre_mojado', $codigo);
    }
    public function emision_ruido($codigo){
        return $this->where('emision_ruido', $codigo);
    }
    public function uso($codigo){
        return $this->where('uso', $codigo);
    }
    public function ancho($codigo){
        return $this->where('ancho', $codigo);
    }
    public function serie($codigo){
        return $this->where('serie', $codigo);
    }
    public function llanta($codigo){
        return $this->where('llanta', $codigo);
    }
    public function indice_carga($codigo){
        return $this->where('indice_carga', $codigo);
    }
    public function codigo_velocidad($codigo){
        return $this->where('codigo_velocidad', $codigo);
    }
    /**
    * Related Models that have ModelFilters as well as the method on the ModelFilter
    * As [relationMethod => [input_key1, input_key2]].
    *
    * @var array
    */
    public $relations = [];
}
