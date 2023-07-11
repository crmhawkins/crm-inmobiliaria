<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaInforme extends Model
{
    use HasFactory;

    protected $table = "categoria_informe";

    protected $fillable = [
        'nombre',
    ];

    /**
     * Mutaciones de fecha.
     *
     * @var array
     */
    protected $dates = [
        'created_at', 'updated_at', 'deleted_at',
    ];

    public function tipos()
    {
        return $this->hasMany("App\Models\TipoInforme", "categoria_id");
    }

}
